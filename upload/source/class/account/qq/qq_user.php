<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

/**
 * QQ
 * 成员管理
 */
class qq_user {
	private $token;
	private $tools;

	public function __construct($token) {
		$this->token = $token;
		$this->tools = new qq_tools();
	}

	/**
	 * 设置token
	 */
	public function setToken($token = NULL) {
		$this->token = $token;
	}

	/**
	 * 获取token
	 */
	public function getToken() {
		return $this->token;
	}

	public function getSsoUrl($query_data) {
		return 'https://graph.qq.com/oauth2.0/authorize?'.http_build_query($query_data);
	}

	/**
	 * 获取访问用户身份
	 * @return array|mixed
	 */
	public function getOpenid() {
		$url = 'https://graph.qq.com/oauth2.0/me';
		$data = ['access_token' => $this->token['access_token'], 'fmt' => 'json'];

		$result = $this->tools->httpRequest($url, $data);
		return $result ?? [];
	}

	/**
	 * 获取访问用户身份
	 * @return array|mixed
	 */
	public function getAuthUser($openid) {
		$url = 'https://graph.qq.com/user/get_user_info';
		$data = ['access_token' => $this->token['access_token'], 'oauth_consumer_key' => $openid['client_id'], 'openid' => $openid['openid'], 'fmt' => 'json'];

		$result = $this->tools->httpRequest($url, $data);
		if($result) {
			if(!isset($result['errcode'])) {
				$result['success'] = TRUE;
				return ($result);
			} else {
				$result['success'] = FALSE;
				return ($result);
			}
		} else {
			return (['success' => FALSE, 'errmsg' => 'Query fails!', 'errcode' => -2, 'userid' => '']);
		}
	}

}
/* End of file  */
