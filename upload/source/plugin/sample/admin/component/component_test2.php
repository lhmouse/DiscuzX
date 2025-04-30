<?php

namespace sample\admin;

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class component_test2 {

	var $name = '自定义表格';

	function show(&$var, &$extra) {
		$var['type'] = '<table>';
		for($i = 1; $i <= 9; $i++) {
			$var['type'] .= '<tr><td>'.$i.'年级</td><td><input name="'.$var['variable'].'['.$i.']" '.
				'class="txt" style="width:50px" value="'.$var['value'][$i].'" /></td></tr>';
		}
		$var['type'] .= '</table>';
	}

	function serialize(&$value) {
		$value = json_encode($value);
	}

	function unserialize(&$value) {
		$value = json_decode($value, 1);
	}

}