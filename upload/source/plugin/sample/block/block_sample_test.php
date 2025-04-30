<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class block_sample_test extends discuz_block {

	function __construct() {}

	function name() {
		return 'test';
	}

	function getsetting() {
		global $_G;
		$settings = [
			'content' => [
				'title' => 'blank_content',
				'type' => 'mtextarea'
			]
		];
		return $settings;
	}

	function fields() {
		return [
		];
	}

	function blockclass() {
		return ['sample_test_real', 'test'];
	}

	function getdata($style, $parameter) {
		return ['html' => $parameter['content'].time(), 'data' => null];
	}
}

