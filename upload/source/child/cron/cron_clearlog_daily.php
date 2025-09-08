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
	if(in_array('credit', $clearlogstypes)) {
		table_common_credit_log::t()->delete_by_removetime($removetime);
		table_common_credit_log_field::t()->delete_by_removetime($removetime);
	}
	if(in_array('warn', $clearlogstypes)) {
		table_forum_warning::t()->delete_by_removetime($removetime);
	}
	if(in_array('magic', $clearlogstypes)) {
		table_common_magiclog::t()->delete_by_removetime($removetime);
	}
	if(in_array('medal', $clearlogstypes)) {
		table_forum_medallog::t()->delete_by_removetime($removetime);
	}
	if(in_array('invite', $clearlogstypes)) {
		table_common_invite::t()->delete_by_removetime($removetime);
	}
	if(in_array('crime', $clearlogstypes)) {
		table_common_member_crime::t()->delete_by_removetime($removetime);
	}
}

