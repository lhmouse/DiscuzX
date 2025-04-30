<?php

//childfile:forum/viewthread/postarr
//在原有子文件输出内容后补充内容

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once childfile('postarr', 'forum/viewthread', false);

foreach($postarr as $k => $v) {
	$postarr[$k]['message'] = "哈哈！\n".$v['message'];
}