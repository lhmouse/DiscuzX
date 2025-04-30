<?php

class app_portal_switch {

	const Icon = STATICURL.'image/feed/portal_b.png';
	const Name = 'setting_functions_curscript_portal';
	const Desc = 'setting_functions_curscript_portal_intro';

	public static function getStatus() {
		return getglobal('setting/portalstatus');
	}

	public static function getOptions() {
		return '<a href="'.ADMINSCRIPT.'?action=misc&operation=setnav&do='.(self::getStatus() ? 'close' : 'open').'&type=portal" onclick="showWindow(\'setnav\', this.href, \'get\', 0);return false;">'.(self::getStatus() ? cplang('setting_functions_curscript_close') : cplang('setting_functions_curscript_open')).'</a>';
	}

}