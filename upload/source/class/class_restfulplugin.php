<?php

class restfulplugin {

	public static function discuzcode($type) {
		global $_G;

		$p = func_get_args();
		if(in_array($type, ['audio', 'video', 'flv'])) {
			$url = self::_f_siteurl($p[1]);
		} else {
			$attach = &$p[1];
		}

		$s = '';
		switch($type) {
			case 'audio':
				$s = '<audio controls ><source src="'.$url.'"></audio>';
				break;
			case 'video':
				$s = '<video controls '.($p[2] ? 'width="'.$p[2].'" ' : '').($p[3] ? 'height="'.$p[3].'" ' : '').'"><source src="'.$url.'"></video>';
				break;
			case 'flv':
				$s = '<a href="'.$url.'" target="_blank">'.$url.'</a>';
				break;
			case 'attach':
				$s = '<a href="'.$_G['siteurl'].'forum.php?mod=attachment&aid='.packaids($attach).'" target="_blank">'.($attach['filename']).'</a>';
				break;
			case 'img':
				$s = '<img src="'.self::_f_getImageUrl($attach).'" '.($attach['width'] ? 'width="'.$attach['width'].'" ' : '').($attach['height'] ? 'height="'.$attach['height'].'" ' : '').' />';
				break;
		}
		return $s;
	}

	public static function avatar(&$data, $param) {
		global $_G;
		if(empty($_POST['avatar']) || !$_G['uid']) {
			return;
		}
		if(!preg_match('/^(data:\s*image\/(\w+);base64,)/', $_POST['avatar'], $_r)) {
			return;
		}
		$content = base64_decode(str_replace($_r[1], '', $_POST['avatar']));
		dmkdir(DISCUZ_DATA.'./avatar/');
		$tmpFile = DISCUZ_DATA.'./avatar/'.TIMESTAMP.random(6);
		file_put_contents($tmpFile, $content);

		if(is_file($tmpFile)) {
			$account = new account_base();

			if($account->set_avatar($_G['uid'], $tmpFile)) {
				table_common_member::t()->update($_G['uid'], [
					'avatarstatus' => '1'
				]);
			}
			unlink($tmpFile);
		}
	}

	public static function avatarUrl(&$data, $param) {
		if(empty($data[$param[0]])) {
			return;
		}
		$keys = [];
		foreach(explode('|', $param[1]) as $key) {
			[$fk, $nk] = explode('/', $key);
			$keys[$fk] = $nk;
		}
		foreach($keys as $fk => $nk) {
			if(isset($data[$param[0]][$fk])) {
				$data[$param[0]][$nk] = avatar($data[$param[0]][$fk], 'middle', 1);
			}
		}
		foreach($data[$param[0]] as $k => $v) {
			foreach($keys as $fk => $nk) {
				if(isset($v[$fk])) {
					$data[$param[0]][$k][$nk] = avatar($v[$fk], 'middle', 1);
				}
			}
		}
	}


	public static function searchList(&$data, $param) {
		if(empty($data[$param[0]])) {
			return;
		}
		foreach($data[$param[0]] as $k => $v) {
			$data[$param[0]][$k]['subject'] = strip_tags($data[$param[0]][$k]['subject']);
			$data[$param[0]][$k]['message'] = strip_tags($data[$param[0]][$k]['message']);
		}
	}

	public static function getAttach(&$data, $param) {
		if(empty($data[$param[0]])) {
			return;
		}
		global $_G;
		$attachs = get_attach($data[$param[0]]);
		foreach($data[$param[0]] as $k => $v) {
			if(!empty($attachs[$v['tid']]['attachment'])) {
				foreach($attachs[$v['tid']]['attachment'] as $_k => $_v) {
					$attachs[$v['tid']]['images'][$_k] = $_G['siteurl'].$_v;
				}
			}
			$data[$param[0]][$k] = array_merge($data[$param[0]][$k], $attachs[$v['tid']]);
		}
	}

