<?php

namespace sample;

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

// http://discuz/admin.php?action=plugins&operation=config&identifier=sample&pmod=op3

define('FOOTERDISABLED', true);

class admin_op3 {

	public function __construct() {

	}
	public function init() {
		ob_end_clean();
		echo json_encode(['ret' => 0, 'msg' => 'success']);
	}

}