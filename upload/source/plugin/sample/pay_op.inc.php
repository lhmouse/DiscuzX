<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

/*
 * 此脚本访问地址：http://discuz/?app=plugin&id=sample:pay_op
 */

switch($_GET['op']) {
	case 'add':
		//添加自定义支付通道
		payment::channels_add('sample', [
			'id' => 'sample:test',
			'title' => 'sample',
			'logo' => 'static/image/common/logo.svg',
		]);
		break;
	case 'delete':
		//删除自定义支付通道
		payment::channels_delete('sample');
		break;

}

echo 'Done';
