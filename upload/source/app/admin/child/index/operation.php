<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

!empty($_GET['chart']) && $_GET['operation'] = 'chart';
isset($_GET['blank']) && $_GET['operation'] = 'blank';
isset($_GET['dbsize']) && $_GET['operation'] = 'dbsize';
isset($_GET['attachsize']) && $_GET['operation'] = 'attachsize';

$operation = !empty($_GET['operation']) ? $_GET['operation'] : '';

switch($operation) {
	case 'blank':
		define('FOOTERDISABLED', true);
		cpheader();
		require_once template('admin/blank');
		exit;
	case 'chart':
		define('FOOTERDISABLED', true);
		require_once childfile('index/chart');
		exit;
	case 'benchmark':
		if(FORMHASH != $_GET['formhash']) {
			exit;
		}
		define('FOOTERDISABLED', true);
		$times = 3;
		$r = 0;
		for($i = 0; $i < $times; $i++) {
			$r += get_benchmark();
		}
		$benchmark = sprintf('%1.6f', $r / $times);
		$advice = $benchmark > 2 ? ' '.cplang('home_benchmark_advice') : '';
		$benchmark .= 's';
		include template('common/header');
		echo $benchmark.$advice;
		include template('common/footer');
		exit;
	case 'dbsize':
		if(FORMHASH != $_GET['formhash']) {
			exit;
		}
		define('FOOTERDISABLED', true);
		$dbsize = helper_dbtool::dbsize();
		$value = $dbsize ? sizecount($dbsize) : cplang('unknown');
		include template('common/header');
		echo $value;
		include template('common/footer');
		exit;
	case 'attachsize':
		if(FORMHASH != $_GET['formhash']) {
			exit;
		}
		define('FOOTERDISABLED', true);
		$attachsize = table_forum_attachment_n::t()->get_total_filesize();
		$value = is_numeric($attachsize) ? sizecount($attachsize) : cplang('unknown');
		include template('common/header');
		echo $value;
		include template('common/footer');
		exit;
	case 'note':
		if(!submitcheck('notesubmit')) {
			exit;
		}
		if(!empty($_GET['newmessage'])) {
			$newaccess = 0;
			$_GET['newexpiration'] = TIMESTAMP + (intval($_GET['newexpiration']) > 0 ? intval($_GET['newexpiration']) : 30) * 86400;
			$_GET['newmessage'] = nl2br(dhtmlspecialchars($_GET['newmessage']));
			$data = [
				'admin' => $_G['username'],
				'access' => 0,
				'adminid' => $_G['adminid'],
				'dateline' => $_G['timestamp'],
				'expiration' => $_GET['newexpiration'],
				'message' => $_GET['newmessage'],
			];
			table_common_adminnote::t()->insert($data);
		}
		show_note();
		exit;
	case 'notedel':
		if(FORMHASH != $_GET['formhash']) {
			exit;
		}
		if(!empty($_GET['noteid']) && is_numeric($_GET['noteid'])) {
			table_common_adminnote::t()->delete_note($_GET['noteid'], (isfounder() ? '' : $_G['username']));
		}
		exit;
}