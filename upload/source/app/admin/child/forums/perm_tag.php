<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$s = '';
$exists = false;
$tags = table_common_tag::t()->fetch_all_by_status(3);
if($tags) {
	foreach($tags as $tag) {
		$show = false;
		$colums = [
			'<input class="checkbox" title="'.cplang('select_all').'" type="checkbox" name="chkalltag'.$tag['tagid'].'" onclick="checkAll(\'value\', this.form, \'tag'.$tag['tagid'].'\', \'chkalltag'.$tag['tagid'].'\')" id="chkalltag_'.$tag['tagid'].'" />',
			'<label for="chkalltag_'.$tag['tagid'].'"> '.$tag['tagname'].'</label>', 't'.$tag['tagid']];
		foreach($perms as $perm) {
			$checked = str_contains($forum[$perm], "\tt$tag[tagid]\t") ? 'checked="checked"' : NULL;
			$checked && $exists = true && $show = true;
			$colums[] = '<input class="checkbox" type="checkbox" name="'.$perm.'[]" value="t'.$tag['tagid'].'" chkvalue="tag'.$tag['tagid'].'" '.$checked.'>';
		}
		if($show) {
			$s .= showtablerow('', ['', '', 'class="lightfont"'], $colums, true);
		}
	}

	$tg = '<a href="javascript:;" id="a_gtag" onclick="toggle_group(\'gtag\')">['.($exists ? '-' : '+').']</a>';
	showtablerow('', ['', '', 'class="lightfont"', 'colspan="6"'], [$tg, '<b>'.$lang['forums_edit_perm_usertag'].'</b>', 'tag',
		'<input id="tag_item" class="txt" onkeydown="perm_enter(event, this)" /><a href="javascript:;" style="vertical-align: middle" onclick="perm_search(\'tag\', \'tag_item\')">'.cplang('search').'</a>']);
	showtagheader('tbody', 'gtag', $exists);
	echo $s;
	showtagfooter('tbody');
}