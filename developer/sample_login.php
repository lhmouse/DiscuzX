<?php

// X5 带 Web 页面授权登录示例

$r = new request();
$r->sample();

class request {

	var $url = 'https://test.72zhan.com/api/restful/';
	var $appid = '14842566';
	var $secret = 'JQY1WXXXZSNS1WPY';
	var $token = '';

	public function sample() {
		echo '我不是 Discuz!：<br>';

		if(empty($_GET['op'])) {
			//Step1：访问 /_login.php
			//Step2：访问 web 页面 [data.locationUrl.url] 的值

			$ret = $this->_request('/token', array());
			if (!$ret || $ret['ret'] > 0 || empty($ret['token'])) {
				die('接口错误，无法获取 Token');
			}
			$this->token = $ret['token'];
			setcookie('_token', $this->token);

			$post = array(
				'method' => 'system',
				'returntype' => base64_encode('https://test.72zhan.com/_dev/sample_login.php?op=1'),
			);
			//$post = array('method' => 'plugin_sample'); //第三方登录用这个

			$ret = $this->_request('/member/loginInfo', $post);
			if(!$ret || $ret['ret'] > 0) {
				die('接口错误，无法获取 /index/forumlist 接口');
			}
			setcookie('_authtoken', $ret['data']['authtoken']);
			echo '<a href="'.$ret['data']['locationUrl']['url'].'" target="_blank">点击这里登录</a>';
		} elseif($_GET['op'] == 1) {
			//Step3：正常登录流程完毕后访问 /_login.php?op=1&authtoken=[data.authtoken]

			if(empty($_COOKIE['_token']) || empty($_COOKIE['_authtoken'])) {
				die('请先获取 Token');
			}
			$this->token = $_COOKIE['_token'];

			$ret = $this->_request('/authtoken', array('authtoken' => $_COOKIE['_authtoken']));
			if(!$ret || $ret['ret'] > 0) {
				die('接口错误，无法获取 /index/forumlist 接口');
			}
			header('location: ?op=2');
		} else {
			//Step4: 登录成功，正常访问接口

			if(empty($_COOKIE['_token'])) {
				die('请先获取 Token');
			}
			$this->token = $_COOKIE['_token'];

			$ret = $this->_request('/forumdisplay', array('fid' => 2));
			if (!$ret || $ret['ret'] > 0) {
				die('接口错误，无法获取 /index/forumdisplay 接口');
			}
			echo '登录完毕了，当前用户是 '.$ret['data']['user']['username'];
		}


	}

	private function _request($uri, $post, $validate = false, $unique = false) {
		$nonce = rand(1000, 2000);
		$t = time();
		$headers = array(
			'appid' => $this->appid,
			'nonce' => $nonce,
			't' => $t,
			'sign' => base64_encode(hash('sha256', $nonce.$t.$this->secret)),
		);

		if($validate) {
			$post = json_encode($post);
			$headers['validatecode'] = sha1($this->secret.$post);
			$headers['Content-Type'] = 'application/json;charset=utf-8';
			$headers['Content-Length'] = strlen($post);
		}

		if($unique) {
			$headers['uniqueid'] = time();
		}

		if ($this->token) {
			$headers['token'] = $this->token;
		}

		$headersFmt = array();
		foreach ($headers as $name => $value) {
			$canonicalName = implode('-', array_map('ucfirst', explode('-', $name)));
			$headersFmt[] = $canonicalName . ': ' . $value;
		}

		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => $headersFmt,
			CURLOPT_URL => $this->url . '?' . $uri,
			CURLOPT_POST => 'POST',
			CURLOPT_POSTFIELDS => $post,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
		));
		$response = curl_exec($ch);
		return json_decode($response, true);
	}

}
