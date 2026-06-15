<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

namespace admin;

class widget_view {

	public static function output($type) {
		global $_G;
		$widgetList = [];
		$hideList = [];
		$setting = \admin\widget_setting::get();
		if($setting && !empty($setting['data']['data'][$type])) {
			$widgetList = $setting['data']['data'][$type];
			$hideList = $setting['data']['hide'][$type] ?? [];
		} else {
			$default = \admin\widget_data::get($type);
			$widgetList = $default[$type];
		}

		foreach($widgetList as $widget) {
			$parts = explode(',', $widget);
			$isplugin = count($parts) > 1;
			if($isplugin && !in_array($parts[0], $_G['setting']['plugins']['available'])) {
				continue;
			}
			$func = $isplugin ? [$parts[0].'\\admin_widget', $parts[1]] : $parts[0];
			$name = $isplugin ? lang('plugin/'.$parts[0], $parts[1]) : cplang('widget_'.$widget);
			$isHidden = in_array($widget, $hideList);

			if(!$isHidden) {
				ob_start();
				call_user_func($func);
				$output = ob_get_clean();
				if(trim($output) === '') {
					continue;
				}
			} else {
				include template('admin/index_empty');
			}

			$hiddenAttr = $isHidden ? ' data-hidden="1"' : '';
			$hiddenClass = $isHidden ? ' widget-hidden widget-invisible' : '';
			$toggleTitle = $isHidden ? cplang('widget_show_widget') : cplang('widget_hide_widget');
			$toggleIcon = $isHidden ? '&#x25C9;' : '&#x25CE;';

			echo '<div class="widget-wrapper'.$hiddenClass.'" data-widget="'.dhtmlspecialchars($widget).'" data-type="'.$type.'"'.$hiddenAttr.'>';
			echo '<div class="widget-toolbar" data-title-hide="'.dhtmlspecialchars(cplang('widget_hide_widget')).'" data-title-show="'.dhtmlspecialchars(cplang('widget_show_widget')).'">';
			echo '<span class="widget-drag-handle">'.dhtmlspecialchars($name).'</span>';
			echo '<button type="button" class="widget-toggle" title="'.dhtmlspecialchars($toggleTitle).'">'.$toggleIcon.'</button>';
			echo '</div>';
			echo $output;
			echo '</div>';
		}
	}

}

