<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$count = 0;
if(isset($_GET['aids']) && isset($_GET['formhash']) && formhash() == $_GET['formhash']) {
	$_GET['aids'] = (array)$_GET['aids'];
	foreach($_GET['aids'] as $aid) {
		$attach = table_forum_attachment_n::t()->fetch_attachment('aid:'.$aid, $aid);
		if($attach && ($attach['pid'] && $attach['pid'] == $_GET['pid'] && $_G['uid'] == $attach['uid'])) {
			updatecreditbyaction('postattach', $attach['uid'], [], '', -1, 1, $_G['fid']);
		}
		if($attach && ($attach['pid'] && $attach['pid'] == $_GET['pid'] && $_G['uid'] == $attach['uid'] || $_G['forum']['ismoderator'] || !$attach['pid'] && $_G['uid'] == $attach['uid'])) {
			table_forum_attachment_n::t()->delete_attachment('aid:'.$aid, $aid);
			table_forum_attachment::t()->delete($aid);
			dunlink($attach);
			$count++;
		}
	}
}
if(defined('IN_RESTFUL')) {
	echo $count;
	exit();
}
include template('common/header_ajax');
echo $count;
include template('common/footer_ajax');
dexit();
	