<?php

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

cpheader();

if(!isfounder()) cpmsg('noaccess_isfounder', '', 'error');

shownav('template', 'menu_lang');

require_once childfile('lang/class');

$operation = $operation ? $operation : 'list';

switch($operation) {
	case 'list':
		lang::showMenu($operation);
		lang::list();
		break;
	case 'defaultSubmit':
		lang::defaultSubmit();
		break;
	case 'add':
		lang::showMenu($operation);
		lang::add();
		break;
	case 'addSubmit':
		lang::addSubmit();
		break;
	case 'delete':
		lang::delete();
		break;
	case 'edit':
		lang::edit();
		break;
	case 'editLang':
		lang::editLang();
		break;
	case 'editLangSubmit':
		lang::editLangSubmit();
		break;

}