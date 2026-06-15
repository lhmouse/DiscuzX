<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if($_G['adminid'] != 1) {
	header('Content-Type: application/json');
	echo json_encode(['error' => 1, 'message' => 'Permission denied']);
	exit;
}

$action = $_GET['ajax_action'] ?? '';

// AJAX save: receives complete widget order and hidden state
if($action == 'save') {
	if(!submitcheck('formhash', 1)) {
		header('Content-Type: application/json');
		echo json_encode(['error' => 1, 'message' => 'Invalid formhash']);
		exit;
	}

	$input = json_decode(file_get_contents('php://input'), true);
	if(empty($input) || !isset($input['order'])) {
		header('Content-Type: application/json');
		echo json_encode(['error' => 1, 'message' => 'Invalid data']);
		exit;
	}

	$order = $input['order'];
	$hidden = $input['hidden'] ?? [];

	// Rebuild the setting data structure
	$settingData = [];
	$saveData = [];
	foreach($order as $type => $widgets) {
		if(!is_array($widgets)) {
			continue;
		}
		$settingData[$type] = $widgets;
		foreach($widgets as $widget) {
			if(!empty($hidden[$type]) && in_array($widget, $hidden[$type])) {
				continue;
			}
			$saveData[$type][] = $widget;
		}
	}

	$_G['cache']['admin']['widget'] = [
		'data' => ['data' => $settingData, 'hide' => $hidden],
		'setting' => $saveData,
	];
	savecache('admin', $_G['cache']['admin']);

	header('Content-Type: application/json');
	echo json_encode(['error' => 0, 'message' => 'saved']);
	exit;
}

// AJAX reset: restore to default layout
if($action == 'reset') {
	if(!submitcheck('formhash', 1)) {
		header('Content-Type: application/json');
		echo json_encode(['error' => 1, 'message' => 'Invalid formhash']);
		exit;
	}

	admin\widget_setting::reset();

	header('Content-Type: application/json');
	echo json_encode(['error' => 0, 'message' => 'reset']);
	exit;
}

header('Content-Type: application/json');
echo json_encode(['error' => 1, 'message' => 'Unknown action']);
