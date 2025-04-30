<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class seccode_name {
	var $version = '1.0';//脚本版本号
	var $name = 'name';//验证码类型名称 (可填写语言包项目)
	var $copyright = 'Discuz! Team';//版权 (可填写语言包项目)
	var $setting = [];//后台设置后的变量
	function check() {//检查输入的验证码，返回 true 表示通过
	}
	function make() {//输出验证码，echo 输出内容将显示在页面中
	}
	function image() {//纯图片验证码 URL，用于移动端显示(Discuz! X3.1 新增)
	}
}
