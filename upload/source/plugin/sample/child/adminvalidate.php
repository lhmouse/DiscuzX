<?php

//childfile:global/adminvalidate/sample
//后台二次校验脚本

if(empty($_G['cookie']['admin_sample']) || $_G['cookie']['saltkey'] != authcode($_G['cookie']['admin_sample'], 'DECODE')) {
	if(empty($_POST) || $_POST['key'] != '12345') {
		$url = $_G['siteurl'].$_SERVER['REQUEST_URI'];
		echo '<form action="'.$url.'" method="POST" /><input name="key" /></form>';
		exit;
	} else {
		dsetcookie('admin_sample', authcode($_G['cookie']['saltkey'], 'ENCODE'));
	}
}