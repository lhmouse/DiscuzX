<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if($_GET['type'] == 'ico') {
	$shortcut = @readfile(DISCUZ_ROOT.'favicon.ico');
	$filename = 'favicon.ico';
} else {
	$shortcut = '[InternetShortcut]
URL='.$_G['siteurl'].'
IconFile='.$_G['siteurl'].'favicon.ico
IconIndex=1
';
	$filename = $_G['setting']['bbname'].'.url';
}

// 遵循RFC 6266国际标准，按照RFC 5987中的规则对文件名进行编码
$filenameencode = strtolower(CHARSET) == 'utf-8' ? rawurlencode($filename) : rawurlencode(diconv($filename, CHARSET, 'UTF-8'));
// 连2011年发布的国际标准都没能正确支持的浏览器厂商的黑名单列表
// 目前包括：UC，夸克，搜狗，百度
$rfc6266blacklist = strexists($_SERVER['HTTP_USER_AGENT'], 'UCBrowser') || strexists($_SERVER['HTTP_USER_AGENT'], 'Quark') || strexists($_SERVER['HTTP_USER_AGENT'], 'SogouM') || strexists($_SERVER['HTTP_USER_AGENT'], 'baidu');
dheader('Content-type: application/octet-stream');
dheader('Content-Disposition: attachment; filename="'.$filenameencode.'"'.(($filename == $filenameencode || $rfc6266blacklist) ? '' : '; filename*=utf-8\'\''.$filenameencode));
echo $shortcut;
exit;
	