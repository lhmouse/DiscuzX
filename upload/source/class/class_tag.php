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
	public function add_tag($tags, $itemid, $idtype = 'tid', $returnarray = 0) {
		if($tags == '' || !in_array($idtype, ['', 'tid', 'blogid', 'uid'])) {
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
		foreach($tagarray as $tagname) {
			$tagname = trim($tagname);
			if(preg_match('/^([\x7f-\xff_-]|\w|\s){2,20}$/', $tagname)) {
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
						table_common_tagitem::t()->replace($tagid, $itemid, $idtype);
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

	public function update_field($tags, $itemid, $idtype = 'tid', $typeinfo = []) {

		if($idtype == 'tid') {
			$tagstr = table_forum_post::t()->fetch_threadpost_by_tid_invisible($itemid);
			$tagstr = $tagstr['tags'];

		} elseif($idtype == 'blogid') {
			$tagstr = table_home_blogfield::t()->fetch_tag_by_blogid($itemid);

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
		foreach($tagarray as $tagid => $tagname) {
			if(!in_array($tagname, $tagarraynew)) {
				table_common_tagitem::t()->delete_tagitem($tagid, $itemid, $idtype);
				$tagstr = str_replace("$tagid,$tagname\t", '', $tagstr);
			}
		}
		return $tagstr;
	}

	public function copy_tag($oldid, $newid, $idtype = 'tid') {
		$results = table_common_tagitem::t()->select(0, $oldid, $idtype);
		foreach($results as $result) {
			table_common_tagitem::t()->insert([
				'tagid' => $result['tagid'],
				'itemid' => $newid,
				'idtype' => $idtype
			]);
		}
	}

	public function merge_tag($tagidarray, $newtag, $idtype = '') {
		$newtag = str_replace(',', '', $newtag);
		$newtag = trim($newtag);
		if(!$newtag) {
			return 'tag_empty';
		}
		if(preg_match('/^([\x7f-\xff_-]|\w|\s){2,20}$/', $newtag)) {
			$tidarray = $blogidarray = [];
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
					table_home_blogfield::t()->update($key, ['tag' => $var.$newid.','.$newtag.'\t']);
				}
			}
		} else {
			return 'tag_length';
		}
		return 'succeed';
	}

	public function delete_tag($tagidarray, $idtype = '') {
		$tidarray = $blogidarray = [];
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
		table_common_tag::t()->delete_byids($tagidarray);
		table_common_tagitem::t()->delete_tagitem($tagidarray);
		return true;
	}

}

