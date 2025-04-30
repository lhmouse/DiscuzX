<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
if($_G['setting']['log']['clearlogstime'] && $_G['setting']['log']['clearlogstypes']) {
	$removetime = TIMESTAMP - $_G['setting']['log']['clearlogstime'] * 86400;
	$clearlogstypes = dunserialize($_G['setting']['log']['clearlogstypes']);
	table_common_log::t()->delete_by_removetime($removetime, $clearlogstypes);
}

