<?php

namespace admin;

class widget_view {

	public static function output($type) {
		$widgets = [];
		$setting = \admin\widget_setting::get();
		if($setting) {
			$widgets = $setting['setting'];
		} else {
			$widgets = \admin\widget_data::get($type);
		}

		foreach($widgets[$type] as $widget) {
			$widget = explode(',', $widget);
			$func = count($widget) > 1 ? [$widget[0].'\\admin_widget', $widget[1]] : $widget[0];
			call_user_func($func);
		}
	}

}

