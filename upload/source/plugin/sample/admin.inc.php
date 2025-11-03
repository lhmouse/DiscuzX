<?php

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

echo !empty($_GET['do']) ? 'do='.$_GET['do'] : '';

if(!submitcheck('submit')) {

	showformheader('plugins&operation=config&do='.$do.'&identifier=sample&pmod=admin');
	showtableheader('', 'fixpadding');
	showcomponent('测试1', 'abc1', 'yellow', 'sample:component_test');
	showcomponent('测试2', 'abc2', 'red', 'sample:component_test');
	showcomponent('测试3', 'def', '{}', 'sample:component_test2');

	$conf = '[
	 {"type": "text", "field": "f1", "name": "text1", "width": 80, "maxlen": 5},
	 {"type": "radio", "field": "f2", "name": "radio1"},
	 {"type": "mradio", "field": "f3", "name": "radio2", "options": [{"name":"A","value":"a","default":true},{"name":"B","value":"b"}]},
	 {"type": "checkbox", "field": "f4", "name": "checkbox", "options": [{"name":"C","value":"c","default":true},{"name":"D","value":"d"}]}
	]';
	showcomponent('测试4', 'ghi', '[]', 'component_list', '', $conf);

	showcomponent('权限', 'perm', '', 'component_perm');

	showsubmit('submit');
	showtablefooter();
	showformfooter();

} else {

	//serializecomponent();

	debug($_GET);
}