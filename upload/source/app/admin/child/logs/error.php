<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

showtableheader('', 'fixpadding');

showtablerow('class="header"', ['class="td23" style="box-sizing: unset;"', 'class="td24" style="box-sizing: unset;"', 'class="td23" style="box-sizing: unset;"'], [
	cplang('time'),
	cplang('logs_device'),
	cplang('message'),
]);

foreach($logs as $k => $logrow) {
	$data = json_decode($logrow['data'], true);
	$device = json_decode($logrow['device'], true);
	$log = [];
	$log[1] = dgmdate($logrow['dateline']);
	$log[2] = $_G['group']['allowviewip'] ? 'ClientIP: '.$device['client_ip'].'&nbsp;&nbsp;<a href="javascript:;" onclick="togglelog('.$logrow['id'].')">'.cplang('more').'</a>' : '-';

	showtablerow('', ['class="bold" style="box-sizing: unset;"', 'style="box-sizing: unset;"'], [
		$log[1],
		$log[2],
		str_replace(' -> ', '<br>', $data['message']).'<br>hash:'.$data['hash'].'<br>clientip:'.$data['clientip'].'<br>'.$data['user'].'<br>'.$data['uri']
	]);
	echo showdevice($logrow['id'], $device, 3);
}
	