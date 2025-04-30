<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class magic_name {
	var $version = '1.0';//脚本版本号
	var $name = 'name';//道具名称 (可填写语言包项目)
	var $description = 'desc';//道具说明 (可填写语言包项目)
	var $price = '10';//道具默认价格
	var $weight = '10';//道具默认重量
	var $copyright = 'Discuz! Team';//版权 (可填写语言包项目)
	function getsetting() {//返回设置项目
		$settings = [
			'text' => [
				'title' => 'text_title',//设置项目名称 (可填写语言项目)
				'type' => 'mradio',//项目类型
				'value' => [],//项目选项
				'default' => 0,//项目默认值
			]
		];
		return $settings;
	}
	function setsetting(&$advnew, &$parameters) {//保存设置项目
	}
	function usesubmit($magic, $parameters) {//道具使用
	}
	function show($magic) {//道具显示
	}
}
