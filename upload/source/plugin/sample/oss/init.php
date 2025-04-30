<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class oss_plugin_sample {

	/*
	 * 如需支持单一类型
	 */
	const Name = 'oss_name';
	const Desc = 'oss_desc';

	/*
	 * 如需支持多个类型
	 */
	/*
	const SubType = [
	        'oss1' => ['name' => 'oss_name1', 'desc' => 'oss_desc1'],
	        'oss2' => ['name' => 'oss_name2', 'desc' => 'oss_desc2'],
	];
	*/

	public static function load($param) {
		require_once 'cos.php';
		//require_once DISCUZ_ROOT.'./vendor/oss/tencent/vendor/autoload.php';
		//return new cos_tencent($param['oss_id'], $param['oss_key'], $param['oss_bucket'], $param['oss_endpoint'], $param['oss_url'], $param['oss_bucket_url']);
	}

}