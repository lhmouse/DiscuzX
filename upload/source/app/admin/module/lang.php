<?php

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

cpheader();

if(!isfounder()) cpmsg('noaccess_isfounder', '', 'error');

shownav('template', 'menu_lang');

require_once childfile('lang/class');

$operation = $operation ? $operation : 'list';
lang::showMenu($operation);

switch($operation) {
	case 'list':
		lang::list();
		break;
	case 'defaultSubmit':
		lang::defaultSubmit();
		break;
}