<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

// 用户分表操作前先判定用户分表是否开启
if(getglobal('setting/membersplit')) {
	table_common_member::t()->split(100);
}

