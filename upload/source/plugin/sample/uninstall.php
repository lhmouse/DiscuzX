<?php

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

//自定语言包卸载
//i18n('rm', 'mylang');

$createSql = <<<EOF
DROP TABLE IF EXISTS cdb_sample_test;
EOF;

runquery($createSql);

if(method_exists('menu', 'platform_del')) {
	menu::platform_del('sample');
}

account_base::unregisterAccount('sample');

echo $installlang['uninstall'];

$finish = TRUE;
