<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

/*
 * upload 类范例
 * 此脚本访问地址：http://discuz/?app=plugin&id=sample:upload
 */

loadcache('sample_files');
$_G['cache']['sample_files'] = !empty($_G['cache']['sample_files'])? $_G['cache']['sample_files'] : [];

$u = new upload('sample');

if(!empty($_FILES)) {
	$f = $u->upload();
	if(empty($f['newfile']) || $f['newfile']['error'] > 0) {
		die('upload failed');
	}
	$_G['cache']['sample_files'][$f['newfile']['attachment']] = $f['newfile'];
	savecache('sample_files', $_G['cache']['sample_files']);
	loadcache('sample_files');
} elseif(!empty($_GET['do']) && $_GET['do'] == 'del') {
	$u->delete($_GET['f']);
	unset($_G['cache']['sample_files'][$_GET['f']]);
	savecache('sample_files', $_G['cache']['sample_files']);
	loadcache('sample_files');
}

echo <<<EOF
<form action="plugin.php?id=sample:upload" method="post" enctype="multipart/form-data">
	<input type="file" name="newfile" multiple>
	<input type="submit">
</form>
EOF;

echo '<table>';
foreach($_G['cache']['sample_files'] as $file) {
	$url = $u->getUrl($file['attachment']);
	echo <<<EOF
<tr>
	<td><a href="{$url}" target="_blank">{$file['attachment']}</a></td>
	<td>{$file['type']}</td>
	<td>{$file['size']} Bytes</td>
	<td><a href="plugin.php?id=sample:upload&do=del&f={$file['attachment']}">[Del]</a></td>
</tr>
EOF;
}
echo '</table>';