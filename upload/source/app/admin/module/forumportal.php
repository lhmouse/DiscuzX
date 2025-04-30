<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

cpheader();

shownav('forum', 'menu_forums_portal');

$operation = $operation ? $operation : 'list';

showsubmenu('menu_forums_portal', [
	['forumportal_nav_list', 'forumportal', in_array($operation, ['list', 'add', 'edit'])],
	['forumportal_nav_setting', 'forumportal&operation=setting', $operation == 'setting'],
]);

require_once childfile('forumportal/class');

switch($operation) {
	case 'list':
		fp::list();
		break;
	case 'add':
		fp::add();
		break;
	case 'edit':
		fp::edit();
		break;
	case 'setting':
		fp::setting();
		break;
}