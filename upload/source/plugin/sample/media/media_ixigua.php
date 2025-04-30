<?php

namespace sample;

class media_ixigua {

	public static $version = '1.0';
	public static $name = 'ixigua in sample';
	public static $checkurl = ['ixigua.com/'];

	public static function parse($url, $width, $height) {
		if(preg_match('/^https?:\/\/(|m.|www.)ixigua.com\/(\d+)/i', $url, $matches)) {
			$iframe = 'https://www.ixigua.com/iframe/'.$matches[2].'?autoplay=0';
			$flv = $imgurl = '';
		}
		return [$flv, $iframe, $url, $imgurl];
	}

}