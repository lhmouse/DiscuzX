<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

/**
 * 标签数据表操作类
 *
 * 提供标签的增删改查等功能
 *
 * @package discuz_core
 * @subpackage table
 * @category common
 * @author Discuz! Team
 * @link https://discuz.vip
 */
class table_common_tag extends discuz_table {
	/**
	 * 获取类的单例实例
	 *
	 * @return table_common_tag 单例对象
	 */
	public static function t() {
		static $_instance;
		if(!isset($_instance)) {
			$_instance = new self();
		}
		return $_instance;
	}

	/**
	 * 类构造函数
	 *
	 * 初始化表名、主键和缓存前缀
	 */
	public function __construct() {
		$this->_table = 'common_tag';
		$this->_pk = 'tagid';
		$this->_pre_cache_key = 'common_tag_';
		parent::__construct();
	}

	/**
	 * 通过标签ID获取标签信息
	 *
	 * @param int $tagid 标签ID
	 * @return array 标签数据数组，不存在则返回空数组
	 */
	public function fetch_by_tagid($tagid) {
		return DB::fetch_first('SELECT * FROM %t WHERE tagid=%d', [$this->_table, $tagid]);
	}

	/**
	 * 根据状态获取标签列表
	 *
	 * @param int|null $status 标签状态，NULL表示获取除3以外的所有状态
	 * @param string $tagname 标签名搜索关键词
	 * @param int $startlimit 起始位置，用于分页
	 * @param int $count 返回数量，0表示不限制
	 * @param bool $returncount 是否返回总数而不是列表
	 * @param string $order 排序方式
	 * @return array|int 标签数据数组或总数（根据$returncount参数决定）
	 */
	public function fetch_all_by_status($status = NULL, $tagname = '', $startlimit = 0, $count = 0, $returncount = 0, $order = '') {
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

	/**
	 * 插入标签数据
	 *
	 * @param array $data 标签数据数组
	 * @param bool $return_insert_id 是否返回插入ID
	 * @param bool $replace 是否使用REPLACE INTO
	 * @param bool $silent 是否静默模式，忽略错误
	 * @return int|bool 插入ID或布尔值（根据$return_insert_id参数决定）
	 * @throws Exception 如果启用了DISCUZ_DEPRECATED常量
	 */
	public function insert($data, $return_insert_id = false, $replace = false, $silent = false) {
		if(defined('DISCUZ_DEPRECATED')) {
			throw new Exception('NotImplementedException');
			return parent::insert($data, $return_insert_id, $replace, $silent);
		} else {
			$return_insert_id = $return_insert_id === false ? 0 : $return_insert_id;
			return $this->insert_tag($data, $return_insert_id);
		}
	}

	/**
	 * 插入新标签
	 *
	 * @param string $tagname 标签名
	 * @param int $status 标签状态，默认0
	 * @return int 插入的标签ID
	 */
	public function insert_tag($tagname, $status = 0) {
		DB::query('INSERT INTO %t (tagname, status, related_count, hot_score, created_at) VALUES (%s, %d, 0, 0, %d)', [$this->_table, $tagname, $status, TIMESTAMP]);
		return DB::insert_id();
	}

	/**
	 * 通过标签ID数组获取标签列表
	 *
	 * @param array|int $ids 标签ID数组或单个ID
	 * @return array 标签数据数组，键为标签ID
	 */
	public function get_byids($ids) {
		if(empty($ids)) {
			return [];
		}
		if(!is_array($ids)) {
			$ids = [$ids];
		}
		return DB::fetch_all('SELECT * FROM %t WHERE tagid IN (%n)', [$this->_table, $ids], 'tagid');
	}

	/**
	 * 通过标签名获取标签信息
	 *
	 * @param string $tagname 标签名
	 * @param string $type 查询类型，'uid'表示获取状态为3的标签，其他表示获取状态小于3的标签
	 * @return array 标签数据数组，不存在则返回空数组
	 */
	public function get_bytagname($tagname, $type) {
		if(empty($tagname)) {
			return [];
		}
		$statussql = $type != 'uid' ? ' AND status<\'3\'' : ' AND status=\'3\'';
		return DB::fetch_first('SELECT * FROM %t WHERE tagname=%s '.$statussql, [$this->_table, $tagname]);
	}

	/**
	 * 获取标签基本信息
	 *
	 * @param int $tagid 标签ID
	 * @param string $tagname 标签名
	 * @return array 标签基本信息数组，不存在则返回空数组
	 */
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
		return DB::fetch_first('SELECT * FROM ' .DB::table('common_tag')." WHERE $addsql");
	}

	/**
	 * 通过标签ID数组删除标签
	 *
	 * @param array|int $ids 标签ID数组或单个ID
	 * @return bool 是否删除成功
	 */
	public function delete_byids($ids) {
		if(empty($ids)) {
			return false;
		}
		if(!is_array($ids)) {
			$ids = [$ids];
		}
		return DB::query('DELETE FROM %t WHERE tagid IN (%n)', [$this->_table, $ids]);
	}

	/**
	 * 按热度排序获取标签列表
	 *
	 * @param int|null $status 标签状态，NULL表示获取除3以外的所有状态
	 * @param int $startlimit 起始位置，用于分页
	 * @param int $count 返回数量
	 * @param string $order 排序方式，默认DESC
	 * @return array 标签列表
	 */
	public function fetch_all_by_hot($status = NULL, $startlimit = 0, $count = 0, $order = 'DESC', $order_by = 'hot_score') {
		if($status === NULL) {
			$statussql = 'status<>3';
		} else {
			$statussql = 'status='.intval($status);
		}
		if($order_by == 'rand'){
			$ordersql = " ORDER BY rand()";
		}else{
			$ordersql = " ORDER BY ".DB::order($order_by, $order);
		}
		return DB::fetch_all("SELECT * FROM %t WHERE $statussql $ordersql" .DB::limit($startlimit, $count), [$this->_table]);
	}

	/**
	 * 批量获取标签热度
	 *
	 * @param array $tagids 标签ID数组
	 * @return array 标签热度信息，键为标签ID，值为热度分数
	 */
	public function fetch_hot_by_tagids($tagids) {
		if(empty($tagids)) {
			return [];
		}
		return DB::fetch_all('SELECT tagid, hot_score FROM %t WHERE tagid IN (%n)', [$this->_table, $tagids], 'tagid');
	}

	/**
	 * 增加标签关联数据
	 *
	 * @param array|int $tagids 标签ID数组或单个ID
	 * @param array $setarr 更新的字段数组，支持的字段：related_count
	 */
	public function increase($tagids, $setarr) {
		$tagids = array_map('intval', (array)$tagids);
		$sql = [];
		$allowkey = ['related_count'];
		foreach($setarr as $key => $value) {
			if(($value = intval($value)) && in_array($key, $allowkey)) {
				$sql[] = "`$key`=`$key`+'$value'";
			}
		}
		if(!empty($sql)) {
			DB::query('UPDATE ' .DB::table($this->_table). ' SET ' .implode(',', $sql). ' WHERE tagid IN (' .dimplode($tagids). ')', 'UNBUFFERED');
			$this->increase_cache($tagids, $setarr);
		}
	}
}