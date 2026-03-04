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
 * qq
 * 创建连接
 */
class qq_connection {

	private $tools = NULL;

	function __construct($argument = FALSE) {
		$this->tools = new qq_tools();
	}

	/**
	 * 获取AccessToken
	 * @param $clientid clientid
	 * @param $clientsecret clientsecret
	 */
	public function getAccessToken($clientid = FALSE, $clientsecret = FALSE, $code = FALSE, $callbackUrl = FALSE) {
		if(empty($clientid) || empty($clientsecret) || empty($code) || empty($callbackUrl)) {
			return [];
		}

		$result = $this->getRemoteAccessToken($clientid, $clientsecret, $code, $callbackUrl);
		if(empty($result)) {
			return [];
		} else {
			return $result;
		}
	}

	/**
	 * 获取存储在本地的AccessToken
	 * @param $clientid 企业ID
	 * @param $clientsecret 管理组的凭证密钥
	 */
	private function getLocalAccessToken($clientid = FALSE, $clientsecret = FALSE, $code = FALSE, $callbackUrl = FALSE) {
		if(empty($clientid) || empty($clientsecret) || empty($code) || empty($callbackUrl)) {
			return [];
		}

		return $this->tools->getAccessToken($clientid, $clientsecret, $code, $callbackUrl);
	}

	/**
	 * 从服务器上获取AccessToken
	 * @param $clientid clientid
	 * @param $clientsecret 管理组的凭证密钥
	 */
	private function getRemoteAccessToken($clientid = FALSE, $clientsecret = FALSE, $code = FALSE, $callbackUrl = FALSE) {
		global $_G;
		if(empty($clientid) || empty($clientsecret) || empty($code) || empty($callbackUrl)) {
			return [];
		}

		$url = 'https://graph.qq.com/oauth2.0/token';
		$data = ['grant_type' => 'authorization_code', 'client_id' => $clientid, 'client_secret' => $clientsecret, 'code' => $code, 'redirect_uri' => $callbackUrl, 'fmt' => 'json'];

		$result = $this->tools->httpRequest($url, $data);
		if(isset($result)) {
			$this->tools->saveAccessToken($clientid, $clientsecret, $result);
			return $result;
		} else {
			//echo $result['errcode'].":".$result['errmsg'];
			return [];
		}
	}
}


