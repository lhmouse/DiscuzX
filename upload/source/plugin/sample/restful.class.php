<?php

class restful_sample {

	public static function test_before(&$data, $param) {
		$_GET['text'] = $param;
	}

	public static function test_after(&$data, $param) {
		$data['time'] = $param[0].dgmdate($data['time']);
	}

}