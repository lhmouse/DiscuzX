<?php

namespace sample;

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

// http://discuz/admin.php?action=plugins&operation=config&identifier=sample&pmod=op4

class admin_op4 {

	public static function run() {

		if(!submitcheck('submit')) {
			showchildmenu([], cplang('验证问答类型设置'));

			showformheader('plugins&operation=config&do='.$do.'&identifier=sample&pmod=op4');
			showtableheader('', 'fixpadding');
			showcomponent('测试1', 'abc1', 'yellow', 'sample:component_test');
			showsubmit('submit');
			showtablefooter();
			showformfooter();
		} else {
			debug($_GET);
		}

	}

}