<?php

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

//自定语言包安装
//i18n('set', 'mylang', DISCUZ_PLUGIN('sample').'/mylang');

$createSql = <<<EOF

DROP TABLE IF EXISTS cdb_sample_test;
CREATE TABLE cdb_sample_test (
  `uid` mediumint(8) unsigned NOT NULL,
  `text` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`)
) ENGINE=INNODB;

EOF;

runquery($createSql);

$finish = TRUE;

if(method_exists('menu', 'platform_add')) {
	$xml = '<?xml version="1.0" encoding="ISO-8859-1"?>
<root>
	<name><![CDATA[Sample面板]]></name>
	<title><![CDATA[Sample面板]]></title>
	<desc><![CDATA[Sample面板的说明]]></desc>	
	<logo><![CDATA[<a class="logo"><img src="https://www.dismall.com/template/discuz_dismall/image/logo.png" style="height: 20px" /></a>]]></logo>
	<navbar><![CDATA[
<form style="float:right">欢迎访问xxxx后台</form>
]]></navbar>
	<menubar><![CDATA[About me]]></menubar>
	<defaultId><![CDATA[plugin_sample:admin:a]]></defaultId>
	<menu>
		<menuId>主菜单1</menuId>
		<sub>
			<subId>plugin_sample:admin:a</subId>
			<title>菜单a</title>
		</sub>
	</menu>
	<menu>
		<menuId>主菜单2</menuId>
		<sub>
			<subId>plugin_sample:admin:b</subId>
			<title>菜单b</title>
			<showMethod>sample::test</showMethod>
		</sub>
		<sub>
			<listMethod>sample::testList</listMethod>
		</sub>
	</menu>
</root>';

	menu::platform_add('sample', $xml);
}

account_base::registerAccount('sample');

echo $installlang['install'];

$finish = TRUE;
