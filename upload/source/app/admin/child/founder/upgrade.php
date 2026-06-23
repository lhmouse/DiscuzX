<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!class_exists('ZipArchive')) {
	cpmsg('extension_missing_ZipArchive', '', 'error');
}

$u = new admin\class_upgrade();

if($_GET['downPatch'] && $_GET['formhash'] == formhash()) {
	$patchFile = $u->getPatchFile();
	if(!file_exists($patchFile)) {
		cpmsg('upgrade_patch_not_found', '', 'error');
	}
	define('FOOTERDISABLED', true);
	ob_end_clean();
	header('Content-Type: application/zip');
	header('Content-Disposition: attachment; filename="'.basename($patchFile).'"');
	header('Pragma: no-cache');
	header('Expires: 0');
	readfile($patchFile);
	$u->clearEvent();
	exit;
}

$step = max(1, intval($_GET['step']));
showsubmenusteps('menu_upgrade', [
	['upgrade_step1', $step == 1],
	['upgrade_step2', $step == 2],
	['upgrade_step3', $step == 3],
]);

if($step == 1) {
	savecache('newver', []);
	[$current, $remote, $new] = $u->getVersion();
	$tips = cplang('upgrade_tips');
	if($u->readmeUrl) {
		$tips .= cplang('upgrade_tips_readme', ['URL' => $u->readmeUrl]);
	}
	showtips($tips);
	if(!$new) {
		cpmsg('<h4 class="infotitle2">'.cplang('upgrade_latest').'</h4>', '', 'succeed');
	} else {
		cpmsg('<h4 class="infotitle2">'.cplang('upgrade_info').'</h4><br />'.$current.' &raquo; '.$remote, 'action=founder&operation=upgrade&step=2', 'form');
	}
} elseif($step == 2) {
	cpmsg('upgrade_waiting', 'action=founder&operation=upgrade&step=3', 'loading');
} elseif($step == 3) {
	$u->clearEvent();
	$u->check();
	$u->createPatch();
	$diff = $u->getCurrentDiff();
	if($diff) {
		$s = '<h4 class="infotitle3">'.cplang('upgrade_diff_notice').'</h4><br /><div style="text-align:left;max-height:400px;overflow-y:auto;">';
		$s .= implode('<br>', $diff);
		$s .= '</div>';
	}
	cpmsg('<h4 class="infotitle2">'.cplang('upgrade_patch_download').'</h4>', 'action=founder&operation=upgrade&downPatch=yes', 'form', extra: $s);
}
