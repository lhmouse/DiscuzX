<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if($_GET['action'] == 'send') {

	if(!defined('IN_RESTFUL')) {
		$refererhost = parse_url($_SERVER['HTTP_REFERER']);
		$refererhost['host'] .= !empty($refererhost['port']) ? (':'.$refererhost['port']) : '';

		if($refererhost['host'] != $_SERVER['HTTP_HOST']) {
			showmessage('submit_invalid');
		}
	}

	$svctype = empty($_GET['svctype']) ? 0 : $_GET['svctype'];
	$secmobicc = empty($_GET['secmobicc']) ? $_G['member']['secmobicc'] : $_GET['secmobicc'];
	$secmobile = empty($_GET['secmobile']) ? $_G['member']['secmobile'] : $_GET['secmobile'];
	list($seccodecheck, $secqaacheck) = seccheck('card');

	if((!$seccodecheck && !$secqaacheck) || submitcheck('seccodesubmit', 0, $seccodecheck, $secqaacheck)) {
		$length = $_G['setting']['smsdefaultlength'] ? $_G['setting']['smsdefaultlength'] : 4;
		$secmobseccode = random($length, 1);

		// 短信发送前先校验安全手机号是否正确, 避免错误安全手机号送往短信网关
		if(empty($secmobicc) || !preg_match('#^(\d){1,3}$#', $secmobicc)) {
			showmessage('profile_secmobicc_illegal');
		} else if(empty($secmobile) || !preg_match('#^(\d){1,12}$#', $secmobile)) {
			showmessage('profile_secmobile_illegal');
		}
		// 手机号黑名单检测
		if(!empty($_G['setting']['smsmobileblacklist'])) {
			$smsmobileblacklist = $_G['setting']['smsmobileblacklist'];
			$smsmobileblacklist = explode(',', $smsmobileblacklist);
			if(in_array($secmobile, $smsmobileblacklist)) {
				showmessage('profile_secmobile_blacklist');
			}
		}
		// 手机号段黑名单检测
		if(!empty($_G['setting']['smsmobilesegmentblacklist'])) {
			$smsmobilesegmentblacklist = $_G['setting']['smsmobilesegmentblacklist'];
			$smsmobilesegmentblacklist = explode(',', $smsmobilesegmentblacklist);
			foreach($smsmobilesegmentblacklist as $smsbl) {
				if(!empty($smsbl)) {
					$smsbllen = strlen($smsbl);
					$mobilesub = substr($secmobile, 0, $smsbllen);
					if($mobilesub == $smsbl) {
						showmessage('profile_secmobile_blacklist');
					}
				}
			}
		}

		// 手机号段白名单检测
		if(!empty($_G['setting']['smsmobilesegmentwhitelist'])) {
			$smsmobilesegmentwhitelist = $_G['setting']['smsmobilesegmentwhitelist'];
			$smsmobilesegmentwhitelist = explode(',', $smsmobilesegmentwhitelist);
			$inwl = false;
			foreach($smsmobilesegmentwhitelist as $smswl) {
				if(!empty($smswl)) {
					$smswllen = strlen($smswl);
					$mobilesub = substr($secmobile, 0, $smswllen);
					if($mobilesub == $smswl) {
						$inwl = true;
						break;
					}
				}
			}
			if(!$inwl) {
				showmessage('profile_secmobile_blacklist');
			}
		}

		// 用户 UID : $_G['uid'], 短信类型: 验证类短信, 服务类型: $svctype
		// 国际电话区号: $secmobicc, 手机号: $secmobile, 内容: $secmobseccode, 强制发送: false
		$result = sms::send($_G['uid'], 0, $svctype, $secmobicc, $secmobile, $secmobseccode, 0);

		// 发送时间短于设置返回 -1, 单号码发送次数风控规则不通过返回 -2, 万号段风控规则不通过返回 -3, 全局风控规则不通过返回 -4, 无可用网关返回 -5, 网关接口文件不存在返回 -6,
		// 网关接口类不存在返回 -7, 短信功能已被关闭返回 -8, 短信网关私有异常返回 -9
		if(is_string($result)) {
			showmessage('secmobseccode_send_failure', '', [$result]);
		} else if($result >= 0) {
			if(!checkmobile()) {
				showmessage('secmobseccode_send_success', 'misc.php?mod=secmobseccode&action=send', [], ['showdialog' =>1, 'closetime' => true, 'alert' => 'right']);
			} else {
				showmessage('secmobseccode_send_success', '', [], ['alert' => 'right']);
			}
		} else {
			if($result <= -1 && $result >= -9) {
				showmessage('secmobseccode_send_err_'.abs($result));
			} else {
				showmessage('secmobseccode_send_failure');
			}
		}
	} else {
		$handlekey = 'sendsecmobseccode';

		// 短信发送前先校验安全手机号是否已经填写
		if(empty($secmobicc)) {
			showmessage('profile_secmobicc_null');
		} else if(empty($secmobile)) {
			showmessage('profile_secmobile_null');
		}

		include template('common/secmobseccode');
	}

}