	public static function userField(&$data, $param) {
		if(empty($data[$param[0]])) {
			return;
		}
		$keys = [];
		foreach(explode('|', $param[1]) as $key) {
			$keys[] = $key;
		}
		$return = [];
		foreach($data[$param[0]] as $k => $v) {
			foreach($keys as $key) {
				if($key == 'avatar') {
					$return[$k][$key] = avatar($v['uid'], 'middle', 1);
					continue;
				}
				$return[$k][$key] = $v[$key];
			}
		}
		$data[$param[0]] = $return;
	}

	public static function userInfo(&$data) {
		global $_G;

		if(empty($_G) || !$_G['uid'] || empty($data) || empty($data['user'])) {
			return;
		}
		$data['user']['adminid'] = $_G['member']['adminid'];
		$data['user']['credits'] = $_G['member']['credits'];
		$data['user']['secmobicc'] = $_G['member']['secmobicc'];
		$data['user']['secmobile'] = $_G['member']['secmobile'];
		$data['user']['groupname'] = $_G['group']['grouptitle'];
		$data['user']['allowadmincp'] = $_G['member']['allowadmincp'];
		$data['user']['freeze'] = $_G['member']['freeze'];
		$data['user']['regdate'] = $_G['member']['regdate'];
		$data['user']['lastvisit'] = $_G['member']['lastvisit'];
		$data['user']['avatar'] = avatar($_G['uid'], 'middle', 1);
	}

	public static function addDomain(&$data, $param) {
		$key = $param[0];
		if(empty($data[$key]) || empty($param[1])) {
			return;
		}

		foreach($data[$key] as $k => $v) {
			if(empty($v[$param[1]])) {
				continue;
			}
			$data[$key][$k][$param[1]] = preg_replace_callback('/href="(\w+)\.php/i', function($m) {
				global $_G;
				return 'href="'.$_G['siteurl'].$m[1].'.php';
			}, $v[$param[1]]);
		}
	}

	public static function getCredit(&$data) {
		for($i = 1; $i <= 8; $i++) {
			$k = 'extcredits'.$i;
			$data[$k] = dintval(getuserprofile($k));
		}
	}

	public static function threadList(&$data, $param) {
		global $_G;

		$key = $param[0];
		if(empty($data[$key])) {
			return;
		}
		foreach($data[$key] as $k => $thread) {
			if($thread['authorid'] > 0 && $thread['author'] === '') {
				$data[$key][$k]['author'] = $_G['setting']['anonymoustext'];
				$data[$key][$k]['authorid'] = $thread['authorid'] = 0;
			}
			$data[$key][$k]['authoravatar'] = avatar($thread['authorid'], 'middle', 1);
			$data[$key][$k]['dateline'] = self::_f_gmdate($thread['dateline']);
			$data[$key][$k]['lastpost'] = self::_f_gmdate($thread['lastpost']);
			if(!empty($thread['images'])) {
				foreach($thread['images'] as $_k => $_v) {
					$data[$key][$k]['images'][$_k] = $_G['siteurl'].$_v;
				}
			}
		}
	}

	public static function postList(&$data, $param) {
		global $_G;
		$key = $param[0];
		if(empty($data[$key])) {
			return;
		}
		foreach($data[$key] as $k => $post) {
			if(!empty($post['anonymous'])) {
				$data[$key][$k]['username'] = $data[$key][$k]['usernameenc'] = $data[$key][$k]['author'] = $_G['setting']['anonymoustext'];
				$data[$key][$k]['authorid'] = $post['authorid'] = 0;
			}
			$data[$key][$k]['authoravatar'] = avatar($post['authorid'], 'middle', 1);
			$data[$key][$k]['dateline'] = self::_f_gmdate($post['dateline']);
			if(empty($post['attachments'])) {
				continue;
			}
			$listaids = array_merge($data[$key][$k]['imagelist'], $data[$key][$k]['attachlist']);
			$data[$key][$k]['imagelist'] = $data[$key][$k]['attachlist'] = $data[$key][$k]['attachments'] = [];
			foreach($post['attachments'] as $_k => $attach) {
				if(!in_array($attach['aid'], $listaids)) {
					continue;
				}
				$data[$key][$k]['attachments'][$_k]['downurl'] = $_G['siteurl'].'forum.php?mod=attachment&aid='.packaids($attach);
				if($attach['isimage']) {
					$data[$key][$k]['imagelist'][$attach['aid']] = $_G['siteurl'].getforumimg($attach['aid'], 0, 300, 300);
				} else {
					$data[$key][$k]['attachlist'][$attach['aid']] = [
						'attachsize' => $attach['attachsize'],
						'filename' => $attach['filename'],
					];
				}
			}
		}
	}

