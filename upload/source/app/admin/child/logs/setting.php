<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$log = $_G['setting']['log'];
if(!submitcheck('submit')) {
	showtips('nav_logs_setting_tips');
	showformheader('logs&operation=setting');
	showtableheader();
	if($_G['config']['log']['type'] != 'file') {
		$options = [];
		foreach($menu as $row) {
			if(empty($row[0]['menu']) || !str_starts_with($row[0]['menu'], 'nav_logs')) {
				continue;
			}
			foreach($row[0]['submenu'] as $submenu) {
				$txt = $submenu[0];
				parse_str($submenu[1], $p);
				if(empty($p['operation'])) {
					continue;
				}
				$options[] = [$p['operation'], cplang($txt)];
			}
		}
		showtitle('nav_logs_clearlogs');
		showsetting('nav_logs_clearlogs_time', 'log[clearlogstime]', $log['clearlogstime'] ?? 0, 'number');
		$clearlogstypes = dunserialize($log['clearlogstypes']);
		showsetting('nav_logs_clearlogs_types', ['log[clearlogstypes]', $options], $clearlogstypes, 'mcheckbox');
	}

	showtitle('nav_logs_member');
	showsetting('nav_logs_illegal', 'log[illegal]', $log['illegal'], 'radio');
	showsetting('nav_logs_ban', 'log[ban]', $log['ban'], 'radio');
	showsetting('nav_logs_mods', 'log[mods]', $log['mods'], 'radio');
	showsetting('nav_logs_sms', 'log[sms]', $log['sms'], 'radio');
	showsetting('nav_logs_login', 'log[login]', $log['login'], 'radio');

	showtitle('nav_logs_system');
	showsetting('nav_logs_cp', 'log[cp]', $log['cp'], 'radio');
	showsetting('nav_logs_modcp', 'log[modcp]', $log['modcp'], 'radio');
	showsetting('nav_logs_error', 'log[error]', $log['error'], 'radio');
	showsetting('nav_logs_sendmail', 'log[sendmail]', $log['sendmail'], 'radio');
	showsetting('nav_logs_SMTP', 'log[SMTP]', $log['SMTP'], 'radio');

	showtitle('nav_logs_extended');
	showsetting('nav_logs_rate', 'log[rate]', $log['rate'], 'radio');
	showsetting('nav_logs_pmt', 'log[pmt]', $log['pmt'], 'radio');

	showsubmit('submit');
	showtablefooter();
	showformfooter();
} else {
	$settings = [
		'log' => $_GET['log'],
	];
	table_common_setting::t()->update_batch($settings);
	updatecache('setting');
	cpmsg('setting_update_succeed', 'action=logs', 'succeed');
}
	