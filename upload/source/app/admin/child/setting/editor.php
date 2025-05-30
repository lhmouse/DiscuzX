<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(submitcheck('settingsubmit')) {
	if(isset($settingnew['smthumb'])) {
		$settingnew['smthumb'] = intval($settingnew['smthumb']) >= 20 && intval($settingnew['smthumb']) <= 40 ? intval($settingnew['smthumb']) : 20;
	}

	if(isset($settingnew['defaulteditormode']) && isset($settingnew['allowswitcheditor'])) {
		$settingnew['editoroptions'] = bindec($settingnew['defaulteditormode'].$settingnew['allowswitcheditor'].$settingnew['simplemode']);
	}

	table_forum_forum::t()->update_allowhtml($settingnew['editorfids'], 1);

	if(isset($settingnew['smcols'])) {
		$settingnew['smcols'] = $settingnew['smcols'] >= 8 && $settingnew['smcols'] <= 12 ? $settingnew['smcols'] : 8;
	}
	$settingnew['editormodetype'] = !empty(array_filter((array)$settingnew['editorfids']));
	$settingnew['json_independence'] = false;
} else {
	shownav('style', 'setting_editor');

	showsubmenu('setting_editor', [
		['setting_editor_global', 'setting&operation=editor', 1],
		['setting_editor_code', 'misc&operation=bbcode', 0],
		['setting_editor_media', 'misc&operation=mediacode', 0],
		['setting_editor_block', 'editorblock&operation=list', 0],
	]);

	showformheader('setting&edit=yes', 'enctype');
	showhiddenfields(['operation' => $operation]);

	$_G['setting']['editoroptions'] = str_pad(decbin($setting['editoroptions']), 3, 0, STR_PAD_LEFT);
	$setting['defaulteditormode'] = $_G['setting']['editoroptions'][0];
	$setting['allowswitcheditor'] = $_G['setting']['editoroptions'][1];
	$setting['simplemode'] = $_G['setting']['editoroptions'][2];

	/*search={"setting_editor":"action=setting&operation=editor","setting_editor_global":"action=setting&operation=editor"}*/
	showtableheader();

	showtitle('setting_editor_mode_type_default');
	showsetting('setting_editor_mode_default', ['settingnew[defaulteditormode]', [
		[0, $lang['setting_editor_mode_discuzcode']],
		[1, $lang['setting_editor_mode_wysiwyg']]]], $setting['defaulteditormode'], 'mradio');
	showsetting('setting_editor_swtich_enable', 'settingnew[allowswitcheditor]', $setting['allowswitcheditor'], 'radio');
	showsetting('setting_editor_simplemode', ['settingnew[simplemode]', [
		[1, $lang['setting_editor_simplemode_1']],
		[0, $lang['setting_editor_simplemode_0']]], 1], $setting['simplemode'], 'mradio');
	showsetting('setting_editor_smthumb', 'settingnew[smthumb]', $setting['smthumb'], 'text');
	showsetting('setting_editor_smcols', 'settingnew[smcols]', $setting['smcols'], 'text');
	showsetting('setting_editor_smrows', 'settingnew[smrows]', $setting['smrows'], 'text');

	showtitle('setting_editor_mode_type_json');
	$groupselect = [];
	$query = table_common_usergroup::t()->fetch_all_not([6, 7], true);
	foreach($query as $group) {
		$group['type'] = $group['type'] == 'special' && $group['radminid'] ? 'specialadmin' : $group['type'];
		$groupselect[$group['type']] .= "<option value=\"{$group['groupid']}\" ".(is_array(unserialize($setting['editorgroupid'])) && in_array($group['groupid'], unserialize($setting['editorgroupid'])) ? 'selected' : '').">{$group['grouptitle']}</option>\n";
	}
	$groupselect = '<optgroup label="'.$lang['usergroups_member'].'">'.$groupselect['member'].'</optgroup>'.
		($groupselect['special'] ? '<optgroup label="'.$lang['usergroups_special'].'">'.$groupselect['special'].'</optgroup>' : '').
		($groupselect['specialadmin'] ? '<optgroup label="'.$lang['usergroups_specialadmin'].'">'.$groupselect['specialadmin'].'</optgroup>' : '').
		'<optgroup label="'.$lang['usergroups_system'].'">'.$groupselect['system'].'</optgroup>';
	showsetting('setting_editor_group', '', '', '<select name="settingnew[editorgroupid][]" multiple="multiple" size="10"><option value="">'.cplang('none').'</option>'.$groupselect.'</select>');

	include_once libfile('function/forumlist');
	$forumselect = '<select name="settingnew[editorfids][]" multiple="multiple" size="10"><option value="">'.cplang('none').'</option>'.forumselect(FALSE, 0, unserialize($setting['editorfids']), TRUE).'</select>';
	showsetting('setting_editor_forum', '', '', $forumselect);
	showsetting('setting_editor_anchorparse', 'settingnew[anchorparse]', $setting['anchorparse'], 'textarea');
	/*search*/

	showsubmit('settingsubmit', 'submit', '', $extbutton.(!empty($from) ? '<input type="hidden" name="from" value="'.$from.'">' : ''));
	showtablefooter();
	showformfooter();
}