	public static function myPostList(&$data, $param) {
		$key = $param[0];
		if(empty($data[$key])) {
			return;
		}
		$list = &$data[$key];
		$key = $param[1];
		if(empty($data[$key])) {
			return;
		}
		$post = &$data[$key];
		$posts = [];
		foreach($post as $row) {
			$posts[$row['tid']][] = [
				'pid' => dintval($row['pid']),
				'dateline' => self::_f_gmdate($row['dateline']),
				'message' => $row['message'],
			];
		}
		foreach($list as $k => $v) {
			$list[$k]['posts'] = $posts[$v['tid']];
		}
	}

	public static function forumList(&$data, $param) {
		global $_G;

		$catKey = $param[0];
		$forumKey = $param[1];
		if(empty($data[$catKey]) || empty($data[$forumKey])) {
			return;
		}

		$forumlist = $data[$catKey];

		foreach($data[$forumKey] as $k => $forum) {
			if(!empty($forum['iconUri'])) {
				$data[$forumKey][$k]['icon'] = self::_f_forumimg($forum['iconUri']);
			}
			unset($data[$forumKey][$k]['iconUri']);

			$data[$forumKey][$k]['lastpost'] = $forum['lastpost'] ? [
				'tid' => $forum['lastpost']['tid'],
				'subject' => $forum['lastpost']['subject'],
				'dateline' => $forum['lastpost']['dateline'],
				'authorusername' => $forum['lastpost']['authorusername'],
			] : [];

			if($forum['type'] == 'forum' && !empty($forum['subforums'])) {
				foreach($forum['subforums'] as $k_sub => $forum_sub) {
					if(!empty($forum_sub['iconUri'])) {
						$data[$forumKey][$k]['subforums'][$k_sub]['icon'] = self::_f_forumimg($forum_sub['iconUri']);
					}
					unset($data[$forumKey][$k]['subforums'][$k_sub]['iconUri']);
				}
			}
			unset($forumlist[$forum['fup']]['forums']);
			$forumlist[$forum['fup']]['subforums'][] = $data[$forumKey][$k];
		}
		$forumlist = array_values($forumlist);
		unset($data[$catKey]);
		$data[$forumKey] = $forumlist;
	}

	public static function foruminfo(&$data, $param) {
		$key = $param[0];
		if(empty($data[$key])) {
			return;
		}

		unset($data[$key]['password']);
		$data[$key]['icon'] = self::_f_forumimg($data[$key]['icon']);
		$data[$key]['banner'] = self::_f_siteurl($data[$key]['banner']);
	}

