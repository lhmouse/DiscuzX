<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['setting']['doingstatus']) {
	showmessage('doing_status_off');
}

$doid = empty($_GET['doid']) ? 0 : intval($_GET['doid']);
$docid = empty($_GET['docid']) ? 0 : intval($_GET['docid']);

if($_GET['op'] == 'delete') {
	if(submitcheck('deletesubmit')) {
		if($docid) {
			$allowmanage = checkperm('managedoing');
			if($value = table_home_docomment::t()->fetch($docid)) {
				$home_doing = table_home_doing::t()->fetch($value['doid']);
				$value['duid'] = $home_doing['uid'];
				if($allowmanage || $value['uid'] == $_G['uid'] || $value['duid'] == $_G['uid']) {
					table_home_docomment::t()->update($docid, ['uid' => 0, 'username' => '', 'message' => '']);
					if($value['uid'] != $_G['uid'] && $value['duid'] != $_G['uid']) {
						batchupdatecredit('comment', $value['uid'], [], -1);
					}
					table_home_doing::t()->update_replynum_by_doid(-1, $updo['doid']);
				}
			}
		} else {
			require_once libfile('function/delete');
			deletedoings([$doid]);
		}

		if(defined('IN_RESTFUL')) {
			showmessage('doing_delete_success');
			exit();
		}else{
			dheader('location: '.dreferer());
			exit();
		}
	}

} elseif($_GET['op'] == 'getcomment') {

	$tree = new home\class_tree();

	$list = [];
	$highlight = 0;
	$count = 0;

	if(empty($_GET['close'])) {
		foreach(table_home_docomment::t()->fetch_all_by_doid($doid) as $value) {
			if(!empty($value['ip'])) {
				$value['ip'] = ip::to_display($value['ip']);
			}
			$tree->setNode($value['id'], $value['upid'], $value);
			$count++;
			if($value['uid'] == $space['uid']) $highlight = $value['id'];
		}
	}

	if($count) {
		$values = $tree->getChilds();
		foreach($values as $key => $vid) {
			$one = $tree->getValue($vid);
			$one['layer'] = $tree->getLayer($vid) * 2;
			$one['style'] = "padding-left:{$one['layer']}em;";
			if($one['layer'] > 0) {
				if($one['layer'] % 3 == 2) {
					$one['class'] = ' dtls';
				} else {
					$one['class'] = ' dtll';
				}
			}
			if($one['id'] == $highlight && $one['uid'] == $space['uid']) {
				$one['style'] .= 'color:#F60;';
			}
			$list[] = $one;
		}
	}
} elseif($_GET['op'] == 'recommend') {
	// 处理点赞请求
	$doid = intval($_GET['doid']);
	$uid = $_G['uid'];

	if(!$doid || !$uid) {
		showmessage('to_login', '', array(), array('login' => 1));
	}

	$doing = table_home_doing::t()->fetch($doid);
	if(!$doing) {
		showmessage('doing_not_exists');
	}

	$exists = table_home_doing_recomend_log::t()->fetch_by_doid_uid($doid, $uid);

	if($exists) {
		// 取消点赞
		table_home_doing::t()->update_recommendnum_by_doid(-1, $doid);
		table_home_doing_recomend_log::t()->delete_by_doid_uid($doid, $uid);
		$status = 0;
	} else {
		// 添加点赞
		table_home_doing::t()->update_recommendnum_by_doid(1, $doid);
		table_home_doing_recomend_log::t()->insert(array(
			'doid' => $doid,
			'uid' => $uid,
			'dateline' => TIMESTAMP
		));
		$status = 1;
	}

	// 重新获取点赞数
	$doing = table_home_doing::t()->fetch($doid);
	$recomends = $doing['recomends'];

	if(defined('IN_RESTFUL')) {
		showmessage('doing_recommend_success', '', array(), array('status' => $status,'count' => $recomends));
		exit();
	}else{
		// 直接返回JSON格式数据
		header('Content-Type: application/json');
		echo json_encode(array(
			'message' => 'doing_recommend_success',
			'status' => $status,
			'count' => $recomends
		));
		exit;
	}
	exit;
} elseif($_GET['op'] == 'spacenote') {
	space_merge($space, 'field_home');
}else{
	$type = empty($_GET['type']) ? '' : $_GET['type'];
	$id = empty($_GET['id']) ? 0 : intval($_GET['id']);
	$note_uid = 0;
	$note_message = '';
	$note_values = [];

	$arr = [];
	$feed_hash_data = '';

	switch($type) {
		case 'space':

			$feed_hash_data = "uid{$id}";

			$tospace = getuserbyuid($id);
			if(empty($tospace)) {
				showmessage('space_does_not_exist');
			}
			if(isblacklist($tospace['uid'])) {
				showmessage('is_blacklist');
			}

			$arr['itemid'] = $id;
			$arr['body_template'] = '<b><a href="home.php?mod=space&uid={uid}">{username}</a></b><br>{reside}<br>{spacenote}';
			$arr['body_data'] = [
				'uid' => $id,
				'username' => $tospace['username'],
				'reside' => $tospace['residecountry'].$tospace['resideprovince'].$tospace['residecity'],
				'spacenote' => $tospace['spacenote']
			];

			loaducenter();
			$isavatar = uc_check_avatar($id);
			$arr['body_data']['image'] = $isavatar ? avatar($id, 'middle', true) : $_G['setting']['avatarurl'].'/noavatar.svg';
			$arr['body_data']['image_link'] = "home.php?mod=space&uid=$id";

			$note_uid = $id;
			$note_message = 'share_space';

			break;
		case 'blog':

			$feed_hash_data = "blogid{$id}";

			$blog = array_merge(
				table_home_blog::t()->fetch($id),
				table_home_blogfield::t()->fetch($id)
			);
			if(!$blog) {
				showmessage('blog_does_not_exist');
			}
			if(in_array($blog['status'], [1, 2])) {
				showmessage('moderate_blog_not_share');
			}
			if($blog['friend']) {
				showmessage('logs_can_not_share');
			}
			if(isblacklist($blog['uid'])) {
				showmessage('is_blacklist');
			}
			$arr['itemid'] = $id;
			$arr['body_template'] = '<b><a href="{url}">{subject}</a></b><br><a href="home.php?mod=space&uid={uid}">{username}</a><br>{message}';
			$arr['body_data'] = [
				'url' => "home.php?mod=space&uid=".$blog['uid']."&do=blog&id=".$blog['blogid'],
				'subject' => $blog['subject'],
				'uid' => $blog['uid'],
				'username' => $blog['username'],
				'message' => getstr($blog['message'], 150, 0, 0, 0, -1)
			];
			if($blog['pic']) {
				$arr['body_data']['image'] = pic_cover_get($blog['pic'], $blog['picflag']);
				$arr['body_data']['image_link'] = "home.php?mod=space&uid={$blog['uid']}&do=blog&id={$blog['blogid']}";
			}
			$note_uid = $blog['uid'];
			$note_message = 'share_blog';
			$note_values = ['url' => "home.php?mod=space&uid={$blog['uid']}&do=blog&id={$blog['blogid']}", 'subject' => $blog['subject'], 'from_id' => $id, 'from_idtype' => 'blogid'];

			$hotarr = ['blogid', $blog['blogid'], $blog['hotuser']];

			break;
		case 'album':

			$feed_hash_data = "albumid{$id}";

			if(!$album = table_home_album::t()->fetch_album($id)) {
				showmessage('album_does_not_exist');
			}
			if($album['friend']) {
				showmessage('album_can_not_share');
			}
			if(isblacklist($album['uid'])) {
				showmessage('is_blacklist');
			}

			$arr['itemid'] = $id;
			$arr['body_template'] = '<b><a href="{url}">{albumname}</a></b><br><a href="home.php?mod=space&uid={uid}">{username}</a>';
			$arr['body_data'] = [
				'url' => "home.php?mod=space&uid={$album['uid']}&do=album&id={$album['albumid']}",
				'albumname' => $album['albumname'],
				'uid' => $album['uid'],
				'username' => $album['username']
			];
			$arr['body_data']['image'] = pic_cover_get($album['pic'], $album['picflag']);
			$arr['body_data']['image_link'] = "home.php?mod=space&uid={$album['uid']}&do=album&id={$album['albumid']}";
			$note_uid = $album['uid'];
			$note_message = 'share_album';
			$note_values = ['url' => "home.php?mod=space&uid={$album['uid']}&do=album&id={$album['albumid']}", 'albumname' => $album['albumname'], 'from_id' => $id, 'from_idtype' => 'albumid'];

			break;
		case 'pic':

			$feed_hash_data = "picid{$id}";
			$pic = table_home_pic::t()->fetch($id);
			if(!$pic) {
				showmessage('image_does_not_exist');
			}
			$picfield = table_home_picfield::t()->fetch($id);
			$album = table_home_album::t()->fetch_album($pic['albumid']);
			$pic = array_merge($pic, $picfield, $album);
			if(in_array($pic['status'], [1, 2])) {
				showmessage('moderate_pic_not_share');
			}
			if($pic['friend']) {
				showmessage('image_can_not_share');
			}
			if(isblacklist($pic['uid'])) {
				showmessage('is_blacklist');
			}
			if(empty($pic['albumid'])) $pic['albumid'] = 0;
			if(empty($pic['albumname'])) $pic['albumname'] = lang('spacecp', 'default_albumname');

			$arr['itemid'] = $id;
			$arr['body_template'] = lang('spacecp', 'album').': <b><a href="{url}">{albumname}</b><br><a href="home.php?mod=space&uid={uid}">{username}</a><br>{title}';
			$arr['body_data'] = [
				'url' => "home.php?mod=space&uid={$pic['uid']}&do=album&picid={$pic['picid']}",
				'albumname' => $pic['albumname'],
				'uid' => $pic['uid'],
				'username' => $pic['username'],
				'title' => getstr($pic['title'], 100, 0, 0, 0, -1)
			];
			$arr['body_data']['image'] = pic_get($pic['filepath'], 'album', $pic['thumb'], $pic['remote']);
			$arr['body_data']['image_link'] = "home.php?mod=space&uid={$pic['uid']}&do=album&picid={$pic['picid']}";
			$note_uid = $pic['uid'];
			$note_message = 'share_pic';
			$note_values = ['url' => "home.php?mod=space&uid={$pic['uid']}&do=album&picid={$pic['picid']}", 'albumname' => $pic['albumname'], 'from_id' => $id, 'from_idtype' => 'picid'];

			$hotarr = ['picid', $pic['picid'], $pic['hotuser']];

			break;

		case 'thread':

			$feed_hash_data = "tid{$id}";

			$thread = table_forum_thread::t()->fetch_thread($id);
			if(in_array($thread['displayorder'], [-2, -3])) {
				showmessage('moderate_thread_not_share');
			}
			require_once libfile('function/post');
			$post = table_forum_post::t()->fetch_threadpost_by_tid_invisible($id);
			$arr['body_template'] = '<b><a href="{url}">{subject}</a></b><br><a href="home.php?mod=space&uid={authorid}">{author}</a><br>{message}';
			$attachment = !preg_match('/\[hide=?\d*\](.*?)\[\/hide\]/is', $post['message'], $a) && preg_match('/\[attach\]\d+\[\/attach\]/i', $a[1]);
			$post['message'] = threadmessagecutstr($thread, $post['message']);
			$arr['body_data'] = [
				'url' => "forum.php?mod=viewthread&tid=$id",
				'subject' => $thread['subject'],
				'authorid' => $thread['authorid'],
				'author' => $thread['author'],
				'message' => getstr($post['message'], 150, 0, 0, 0, -1)
			];
			$arr['itemid'] = $id;
			$attachment = $attachment ? table_forum_attachment_n::t()->fetch_max_image('tid:'.$id, 'tid', $id) : false;
			if($attachment) {
				$arr['body_data']['image'] = pic_get($attachment['attachment'], 'forum', $attachment['thumb'], $attachment['remote'], 1);
				$arr['body_data']['image_link'] = "forum.php?mod=viewthread&tid=$id";
			}

			$note_uid = $thread['authorid'];
			$note_message = 'share_thread';
			$note_values = ['url' => "forum.php?mod=viewthread&tid=$id", 'subject' => $thread['subject'], 'from_id' => $id, 'from_idtype' => 'tid'];
			break;

		case 'article':

			$feed_hash_data = "articleid{$id}";

			$article = table_portal_article_title::t()->fetch($id);
			if(!$article) {
				showmessage('article_does_not_exist');
			}
			if(in_array($article['status'], [1, 2])) {
				showmessage('moderate_article_not_share');
			}

			require_once libfile('function/portal');
			$article_url = fetch_article_url($article);
			$arr['itemid'] = $id;
			$arr['body_template'] = '<b><a href="{url}">{title}</a></b><br><a href="home.php?mod=space&uid={uid}\">{username}</a><br>{summary}';
			$arr['body_data'] = [
				'url' => $article_url,
				'title' => $article['title'],
				'uid' => $article['uid'],
				'username' => $article['username'],
				'summary' => getstr($article['summary'], 150, 0, 0, 0, -1)
			];
			if($article['pic']) {
				$arr['body_data']['image'] = pic_get($article['pic'], 'portal', $article['thumb'], $article['remote'], 1, 1);
				$arr['body_data']['image_link'] = $article_url;
			}
			$note_uid = $article['uid'];
			$note_message = 'share_article';
			$note_values = ['url' => $article_url, 'subject' => $article['title'], 'from_id' => $id, 'from_idtype' => 'aid'];

			break;
		default:
			$arr['itemid'] = 0;
			$arr['body_template'] = '';
			break;
	}
	$commentcable = ['blog' => 'blogid', 'pic' => 'picid', 'thread' => 'thread', 'article' => 'article'];

	if(submitcheck('addsubmit')) {

		if(!checkperm('allowdoing')) {
			showmessage('no_privilege_doing');
		}

		cknewuser();

		$waittime = interval_check('post');
		if($waittime > 0) {
			showmessage('operating_too_fast', '', ['waittime' => $waittime]);
		}

		$message = getstr($_POST['message'], 200, 0, 0, 1);
		$message = preg_replace('/\<br.*?\>/i', ' ', $message);
		if(strlen($message) < 1) {
			showmessage('should_write_that');
		}

		$message = censor($message, NULL, TRUE, TRUE);
		if(is_array($message) && $message['message']) {
			showmessage('do_success', dreferer(), ['message' => $message['message']]);
		}

		if(censormod($message) || $_G['group']['allowdoingmod']) {
			$doing_status = 1;
		} else {
			$doing_status = 0;
		}

		$arr['body_data'] = serialize($arr['body_data']);
		$setarr = [
			'itemid' => $arr['itemid'],
			'type' => $type,
			'uid' => $_G['uid'],
			'username' => $_G['username'],
			'dateline' => $_G['timestamp'],
			'body_template' => $arr['body_template'],
			'body_data' => $arr['body_data'],
			'message' => $message,
			'ip' => $_G['clientip'],
			'port' => $_G['remoteport'],
			'recomends' => 0,
			'status' => $doing_status,
		];
		$newdoid = table_home_doing::t()->insert($setarr, 1);

		$setarr = ['recentnote' => $message, 'spacenote' => $message];
		$credit = $experience = 0;
		$extrasql = ['doings' => 1];

		updatecreditbyaction('doing', 0, $extrasql);

		table_common_member_field_home::t()->update($_G['uid'], $setarr);

		if (!empty($_FILES)) {
			$upload = new upload('doing');
			$f = $upload->upload();

			// 检查上传结果
			$photos = [];
			if (!empty($f['photos'])) {
				// 处理多图上传情况
				if (isset($f['photos'][0])) {
					// 多图数组
					$photos = $f['photos'];
				} else {
					// 单图情况
					$photos[] = $f['photos'];
				}
			}

			// 保存图片信息到数据库
			if (!empty($photos)) {
				foreach ($photos as $key => $value) {
					if (!$value['attachment']) continue;

					// 保存图片信息
					table_home_doing_attachment::t()->insert_attachment([
						'doid' => $newdoid,
						'uid' => $_G['uid'],
						'dateline' => TIMESTAMP,
						'filename' => $value['name'],
						'filesize' => $value['size'],
						'attachment' => $value['attachment'],
						'remote' => $value['remote'],
						'isimage' => $value['isimage'],
						'width' => $value['imageinfo'][0],
						'height' => $value['imageinfo'][1],
						'displayorder' => $key
					], true);
				}
			}
		}
		if($_GET['iscomment'] && $message && $commentcable[$type] && $id) {

			$message = censor($message);

			$currenttype = $commentcable[$type];
			$currentid = $id;

			if($currenttype == 'article') {
				$article = table_portal_article_title::t()->fetch($currentid);
				include_once libfile('function/portal');
				loadcache('portalcategory');
				$cat = $_G['cache']['portalcategory'][$article['catid']];
				$article['allowcomment'] = !empty($cat['allowcomment']) && !empty($article['allowcomment']) ? 1 : 0;
				if(!$article['allowcomment']) {
					showmessage('no_privilege_commentadd', '', [], ['return' => true]);
				}
				if($article['idtype'] == 'blogid') {
					$currentid = $article['id'];
					$currenttype = 'blogid';
				} elseif($article['idtype'] == 'tid') {
					$currentid = $article['id'];
					$currenttype = 'thread';
				}
			}

			if($currenttype == 'thread') {
				if($commentcable[$type] == 'article') {
					$_POST['portal_referer'] = $article_url ? $article_url : 'portal.php?mod=view&aid='.$id;
				}

				$_G['setting']['seccodestatus'] = 0;
				$_G['setting']['secqaa']['status'] = 0;

				$_POST['replysubmit'] = true;
				$_GET['tid'] = $currentid;
				$_GET['action'] = 'reply';
				$_GET['message'] = $message;
				include_once libfile('function/forum');
				require_once libfile('function/post');
				loadforum();

				$inspacecpshare = 1;
				include_once appfile('module/post', 'forum');

				if($_POST['portal_referer']) {
					$redirecturl = $_POST['portal_referer'];
				} else {
					if($modnewreplies) {
						$redirecturl = 'forum.php?mod=viewthread&tid='.$currentid;
					} else {
						$redirecturl = 'forum.php?mod=viewthread&tid='.$currentid.'&pid='.$modpost->pid.'&page='.$modpost->param('page').'&extra='.$extra.'#pid'.$modpost->pid;
					}
				}
				$showmessagecontent = ($modnewreplies && $commentcable[$type] != 'article') ? 'do_success_thread_share_mod' : '';

			} elseif($currenttype == 'article') {

				if(!checkperm('allowcommentarticle')) {
					showmessage('group_nopermission', NULL, ['grouptitle' => $_G['group']['grouptitle']], ['login' => 1]);
				}

				include_once libfile('function/spacecp');
				include_once libfile('function/portalcp');

				cknewuser();

				$waittime = interval_check('post');
				if($waittime > 0) {
					showmessage('operating_too_fast', '', ['waittime' => $waittime], ['return' => true]);
				}

				$aid = intval($currentid);
				$message = $message;

				$retmessage = addportalarticlecomment($aid, $message);
				if($retmessage != 'do_success') {
					showmessage($retmessage);
				}

			} elseif($currenttype == 'picid' || $currenttype == 'blogid') {

				if(!checkperm('allowcomment')) {
					showmessage('no_privilege_comment', '', [], ['return' => true]);
				}
				cknewuser();
				$waittime = interval_check('post');
				if($waittime > 0) {
					showmessage('operating_too_fast', '', ['waittime' => $waittime], ['return' => true]);
				}
				$message = getstr($message, 0, 0, 0, 2);
				if(strlen($message) < 2) {
					showmessage('content_is_too_short', '', [], ['return' => true]);
				}
				include_once libfile('class/bbcode');
				$bbcode = &bbcode::instance();

				require_once libfile('function/comment');
				$cidarr = add_comment($message, $currentid, $currenttype, 0);
				if($cidarr['cid']) {
					$magvalues['cid'] = $cidarr['cid'];
					$magvalues['id'] = $currentid;
				}
			}
			$magvalues['type'] = $commentcable[$type];
		}
		
		if(helper_access::check_module('feed') && ckprivacy('doing', 'feed') && $doing_status == '0') {
			$feedarr = [
				'icon' => 'doing',
				'uid' => $_G['uid'],
				'username' => $_G['username'],
				'dateline' => $_G['timestamp'],
				'title_template' => lang('feed', 'feed_doing_title'),
				'title_data' => serialize(['message' => $message]),
				'body_template' => $arr['body_template'],
				'body_data' => $arr['body_data'],
				'id' => $newdoid,
				'idtype' => 'doid'
			];
			table_home_feed::t()->insert($feedarr);
		}
		if($doing_status == '1') {
			updatemoderate('doid', $newdoid);
			manage_addnotify('verifydoing');
		}
		require_once libfile('function/stat');
		updatestat('doing');
		table_common_member_status::t()->update($_G['uid'], ['lastpost' => TIMESTAMP], 'UNBUFFERED');
		if($type) {
			switch($type) {
				case 'space':
					table_common_member_status::t()->increase($id, ['sharetimes' => 1]);
					break;
				case 'blog':
					table_home_blog::t()->increase($id, null, ['sharetimes' => 1]);
					break;
				case 'album':
					table_home_album::t()->update_num_by_albumid($id, 1, 'sharetimes');
					break;
				case 'pic':
					table_home_pic::t()->update_sharetimes($id);
					break;
				case 'thread':
					table_forum_thread::t()->increase($id, ['sharetimes' => 1]);
					require_once libfile('function/forum');
					update_threadpartake($id);
					break;
				case 'article':
					table_portal_article_count::t()->increase($id, ['sharetimes' => 1]);
					break;
			}
			updatestat('share');
			if($note_uid && $note_uid != $_G['uid']) {
				notification_add($note_uid, 'sharenotice', $note_message, $note_values);
			}
		}
		if(!empty($_GET['fromcard'])) {
			showmessage($message.lang('spacecp', 'card_update_doing'));
		} else {
			showmessage('do_success', dreferer(), ['doid' => $newdoid], $_GET['spacenote'] ? ['showmsg' => false] : ['header' => true]);
		}

	} elseif(submitcheck('commentsubmit')) {

		if(!checkperm('allowdoing')) {
			showmessage('no_privilege_doing_comment');
		}
		cknewuser();

		$waittime = interval_check('post');
		if($waittime > 0) {
			showmessage('operating_too_fast', '', ['waittime' => $waittime]);
		}

		$message = getstr($_POST['message'], 200, 0, 0, 1);
		$message = preg_replace('/\<br.*?\>/i', ' ', $message);
		if(strlen($message) < 1) {
			showmessage('should_write_that');
		}
		$message = censor($message);


		$updo = [];
		if($docid) {
			$updo = table_home_docomment::t()->fetch($docid);
		}
		if(empty($updo) && $doid) {
			$updo = table_home_doing::t()->fetch($doid);
		}
		if(empty($updo)) {
			showmessage('docomment_error');
		} else {
			if(isblacklist($updo['uid'])) {
				showmessage('is_blacklist');
			}
		}

		$updo['id'] = intval($updo['id']);
		$updo['grade'] = intval($updo['grade']);

		$setarr = [
			'doid' => $updo['doid'],
			'upid' => $updo['id'],
			'uid' => $_G['uid'],
			'username' => $_G['username'],
			'dateline' => $_G['timestamp'],
			'message' => $message,
			'ip' => $_G['clientip'],
			'port' => $_G['remoteport'],
			'grade' => $updo['grade'] + 1
		];

		if($updo['grade'] >= 3) {
			$setarr['upid'] = $updo['upid'];
		}

		$newid = table_home_docomment::t()->insert($setarr, true);

		table_home_doing::t()->update_replynum_by_doid(1, $updo['doid']);

		if($updo['uid'] != $_G['uid']) {
			notification_add($updo['uid'], 'comment', 'doing_reply', [
				'url' => "home.php?mod=space&uid={$updo['uid']}&do=doing&view=me&doid={$updo['doid']}&highlight=$newid",
				'from_id' => $updo['doid'],
				'from_idtype' => 'doid'
			]);
			updatecreditbyaction('comment', 0, [], 'doing'.$updo['doid']);
		}

		include_once libfile('function/stat');
		updatestat('docomment');
		table_common_member_status::t()->update($_G['uid'], ['lastpost' => TIMESTAMP], 'UNBUFFERED');
		showmessage('do_success', dreferer(), ['doid' => $updo['doid']]);
	}
}
require_once libfile('function/share');
$arr = mkshare($arr);
$arr['dateline'] = $_G['timestamp'];
$share_count = table_home_doing::t()->count_by_uid_itemid_type(null, $id ? $id : '', $type ? $type : '');
include template('home/spacecp_doing');