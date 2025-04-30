<?php

if(empty($_POST)) {
	echo
	<<<EOF
<form method="post">
	User:<input name="user"><br />
	Pass:<input name="pass" type="password"><br />
	<input type="submit">	
</form>
EOF;
} else {
	if($_GET['user'] != 'test123' || $_GET['pass'] != '1') {
		die('接口返回错误或登录失败');
	}

	$account = account_base::getConfig('plugin_sample');

	$callback = $account['callbackUrl'].'&referer='.rawurlencode($_GET['referer']).'&user='.rawurlencode(base64_encode(json_encode(['username' => 'test123', 'uid' => 1])));
	echo '<a href="'.$callback.'">callback</a>';
}