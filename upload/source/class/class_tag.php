<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class tag {
	// 时间衰减系数 - 热度随时间衰减的速率
	private const DECAY_FACTOR = 0.8;

	// 基础热度值 - 新关联内容的初始热度
	private const BASE_HOT_SCORE = 10;

	// 热度计算时间窗口(小时)
	private const TIME_WINDOW = 72;

	/**
	 * 获取不同内容类型的权重
	 *
	 * @param string $itemType 内容类型
	 * @return float 权重值
	 */
	private static function getContentWeight(string $itemType): float {
		$weights = [
			'tid' => 1.0,    // 主题
			'pid' => 0.5,      // 回复
			'articleid' => 1.2,   // 文章
			'commentid' => 0.5,   // 评论
			'blogid' => 1.0,     // 日志
			'doid' => 0.5,     // 记录
			// 可以添加更多Discuz!内容类型
		];

		return $weights[$itemType] ?? 1.0;
	}

	/**
	 * 添加标签
	 *
	 * @param string $tags 标签字符串
	 * @param int $itemid 关联内容ID
	 * @param string $idtype 内容类型
	 * @param int $returnarray 返回格式
	 * @return mixed
	 */
	public function add_tag($tags, $itemid, $idtype = 'tid', $returnarray = 0) {
		global $_G;

		if($tags == '' || !in_array($idtype, ['', 'tid', 'blogid', 'articleid', 'doid', 'uid'])) {
			return;
		}

		$tags = str_replace([chr(0xa3).chr(0xac), chr(0xa1).chr(0x41), chr(0xef).chr(0xbc).chr(0x8c)], ',', censor($tags));
		if(strexists($tags, ',')) {
			$tagarray = array_unique(explode(',', $tags));
		} else {
			$langcore = lang('core');
			$tags = str_replace($langcore['fullblankspace'], ' ', $tags);
			$tagarray = array_unique(explode(' ', $tags));
		}
		$tagcount = 0;
		$return = null;
		foreach($tagarray as $tagname) {
			$tagname = trim($tagname);
			if(preg_match('/^([\x7f-\xff_-]|\w|\s){2,50}$/', $tagname)) {
				$status = $idtype != 'uid' ? 0 : 3;
				$result = table_common_tag::t()->get_bytagname($tagname, $idtype);
				if($result['tagid']) {
					if($result['status'] == $status) {
						$tagid = $result['tagid'];
					}
				} else {
					$tagid = table_common_tag::t()->insert_tag($tagname, $status);
				}
				if($tagid) {
					if($itemid) {
						if(!table_common_tagitem::t()->select($tagid, $itemid, $idtype)) {
							table_common_tagitem::t()->replace($tagid, $itemid, $idtype);

							// 增加标签关联计数并触发热度计算
							table_common_tag::t()->increase($tagid, ['related_count' => 1]);

							// 异步更新热度，避免影响主线程性能
							//if(empty($_G['setting']['tag_hot_async']) || $_G['setting']['tag_hot_async'] != 1) {
							self::update_tag_hot_score($tagid);
							//} else {
							//todo 使用Discuz!的任务队列机制异步更新热度
							//}
						}
					}
					$tagcount++;
					if(!$returnarray) {
						$return .= $tagid.','.$tagname."\t";
					} else {
						$return[$tagid] = $tagname;
					}
				}
				if($tagcount > 4) {
					unset($tagarray);
					break;
				}
			}
		}
		return $return;
	}

	/**
	 * 更新标签热度值
	 *
	 * @param int $tagId 标签ID
	 * @return float|false 新的热度值，失败时返回false
	 */
	public static function update_tag_hot_score(int $tagId): float|false {
		// 1. 获取当前标签信息
		$tag = table_common_tag::t()->fetch_by_tagid($tagId);
		if (empty($tag)) {
			return false;
		}

		// 2. 获取上次更新时间
		$lastUpdateTime = $tag['updated_at'] ?? 0;
		$now = TIMESTAMP;

		// 3. 如果是首次计算或上次更新时间超过时间窗口，则完全重新计算
		if (empty($lastUpdateTime) || ($now - $lastUpdateTime) > (self::TIME_WINDOW * 3600)) {
			$hotScore = self::calculate_new_content_hot($tagId);
		} else {
			// 4. 否则，只计算上次更新后新增内容的热度
			$newContentHot = self::calculate_new_content_hot($tagId, $lastUpdateTime);

			// 5. 计算旧热度的衰减值
			$hoursDiff = ($now - $lastUpdateTime) / 3600;
			$decayedHot = $tag['hot_score'] * pow(self::DECAY_FACTOR, $hoursDiff);

			// 6. 合并新旧热度
			$hotScore = round($decayedHot + $newContentHot, 2);
		}

		// 7. 更新标签热度值
		table_common_tag::t()->update($tagId, [
			'hot_score' => $hotScore,
			'updated_at' => $now
		]);

		return $hotScore;
	}

	/**
	 * 计算新内容带来的热度
	 *
	 * @param int $tagId 标签ID
	 * @param int $startTime 开始时间戳，默认为时间窗口开始时间
	 * @return float 新内容带来的热度值
	 */
	private static function calculate_new_content_hot(int $tagId, int $startTime = 0): float {
		// 如果未指定开始时间，则使用时间窗口
		if (empty($startTime)) {
			$startTime = TIMESTAMP - (self::TIME_WINDOW * 3600);
		}

		// 获取指定时间后的关联记录
		$records = table_common_tagitem::t()->fetch_all_by_tagid_and_time($tagId, $startTime);
		$hotScore = 0;
		$now = TIMESTAMP;

		foreach ($records as $record) {
			// 计算内容创建时间与当前时间的差值(小时)
			$hoursDiff = ($now - $record['created_at']) / 3600;

			// 根据时间衰减计算热度贡献
			$timeFactor = pow(self::DECAY_FACTOR, $hoursDiff);

			// 不同内容类型可以有不同的热度权重
			$weight = self::getContentWeight($record['idtype']);

			// 累加热度值
			$hotScore += self::BASE_HOT_SCORE * $timeFactor * $weight;
		}

		return $hotScore;
	}

	/**
	 * 批量更新标签热度(用于定时任务)
	 *
	 * @param int $limit 每次更新的标签数量
	 * @param int $start 起始位置
	 * @return int 更新的标签数量
	 */
	public static function batch_update_tag_hot($limit = 100, $start = 0): int {
		// 获取需要更新的标签ID列表(按热度排序，优先更新热门标签)
		$tags = table_common_tag::t()->fetch_all_by_hot(NULL, $start, $limit, 'DESC');

		$updatedCount = 0;
		foreach ($tags as $tag) {
			if (self::update_tag_hot_score($tag['tagid']) !== false) {
				$updatedCount++;
			}
		}

		return $updatedCount;
	}

	/**
	 * 初始化所有标签的热度值
	 * 用于系统升级后首次计算热度
	 *
	 * @param int $limit 每次处理的标签数量
	 * @return int 处理的标签数量
	 */
	public static function initialize_all_tag_hot($limit = 100): int {
		$count = 0;
		$page = 0;

		while (true) {
			$tagIds = table_common_tag::t()->fetch_all_by_status(NULL, '', $page * $limit, $limit, '');

			if (empty($tagIds)) {
				break;
			}

			foreach ($tagIds as $tagId) {
				self::update_tag_hot_score($tagId);
				$count++;
			}

			$page++;
		}

		return $count;
	}

	/**
	 * 更新标签字段
	 *
	 * @param string $tags 标签字符串
	 * @param int $itemid 内容ID
	 * @param string $idtype 内容类型
	 * @param array $typeinfo 类型信息
	 * @return string 更新后的标签字符串
	 */
	public function update_field($tags, $itemid, $idtype = 'tid', $typeinfo = []) {
		if($idtype == 'tid') {
			$tagstr = table_forum_post::t()->fetch_threadpost_by_tid_invisible($itemid);
			$tagstr = $tagstr['tags'];
		} elseif($idtype == 'blogid') {
			$tagstr = table_home_blogfield::t()->fetch_tag_by_blogid($itemid);
		} elseif($idtype == 'articleid') {
			$article = table_portal_article_title::t()->fetch($itemid);
			$tagstr = $article['tags'];
		} else {
			return '';
		}

		$tagarray = $tagidarray = $tagarraynew = [];
		$results = table_common_tagitem::t()->select(0, $itemid, $idtype);
		foreach($results as $result) {
			$tagidarray[] = $result['tagid'];
		}
		if($tagidarray) {
			$results = table_common_tag::t()->get_byids($tagidarray);
			foreach($results as $result) {
				$tagarray[$result['tagid']] = $result['tagname'];
			}
		}
		$tags = $this->add_tag($tags, $itemid, $idtype, 1);
		foreach($tags as $tagid => $tagname) {
			$tagarraynew[] = $tagname;
			if(empty($tagarray[$tagid])) {
				$tagstr = $tagstr.$tagid.','.$tagname."\t";
			}
		}
		$removedTags = [];
		foreach($tagarray as $tagid => $tagname) {
			if(!in_array($tagname, $tagarraynew)) {
				table_common_tagitem::t()->delete_tagitem($tagid, $itemid, $idtype);
				$tagstr = str_replace("$tagid,$tagname\t", '', $tagstr);
				$removedTags[] = $tagid;
			}
		}
		// 更新被删除标签的热度
		foreach($removedTags as $tagid) {
			table_common_tag::t()->increase($tagid, ['related_count' => -1]);
			self::update_tag_hot_score($tagid);
		}
		return $tagstr;
	}

	public function copy_tag($oldid, $newid, $idtype = 'tid') {
		$results = table_common_tagitem::t()->select(0, $oldid, $idtype);
		foreach($results as $result) {
			table_common_tagitem::t()->insert([
				'tagid' => $result['tagid'],
				'itemid' => $newid,
				'idtype' => $idtype,
				'created_at' => TIMESTAMP
			]);
		}
	}

	public function merge_tag($tagidarray, $newtag, $idtype = '') {
		$newtag = str_replace(',', '', $newtag);
		$newtag = trim($newtag);
		if(!$newtag) {
			return 'tag_empty';
		}
		if(preg_match('/^([\x7f-\xff_-]|\w|\s){2,50}$/', $newtag)) {
			$tidarray = $blogidarray = $articleidarray = [];
			$newtaginfo = $this->add_tag($newtag, 0, $idtype, 1);
			foreach($newtaginfo as $tagid => $tagname) {
				$newid = $tagid;
			}
			$tagidarray = array_diff((array)$tagidarray, (array)$newid);
			if($idtype !== 'uid') {
				$tagnames = table_common_tag::t()->get_byids($tagidarray);
				$results = table_common_tagitem::t()->select($tagidarray);
				foreach($results as $result) {
					$result['tagname'] = addslashes($tagnames[$result['tagid']]['tagname']);
					if($result['idtype'] == 'tid') {
						$itemid = $result['itemid'];
						if(!isset($tidarray[$itemid])) {
							$post = table_forum_post::t()->fetch_threadpost_by_tid_invisible($itemid);
							$tidarray[$itemid] = $post['tags'];
						}
						$tidarray[$itemid] = str_replace("{$result['tagid']},{$result['tagname']}\t", '', $tidarray[$itemid]);
					} elseif($result['idtype'] == 'blogid') {
						$itemid = $result['itemid'];
						if(!isset($blogidarray[$itemid])) {
							$blogfield = table_home_blogfield::t()->fetch($itemid);
							$blogidarray[$itemid] = $blogfield['tag'];
						}
						$blogidarray[$itemid] = str_replace("{$result['tagid']},{$result['tagname']}\t", '', $blogidarray[$itemid]);
					} elseif($result['idtype'] == 'articleid') {
						$itemid = $result['itemid'];
						if(!isset($articleidarray[$itemid])) {
							$article = table_portal_article_title::t()->fetch($itemid);
							$articleidarray[$itemid] = $article['tags'];
						}
						$articleidarray[$itemid] = str_replace("{$result['tagid']},{$result['tagname']}\t", '', $articleidarray[$itemid]);
					}
				}
			}
			$checkunique = [];
			$checktagids = $tagidarray;
			$checktagids[] = $newid;
			$results = table_common_tagitem::t()->select($checktagids);
			foreach($results as $row) {
				if($checkunique[$row['itemid'].'_'.$row['idtype']] == 'deleted' || empty($checkunique[$row['itemid'].'_'.$row['idtype']])) {
					if(empty($checkunique[$row['itemid'].'_'.$row['idtype']])) {
						$checkunique[$row['itemid'].'_'.$row['idtype']] = 1;
					}
				} else {
					table_common_tagitem::t()->unique($row['tagid'], $row['itemid'], $row['idtype']);
					$checkunique[$row['itemid'].'_'.$row['idtype']] = 'deleted';
				}
			}
			table_common_tagitem::t()->merge_by_tagids($newid, $tagidarray);
			table_common_tag::t()->delete_byids($tagidarray);

			if($tidarray) {
				foreach($tidarray as $key => $var) {
					table_forum_post::t()->update_by_tid('tid:'.$key, $key, ['tags' => $var], false, false, 1);
					table_forum_post::t()->concat_threadtags_by_tid($key, "$newid,$newtag\t");
				}
			}
			if($blogidarray) {
				foreach($blogidarray as $key => $var) {
					table_home_blogfield::t()->update($key, ['tag' => $var.$newid.','.$newtag."\t"]);
				}
			}
			if($articleidarray) {
				foreach($articleidarray as $key => $var) {
					table_portal_article_title::t()->update($key, ['tags' => $var]);
				}
			}
		} else {
			return 'tag_length';
		}
		return 'succeed';
	}

	public function delete_tag($tagidarray, $idtype = '') {
		$tidarray = $blogidarray = $articleidarray = [];
		if(!is_array($tagidarray)) {
			return false;
		}
		if($idtype != 'uid') {
			$tagnames = table_common_tag::t()->get_byids($tagidarray);
			$items = table_common_tagitem::t()->select($tagidarray);
			foreach($items as $result) {
				$result['tagname'] = addslashes($tagnames[$result['tagid']]['tagname']);
				if($result['idtype'] == 'tid') {
					$itemid = $result['itemid'];
					if(!isset($tidarray[$itemid])) {
						$post = table_forum_post::t()->fetch_threadpost_by_tid_invisible($itemid);
						$tidarray[$itemid] = $post['tags'];
					}
					$tidarray[$itemid] = str_replace("{$result['tagid']},{$result['tagname']}\t", '', $tidarray[$itemid]);
				} elseif($result['idtype'] == 'blogid') {
					$itemid = $result['itemid'];
					if(!isset($blogidarray[$itemid])) {
						$blogfield = table_home_blogfield::t()->fetch($itemid);
						$blogidarray[$itemid] = $blogfield['tag'];
					}
					$blogidarray[$itemid] = str_replace("{$result['tagid']},{$result['tagname']}\t", '', $blogidarray[$itemid]);
				} elseif($result['idtype'] == 'articleid') {
					$itemid = $result['itemid'];
					if(!isset($articleidarray[$itemid])) {
						$article = table_portal_article_title::t()->fetch($itemid);
						$articleidarray[$itemid] = $article['tags'];
					}
					$articleidarray[$itemid] = str_replace("{$result['tagid']},{$result['tagname']}\t", '', $articleidarray[$itemid]);
				}
			}
		}

		if($tidarray) {
			foreach($tidarray as $key => $var) {
				table_forum_post::t()->update_by_tid('tid:'.$key, $key, ['tags' => $var], false, false, 1);
			}
		}
		if($blogidarray) {
			foreach($blogidarray as $key => $var) {
				table_home_blogfield::t()->update($key, ['tag' => $var]);
			}
		}
		if($articleidarray) {
			foreach($articleidarray as $key => $var) {
				table_portal_article_title::t()->update($key, ['tags' => $var]);
			}
		}
		table_common_tag::t()->delete_byids($tagidarray);
		table_common_tagitem::t()->delete_tagitem($tagidarray);
		return true;
	}

	public static function getthreadsbytids($tidarray) {
		global $_G;

		$threadlist = [];
		if(!empty($tidarray)) {
			loadcache('forums');
			include_once libfile('function_misc', 'function');
			$fids = [];
			foreach(table_forum_thread::t()->fetch_all_by_tid($tidarray) as $result) {
				if($result['displayorder'] >= 0) {
					if(!isset($_G['cache']['forums'][$result['fid']]['name'])) {
						$fids[$result['fid']][] = $result['tid'];
					} else {
						$result['name'] = $_G['cache']['forums'][$result['fid']]['name'];
					}
					$threadlist[$result['tid']] = procthread($result);
				}
			}
			if(!empty($fids)) {
				foreach(table_forum_forum::t()->fetch_all_by_fid(array_keys($fids)) as $fid => $forum) {
					foreach($fids[$fid] as $tid) {
						$threadlist[$tid]['forumname'] = $forum['name'];
					}
				}
			}
		}
		return $threadlist;
	}

	public static function getarticlebyid($articleidarray) {
		global $_G, $summarylen;

		$articlelist = [];
		if(!empty($articleidarray)) {
			$data_article = table_portal_article_title::t()->fetch_all_for_search($articleidarray, 'dateline', 'DESC',0, 99);

			require_once libfile('function/home');
			foreach($data_article as $curarticleid => $result) {
				$result['dateline'] = dgmdate($result['dateline']);
				$result['summary'] = preg_replace('/&[a-z]+\;/i', '', $result['summary']);
				if($result['pic']) {
					$result['pic'] = pic_get($result['pic'], '', $result['thumb'], $result['remote'], 1, 1);
				}
				$articlelist[] = $result;
			}
		}
		return $articlelist;
	}

	public static function getblogbyid($blogidarray) {
		global $_G, $summarylen;

		$bloglist = [];
		if(!empty($blogidarray)) {
			$data_blog = table_home_blog::t()->fetch_all_blog($blogidarray, 'dateline', 'DESC');
			$data_blogfield = table_home_blogfield::t()->fetch_all($blogidarray);

			require_once libfile('function/spacecp');
			require_once libfile('function/home');
			$classarr = [];
			foreach($data_blog as $curblogid => $result) {
				$result = array_merge($result, (array)$data_blogfield[$curblogid]);
				$result['dateline'] = dgmdate($result['dateline']);
				$classarr = getclassarr($result['uid']);
				$result['classname'] = $classarr[$result['classid']]['classname'];
				if($result['friend'] == 4) {
					$result['message'] = $result['pic'] = '';
				} else {
					$result['message'] = getstr($result['message'], $summarylen, 0, 0, 0, -1);
				}
				$result['message'] = preg_replace('/&[a-z]+\;/i', '', $result['message']);
				if($result['pic']) {
					$result['pic'] = pic_cover_get($result['pic'], $result['picflag']);
				}
				$bloglist[] = $result;
			}
		}
		return $bloglist;
	}

	public static function getdoingbyid($doidarray) {
		global $_G, $summarylen;

		$dolist = [];
		if(!empty($doidarray)) {
			$data_doing = table_home_doing::t()->fetch_all_search(0, 20, 1, [], null, null, 0, 0, 0, 0, $doidarray, '');
			require_once libfile('function/home');
			foreach($data_doing as $doid => $result) {
				$result['dateline'] = dgmdate($result['dateline']);
				$result['message'] = getstr($result['message'], 120, 0, 0, 0, -1);
				$result['message'] = preg_replace('/&[a-z]+\;/i', '', $result['message']);
				$dolist[] = $result;
			}
		}
		return $dolist;
	}
}