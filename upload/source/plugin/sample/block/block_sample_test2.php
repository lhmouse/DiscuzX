<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class block_sample_test2 extends discuz_block {

	function __construct() {
	}

	function name() {
		return 'test2';
	}

	function blockclass() {
		return ['sample_test2', 'test2'];
	}

	function fields() {
		return [
			'posts' => ['name' => lang('blockclass', 'blockclass_other_stat_posts'), 'formtype' => 'text', 'datatype' => 'int'],
			'field1' => ['name' => '示例字段1', 'formtype' => 'text', 'datatype' => 'string'],
			'field2' => ['name' => '示例字段2', 'formtype' => 'title', 'datatype' => 'title'],
		];
	}

	function getsetting() {
		global $_G;
		$settings = [
			'option' => [
				'title' => 'aaao',
				'type' => 'mcheckbox',
				'value' => [
					['a', 'aaa'],
					['b', 'bbb'],
					['c', 'ccc'],
				],
				'default' => ['a', 'b']
			],
		];
		return $settings;
	}

	function getdata($style, $parameter) {
		global $_G;
		$parameter = $this->cookparameter($parameter);
		$fields = [
			'posts' => 0,
			'field1' => 'aaaaa',
			'field2' => 'bbbbb',
		];
		$list = [];
		$list[1] = [
			'id' => 1,
			'idtype' => 'statid',
			'fields' => $fields,
		];
		return ['html' => '', 'data' => $list];
	}
}

