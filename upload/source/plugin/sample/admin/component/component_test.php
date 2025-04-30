<?php

namespace sample\admin;

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class component_test {

	var $name = '自定义颜色';

	function show(&$var, &$extra) {
		$colors = ['red', 'green', 'blue', 'yellow', 'purple', 'black'];
		$var['type'] = '';
		foreach ($colors as $color) {
			$var['type'] .= '<label class="ssbox"><input name="'.$var['variable'].'" type="radio" value="'.$color.'"'.($var['value'] == $color ? ' checked="checked"' : '').'>'.
			'<span style="background-color: '.$color.'"></span></label>';
		}

		$extra['sample_test'] = '<style>.ssbox { display:block; float:left; width: 50px } .ssbox span { width:20px;height:20px;display:inline-block;margin-bottom: 2px;vertical-align: middle; }</style>';
	}

}