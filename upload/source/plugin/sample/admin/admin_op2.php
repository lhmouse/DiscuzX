<?php

namespace sample;

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

// http://discuz/admin.php?action=plugins&operation=config&identifier=sample&pmod=op2

class admin_op2 {

	public function __construct() {

	}
	public function init() {
		if(!submitcheck('submit')) {
			showformheader('plugins&operation=config&do='.$do.'&identifier=sample&pmod=op2');
			showtableheader('', 'fixpadding');
			showcomponent('测试2', 'abc1', 'yellow', 'sample:component_test');
			showsubmit('submit');
			showtablefooter();
			showformfooter();
		} else {
			debug($_GET);
		}
	}

}