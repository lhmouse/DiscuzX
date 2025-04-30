<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_home_docomment extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'home_docomment';
		$this->_pk = 'id';

		parent::__construct();
	}

	public function delete_by_doid_uid($doids = null, $uids = null) {
		$sql = [];
		$doids && $sql[] = DB::field('doid', $doids);
		$uids && $sql[] = DB::field('uid', $uids);
		if($sql) {
			return DB::query('DELETE FROM %t WHERE %i', [$this->_table, implode(' OR ', $sql)]);
		} else {
			return false;
		}
	}

	public function fetch_all_by_doid($doids) {
		if(empty($doids)) {
			return [];
		}
		return DB::fetch_all('SELECT * FROM %t WHERE '.DB::field('doid', $doids).' ORDER BY dateline', [$this->_table]);
	}

}

