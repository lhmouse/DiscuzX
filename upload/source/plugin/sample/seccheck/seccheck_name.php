<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class seccheck_name {
	var $version = '1.0';//脚本版本号
	var $name = 'seccheck_name';//安全验证场景名称 (可填写语言包项目)
	var $copyright = 'Discuz! Team';//版权 (可填写语言包项目)
	var $settingurl = 'action=plugins&operation=config&identifier=sample&pmod=op4';//安全验证场景设置页URL

	function rule($param = []) {//安全验证显示规则，返回 true 表示此处显示安全验证
		return true;
	}

}
