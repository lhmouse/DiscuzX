<?php

namespace sample;

use discuz_table;
use DB;

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_test1 extends discuz_table
{

	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'sample_test1';
		$this->_pk    = '';

		parent::__construct();
	}

	public function fetch_all_by_uid($uid) {
		return ['uid' => $uid, 'name' => 'test'];
		//return DB::fetch_all('SELECT * FROM %t WHERE uid=%d', [$this->_table, $uid]);
	}

}

