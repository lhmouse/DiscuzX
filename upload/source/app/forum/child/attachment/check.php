<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$requestmode) {
	// 获取附件所在版块信息
	$forum = table_forum_forumfield::t()->fetch_info_for_attach($thread['fid'], $_G['uid']);
	$_GET['fid'] = $forum['fid'];

	// 判断附件下载权限
	if($attach['isimage']) {
		$allowgetattach = ($_G['uid'] == $attach['uid']) ? true : ((!empty($forum['allowgetimage'])) ? $forum['allowgetimage'] == 1 : ($forum['getattachperm'] ? forumperm($forum['getattachperm']) : $_G['group']['allowgetimage']));
	} else {
		$allowgetattach = ($_G['uid'] == $attach['uid']) ? true : ((!empty($forum['allowgetattach'])) ? $forum['allowgetattach'] == 1 : ($forum['getattachperm'] ? forumperm($forum['getattachperm']) : $_G['group']['allowgetattach']));
	}
	if(($attach['readperm'] && $attach['readperm'] > $_G['group']['readaccess']) && $_G['adminid'] <= 0 && !($_G['uid'] && $_G['uid'] == $attach['uid'])) {
		$allowgetattach = FALSE;
		showmessage('attachment_forum_nopermission', NULL, [], ['login' => 1]);
	}

	$ismoderator = in_array($_G['adminid'], [1, 2]) ? 1 : ($_G['adminid'] == 3 ? table_forum_moderator::t()->fetch_uid_by_tid($attach['tid'], $_G['uid'], $archiveid) : 0);

	// 检查附件所在主题是否付费
	$ispaid = FALSE;
	$exemptvalue = $ismoderator ? 128 : 16;
	if(!$thread['special'] && $thread['price'] > 0 && (!$_G['uid'] || ($_G['uid'] != $attach['uid'] && !($_G['group']['exempt'] & $exemptvalue)))) {
		if(!$_G['uid'] || $_G['uid'] && !($ispaid = table_common_credit_log::t()->count_by_uid_operation_relatedid($_G['uid'], 'BTC', $attach['tid']))) {
			showmessage('attachment_payto', 'forum.php?mod=viewthread&tid='.$attach['tid']);
		}
	}

	// 检查收费附件及购买记录查询
	$exemptvalue = $ismoderator ? 64 : 8;
	if($attach['price'] && (!$_G['uid'] || ($_G['uid'] != $attach['uid'] && !($_G['group']['exempt'] & $exemptvalue)))) {
		$payrequired = $_G['uid'] ? !table_common_credit_log::t()->count_by_uid_operation_relatedid($_G['uid'], 'BAC', $attach['aid']) : 1;
		$payrequired && showmessage('attachement_payto_attach', 'forum.php?mod=misc&action=attachpay&aid='.$attach['aid'].'&tid='.$attach['tid']);
	}

	// 检查版块权限，如果该主题已付费，则放行
	if(!$ispaid && !$allowgetattach) {
		if(($forum['getattachperm'] && !forumperm($forum['getattachperm'])) || ($forum['viewperm'] && !forumperm($forum['viewperm']))) {
			showmessagenoperm('getattachperm', $forum['fid']);
		} else {
			showmessage('getattachperm_none_nopermission', NULL, [], ['login' => 1]);
		}
	}
}

$isimage = $attach['isimage'] == 1;
$_G['setting']['ftp']['hideurl'] = $_G['setting']['ftp']['hideurl'] || ($isimage && !empty($_GET['noupdate']) && $_G['setting']['attachimgpost'] && strtolower(substr($_G['setting']['ftp']['attachurl'], 0, 3)) == 'ftp');

// 输出图片附件的预览图
if(empty($_GET['nothumb']) && $isimage && $attach['thumb']) {
	$db = DB::object();
	$db->close();
	!$_G['config']['output']['gzip'] && ob_end_clean();
	dheader('Content-Disposition: inline; filename='.getimgthumbname($attach['filename']));
	dheader('Content-Type: image/pjpeg');
	if($attach['remote']) {
		$_G['setting']['ftp']['hideurl'] ? getremotefile(getimgthumbname($attach['attachment'])) : dheader('location:'.$_G['setting']['ftp']['attachurl'].'forum/'.getimgthumbname($attach['attachment']));
	} else {
		getlocalfile($_G['setting']['attachdir'].'/forum/'.getimgthumbname($attach['attachment']));
	}
	exit();
}

$filename = $_G['setting']['attachdir'].'/forum/'.$attach['attachment'];
if(!$attach['remote'] && !is_readable($filename)) {
	if(!$requestmode) {
		showmessage('attachment_nonexistence');
	} else {
		exit;
	}
}

if(!$requestmode) {
	// 非图片附件下载进行积分检查
	$exemptvalue = $ismoderator ? 32 : 4;
	if(!$isimage && !($_G['group']['exempt'] & $exemptvalue)) {
		$creditlog = updatecreditbyaction('getattach', $_G['uid'], array(), '', 1, 0, $thread['fid']);
		if($creditlog['updatecredit']) {
			if($_G['uid']) {
				$k = $_GET['ck'];
				$t = $_GET['t'];
				if(empty($k) || empty($t) || $k != substr(md5($aid.$t.md5($_G['config']['security']['authkey'])), 0, 8) || TIMESTAMP - $t > 3600) {
					dheader('location: forum.php?mod=misc&action=attachcredit&aid='.$attach['aid'].'&formhash='.FORMHASH);
					exit();
				}
			} else {
				showmessage('attachment_forum_nopermission', NULL, array(), array('login' => 1));
			}
		}
	}
}

// 多线程下载支持
// 解析 range 的范围，readmod = 1 or 4 的时候，支持 range
// range 传入有可能没有 end，这时候要在获取了文件大小后，根据文件大小设置 range_end
$range_start = 0;
$range_end = 0;
$has_range_header = false;
if(($readmod == 4 || $readmod == 1) && !empty($_SERVER['HTTP_RANGE'])) {
	$has_range_header = true;
	list($range_start, $range_end) = explode('-', (str_replace('bytes=', '', $_SERVER['HTTP_RANGE'])));
}

// 更新附件下载次数
if(!$requestmode && !$has_range_header && empty($_GET['noupdate'])) {
	if($_G['setting']['delayviewcount']) {
		$_G['forum_logfile'] = DISCUZ_DATA.'./cache/forum_attachviews_'.intval(getglobal('config/server/id')).'.log';
		if(substr(TIMESTAMP, -1) == '0') {
			attachment_updateviews($_G['forum_logfile']);
		}

		if(file_put_contents($_G['forum_logfile'], "$aid\n", FILE_APPEND) === false) {
			if($_G['adminid'] == 1) {
				showmessage('view_log_invalid', '', ['logfile' => 'cache/'.basename($_G['forum_logfile'])]);
			}
			table_forum_attachment::t()->update_download($aid);
		}
	} else {
		table_forum_attachment::t()->update_download($aid);
	}
}