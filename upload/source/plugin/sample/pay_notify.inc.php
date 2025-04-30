<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

//自定义支付通道 notify 回调

/*
 * 此脚本访问地址：http://discuz/?app=plugin&id=sample:pay_notify
 */

payment::finish_order('sample:test', $_GET['out_biz_no'], $_GET['trade_no'], time());

dheader('location: '.dreferer($_GET['referer']));