	public static function thread(&$data, $param) {
		global $_G;

		$key = $param[0];
		if(empty($data[$key])) {
			return;
		}

		if($data[$key]['authorid'] > 0 && $data[$key]['author'] === '') {
			$data[$key]['author'] = $_G['setting']['anonymoustext'];
			$data[$key]['authorid'] = 0;
		}
		$data[$key]['dateline'] = self::_f_gmdate($data[$key]['dateline']);
		$data[$key]['lastpost'] = self::_f_gmdate($data[$key]['lastpost']);

		//投票帖
		if($data[$key]['special'] == 1 && !empty($data['polloptions'])) {
			$options = $data['polloptions'];
			foreach($data['polloptions'] as $k => $v) {
				if($v['votes'] == 0) {
					$options[$k]['width'] = '';
				}
				if(!$v['imginfo']) {
					unset($options[$k]['imginfo']);
					continue;
				}
				$options[$k]['imginfo']['small'] = self::_f_siteurl($v['imginfo']['small']);
				$options[$k]['imginfo']['big'] = self::_f_siteurl($v['imginfo']['big']);
			}
			$data['polloptions'] = [
				'options' => $options,
				'isimagepoll' => $data['isimagepoll'],
				'voterscount' => $data['voterscount'],
				'maxchoices' => $data['maxchoices'],
				'multiple' => $data['multiple'],
			];
			unset($data['isimagepoll'], $data['voterscount'], $data['maxchoices'], $data['multiple']);
		}

		//悬赏帖
		if($data[$key]['special'] == 3) {
			$data['rewardoptions'] = [
				'rewardprice' => $data['rewardprice'],
				'credits' => $_G['setting']['extcredits'][$_G['setting']['creditstransextra'][2]],
				'price' => $_G['forum_thread']['price'],
				'bestpost' => $data['bestpost'],
			];
			unset($data['rewardprice'], $data['bestpost']);
		}

		//活动帖
		if($data[$key]['special'] == 4) {
			$data['activityoptions'] = $GLOBALS['activity'];
			$data['activityoptions']['thumb'] = $data['activityoptions']['thumb'] ? self::_f_siteurl($data['activityoptions']['thumb']) : '';
			$data['activityoptions']['attachurl'] = $data['activityoptions']['attachurl'] ? self::_f_siteurl($data['activityoptions']['attachurl']) : '';
		}

		//辩论帖
		if($data[$key]['special'] == 5) {
			$data['debateoptions'] = $GLOBALS['debate'];
		}
	}

	public static function portalBefore(&$data) {
		$_ENV['cells']['forum_portal_threadlist'] = ['message' => true, 'image' => true];
	}

	public static function setUploadHash(&$data, $param) {
		global $_G;

		$data['uploadhash'] = md5(substr(md5($_G['config']['security']['authkey']), 8).$_G['uid']);
	}

	public static function registerBefore(&$data) {
		if(empty($_POST['reginput'])) {
			return;
		}
		$reginput = json_decode($_POST['reginput'], true);
		if(!is_array($reginput)) {
			return;
		}
		foreach($reginput as $k => $v) {
			$_POST[$k] = $v;
		}
	}

	public static function arrayAssoc2List(&$data, $param) {
		foreach($param as $key) {
			if(!isset($data[$key]) || !is_array($data[$key])) {
				continue;
			}
			$new = [];
			foreach($data[$key] as $k => $v) {
				$new[] = ['key' => $k, 'value' => $v];
			}
			$data[$key] = $new;
		}

		if(!empty($data[$key])) {
			$data[$key] = array_values($data[$key]);
		}
	}

	public static function arrayValues(&$data, $param) {
		global $_G;

		$key = $param[0];
		if(empty($data[$key])) {
			return;
		}

		if(!empty($data[$key])) {
			$data[$key] = array_values($data[$key]);
		}
	}

	private static function _f_getImageUrl($attach) {
		global $_G;

		$refcheck = (!$attach['remote'] && $_G['setting']['attachrefcheck']) || ($attach['remote'] && ($_G['setting']['ftp']['hideurl'] || ($attach['isimage'] && $_G['setting']['attachimgpost'] && strtolower(substr($_G['setting']['ftp']['attachurl'], 0, 3)) == 'ftp')));
		if($refcheck) {
			$aidencode = packaids($attach);
			$url = $_G['siteurl'].'forum.php?mod=attachment&aid='.$aidencode.'&noupdate=yes&nothumb=yes';
		} else {
			$url = $attach['url'].$attach['attachment'];
		}
		return self::_f_siteurl($url);
	}

	private static function _f_siteurl($url) {
		global $_G;

		if(!str_starts_with($url, 'http') && $url) {
			$url = $_G['siteurl'].$url;
		}
		return $url;
	}

