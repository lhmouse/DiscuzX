<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

/*
 * 此脚本访问地址：http://discuz/?app=plugin&id=sample
 */

$time = time();
$text = !empty($_GET['text']) ? $_GET['text'] : [];

//调用插件类库
$c = sample\lib_base::add(1,1);
$c += sample\lib\sub_base::add(1,1);
$c += sample\lib\sub\ssub_base::add(1,1);

//日志系统
logger('sample:text', $_G['member'], $_G['member']['uid'], ['p' => $text, 't' => $time]);

//新数据库类调用方式
print_r(sample\table_test::t()->fetch_all_by_uid($_G['member']['uid']));

require_once template('diy:sample', 0, DISCUZ_PLUGIN('sample').'/template');

