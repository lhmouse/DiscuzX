<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

@set_time_limit(0);
@ignore_user_abort(TRUE);

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(file_exists(DISCUZ_DATA.'./install.lock') && file_exists(DISCUZ_DATA.'./update.lock')) {
	exit('Access Denied');
}

@touch(DISCUZ_DATA.'./install.lock');
@touch(DISCUZ_DATA.'./update.lock');

$force = !empty($_GET['force']) && authcode($_GET['force'], 'DECODE', $_G['config']['security']['authkey']) - time() < 10;
if(!($_G['adminid'] == 1 && $_GET['formhash'] == formhash()) && $_G['setting'] && !$force) {
	exit('Access Denied');
}

require_once libfile('function/cache');
updatecache();

require_once libfile('function/block');
blockclass_cache();

if($_G['config']['output']['tplrefresh']) {
	cleartemplatecache();
}

C::memory()->clear();