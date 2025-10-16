<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$_FILES['Filedata']['name'] = addslashes(diconv(urldecode($_FILES['Filedata']['name']), 'UTF-8'));
$forumattachextensions = '';
$fid = intval($_GET['fid']);
if($fid) {
	$forum = $fid != $_G['fid'] || !$_G['forum'] ? table_forum_forum::t()->fetch_info_by_fid($fid) : $_G['forum'];
	if(empty($_G['setting']['editormodetype']) && $forum['editormode'] != 2) {
		$ret = [
			'success' => 0
		];
		echo json_encode($ret);
		exit();
	}

	if($forum['status'] == 3 && $forum['level']) {
		$levelinfo = table_forum_grouplevel::t()->fetch($forum['level']);
		if($postpolicy = $levelinfo['postpolicy']) {
			$postpolicy = dunserialize($postpolicy);
			$forumattachextensions = $postpolicy['attachextensions'];
		}
	} else {
		$forumattachextensions = $forum['attachextensions'];
	}
	if($forumattachextensions) {
		$_G['group']['attachextensions'] = $forumattachextensions;
	}
}
$upload = new forum_upload(1);
if($upload) {
	$aid = $upload->getaid;
	if($aid < 0) {
		$ret = [
			'success' => 0
		];
		echo json_encode($ret);
		exit();
	}
	$attach = table_forum_attachment_n::t()->fetch_attachment('aid:'.$aid, $aid);
	$picsource = ($attach['remote'] ? $_G['setting']['ftp']['attachurl'] : $_G['setting']['attachurl']).'forum/'.$attach['attachment'];
	$ret = [
		'success' => 1,
		'file' => [
			'aid' => $upload->aid,
			'url' => $picsource
		]
	];
	echo json_encode($ret);
	exit();
} else {
	$ret = [
		'success' => 0
	];
	echo json_encode($ret);
	exit();
}
	