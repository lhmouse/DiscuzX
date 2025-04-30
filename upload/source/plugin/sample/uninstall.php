<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

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
