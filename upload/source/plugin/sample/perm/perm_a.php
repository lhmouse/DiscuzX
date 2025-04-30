<?php

namespace sample;

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class perm_a {

	var $name = 'perm_a';

	public function fetch_perm($uid) {
		return 1;
	}

}