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
 * 工具类
 */
class wechat_tools {

	const tokenKey = 'weChatToken';

	function __construct($argument = FALSE) {

	}

	/**
	 * 发起http请求
	 * @param $url 请求的URL
	 * @param $parameters 请求的参数
	 * @param $method 请求的方法，只能是get或post
	 */
	public function httpRequest($url, $parameters = [], $method = 'get', $postData = []) {
		$method = strtolower($method);
		return match ($method) {
			'get' => $this->httpGetRequest($url, $parameters),
			'post' => $this->httpPostRequest($url, $parameters, $postData),
			default => FALSE,
		};
	}

	/**
	 * 发起httpGET请求
	 * @param $url 请求的URL
	 * @param $parameters 请求的参数，以数组形式传递
	 */
	private function httpGetRequest($url, $parameters = NULL) {
		if(empty($url)) {
			return FALSE;
		}
		// 将请求参数追加在url后面
		if(!empty($parameters) && is_array($parameters) && count($parameters)) {
			$is_first = TRUE;
			foreach($parameters as $key => $value) {
				if($is_first) {
					$url .= '?'.$key.'='.urlencode($value);
					$is_first = FALSE;
				} else {
					$url .= '&'.$key.'='.urlencode($value);
				}
			}
		}

		//初始化CURL
		$ch = curl_init();
		// 设置要请求的URL
		curl_setopt($ch, CURLOPT_URL, $url);
		// 设置不显示头部信息
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		// 将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		// 设置本地不检测SSL证书
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		// 执行请求动作，并获取结果
		$result = curl_exec($ch);
		if($error = curl_error($ch)) {
			die($error);
		}

		return json_decode($result, TRUE);
	}

	/**
	 * 发起httpPOST请求
	 * @param $url 请求的URL
	 * @param $parameters 请求的参数，以数组形式传递
	 */
	private function httpPostRequest($url, $parameters = [], $postData = []) {
		if(empty($url)) {
			return FALSE;
		}
		// 将请求参数追加在url后面
		if(!empty($parameters) && is_array($parameters) && count($parameters)) {
			$is_first = TRUE;
			foreach($parameters as $key => $value) {
				if($is_first) {
					$url .= '?'.$key.'='.urlencode($value);
					$is_first = FALSE;
				} else {
					$url .= '&'.$key.'='.urlencode($value);
				}
			}
		}

		// 初始化CURL
		$ch = curl_init();
		// 设置要请求的URL
		curl_setopt($ch, CURLOPT_URL, $url);
		// 设置不显示头部信息
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		// 设置不将请求结果直接输出在标准输出里，而是返回
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		// 设置本地不检测SSL证书
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		//设置post方式提交
		curl_setopt($ch, CURLOPT_POST, TRUE);
		// 设置请求参数
		if(!empty($postData)) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, $this->json_encode_ex($postData));
		}
		// 执行请求动作，并获取结果
		$result = curl_exec($ch);
		if($error = curl_error($ch)) {
			die($error);
		}

		return json_decode($result, TRUE);
	}

	/**
	 * 使用POST请求上传文件
	 */
	public function uploadFileByPost($url, $data) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$result = curl_exec($ch);
		if($error = curl_error($ch)) {
			die($error);
		}

		return json_decode($result, TRUE);
	}

	/**
	 * 用CURL发起一个HTTP请求
	 * @param $url 访问的URL
	 * @param $post post数据(不填则为GET)
	 * @param $cookie 提交的$cookies
	 * @param $returnCookie 是否返回$cookies
	 */
	public function curlRequest($url, $post = '', $cookie = '', $returnCookie = 0) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
		curl_setopt($curl, CURLOPT_REFERER, 'http://XXX');
		if($post) {
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
		}
		if($cookie) {
			curl_setopt($curl, CURLOPT_COOKIE, $cookie);
		}
		curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		$data = curl_exec($curl);
		if(curl_errno($curl)) {
			return curl_error($curl);
		}
		if($returnCookie) {
			list($header, $body) = explode("\r\n\r\n", $data, 2);
			preg_match_all('/Set\-Cookie:([^;]*);/', $header, $matches);
			$info['cookie'] = substr($matches[1][0], 1);
			$info['content'] = $body;
			return $info;
		} else {
			return $data;
		}
	}

	/**
	 * 保存从网络上获取到的AccessToken
	 * @param $appid appid
	 * @param $appsecret 管理组的凭证密钥
	 * @param $token 从网络上获取到的AccessToken
	 */
	public function saveAccessToken($appid, $appsecret, $token) {
		if(empty($appid) || empty($appsecret) || empty($token)) {
			return FALSE;
		}

		$result = memory('get', self::tokenKey.$appid);

		$result = json_decode($result, TRUE);
		$key = $appid.$appsecret;
		$result[$key] = [$token, time()];
		if(memory('set', self::tokenKey.$appid, json_encode($result))) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * 保存从网络上获取到的AccessToken
	 * @param $appid appid
	 * @param $appsecret 管理组的凭证密钥
	 * @return 当前appID和管理组的凭证密钥对应的AccessToken，没有则返回false
	 */
	public function getAccessToken($appid, $appsecret) {
		if(empty($appid) || empty($appsecret)) {
			return FALSE;
		}

		$result = memory('get', self::tokenKey.$appid);
		if(empty($result)) {
			return FALSE;
		}

		$result = json_decode($result, TRUE);
		$key = $appid.$appsecret;
		if(isset($result[$key])) {
			if(time() - 7200 > $result[$key][1]) {
				// token已超时
				return FALSE;
			} else {
				// token未超时
				return $result[$key][0];
			}
		} else {
			return FALSE;
		}
	}

	/**
	 * 对内容进行json编码，并且保持汉字不会被编码
	 * @param $value 被编码的对象
	 * @return 编码结果字符串
	 */
	public function json_encode_ex($value) {
		return json_encode($value, JSON_UNESCAPED_UNICODE);
	}

}

