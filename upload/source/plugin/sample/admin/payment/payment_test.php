<?php

//自定义支付通道 后台入口

namespace sample\admin;

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class payment_test {

	var $name = 'test支付';

	public function admincp() {
		if(!submitcheck('submit')) {
			showformheader('ec&operation=method&id=sample:test');
			showtableheader();
			showtitle('baseSetting');
			showsetting('allow', 'conf[allow]', $conf['allow'], 'radio');
			showsetting('callbackUrl', 'conf[callbackUrl]', $conf['callbackUrl'], 'text');
			showsubmit('submit');
			showtablefooter();
			showformfooter();
		} else {
			debug($_GET['conf']);
		}
	}

}