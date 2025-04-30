<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class block_sample_test3 extends discuz_block {

	function __construct() {}

	function name() {
		return 'test3';
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
		return ['sample_test3', '天气'];
	}

	function getdata($style, $parameter) {
		return ['html' => '<iframe scrolling="no" src="https://widget.tianqiapi.com/?style=yf&skin=pitaya" frameborder="0" width="320" height="320" allowtransparency="true"></iframe>', 'data' => null];
	}
}

