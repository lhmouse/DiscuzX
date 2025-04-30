<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

global $_G;
$op = in_array($_GET['op'], ['search', 'manage', 'set']) ? $_GET['op'] : '';
$taglist = [];
$thread = &$_G['thread'];

$file = childfile($op);
if(file_exists($file)) {
	require_once $file;
}

include_once template('forum/tag');
