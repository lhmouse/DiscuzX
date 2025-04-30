<?php

namespace sample;

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class threadtype_table {

	var $name = '自定义表格';

	function show($option, $params) {
		$string = '<table>';
		for($i = 1; $i <= 9; $i++) {
			$string .= '<tr><td>'.$i.'年级</td><td><input name="typeoption['.$option['identifier'].']['.$i.']" '.
				'style="width:50px" value="'.$option['value'][$i].'" /></td></tr>';
		}
		$string  .= '</table>';
		return $string;
	}

	function view($viewtype, $option, $params, $value) {
		$string = '';
		if($viewtype == 'viewthread') {
			for($i = 1; $i <= 9; $i++) {
				$string .= $i.'年级：'.$value[$i].'<br/>';
			}
		} elseif($viewtype == 'template') {
			for($i = 1; $i <= 9; $i++) {
				$string .= $i.'年级：'.$value[$i].' - ';
			}
		}
		return $string;
	}

	function serialize($params, &$value) {
		$value = json_encode($value);
	}

	function unserialize($params, &$value) {
		$value = json_decode($value, 1);
	}

}