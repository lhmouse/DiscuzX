<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

class lang {

	public static function showMenu($operation) {
		showsubmenu('menu_lang', [
			['lang_list', 'lang&operation=list', $operation == 'list'],
		]);
	}

	public static function list() {
		global $_G;
		$list = i18n('get');

		showformheader('lang&operation=defaultSubmit');
		showtableheader('', '');
		showsubtitle(['default', 'available', 'lang_key', 'attach_path', '', '']);

		showtablerow('header', ['width="30"', 'width="30"', 'width="200"', 'width="200"', ''], [
			'<input id="i18n_default" name="i18n_default" type="radio" class="radio" value="0"'.(empty($_G['setting']['i18n_default']) ? ' checked' : '').'>',
			'',
			'<label for="i18n_default">'.cplang('default').'</label>', '', '', ''
		]);

		foreach($list as $key => $value) {
			$path = str_replace(DISCUZ_ROOT, '', $value);
			showtablerow('header', ['width="30"', 'width="30"', 'width="200"', 'width="200"', ''], [
				'<input id="i18n_'.$key.'" name="i18n_default" type="radio" class="radio" value="'.$key.'"'.(!empty($_G['setting']['i18n_default']) && $_G['setting']['i18n_default'] == $key ? ' checked' : '').'>',
				'<input name="i18ns[]" type="checkbox" class="checkbox" value="'.$key.'"'.(!isset($_G['setting']['i18ns']) || !empty($_G['setting']['i18ns']) && in_array($key, $_G['setting']['i18ns']) ? ' checked' : '').'>',
				'<label for="i18n_'.$key.'">'.$key.'</label>', $path,
				isset($_G['setting']['i18n_custom'][$key]) ? (!empty($_G['setting']['i18n_custom'][$key]) ? $_G['setting']['i18n_custom'][$key] : cplang('default')) : '',
			]);
		}

		showsubmit('submit');
		showtablefooter();
		showformfooter();
	}

	public static function defaultSubmit() {
		if(!submitcheck('submit')) {
			cpmsg('undefined_action', '', 'error');
		}

		if(!in_array($_GET['i18n_default'], (array)$_GET['i18ns'])) {
			$_GET['i18ns'][] = $_GET['i18n_default'];
		}

		table_common_setting::t()->update_batch([
			'i18n_default' => $_GET['i18n_default'],
			'i18ns' => $_GET['i18ns'],
		]);

		updatecache('setting');
		cpmsg('setting_update_succeed', 'action=lang', 'succeed');
	}

}
