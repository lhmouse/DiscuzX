<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class discuz_rank extends discuz_base {
	public $name = '';

	public function __construct($name) {
		if($name) {
			$this->name = $name;
		} else {
			throw new Exception('The property "'.get_class($this).'->name" is empty');
		}
	}

	public function fetch_list($order = 'DESC', $start = 0, $limit = 0) {
		return table_common_rank::t()->fetch_list($this->name, $order, $limit);
	}

	public function fetch_rank($key) {
		return table_common_rank::t()->fetch_rank($this->name, $key);
	}

	public function set($key, $value) {
		return table_common_rank::t()->insert($this->name, $key, $value);
	}

	public function inc($key, $value) {
		return table_common_rank::t()->inc($this->name, $key, $value);
	}

	public function dec($key, $value) {
		return table_common_rank::t()->dec($this->name, $key, $value);
	}

	public function clear() {
		return table_common_rank::t()->delete($this->name);
	}

	public function rm($key) {
		return $key ? table_common_rank::t()->delete($this->name, $key) : false;
	}

}

