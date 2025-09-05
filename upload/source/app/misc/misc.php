<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('MITFRAME_APP')) {
	exit('Access Denied');
}

if(isset($_GET['css'])) {
	$css = explode('|', $_GET['css']);
	$string = '';
	$size = 0;
	foreach($css as $file) {
		if(preg_match('/^\w+$/', $file)) {
			$file = './data/cache/style_'.$file.'.css';
			$string .= @implode('', file($file));
		}
	}
	ob_start('ob_gzhandler');
	header('Content-Type: text/css');
	header('Expires: '.gmdate('D, d M Y H:i:s', time() + 2592000).' GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s', time()).' GMT');
	echo $string;
	exit;
}
if(isset($_GET['js'])) {
	$js = explode('|', $_GET['js']);
	$string = '';
	$size = 0;
	foreach($js as $file) {
		$file = substr($file, 0, strpos($file, '.'));
		if(preg_match('/^\w+$/', $file)) {
			$file = './data/cache/'.$file.'.js';
			$string .= @implode('', file($file));
		}
	}
	ob_start('ob_gzhandler');
	header('Content-Type: text/javascript');
	header('Expires: '.gmdate('D, d M Y H:i:s', time() + 2592000).' GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s', time()).' GMT');
	echo $string;
	exit;
}

const APPTYPEID = 100;

require './source/class/class_core.php';

$discuz = C::app();

if(isset($_GET['mod']) && $_GET['mod'] != 'tag') {
	$discuz->reject_robot();
}
$modarray = ['seccode', 'secqaa', 'initsys', 'invite', 'faq', 'report',
	'swfupload', 'stat', 'ranklist', 'buyinvitecode',
	'tag', 'diyhelp', 'mobile', 'patch', 'getatuser', 'imgcropper',
	'userstatus', 'secmobseccode', 'secemailseccode', 'share', 'wechat', 'i18n', 'darkroom'];

$modcachelist = [
	'ranklist' => ['forums', 'diytemplatename'],
];

$mod = getgpc('mod');
$mod = (empty($mod) || !in_array($mod, $modarray)) ? 'error' : $mod;

if(in_array($mod, ['seccode', 'secqaa', 'initsys', 'faq', 'swfupload', 'mobile', 'secmobseccode', 'secemailseccode'])) {
	define('ALLOWGUEST', 1);
}

$cachelist = [];
if(isset($modcachelist[$mod])) {
	$cachelist = $modcachelist[$mod];
}

$discuz->cachelist = $cachelist;

switch($mod) {
	case 'secqaa':
	case 'userstatus':
	case 'seccode':
		$discuz->init_cron = false;
		$discuz->init_session = false;
		break;
	default:
		break;
}

$discuz->init();

define('CURMODULE', $mod);
runhooks();

require_once appfile('module/'.$mod);