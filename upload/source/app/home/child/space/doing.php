<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['setting']['doingstatus']) {
	showmessage('doing_status_off');
}

$perpage = 20;
$perpage = mob_perpage($perpage);

$page = empty($_GET['page']) ? 0 : intval($_GET['page']);
if($page < 1) $page = 1;
$start = ($page - 1) * $perpage;

ckstart($start, $perpage);

$dolist = [];
$count = 0;

$_GET['view'] = in_array($_GET['view'], ['we', 'me', 'all']) ? $_GET['view'] : 'all';

$gets = [
	'mod' => 'space',
	'uid' => $space['uid'],
	'do' => 'doing',
	'view' => $_GET['view'],
	'searchkey' => $_GET['searchkey'],
	'from' => $_GET['from']
];
$theurl = 'home.php?'.url_implode($gets);

$f_index = '';
$diymode = 0;
if($_GET['view'] == 'all') {

	$f_index = 'dateline';

} elseif($_GET['view'] == 'we') {

	space_merge($space, 'field_home');
	if($space['feedfriend']) {
		$uids = array_merge(explode(',', $space['feedfriend']), [$space['uid']]);
		$f_index = 'dateline';
	} else {
		$uids = [$space['uid']];
	}

} else {

	if($_GET['from'] == 'space') $diymode = 1;

	$uids = $_GET['highlight'] ? [] : [$space['uid']];
}
$actives = [$_GET['view'] => ' class="a"'];

$doid = empty($_GET['doid']) ? 0 : intval($_GET['doid']);
$doids = $clist = $newdoids = [];
$pricount = 0;
if($doid) {
	$count = 1;
	$f_index = '';
	$theurl .= "&doid=$doid";
}

if($searchkey = stripsearchkey($_GET['searchkey'])) {
	$searchkey = dhtmlspecialchars($searchkey);
}

if(empty($count)) {
	$count = table_home_doing::t()->fetch_all_search($start, $perpage, 3, $uids, '', $searchkey, '', '', '', 1, $doid, $f_index);
}
if($count) {
	$query = table_home_doing::t()->fetch_all_search($start, $perpage, 1, $uids, '', $searchkey, '', '', '', 1, $doid, $f_index);
	foreach($query as $value) {
		if(!empty($value['ip'])) {
			$value['ip'] = ip::to_display($value['ip']);
		}

		// 处理记录内容，确保链接和媒体正确显示
		require_once libfile('function/discuzcode');
		$value['message'] = preg_replace_callback('/http[s]?:\/\/[a-zA-Z0-9\-\._~:\/?#[\]@!\$&\'\(\)\*\+,;=.]+/', function($matches) {
			$url = $matches[0];
			// 尝试解析媒体链接
			if($media = parsemedia('x,500,373', $url)) {
				// 媒体内容单独一行显示
				return '<br />'.$media.'<br />';
			} elseif($audio = parseaudio($url)) {
				// 音频内容单独一行显示
				return '<br />'.$audio.'<br />';
			} elseif($flv = parseflv($url, 500, 373)) {
				// 视频内容单独一行显示
				return '<br />'.$flv.'<br />';
			} else {
				// 普通链接，转换为可点击链接
				return '<a href="'.dhtmlspecialchars($url).'" target="_blank" rel="nofollow">'.dhtmlspecialchars($url).'</a>';
			}
		}, $value['message']);
		$value['body_data'] = dunserialize($value['body_data']);
		$searchs = $replaces = [];
		if($value['body_data']) {
			foreach(array_keys($value['body_data']) as $key) {
				$searchs[] = '{'.$key.'}';
				$replaces[] = $value['body_data'][$key];
			}
		}
		$value['body_template'] = str_replace($searchs, $replaces, $value['body_template']);

		// 查询点赞状态和点赞数
		$value['recomends'] = $value['recomends'] ? $value['recomends'] : 0;
		if($_G['uid']) {
			$value['recommendstatus'] = table_home_doing_recomend_log::t()->fetch_by_doid_uid($value['doid'], $_G['uid']) ? 1 : 0;
		} else {
			$value['recommendstatus'] = 0;
		}

		if($value['status'] == 0 || $value['uid'] == $_G['uid'] || $_G['adminid'] == 1) {
			$doids[] = $value['doid'];
			$dolist[] = $value;
		} else {
			$pricount++;
		}
	}
}
if($doid) {
	$dovalue = empty($dolist) ? [] : $dolist[0];
	if($dovalue) {
		if($dovalue['uid'] == $_G['uid']) {
			$actives = ['me' => ' class="a"'];
		} else {
			$actives = ['all' => ' class="a"'];
		}
	}
}


if($doids) {

	$tree = new home\class_tree();

	$values = [];
	foreach(table_home_docomment::t()->fetch_all_by_doid($doids) as $value) {
		$newdoids[$value['doid']] = $value['doid'];
		if(empty($value['upid'])) {
			$value['upid'] = "do{$value['doid']}";
		}
		if(!empty($value['ip'])) {
			$value['ip'] = ip::to_display($value['ip']);
		}
		$tree->setNode($value['id'], $value['upid'], $value);
	}

	// 查询记录对应的附件信息
	$attachments = [];
	if (!empty($doids)) {
		$attach_list = table_home_doing_attachment::t()->fetch_all_by_id(0, 'doid', $doids);
		foreach ($attach_list as $attach) {
			$attach['thumb'] = getdiscuzimg('doing', $attach['aid'], 0, 140, 140);
			$attachments[$attach['doid']][] = $attach;
		}
	}

	// 将附件信息添加到记录数据中
	foreach ($dolist as &$dv) {
		$dv['attachments'] = isset($attachments[$dv['doid']]) ? $attachments[$dv['doid']] : [];
	}
	unset($dv);
}

$showdoinglist = [];
foreach($newdoids as $cdoid) {
	$values = $tree->getChilds("do$cdoid");
	$show = false;
	foreach($values as $key => $id) {
		$one = $tree->getValue($id);
		$one['layer'] = $tree->getLayer($id) * 2 - 2;
		$one['style'] = "padding-left:{$one['layer']}em;";
		if($_GET['highlight'] && $one['id'] == $_GET['highlight']) {
			$one['style'] .= 'color:#F60;';
		}
		if($one['layer'] > 0) {
			if($one['layer'] % 3 == 2) {
				$one['class'] = ' dtls';
			} else {
				$one['class'] = ' dtll';
			}
		}
		if(!$show && $one['uid']) {
			$show = true;
		}
		$clist[$cdoid][] = $one;
	}
	$showdoinglist[$cdoid] = $show;
}

$multi = multi($count, $perpage, $page, $theurl);

dsetcookie('home_diymode', $diymode);
if($_G['uid']) {
	if($_GET['view'] == 'all') {
		$navtitle = lang('core', 'title_view_all').lang('core', 'title_doing');
	} elseif($_GET['view'] == 'me') {
		$navtitle = lang('core', 'title_doing_view_me');
	} else {
		$navtitle = lang('core', 'title_me_friend_doing');
	}
	$defaultstr = getdefaultdoing();
} else {
	$navtitle = lang('core', 'title_newest_doing');
}

if($space['username']) {
	$navtitle = lang('space', 'sb_doing', ['who' => $space['username']]);
}
$metakeywords = $navtitle;
$metadescription = $navtitle;
include_once template('diy:home/space_doing');