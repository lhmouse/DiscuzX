<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

@include_once DISCUZ_ROOT.'./source/discuz_version.php';

require_once childfile('index/function');
require_once childfile('index/operation');

$sensitivedirs = ['./', './uc_server/', './ucenter/'];

foreach($sensitivedirs as $sdir) {
	if(@file_exists(DISCUZ_ROOT.$sdir.'install/index.php') && !DISCUZ_DEBUG) {
		@unlink(DISCUZ_ROOT.$sdir.'install/index.php');
		if(@file_exists(DISCUZ_ROOT.$sdir.'install/index.php')) {
			dexit('Please delete '.$sdir.'install/index.php via FTP!');
		}
	}
}

require_once libfile('function/attachment');
require_once libfile('function/discuzcode');
require_once libfile('function/cloudaddons');

$newversion = (CHARSET == 'utf-8') ? dunserialize($_G['setting']['cloudaddons_newversion']) : json_decode($_G['setting']['cloudaddons_newversion'], true);
if(empty($newversion['newversion']) || !is_array($newversion['newversion']) || abs($_G['timestamp'] - $newversion['updatetime']) > 86400 || (isset($_GET['checknewversion']) && $_G['formhash'] == $_GET['formhash'])) {
	$newversion = json_decode(cloudaddons_open('&mod=app&ac=upgrade'), true);
	if(!empty($newversion['newversion'])) {
		$newversion['updatetime'] = $_G['timestamp'];
		table_common_setting::t()->update_setting('cloudaddons_newversion', ((CHARSET == 'utf-8') ? $newversion : json_encode($newversion)));
		updatecache('setting');
	} else {
		$newversion = [];
	}
}

$reldisp = is_numeric(DISCUZ_RELEASE) ? ('Release '.DISCUZ_RELEASE) : DISCUZ_RELEASE;
$now = date('Y');

cpheader();
shownav();

show_user_bar();

require_once template('admin/index');
