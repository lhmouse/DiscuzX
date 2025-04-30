<?php

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

showtableheader('', 'fixpadding');

showtablerow('class="header"', ['class="td23"', 'class="td24"', 'class="td23"', 'class="td23"', 'class="td23"'], [
	cplang('time'),
	'text',
	cplang('logs_device'),
]);

foreach($logs as $k => $logrow) {
	$data = json_decode($logrow['data'], true);
	$device = json_decode($logrow['device'], true);
	showtablerow('', ['class="smallefont"', 'class="smallefont"', 'class="bold"', 'class="smallefont"', 'class="smallefont"'], [
		dgmdate($logrow['dateline']),
		is_array($data['p']) ? print_r($data['p'], 1) : $data['p'],
		$_G['group']['allowviewip'] ? 'ClientIP: '.$device['client_ip'].'&nbsp;&nbsp;<a href="javascript:;" onclick="togglelog('.$logrow['id'].')">'.cplang('more').'</a>' : '-',

	]);
	echo showdevice($logrow['id'], $device, 6);
}
	