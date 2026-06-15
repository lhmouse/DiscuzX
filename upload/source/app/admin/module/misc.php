<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

// AJAX operations skip the admin page wrapper
$ajaxOperations = ['ajax_widget'];
if(in_array($operation, $ajaxOperations)) {
	$file = childfile('misc/'.$operation);
	if(!file_exists($file)) {
		header('Content-Type: application/json');
		echo json_encode(['error' => 1, 'message' => 'Operation not found']);
		exit;
	}
	require_once $file;
	exit;
}

cpheader();

$file = childfile('misc/'.$operation);
if(!file_exists($file)) {
	cpmsg('undefined_action');
}

require_once $file;

