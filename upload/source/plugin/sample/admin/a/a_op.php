<?php

namespace sample\admin;

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

// http://discuz/admin.php?action=plugins&operation=config&identifier=sample&pmod=a/op

class a_op {

	public static function run() {

		if(!submitcheck('submit')) {
			showformheader('plugins&operation=config&do='.$do.'&identifier=sample&pmod=a/op');
			showtableheader('', 'fixpadding');
			showcomponent('测试3', 'abc1', 'yellow', 'sample:component_test');
			showsubmit('submit');
			showtablefooter();
			showformfooter();
		} else {
			debug($_GET);
		}

	}

}