<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class smsgw_sample {

	var $version = '1.0';
	var $name = 'xxx网关接口';
	var $description = 'xxx短信网关接口';
	var $copyright = '';
	var $customname = '';
	var $sendrule = '86';
	var $type = '0';

	function __construct() {

	}

	function getsetting() {
		global $_G;
		$settings = [
			'account' => [
				'title' => 'account',
				'type' => 'text',
				'value' => '',
			],
			'password' => [
				'title' => 'password',
				'type' => 'text',
				'value' => '',
			],
		];
		return $settings;
	}

	function setsetting(&$smsgwnew, &$parameters) {
	}

	function send($uid, $smstype, $svctype, $secmobicc = '86', $secmobile, $content) {
		global $_G;
		
		//$this->parameters['account']
		//$this->parameters['password']

		// logger start
		if($_G['setting']['log']['sms']) {
			$log = [
				'timestamp' => TIMESTAMP,
				'uid' => $uid,
				'smstype' => $smstype,
				'secmobicc' => $secmobicc,
				'secmobile' => $secmobile,
				'content' => $content,
				'ret' => $result,
			];
			$member_log = getuserbyuid($uid);
			logger('sms', $member_log, $uid, $log);
		}
		// logger end

		return $result == 0 ? 1 : -9;
	}

}