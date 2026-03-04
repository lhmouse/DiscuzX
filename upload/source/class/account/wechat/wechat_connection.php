<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

/**
 * 微信
 * 创建连接
 */
class wechat_connection {

	const jsTicketKey = 'workWxJsTicket';

	private $tools = NULL;

	function __construct($argument = FALSE) {
		$this->tools = new wechat_tools();
	}

	/**
	 * 获取AccessToken
	 * @param $appid appid
	 * @param $appsecret appsecret
	 */
	public function getAccessToken($appid = FALSE, $appsecret = FALSE, $code = FALSE) {
		if(empty($appid) || empty($appsecret) || empty($code)) {
			return [];
		}

		$result = $this->getRemoteAccessToken($appid, $appsecret, $code);
		if(empty($result)) {
			return [];
		} else {
			return $result;
		}
	}

	/**
	 * 更新AccessToken
	 * @param $appid appid
	 * @param $refresh_token refresh_token
	 */
	public function refreshAccessToken($appid = FALSE, $appsecret = FALSE, $refresh_token = FALSE) {
		if(empty($appid) || empty($appsecret) || empty($refresh_token)) {
			return [];
		}

		$result = $this->getRemoteAccessToken($appid, $appsecret, $refresh_token);
		if(empty($result)) {
			return [];
		} else {
			return $result;
		}
	}

	/**
	 * 获取存储在本地的AccessToken
	 * @param $appid 企业ID
	 * @param $appsecret 管理组的凭证密钥
	 */
	private function getLocalAccessToken($appid = FALSE, $appsecret = FALSE, $code = FALSE) {
		if(empty($appid) || empty($appsecret) || empty($code)) {
			return [];
		}

		return $this->tools->getAccessToken($appid, $appsecret, $code);
	}

	/**
	 * 从服务器上获取AccessToken
	 * @param $appid appid
	 * @param $appsecret 管理组的凭证密钥
	 */
	private function getRemoteAccessToken($appid = FALSE, $appsecret = FALSE, $code = FALSE) {
		if(empty($appid) || empty($appsecret) || empty($code)) {
			return [];
		}

		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token';
		$data = ['appid' => $appid, 'secret' => $appsecret, 'code' => $code, 'grant_type' => 'authorization_code'];

		$result = $this->tools->httpRequest($url, $data);
		if(isset($result)) {
			$this->tools->saveAccessToken($appid, $appsecret, $result);
			return $result;
		} else {
			//echo $result['errcode'].":".$result['errmsg'];
			return [];
		}
	}

	/**
	 * 从服务器上获取更新的AccessToken
	 * @param $appid appid
	 * @param $appsecret 管理组的凭证密钥
	 */
	private function getRemoteRefreshAccessToken($appid = FALSE, $appsecret = FALSE, $refresh_token = FALSE) {
		if(empty($appid) || empty($appsecret) || empty($refresh_token)) {
			return [];
		}

		$url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token';
		$data = ['appid' => $appid, 'grant_type' => 'refresh_token', 'refresh_token' => $refresh_token];

		$result = $this->tools->httpRequest($url, $data);
		if(isset($result)) {
			$this->tools->saveAccessToken($appid, $appsecret, $result);
			return $result;
		} else {
			//echo $result['errcode'].":".$result['errmsg'];
			return [];
		}
	}
}


