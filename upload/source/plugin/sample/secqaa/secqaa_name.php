<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class secqaa_name {
	var $version = '1.0';//脚本版本号
	var $name = 'secqaa_name';//安全验证组件名称 (可填写语言包项目)
	var $description = 'secqaa_name_desc';//安全验证组件说明 (可填写语言包项目)
	var $copyright = 'copyright';//版权 (可填写语言包项目)
	var $settingurl = 'action=plugins&operation=config&identifier=sample&pmod=op1';//安全验证设置页URL

	function create(&$question) {//返回安全验证的答案和问题 ($question 为问题，函数返回值为答案)
		$question = '<div>&#x8F93;&#x5165;&#x20;&#x61;&#x6E;&#x73;&#x77;&#x65;&#x72;</div>';
		return 'answer';//可返回答案也可以返回校验ID
	}

	function check($value, $answer) {//通过返回的 $value 和 create 返回的 $answer 校验是否正确
		return $value == $answer;
	}

}
