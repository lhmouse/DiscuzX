<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_common_tag extends discuz_table {
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {

		$this->_table = 'common_tag';
		$this->_pk = 'tagid';

		parent::__construct();
	}

	public function fetch_by_tagid($tagid) {
		return DB::fetch_first('SELECT * FROM %t WHERE tagid=%d', [$this->_table, $tagid]);
	}

	public function fetch_all_by_status($status = NUll, $tagname = '', $startlimit = 0, $count = 0, $returncount = 0, $order = '') {
		if($status === NULL) {
			$statussql = 'status<>3';
		} else {
			$statussql = 'status='.intval($status);
		}
		$data = [$this->_table];
		if($tagname) {
			$namesql = ' AND tagname LIKE %s';
			$data[] = '%'.$tagname.'%';
		}
		if($returncount) {
			return DB::result_first("SELECT count(*) FROM %t WHERE $statussql $namesql", $data);
		}
		return DB::fetch_all("SELECT * FROM %t WHERE $statussql $namesql ORDER BY ".DB::order('tagid', $order). ' ' .DB::limit($startlimit, $count), $data);
	}

	public function fetch_all_by_cid($cid = '', $startlimit = 0, $count = 0, $returncount = 0, $order = 'ASC') {
		$data = [$this->_table];
		$sql = '';
		if($cid) {
			$sql = ' cid = %d';
			$data[] = $cid;
		}
		if($returncount) {
			return DB::result_first("SELECT count(*) FROM %t WHERE $sql", $data);
		}
		return DB::fetch_all("SELECT * FROM %t WHERE$sql ORDER BY ".DB::order('tagid', $order). ' ' .DB::limit($startlimit, $count), $data);
	}

	public function fetch_all_by_cid_and_status($cid = '', $status = NUll, $startlimit = 0, $count = 0, $returncount = 0, $order = 'ASC') {
		$data = [$this->_table];
		$sql = ' 1=1 ';
		if($cid) {
			$sql .= ' AND cid = %d';
			$data[] = $cid;
		}
		if($status !== NULL) {
			$sql .= ' AND status= %d';
			$data[] = intval($status);
		}
		if($returncount) {
			return DB::result_first("SELECT count(*) FROM %t WHERE $sql", $data);
		}
		return DB::fetch_all("SELECT * FROM %t WHERE$sql ORDER BY ".DB::order('tagid', $order). ' ' .DB::limit($startlimit, $count), $data);
	}

	public function fetch_all_by_tagname_and_cid($tagname = '', $cid = 0) {
		$data = [$this->_table];
		if($tagname) {
			$namesql = ' tagname = %s AND cid = %d';
			$data[] = $tagname;
			$data[] = $cid;
		}
		return DB::fetch_all("SELECT * FROM %t WHERE $namesql", $data);
	}

	public function insert($data, $return_insert_id = false, $replace = false, $silent = false) {
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::insert($data, $return_insert_id, $replace, $silent);
		} else {
			$return_insert_id = $return_insert_id === false ? 0 : $return_insert_id;
			return $this->insert_tag($data, $return_insert_id);
		}
	}

	public function insert_newtag($data) {
		return DB::insert($this->_table, $data);
	}

	public function insert_tag($tagname, $status = 0) {
		DB::query('INSERT INTO %t (tagname, status) VALUES (%s, %d)', [$this->_table, $tagname, $status]);
		return DB::insert_id();
	}

	public function get_byids($ids) {
		if(empty($ids)) {
			return [];
		}
		if(!is_array($ids)) {
			$ids = [$ids];
		}
		return DB::fetch_all('SELECT * FROM %t WHERE tagid IN (%n)', [$this->_table, $ids], 'tagid');
	}

	public function get_bytagname($tagname, $type) {
		if(empty($tagname)) {
			return [];
		}
		$statussql = $type != 'uid' ? ' AND status<\'3\'' : ' AND status=\'3\'';
		return DB::fetch_first('SELECT * FROM %t WHERE tagname=%s '.$statussql, [$this->_table, $tagname]);
	}

	public function fetch_info($tagid, $tagname = '') {
		if(empty($tagid) && empty($tagname)) {
			return [];
		}
		$addsql = $sqlglue = '';
		if($tagid) {
			$addsql = ' tagid=' .intval($tagid);
			$sqlglue = ' AND ';
		}
		if($tagname) {
			$addsql .= $sqlglue.' '.DB::field('tagname', $tagname);
		}
		return DB::fetch_first('SELECT tagid,tagname,status FROM ' .DB::table('common_tag')." WHERE $addsql");
	}

	public function delete_byids($ids) {
		if(empty($ids)) {
			return false;
		}
		if(!is_array($ids)) {
			$ids = [$ids];
		}
		return DB::query('DELETE FROM %t WHERE tagid IN (%n)', [$this->_table, $ids]);
	}
}

