<?php

namespace sample;

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

class admin_widget {

	public static function widget_sample1() {

		echo '
		<div class="dbox">
			<div class="boxheader">Sample_right</div>
			<div class="boxbody">Content</div>
		</div>';

	}

	public static function widget_sample1_left() {

		echo '
		<div class="dbox">
			<div class="boxheader">Sample_left</div>
			<div class="boxbody">Content</div>
		</div>';

	}

}