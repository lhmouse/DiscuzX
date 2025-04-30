<?php

// index.php?app=sample

require './source/class/class_core.php';

$discuz = C::app();
$discuz->cachelist = [];
$discuz->init();

if(!empty($_GET['notice'])) {
	showmessage('sample_message');
}

//新数据库类调用方式
$userinfo = sample\table_test1::t()->fetch_all_by_uid($_G['member']['uid']);

require_once appfile('yyy/zzz');