<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

showtips('article_tag_tip');

if(submitcheck('articletagsubmit')) {
	table_common_setting::t()->update_setting('article_tags', $_POST['tag']);
	updatecache('setting');
	cpmsg('update_articletag_succeed', 'action=article&operation=tag', 'succeed');
}

require_once libfile('function/portalcp');
$tag_names = article_tagnames();
showformheader('article&operation=tag');
showtableheader('article_tag_setting');
for($i = 1; $i <= 8; $i++) {
	showtablerow('', ['width=80', ''],
		[lang('portalcp', 'article_tag').$i, "<input type=\"text\" class=\"txt\" name=\"tag[$i]\" value=\"$tag_names[$i]\" />"]);
}
showsubmit('articletagsubmit', 'submit');
showtablefooter();
showformfooter();
	