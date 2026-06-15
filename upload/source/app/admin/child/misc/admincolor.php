<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!submitcheck('submit')) {
	exit;
}

$setting = (array)$_G['setting']['admincolor'];

$saveData = [];
foreach($_GET as $key => $value) {
	if(str_starts_with($key, '--') && preg_match('/^\d+,\d+,\d+$/', $value)) {
		$saveData[$key] = $value;
	}
}

$profile = table_common_member_profile::t()->fetch($_G['member']['uid']);
$fields = json_decode($profile['fields'], true);
$fields['admincolor'] = $saveData;
table_common_member_profile::t()->update($_G['member']['uid'], ['fields' => json_encode($fields)]);

echo '<script>parent.display(\'adminColor_menu\')</script>';
