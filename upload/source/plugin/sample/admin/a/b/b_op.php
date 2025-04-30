<?php

namespace sample\admin\a;

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

// http://discuz/admin.php?action=plugins&operation=config&identifier=sample&pmod=a/b/op

class b_op {

	public static function run() {

		if(!submitcheck('submit')) {
			showformheader('plugins&operation=config&do='.$do.'&identifier=sample&pmod=a/b/op');
			showtableheader('', 'fixpadding');
			showcomponent('测试4', 'abc1', 'yellow', 'sample:component_test');
			showsubmit('submit');
			showtablefooter();
			showformfooter();
		} else {
			debug($_GET);
		}

	}

}