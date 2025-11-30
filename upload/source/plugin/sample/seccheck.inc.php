<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

/*
 * seccheck 范例
 * 此脚本访问地址：http://discuz/?app=plugin&id=sample:seccheck
 */

list($seccodecheck, $secqaacheck) = seccheck('sample:name', [1]);

if(submitcheck('testsubmit', 0, $seccodecheck, $secqaacheck)) {
	showmessage('安全验证通过', '?app=plugin&id=sample:seccheck');
} else {
	require_once template('sample:seccheck');
}
