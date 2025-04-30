<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['uid']) {
	showmessage('forum_nopermission', NULL, [$_G['group']['grouptitle']], ['login' => 1]);
}

$file = childfile($_GET['action']);
if(file_exists($file)) {
	require_once $file;
}