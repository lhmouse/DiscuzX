<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

// 判断是否是多文件上传
$isMultiUpload = false;
if(isset($_FILES['Filedata']) && is_array($_FILES['Filedata']) && isset($_FILES['Filedata']['name']) && is_array($_FILES['Filedata']['name'])) {
	$isMultiUpload = true;
} else if(isset($_FILES['Filedata'])) {
	// 单文件上传，保持原有逻辑
	$_FILES['Filedata']['name'] = addslashes(diconv(urldecode($_FILES['Filedata']['name']), 'UTF-8'));
}

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

// 处理多文件上传
if($isMultiUpload) {
	$files = [];
	$successCount = 0;

	// 处理编码并重新组织$_FILES格式
	$fileCount = count($_FILES['Filedata']['name']);
	for($i = 0; $i < $fileCount; $i++) {
		if(empty($_FILES['Filedata']['name'][$i])) continue;

		// 创建临时文件数组
		$tempFile = [
			'name' => addslashes(diconv(urldecode($_FILES['Filedata']['name'][$i]), 'UTF-8')),
			'type' => $_FILES['Filedata']['type'][$i],
			'tmp_name' => $_FILES['Filedata']['tmp_name'][$i],
			'error' => $_FILES['Filedata']['error'][$i],
			'size' => $_FILES['Filedata']['size'][$i]
		];

		// 保存原有的Filedata
		$originalFiledata = $_FILES['Filedata'];
		// 替换为当前文件
		$_FILES['Filedata'] = $tempFile;

		// 执行上传
		$upload = new forum_upload(1);
		if($upload) {
			$aid = $upload->getaid;
			if($aid >= 0) {
				$attach = table_forum_attachment_n::t()->fetch_attachment('aid:'.$aid, $aid);
				// $picsource = ($attach['remote'] ? $_G['setting']['ftp']['attachurl'] : $_G['setting']['attachurl']).'forum/'.$attach['attachment'];
				$files[] = [
					'aid' => $upload->aid,
					'remote' => $attach['remote'],
					'directory' => 'forum',
					'url' => $attach['attachment'],
				];
				$successCount++;
			}
		}

		// 恢复原有的Filedata
		$_FILES['Filedata'] = $originalFiledata;
	}

	// 构建返回结果
	$ret = [
		'success' => $successCount > 0 ? 1 : 0,
		'files' => $files
	];
	echo json_encode($ret);
	exit();
} else {
	// 原有单文件上传逻辑
	$upload = new forum_upload(1);
	if($upload) {
		$aid = $upload->getaid;
		if($aid < 0) {
			$ret = [
				'success' => 0,
				'statusid' => $aid,
				'sizelimit' => $upload->error_sizelimit,
			];
			echo json_encode($ret);
			exit();
		}
		$attach = table_forum_attachment_n::t()->fetch_attachment('aid:'.$aid, $aid);
		// $picsource = ($attach['remote'] ? $_G['setting']['ftp']['attachurl'] : $_G['setting']['attachurl']).'forum/'.$attach['attachment'];
		$ret = [
			'success' => 1,
			'file' => [
				'aid' => $upload->aid,
				'remote' => $attach['remote'],
				'directory' => 'forum',
				'url' => $attach['attachment'],
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
}