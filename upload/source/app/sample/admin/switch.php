<?php

class app_sample_switch {

	const Icon = STATICURL.'image/app/empty.svg';
	const Name = 'sample_app_name';
	const Desc = 'sample_app_desc';

	public static function getStatus() {
		return 1;
	}

	public static function getOptions() {
		return '<a href="'.ADMINSCRIPT.'?action=app&operation=sample:op">开启</a>';
	}

}