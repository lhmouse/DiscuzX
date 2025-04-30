<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class adv_name {
	var $version = '1.0';//脚本版本号
	var $name = 'name';//广告类型名称 (可填写语言包项目)
	var $description = 'desc';//广告类型说明 (可填写语言包项目)
	var $copyright = 'Discuz! Team';//版权 (可填写语言包项目)
	var $targets = ['portal', 'home', 'member', 'forum', 'group', 'userapp', 'plugin', 'custom'];//广告类型适用的投放范围
	var $imagesizes = ['120x60', '120x240'];//图片广告推荐大小
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
	function evalcode() {//广告显示时的运行代码
		return [
			//检测广告是否投放时的代码
			'check' => '
            if(condition) {
                $checked = false;
            }',
			//广告显示时的代码 (随机调用投放的广告)
			'create' => '$adcode = $codes[$adids[array_rand($adids)]];',
		];
	}
}
