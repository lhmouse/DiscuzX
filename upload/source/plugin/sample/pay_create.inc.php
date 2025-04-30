<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

//自定义支付入口 创建订单

/*
 * 此脚本访问地址：http://discuz/?app=plugin&id=sample:pay_create
 */

$ret = payment::create_order('sample:test',
	'test title', 'test desc', 1,
	$_G['siteurl'].'/index.php?app=plugin&id=sample:pay_callback', ['xxx' => time()]);

echo '<a href="'.$ret.'">点击这里支付</a>';
