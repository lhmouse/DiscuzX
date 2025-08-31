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
	if(isset($settingnew['seccodedata'])) {
		$settingnew['seccodedata']['width'] = intval($settingnew['seccodedata']['width']);
		$settingnew['seccodedata']['height'] = intval($settingnew['seccodedata']['height']);
		if($settingnew['seccodedata']['type'] != 3) {
			$settingnew['seccodedata']['width'] = $settingnew['seccodedata']['width'] < 100 ? 100 : ($settingnew['seccodedata']['width'] > 200 ? 200 : $settingnew['seccodedata']['width']);
			$settingnew['seccodedata']['height'] = $settingnew['seccodedata']['height'] < 30 ? 30 : ($settingnew['seccodedata']['height'] > 80 ? 80 : $settingnew['seccodedata']['height']);
		} else {
			$settingnew['seccodedata']['width'] = 85;
			$settingnew['seccodedata']['height'] = 25;
		}
		$seccoderoot = '';
		if($settingnew['seccodedata']['type'] == 0 || $settingnew['seccodedata']['type'] == 2) {
			$seccoderoot = 'source/data/seccode/font/en/';
		} elseif($settingnew['seccodedata']['type'] == 1) {
			$seccoderoot = 'source/data/seccode/font/ch/';
		}
		if($seccoderoot) {
			$dirs = opendir($seccoderoot);
			$seccodettf = [];
			while($entry = readdir($dirs)) {
				if($entry != '.' && $entry != '..' && in_array(strtolower(fileext($entry)), ['ttf', 'ttc'])) {
					$seccodettf[] = $entry;
				}
			}
			if(!$seccodettf) {
				cpmsg('setting_seccode_ttf_lost', '', 'error', ['path' => $seccoderoot]);
			}
		}
	}

	if(!is_numeric($settingnew['seccodedata']['type']) && !preg_match('/^[\w\_]+:[\w\_]+$/', $settingnew['seccodedata']['type'])) {
		$settingnew['seccodedata']['type'] = 0;
	}
	$settingnew['seccodestatus'] = $settingnew['seccodedata']['rule']['register']['allow'] || $settingnew['seccodedata']['rule']['login']['allow'] || $settingnew['seccodedata']['rule']['post']['allow'] || $settingnew['seccodedata']['rule']['password']['allow'] || $settingnew['seccodedata']['rule']['card']['allow'] ? 1 : 0;
	if(is_array($_GET['delete'])) {
		table_common_secquestion::t()->delete($_GET['delete']);
	}

	if(is_array($_GET['question'])) {
		foreach($_GET['question'] as $key => $q) {
			$q = trim($q);
			$a = cutstr(dhtmlspecialchars(trim($_GET['answer'][$key])), 50);
			if($q !== '' && $a !== '') {
				table_common_secquestion::t()->update($key, ['question' => $q, 'answer' => $a]);
			}
		}
	}
	table_common_secquestion::t()->delete_by_type(1);
	if(is_array($_GET['secqaaext'])) {
		foreach($_GET['secqaaext'] as $ext) {
			if(preg_match('/^[\w\_:]+$/', $ext)) {
				DB::insert('common_secquestion', ['type' => '1', 'question' => $ext]);
			}
		}
	}

	if(is_array($_GET['newquestion']) && is_array($_GET['newanswer'])) {
		foreach($_GET['newquestion'] as $key => $q) {
			$q = trim($q);
			$a = cutstr(dhtmlspecialchars(trim($_GET['newanswer'][$key])), 50);
			if($q !== '' && $a !== '') {
				DB::insert('common_secquestion', ['question' => $q, 'answer' => $a]);
			}
		}
	}

	$setting['secqaa'] = dunserialize($setting['secqaa']);
	$setting['secqaa']['status'] = bindec(intval($settingnew['secqaa']['status'][5]).intval($settingnew['secqaa']['status'][4]).intval($settingnew['secqaa']['status'][3]).intval($settingnew['secqaa']['status'][2]).intval($settingnew['secqaa']['status'][1]));
	$setting['secqaa']['minposts'] = intval($settingnew['secqaa']['minposts']);
	$setting['secqaa']['allowqa'] = intval($settingnew['secqaa']['allowqa']);
	$_G['setting']['secqaa'] = $setting['secqaa'];
	$settingnew['secqaa'] = serialize($setting['secqaa']);
	updatecache('secqaa');
} else {
	shownav('safe', 'setting_seccheck');

	$_GET['anchor'] = in_array($_GET['anchor'], ['seccode', 'secqaa']) ? $_GET['anchor'] : 'seccode';
	showsubmenuanchors('setting_seccheck', [
		['setting_sec_seccode', 'seccode', $_GET['anchor'] == 'seccode'],
		['setting_sec_secqaa', 'secqaa', $_GET['anchor'] == 'secqaa'],
	]);

	showformheader('setting&edit=yes', 'enctype');
	showhiddenfields(['operation' => $operation]);

	$seccodecheck = 1;
	$sechash = 'S'.$_G['sid'];
	$seccheckhtml = "<span id=\"seccode_c$sechash\"></span><script type=\"text/javascript\">updateseccode('c$sechash', '<br /><sec> <sec> <sec>', 'admin');</script>";

	$checksc = [];
	$setting['seccodedata'] = dunserialize($setting['seccodedata']);

	$seccodetypearray = [
		[0, cplang('setting_sec_seccode_type_image'), ['seccodeimageext' => '', 'seccodeimagewh' => '']],
		[1, cplang('setting_sec_seccode_type_chnfont'), ['seccodeimageext' => '', 'seccodeimagewh' => '']],
		[99, cplang('setting_sec_seccode_type_bitmap'), ['seccodeimageext' => 'none', 'seccodeimagewh' => 'none']],
	];

	$seccodetypearray = array_merge($seccodetypearray, getseccodes($seccodesettings));

	/*search={"setting_seccheck":"action=setting&operation=sec","setting_sec_seccode":"action=setting&operation=sec&anchor=seccode"}*/
	showtips('setting_sec_code_tips', 'seccode_tips', $_GET['anchor'] == 'seccode');

	showtableheader('', '', 'id="seccode"'.($_GET['anchor'] != 'seccode' ? ' style="display: none"' : ''));
	showtitle('setting_sec_seccode_rule_setting');
	showsetting('setting_sec_seccode_rule_register', ['settingnew[seccodedata][rule][register][allow]', [
		[2, cplang('setting_sec_seccode_rule_register_auto'), ['secrule_register' => '']],
		[1, cplang('setting_sec_seccode_rule_register_on'), ['secrule_register' => 'none']],
		[0, cplang('setting_sec_seccode_rule_register_off'), ['secrule_register' => 'none']],
	]], $setting['seccodedata']['rule']['register']['allow'], 'mradio');
	showtagheader('tbody', 'secrule_register', $setting['seccodedata']['rule']['register']['allow'] == 2, 'sub');
	showsetting('setting_sec_seccode_rule_register_numlimit', 'settingnew[seccodedata][rule][register][numlimit]', $setting['seccodedata']['rule']['register']['numlimit'], 'text');
	showsetting('setting_sec_seccode_rule_register_timelimit', ['settingnew[seccodedata][rule][register][timelimit]', [
		[60, '1 '.cplang('setting_sec_seccode_rule_min')],
		[180, '3'.cplang('setting_sec_seccode_rule_min')],
		[300, '5'.cplang('setting_sec_seccode_rule_min')],
		[900, '15'.cplang('setting_sec_seccode_rule_min')],
		[1800, '30'.cplang('setting_sec_seccode_rule_min')],
		[3600, '1'.cplang('setting_sec_seccode_rule_hour')],
	]], $setting['seccodedata']['rule']['register']['timelimit'], 'select', 'noborder');
	showtagfooter('tbody');

	showsetting('setting_sec_seccode_rule_login', ['settingnew[seccodedata][rule][login][allow]', [
		[2, cplang('setting_sec_seccode_rule_login_auto'), ['secrule_login' => '']],
		[1, cplang('setting_sec_seccode_rule_login_on'), ['secrule_login' => 'none']],
		[0, cplang('setting_sec_seccode_rule_login_off'), ['secrule_login' => 'none']],
	]], $setting['seccodedata']['rule']['login']['allow'], 'mradio');
	showtagheader('tbody', 'secrule_login', $setting['seccodedata']['rule']['login']['allow'] == 2, 'sub');
	showsetting('setting_sec_seccode_rule_login_nolocal', 'settingnew[seccodedata][rule][login][nolocal]', $setting['seccodedata']['rule']['login']['nolocal'], 'radio');
	showsetting('setting_sec_seccode_rule_login_pwsimple', 'settingnew[seccodedata][rule][login][pwsimple]', $setting['seccodedata']['rule']['login']['pwsimple'], 'radio');
	showsetting('setting_sec_seccode_rule_login_pwerror', 'settingnew[seccodedata][rule][login][pwerror]', $setting['seccodedata']['rule']['login']['pwerror'], 'radio');
	showsetting('setting_sec_seccode_rule_login_outofday', 'settingnew[seccodedata][rule][login][outofday]', $setting['seccodedata']['rule']['login']['outofday'], 'text');
	showsetting('setting_sec_seccode_rule_login_numiptry', 'settingnew[seccodedata][rule][login][numiptry]', $setting['seccodedata']['rule']['login']['numiptry'], 'text');
	showsetting('setting_sec_seccode_rule_login_timeiptry', ['settingnew[seccodedata][rule][login][timeiptry]', [
		[60, '1 '.cplang('setting_sec_seccode_rule_min')],
		[180, '3'.cplang('setting_sec_seccode_rule_min')],
		[300, '5'.cplang('setting_sec_seccode_rule_min')],
		[900, '15'.cplang('setting_sec_seccode_rule_min')],
		[1800, '30'.cplang('setting_sec_seccode_rule_min')],
		[3600, '1'.cplang('setting_sec_seccode_rule_hour')],
	]], $setting['seccodedata']['rule']['login']['timeiptry'], 'select', 'noborder');
	showtagfooter('tbody');

	showsetting('setting_sec_seccode_rule_post', ['settingnew[seccodedata][rule][post][allow]', [
		[2, cplang('setting_sec_seccode_rule_post_auto'), ['secrule_post' => '', 'secrule_post_common' => '']],
		[1, cplang('setting_sec_seccode_rule_post_on'), ['secrule_post' => 'none', 'secrule_post_common' => '']],
		[0, cplang('setting_sec_seccode_rule_post_off'), ['secrule_post' => 'none', 'secrule_post_common' => 'none']],
	]], $setting['seccodedata']['rule']['post']['allow'], 'mradio');
	showtagheader('tbody', 'secrule_post_common', $setting['seccodedata']['rule']['post']['allow'], 'sub');
	showtagheader('tbody', 'secrule_post', $setting['seccodedata']['rule']['post']['allow'] == 2, 'sub');
	showsetting('setting_sec_seccode_rule_post_numlimit', 'settingnew[seccodedata][rule][post][numlimit]', $setting['seccodedata']['rule']['post']['numlimit'], 'text');
	showsetting('setting_sec_seccode_rule_post_timelimit', ['settingnew[seccodedata][rule][post][timelimit]', [
		[60, '1 '.cplang('setting_sec_seccode_rule_min')],
		[180, '3'.cplang('setting_sec_seccode_rule_min')],
		[300, '5'.cplang('setting_sec_seccode_rule_min')],
		[900, '15'.cplang('setting_sec_seccode_rule_min')],
		[1800, '30'.cplang('setting_sec_seccode_rule_min')],
		[3600, '1'.cplang('setting_sec_seccode_rule_hour')],
	]], $setting['seccodedata']['rule']['post']['timelimit'], 'select', 'noborder');
	showsetting('setting_sec_seccode_rule_post_nplimit', 'settingnew[seccodedata][rule][post][nplimit]', $setting['seccodedata']['rule']['post']['nplimit'], 'text');
	showsetting('setting_sec_seccode_rule_post_vplimit', 'settingnew[seccodedata][rule][post][vplimit]', $setting['seccodedata']['rule']['post']['vplimit'], 'text');
	showtagfooter('tbody');

	// showsetting('setting_sec_seccode_rule_password', 'settingnew[seccodedata][rule][password][allow]', $setting['seccodedata']['rule']['password']['allow'], 'radio');
	showsetting('setting_sec_seccode_rule_card', 'settingnew[seccodedata][rule][card][allow]', $setting['seccodedata']['rule']['card']['allow'], 'radio');
	showsetting('setting_sec_seccode_minposts', 'settingnew[seccodedata][minposts]', $setting['seccodedata']['minposts'], 'text');

	showtitle('setting_sec_seccode_type_setting');
	showsetting('setting_sec_seccode_type', ['settingnew[seccodedata][type]', $seccodetypearray], $setting['seccodedata']['type'], 'mradio', '', 0, cplang('setting_sec_seccode_type_comment').$seccheckhtml);
	showtagheader('tbody', 'seccodeimagewh', is_numeric($setting['seccodedata']['type']) && $setting['seccodedata']['type'] != 3 && $setting['seccodedata']['type'] != 99, 'sub');
	showsetting('setting_sec_seccode_width', 'settingnew[seccodedata][width]', $setting['seccodedata']['width'], 'text');
	showsetting('setting_sec_seccode_height', 'settingnew[seccodedata][height]', $setting['seccodedata']['height'], 'text');
	showtagfooter('tbody');
	showtagheader('tbody', 'seccodeimageext', is_numeric($setting['seccodedata']['type']) && $setting['seccodedata']['type'] != 2 && $setting['seccodedata']['type'] != 3 && $setting['seccodedata']['type'] != 99, 'sub');
	showsetting('setting_sec_seccode_shuffer_order', 'settingnew[seccodedata][shuffer_order]', $setting['seccodedata']['shuffer_order'], 'radio');
	showsetting('setting_sec_seccode_scatter', 'settingnew[seccodedata][scatter]', $setting['seccodedata']['scatter'], 'text');
	showsetting('setting_sec_seccode_background', 'settingnew[seccodedata][background]', $setting['seccodedata']['background'], 'radio');
	showsetting('setting_sec_seccode_adulterate', 'settingnew[seccodedata][adulterate]', $setting['seccodedata']['adulterate'], 'radio');
	showsetting('setting_sec_seccode_ttf', 'settingnew[seccodedata][ttf]', $setting['seccodedata']['ttf'], 'radio', !function_exists('imagettftext'));
	showsetting('setting_sec_seccode_angle', 'settingnew[seccodedata][angle]', $setting['seccodedata']['angle'], 'radio');
	showsetting('setting_sec_seccode_warping', 'settingnew[seccodedata][warping]', $setting['seccodedata']['warping'], 'radio');
	showsetting('setting_sec_seccode_color', 'settingnew[seccodedata][color]', $setting['seccodedata']['color'], 'radio');
	showsetting('setting_sec_seccode_size', 'settingnew[seccodedata][size]', $setting['seccodedata']['size'], 'radio');
	showsetting('setting_sec_seccode_shadow', 'settingnew[seccodedata][shadow]', $setting['seccodedata']['shadow'], 'radio');
	showsetting('setting_sec_seccode_animator', 'settingnew[seccodedata][animator]', $setting['seccodedata']['animator'], 'radio', !function_exists('imagegif'));
	showtagfooter('tbody');

	showsubmit('settingsubmit');
	showtablefooter();
	/*search*/

	$setting['secqaa'] = dunserialize($setting['secqaa']);
	$start_limit = ($page - 1) * 10;
	$secqaanums = table_common_secquestion::t()->count();
	$multipage = multi($secqaanums, 10, $page, ADMINSCRIPT.'?action=setting&operation=seccheck&anchor=secqaa');


	echo <<<EOT
	<script type="text/JavaScript">
		var rowtypedata = [
			[[1,''], [1,'<input name="newquestion[]" type="text" class="txt">','td26'], [1, '<input name="newanswer[]" type="text" class="txt">']],
		];
		</script>
	EOT;
	/*search={"setting_seccheck":"action=setting&operation=sec","setting_sec_secqaa":"action=setting&operation=sec&anchor=secqaa"}*/
	showtips('setting_sec_qaa_tips', 'secqaa_tips', $_GET['anchor'] == 'secqaa');
	showtagheader('div', 'secqaa', $_GET['anchor'] == 'secqaa');
	showtableheader('setting_sec_secqaa', 'nobottom');
	showsetting('setting_sec_secqaa_status', ['settingnew[secqaa][status]', [
		cplang('setting_sec_seccode_status_register'),
		cplang('setting_sec_seccode_status_post'),
		false,
		cplang('setting_sec_seccode_status_login'),
		cplang('setting_sec_seccode_status_card')
	]], $setting['secqaa']['status'], 'binmcheckbox');
	showsetting('setting_sec_secqaa_minposts', 'settingnew[secqaa][minposts]', $setting['secqaa']['minposts'], 'text');
	showtablefooter();

	showtableheader('setting_sec_secqaa_qaa', '');
	showsubtitle(['', 'name', '']);

	$qaaext = [];
	$items = table_common_secquestion::t()->fetch_all_secquestion($start_limit);
	foreach($items as $item) {
		if($item['type']) {
			$qaaext[] = $item['question'];
		}
	}
	echo getsecqaas($qaaext);
	$allowqa = !empty($setting['secqaa']['allowqa']) ? 'checked' : '';
	echo showtablerow('class="hover"', ['', 'class="td26"'], [
		'',
		'<label><input class="checkbox" type="checkbox" value="1" name="settingnew[secqaa][allowqa]" '.$allowqa.' /><b>'.cplang('setting_sec_secqaa_question').'</b></label>',
		'<b>'.cplang('setting_sec_secqaa_answer').'</b>',
	], true);
	foreach($items as $item) {
		if(!$item['type']) {
			showtablerow('class="hover"', ['"', 'class="td26"'], [
				'<input class="checkbox" type="checkbox" name="delete[]" value="'.$item['id'].'">',
				'<input type="text" class="txt" name="question['.$item['id'].']" value="'.dhtmlspecialchars($item['question']).'" class="txtnobd" onblur="this.className=\'txtnobd\'" onfocus="this.className=\'txt\'">',
				'<input type="text" class="txt" name="answer['.$item['id'].']" value="'.$item['answer'].'" class="txtnobd" onblur="this.className=\'txtnobd\'" onfocus="this.className=\'txt\'">'
			]);
		}
	}
	echo '<tr><td></td><td class="td26"><div><a href="###" onclick="addrow(this, 0)" class="addtr">'.$lang['setting_sec_secqaa_add'].'</a></div></td><td></td></tr>';
	/*search*/

	showsubmit('settingsubmit', 'submit', 'del', '', $multipage);
	showtablefooter();
	showtagfooter('div');
	showformfooter();
}