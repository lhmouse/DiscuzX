<?php

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(empty($admincp) || !is_object($admincp) || !$admincp->isfounder) {
	exit('Access Denied');
}

require_once childfile('restful/class');

cpheader();

shownav('founder', 'menu_founder_restful');

$operation = $operation ? $operation : 'list';
rp::showMenu($operation);

switch($operation) {
	case 'list':
		rp::list();
		break;
	case 'add':
		rp::add();
		break;
	case 'view':
		if(str_starts_with($_GET['id'], 'system:')) {
			rp::viewSystem();
		} else {
			rp::view();
		}
		break;
	case 'appList':
		rp::appList();
		break;
	case 'appAdd':
		rp::appAdd();
		break;
	case 'app':
		rp::app();
		break;
	case 'stat':
		rp::stat();
		break;
}