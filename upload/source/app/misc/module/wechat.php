<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
global $_G;
if(empty($_G['setting']['wechat']['appId']) || empty($_G['setting']['wechat']['appSecret'])) {
	jsonExit(-99);
}

$ac = !empty($_GET['ac']) ? $_GET['ac'] : '';
if(!empty($ac) && $_GET['formhash'] != FORMHASH) {
	jsonMsg('formhash error');
}

switch($ac) {
	case 'qrcode':
		$authcode = !empty($_GET['authcode']) ? $_GET['authcode'] : '';
		if(!empty($ac) && empty($authcode)) {
			jsonMsg('authcode error');
		}
		require_once libfile('account/wechat', 'class');
		$wechat = new account_wechat();
		$wechat_login_url = $wechat->getWeChatUrl($authcode);
		require_once libfile('class/qrcode');
		$value = urldecode($wechat_login_url);
		$Level = 'L';
		$Size = 9;
		echo QRcode::png($value, false, $Level, $Size);
		break;
	case 'check':
		$authcode = !empty($_GET['authcode']) ? $_GET['authcode'] : '';
		if(!empty($ac) && empty($authcode)) {
			jsonMsg('authcode error');
		}
		$echostr = '0';
		$authcode = authcode(base64_decode(urldecode($authcode)), 'DECODE', $_G['config']['security']['authkey']);
		if($authcode) {
			$checkstutas = memory('get', 'wechat_code_'.$authcode);
			if($checkstutas['status'] == 1) {
				$echostr = '1';
				require_once libfile('function/member');
				$member = getuserbyuid($checkstutas['uid'], 1);
				setloginstatus($member, 1296000);
				memory('rm', 'wechat_code_'.$authcode);
			}
		}
		include template('common/header_ajax');
		echo $echostr;
		include template('common/footer_ajax');
		break;
	case 'getauthcode':
		require_once libfile('account/wechat', 'class');
		$wechat = new account_wechat();
		list($authcode, $code) = $wechat->getAuthCode();
		jsonMsg(['authcode' => $authcode]);
		break;
	case 'snapshotuser':
		include template('wechat/snapshotuser');
		break;
	default:
		if($_G['uid']) {
			$account = new account();
			$user = $account->getUser($_G['uid'], account::aType_wechatOpenid);
			if($user) {
				showmessage('account_bind_exists');
			}
		}
		require_once libfile('account/wechat', 'class');
		$wechat = new account_wechat();

		dsetcookie('wechat_referer', dreferer(), 86400);
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		if(!str_contains($user_agent, 'MicroMessenger')) {
			[$authcode, $code] = $wechat->getAuthCode();
			include template('wechat/wechat');
		} else {
			$wechat->login();
		}
		break;
}

function jsonExit($err = 0) {
	if($err == 0) {
		exit('{}');
	}
	exit('{"errcode":'.$err.'}');
}

function jsonMsg($return) {
	exit(json_encode($return));
}