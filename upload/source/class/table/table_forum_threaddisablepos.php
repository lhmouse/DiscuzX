<?php
/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_threaddisablepos extends discuz_table {

	private $enable_set = false;

	/*
	 * memory支持Set的情况下，所有的数据保存于一个Set下
	 */
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	public function __construct() {
		$this->_table = 'forum_threaddisablepos';
		$this->_pk = 'tid';
		$this->_pre_cache_key = 'forum_threaddisablepos_';
		$this->_cache_ttl = 604800;
		parent::__construct();

		// 依赖set
		// 不影响原_allowmem变量，这样在没有Set的情况下，还能使用原来的缓存加速方案
		$this->enable_set = $this->_allowmem && C::memory()->gotset;
	}

	public function truncate() {
		if(!$this->enable_set) {
			DB::query('TRUNCATE '.DB::table('forum_threaddisablepos'));
		}
		return memory('rm', 'idx_threaddisablepos', $this->_pre_cache_key);
	}

	public function insert($data, $return_insert_id = false, $replace = false, $silent = false) {
		if(!$this->enable_set) {
			return parent::insert($data, $return_insert_id, $replace, $silent);
		}
		return memory('sadd', 'idx_threaddisablepos', $data['tid'], 0, $this->_pre_cache_key);
	}

	public function fetch($id, $force_from_db = false) {
		if(!$this->enable_set) {
			return parent::fetch($id, $force_from_db);
		}
		return memory('sismember', 'idx_threaddisablepos', $id, 0, $this->_pre_cache_key);
	}

}