	private static function _f_gmdate($v) {
		if(is_numeric($v)) {
			$v = dgmdate($v);
		}
		return str_replace('&nbsp;', ' ', strip_tags($v));
	}

	private static function _f_forumimg($v) {
		return $v ? self::_f_siteurl(get_forumimg($v)) : '';
	}

	public static function portalBlockItem(&$data) {
		global $_G;
		$CachekeyPre = 'blockApi_';
		$TTL = 300;
		$data = ['bid' => 0, 'itemlist' => []];
		$bid = intval($_GET['bid']);
		if(empty($bid)) {
			$name = daddslashes($_GET['name']);
			if(empty($name)) {
				return;
			}
			$block = table_common_block::t()->fetch_by_name($name);
			if(empty($block)) {
				return;
			}
			$bid = $block['bid'];
		}
		if(empty($bid)) {
			return;
		}
		$data['bid'] = $bid;
		$m_data = memory('get', $CachekeyPre.$bid);
		if(!empty($m_data['v'])) {
			$data['itemlist'] = $m_data['v'];
			return;
		}

		include_once libfile('function/block');
		block_get_batch($bid);
		block_updatecache($bid);

		$itemlist = [];
		foreach($_G['block'][$bid]['itemlist'] as $item) {
			$item['fields'] = unserialize($item['fields']);
			$item['pic'] = self::_f_siteurl((!str_starts_with($item['pic'], 'http') ? $_G['setting']['attachurl'] : '').$item['pic']);
			$item['thumbpath'] = self::_f_siteurl((!str_starts_with($item['thumbpath'], 'http') ? $_G['setting']['attachurl'] : '').$item['thumbpath']);
			$itemlist[] = $item;
		}

		memory('set', $CachekeyPre.$bid, ['v' => $itemlist], $TTL);
		$data['itemlist'] = $itemlist;
	}

	public static function portalList(&$data, $param) {
		$wheresql = category_get_wheresql($GLOBALS['cat']);
		$tmp = category_get_list($GLOBALS['cat'], $wheresql, $GLOBALS['page']);
		$data['list'] = $tmp['list'];
		foreach($data['list'] as $k => $item) {
			$data['list'][$k]['pic'] = self::_f_siteurl($item['pic']);
		}
	}

	public static function portalView(&$data, $param) {
		$data['article']['pic'] = self::_f_siteurl($data['article']['pic']);
		$data['content']['content'] = preg_replace_callback('/"(data\/.+?)"/', function($matches) {
			return '"'.self::_f_siteurl($matches[1]).'"';
		}, $data['content']['content']);
	}

	public static function tasklist(&$data, $param) {
		$key = $param[0];
		if(empty($data[$key])) {
			return;
		}

		foreach($data[$key] as $k => $v) {
			$data[$key][$k]['icon'] = self::_f_siteurl($v['icon']);
		}
	}

	public static function medallist(&$data, $param) {
		$key = $param[0];
		if(empty($data[$key])) {
			return;
		}

		foreach($data[$key] as $k => $v) {
			$data[$key][$k]['image'] = self::_f_siteurl($v['image']);
		}
		global $medalcredits;
		foreach($medalcredits as $id) {
			$data['mycredits'][$id] = getuserprofile('extcredits'.$id);
		}
	}

	public static function magiclist(&$data, $param) {
		$key = $param[0];
		if(empty($data[$key])) {
			return;
		}

		foreach($data[$key] as $k => $v) {
			$data[$key][$k]['pic'] = self::_f_siteurl($v['pic']);
		}
	}

	public static function attachNew(&$data, $param) {
		global $_G;
		if(!empty($_POST['attachnew']) && is_string($_POST['attachnew'])) {
			$aids = [];
			$attachnew = explode(',', trim($_POST['attachnew']));
			if($attachnew) {
				foreach($attachnew as $attach) {
					$aids[$attach] = ['description' => '', 'readperm' => '', 'price' => 0];
				}
			}
			if($aids) {
				$_POST['attachnew'] = $aids;
			}
		}
	}

}