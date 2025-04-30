<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_restful_app extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'restful_app';

		$this->_pk = 'appid';

		parent::__construct();
	}

	public function fetch_all_data() {
		return DB::fetch_all('SELECT * FROM %t', [$this->_table]);
	}
}
