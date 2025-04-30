<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

cpheader();

showsubmenu('mitframe_apps');

$allowfuntype = ['portal', 'forum', 'friend', 'group', 'follow', 'collection', 'guide', 'feed', 'blog', 'doing', 'album', 'share', 'wall', 'homepage', 'ranklist', 'medal', 'task', 'magic', 'favorite'];
$_GET['type'] = in_array($_GET['type'], $allowfuntype) ? trim($_GET['type']) : '';
echo "<script>disallowfloat = '{$_G['setting']['disallowfloat']}';</script>";
echo "<style>.td25 { width: 40px; } .light { margin-top: 2px; }</style>";

/*search={"setting_functions":"action=mitframe"}*/
showboxheader('');
showtableheader('', 'nobottom');
$apps = [];

getapps($apps);

foreach($apps as $app => $data) {
	[$icon, $status, $name, $desc, $op] = $data;
	echo '<tr>'.
		'<td class="td25"><img src="'.$icon.'" /></td>'.
		'<td width="490" s="1">'.cplang($name, mitframeApp: $app).'<div class="light">'.cplang($desc, mitframeApp: $app).'</div></td>'.
		'<td class="td30"><img class="vm" src="'.$_G['style']['imgdir'].'/data_'.($status ? 'valid' : 'invalid').'.gif"></td>'.
		'<td>'.$op.'</td>'.
		'</tr>';
}
showtablefooter();
showboxfooter();
/*search*/

function getapps(&$apps) {
	global $_G;
	$dir = MITFRAME_APP();
	$advdir = dir($dir);
	while($entry = $advdir->read()) {
		if($entry == '.' || $entry == '..') {
			continue;
		}
		$f = appfile('admin/switch', $entry);
		if(file_exists($f)) {
			include_once $f;
			$c = 'app_'.$entry.'_switch';
			if(!class_exists($c)) {
				continue;
			}
			if(method_exists($c, 'getModules')) {
				$modules = $c::getModules();
				foreach($modules as $module) {
					$c = 'app_'.$entry.'_switch_'.$module;
					if(class_exists($c)) {
						$apps[$module] = [$c::Icon, $c::getStatus(), $c::Name, $c::Desc, $c::GetOptions()];
					}
				}
			} else {
				$apps[$entry] = [$c::Icon, $c::getStatus(), $c::Name, $c::Desc, $c::GetOptions()];
			}
		}
	}
}