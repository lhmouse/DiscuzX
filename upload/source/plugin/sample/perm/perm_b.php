<?php

namespace sample;

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class perm_b {

	var $name = 'perm_b';

	public function fetch_perm($uid) {
		return 0;
	}

}