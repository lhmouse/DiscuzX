<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class task_name {
	var $version = '1.0';//脚本版本号
	var $name = 'name';//任务名称 (可填写语言包项目)
	var $description = 'desc';//任务说明 (可填写语言包项目)
	var $copyright = 'Discuz! Team';//版权 (可填写语言包项目)
	var $icon = '';//默认图标
	var $period = '';//默认任务间隔周期
	var $periodtype = 0;//默认任务间隔周期单位
	var $conditions = [//任务附加条件
		'text' => [
			'title' => 'text_title',//设置项目名称 (可填写语言项目)
			'type' => 'mradio',//项目类型
			'value' => [],//项目选项
			'default' => 0,//项目默认值
			'sort' => 'complete',//条件类型 (apply:申请任务条件 complete:完成任务条件)
		],
	];
	function preprocess($task) {//申请任务成功后的附加处理
	}
	function csc($task = []) {//判断任务是否完成 (返回 TRUE:成功 FALSE:失败 0:任务进行中进度未知或尚未开始  大于0的正数:任务进行中返回任务进度)
	}
	function sufprocess($task) {//完成任务后的附加处理
	}
	function view($task, $taskvars) {//任务显示
	}
	function install() {//任务安装的附加处理
	}
	function uninstall() {//任务卸载的附加处理
	}
	function upgrade() {//任务升级的附加处理
	}
}
