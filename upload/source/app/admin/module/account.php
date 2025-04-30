<?php

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

cpheader();

shownav('global', 'account');

$method = $_GET['method'] ?? '';
$menus = [
	['account_base', 'account', !$method],
];

$interfaces = account_base::getInterfaces();
if(in_array($method, $interfaces)) {
	$menus[] = [account_base::getName($method), 'account&method='.$method, true];
}

showsubmenu('account', $menus);

if($method) {
	require_once childfile('account/interface');
}

require_once childfile('account/list');