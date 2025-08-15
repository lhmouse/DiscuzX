<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

//超级分类信息数据的安装和卸载

/*
 * 此脚本访问地址：http://discuz/?app=plugin&id=sample:threadtype
 */

require_once libfile('function/plugin');

switch($_GET['op']) {
	case 'install':
		threadtype_install(
			name: 'sample',
			fieldPrefix: 'sample',
			typeData: [
				'name' => 'sample 分类信息',
				'description' => 'sample 超级分类信息范例',
				'special' => 1,
				'modelid' => 0,
				'expiration' => 0,
				'template' => '[file]sample:forum/super_viewthread_node[/file]',
				'stemplate' => '[file]sample:forum/super_forumdisplay_node[/file]',
				'super' => serialize([
					'forumdisplay' => 'sample:forum/super_forumdisplay',
					'viewthread' => 'sample:forum/super_viewthread',
				]),
			],
			fieldData: [
				[
					'title' => '字段1',
					'identifier' => 'sample_field1',
					'type' => 'text',
				],
				[
					'title' => '字段2',
					'identifier' => 'sample_field2',
					'type' => 'textarea',
				],
			],
		);
		echo 'Done';
		break;
	case 'uninstall':
		threadtype_uninstall('sample');
		echo 'Done';
		break;
}