<?php

// 第三方登录插件示例
class account_sample extends account_base {

	// 不自动生成头像
	public bool $interface_noAutoAvatar = true;

	// 不支持绑定
	public bool $interface_noBind = false;

	public function __construct() {
		$this->conf = parent::getConfig('plugin_sample');
	}

	// 名称
	public static function name() {
		return '测试登录';
	}

	// 图标
	public static function icon() {
		return '<svg t="1715821283732" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="28056" width="20" height="20"><path d="M840.7 626.2c-33.3 33.3-144 23.5-144 23.5s-9.7-110.7 23.5-144c33.3-33.3 77.3-0.5 77.3 43.2 44.3 0 76.5 44 43.2 77.3zM611.2 560.7c-29.7 0-53.9-24.3-53.9-53.9V245.3c0-29.7 24.3-53.9 53.9-53.9 29.7 0 53.9 24.3 53.9 53.9v261.5c0 29.7-24.3 53.9-53.9 53.9zM226.3 614.7c-29.7 0-53.9-24.3-53.9-53.9V338.5c0-29.7 24.3-53.9 53.9-53.9 29.7 0 53.9 24.3 53.9 53.9v222.2c0.1 29.7-24.2 54-53.9 54zM482.9 551.5c-29.7 0-53.9-24.3-53.9-53.9V121.2c0-29.7 24.3-53.9 53.9-53.9 29.7 0 53.9 24.3 53.9 53.9v376.3c0 29.7-24.2 54-53.9 54zM354.6 571.5c-29.7 0-53.9-24.3-53.9-53.9v-337c0-29.7 24.3-53.9 53.9-53.9 29.7 0 53.9 24.3 53.9 53.9v337c0.1 29.7-24.2 53.9-53.9 53.9zM445.7 960c-67.4 0-130.8-26.2-178.4-73.9-47.7-47.7-73.9-111-73.9-178.4 0-18.2 14.8-33 33-33s33 14.8 33 33c0 102.8 83.6 186.4 186.4 186.4 102.8 0 186.4-83.6 186.4-186.4 0-18.2 14.8-33 33-33s33 14.8 33 33c0 67.4-26.2 130.8-73.9 178.4S513.1 960 445.7 960z" fill="#E95431" p-id="28057"></path></svg>';
	}

	// 通知发送
	public function notificationAdd($touid, $note, $notestring) {
	}

	// 用于自动登录，判断是否在环境中
	public function inEnv() {
		return isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'xxx');
	}

	// 登录(绑定)跳转
	public function login($referer = '', $op = 0) {
		global $_G;
		dheader('location: '.$_G['siteurl'].'index.php?app=plugin&id=sample:account&referer='.urlencode($referer));
	}

	// 注册接管跳转
	public function register($referer = '', $op = 0) {
		$this->login();
	}

	// 登录回调，/api/account/callback.php?id=sample 会调用此方法
	public function getLoginUser() {
		global $_G;
		$account = new account();

		$user = json_decode(base64_decode($_GET['user']), true);
		$param = [
			'type' => 'plugin_sample',
			'atype' => parent::getAccountType('sample'),
			'account' => $user['uid'],
			'bindname' => $user['username'],
		];
		if(!$account->checkUser($param)) {
			if($_G['uid']) {
				$account->userBind($_G['uid'], $param);
			} else {
				$param += [
					'username' => $user['username'],
					'password' => '',
				];
				$msg = $account->userRegister($param);
				if($msg) {
					if($msg == 'profile_username_duplicate' || $msg == 'profile_email_duplicate') {
						dheader('Location: '.$_G['siteurl'].'?index=member&mod=register');
					}
					showmessage($msg);
				}
			}
		} else {
			$account->userLogin();
		}
		dheader('Location: '.$_GET['referer']);
	}

	// 管理后台
	public function admincp() {
		global $_G;

		$conf = $_G['setting']['account_plugin_confs']['sample'];

		if(!submitcheck('submit')) {
			showformheader('account&method=plugin_sample');
			showtableheader();
			showtitle('baseSetting');
			showsetting('allow', 'conf[allow]', $conf['allow'], 'radio');
			showsetting('callbackUrl', 'conf[callbackUrl]', $conf['callbackUrl'], 'text');
			showsubmit('submit');
			showtablefooter();
			showformfooter();
		} else {
			if(empty($_GET['conf']['callbackUrl'])) {
				$_GET['conf']['callbackUrl'] = $_G['siteurl'].'api/account/callback.php?id=sample';
			}

			$_G['setting']['account_plugin_confs']['sample'] = $_GET['conf'];
			table_common_setting::t()->update('account_plugin_confs', $_G['setting']['account_plugin_confs']);
			updatecache('setting');

			cpmsg('setting_update_succeed', 'action=account&method=plugin_sample', 'succeed');
		}
	}

}