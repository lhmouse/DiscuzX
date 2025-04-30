<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$filename = $_G['setting']['attachdir'].'/portal/'.$attach['attachment'];
if(!$attach['remote'] && !is_readable($filename)) {
	showmessage('attachment_nonexistence');
}

$readmod = 2;//read local file's function: 1=fread 2=readfile 3=fpassthru 4=fpassthru+multiple
$range = 0;
if($readmod == 4 && !empty($_SERVER['HTTP_RANGE'])) {
	list($range) = explode('-', (str_replace('bytes=', '', $_SERVER['HTTP_RANGE'])));
}

if($attach['remote'] && !$_G['setting']['ftp']['hideurl'] && $attach['isimage']) {
	dheader('location:'.$_G['setting']['ftp']['attachurl'].'portal/'.$attach['attachment']);
}

$filesize = $attach['filesize'];
// 遵循RFC 6266国际标准，按照RFC 5987中的规则对文件名进行编码
$filenameencode = strtolower(CHARSET) == 'utf-8' ? rawurlencode($attach['filename']) : rawurlencode(diconv($attach['filename'], CHARSET, 'UTF-8'));

// 连2011年发布的国际标准都没能正确支持的浏览器厂商的黑名单列表
// 目前包括：UC，夸克，搜狗，百度
$rfc6266blacklist = strexists($_SERVER['HTTP_USER_AGENT'], 'UCBrowser') || strexists($_SERVER['HTTP_USER_AGENT'], 'Quark') || strexists($_SERVER['HTTP_USER_AGENT'], 'SogouM') || strexists($_SERVER['HTTP_USER_AGENT'], 'baidu');

dheader('Date: '.gmdate('D, d M Y H:i:s', $attach['dateline']).' GMT');
dheader('Last-Modified: '.gmdate('D, d M Y H:i:s', $attach['dateline']).' GMT');
dheader('Content-Encoding: none');
dheader('Content-Disposition: attachment; filename="'.$filenameencode.'"'.(($attach['filename'] == $filenameencode || $rfc6266blacklist) ? '' : '; filename*=utf-8\'\''.$filenameencode));
dheader('Content-Type: '.$attach['filetype']);
dheader('Content-Length: '.$filesize);

if($readmod == 4) {
	dheader('Accept-Ranges: bytes');
	if(!empty($_SERVER['HTTP_RANGE'])) {
		$rangesize = ($filesize - $range) > 0 ? ($filesize - $range) : 0;
		dheader('Content-Length: '.$rangesize);
		dheader('HTTP/1.1 206 Partial Content');
		dheader('Content-Range: bytes='.$range.'-'.($filesize - 1).'/'.($filesize));
	}
}

$attach['remote'] ? getremotefile($attach['attachment']) : getlocalfile($filename, $readmod, $range);
	