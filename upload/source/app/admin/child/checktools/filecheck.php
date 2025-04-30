<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$homecheck = !empty($_GET['homecheck']);

if(!$homecheck) {
	$step = max(1, intval($_GET['step']));
	shownav('tools', 'nav_filecheck');
	showsubmenusteps('nav_filecheck', [
		['nav_filecheck_confirm', $step == 1],
		['nav_filecheck_verify', $step == 2],
		['nav_filecheck_completed', $step == 3]
	]);
} else {
	define('FOOTERDISABLED', true);
	$step = 3;
}

if($step == 1) {
	cpmsg(cplang('filecheck_tips_step1'), 'action=checktools&operation=filecheck&step=2', 'button', '', FALSE);
} elseif($step == 2) {
	cpmsg(cplang('filecheck_verifying'), 'action=checktools&operation=filecheck&step=3', 'loading', '', FALSE);
} elseif($step == 3) {

	if(!$discuzfiles = @file('./source/data/admincp/discuzfiles.md5')) {
		if(!$homecheck) {
			cpmsg('filecheck_nofound_md5file', '', 'error');
		} else {
			ajaxshowheader();
			ajaxshowfooter();
		}
	}

	$md5data = $md5datanew = $addlist = $dellist = $modifylist = $showlist = [];
	$cachelist = checkcachefiles('data/sysdata/');
	checkfiles('./', '', 0);
	checkfiles('config/', '', 1, 'config_global.php,config_ucenter.php');
	checkfiles('data/', '\.xml', 0);
	checkfiles('data/', '\.htm', 0);
	checkfiles('data/log/', '\.htm', 0);
	checkfiles('data/plugindata/', '\.htm', 0);
	checkfiles('data/download/', '\.htm', 0);
	checkfiles('data/addonmd5/', '\.htm', 0);
	checkfiles('data/avatar/', '\.htm', 0);
	checkfiles('data/cache/', '\.htm', 0);
	checkfiles('data/ipdata/', '\.htm|\.dat', 0);
	checkfiles('data/template/', '\.htm', 0);
	checkfiles('data/threadcache/', '\.htm', 0);
	checkfiles('template/', '');
	checkfiles('api/', '');
	checkfiles('source/', '', 1, 'discuzfiles.md5,plugin');
	checkfiles('source/app/plugin/', '', 1);
	checkfiles('static/', '');
	checkfiles('archiver/', '');


	table_common_cache::t()->insert([
		'cachekey' => 'checktools_filecheck',
		'cachevalue' => serialize(['dateline' => $_G['timestamp']]),
		'dateline' => $_G['timestamp'],
	], false, true);

	foreach($discuzfiles as $line) {
		$file = trim(substr($line, 34));
		$md5datanew[$file] = substr($line, 0, 32);
		if($md5datanew[$file] != $md5data[$file]) {
			$modifylist[$file] = $md5data[$file];
		}
		$md5datanew[$file] = $md5data[$file];
	}

	$weekbefore = TIMESTAMP - 604800;
	$md5data = is_array($md5data) ? $md5data : [];
	$md5datanew = is_array($md5datanew) ? $md5datanew : [];
	$addlist = array_merge(array_diff_assoc($md5data, $md5datanew), is_array($cachelist[2]) ? $cachelist[2] : []);
	$dellist = array_diff_assoc($md5datanew, $md5data);
	$modifylist = array_merge(array_diff_assoc($modifylist, $dellist), is_array($cachelist[1]) ? $cachelist[1] : []);
	$showlist = array_merge($md5data, $md5datanew, $cachelist[0]);
	$doubt = 0;
	$dirlist = $dirlog = [];
	foreach($showlist as $file => $md5) {
		$dir = dirname($file);
		if(is_array($modifylist) && array_key_exists($file, $modifylist)) {
			$fileststus = 'modify';
		} elseif(is_array($dellist) && array_key_exists($file, $dellist)) {
			$fileststus = 'del';
		} elseif(is_array($addlist) && array_key_exists($file, $addlist)) {
			$fileststus = 'add';
		} else {
			$filemtime = @filemtime($file);
			if($filemtime > $weekbefore) {
				$fileststus = 'doubt';
				$doubt++;
			} else {
				$fileststus = '';
			}
		}
		if(file_exists($file)) {
			$filemtime = @filemtime($file);
			$fileststus && $dirlist[$fileststus][$dir][basename($file)] = [number_format(filesize($file)).' Bytes', dgmdate($filemtime)];
		} else {
			$fileststus && $dirlist[$fileststus][$dir][basename($file)] = ['', ''];
		}
	}

	$modifiedfiles = count($modifylist);
	$deletedfiles = count($dellist);
	$unknownfiles = count($addlist);
	$doubt = intval($doubt);

	table_common_cache::t()->insert([
		'cachekey' => 'checktools_filecheck_result',
		'cachevalue' => serialize([$modifiedfiles, $deletedfiles, $unknownfiles, $doubt]),
		'dateline' => $_G['timestamp'],
	], false, true);

	if($homecheck) {
		ajaxshowheader();
		echo "<div><em class=\"".($modifiedfiles ? 'edited' : 'correct')."\">{$lang['filecheck_modify']}<span class=\"bignum\">$modifiedfiles</span></em>".
			"<em class=\"".($deletedfiles ? 'del' : 'correct')."\">{$lang['filecheck_delete']}<span class=\"bignum\">$deletedfiles</span></em>".
			"<em class=\"unknown\">{$lang['filecheck_unknown']}<span class=\"bignum\">$unknownfiles</span></em>".
			"<em class=\"unknown\">{$lang['filecheck_doubt']}<span class=\"bignum\">$doubt</span></em></div><p>".
			$lang['filecheck_last_homecheck'].': '.dgmdate(TIMESTAMP, 'u').' <a href="'.ADMINSCRIPT.'?action=checktools&operation=filecheck&step=3">['.$lang['filecheck_view_list'].']</a></p>';
		ajaxshowfooter();
	}

	$result = $resultjs = '';
	$dirnum = 0;
	foreach($dirlist as $status => $filelist) {
		$dirnum++;
		$class = $status == 'modify' ? 'edited' : ($status == 'del' ? 'del' : 'unknown');
		$result .= '<tbody id="status_'.$status.'" style="display:'.($status != 'modify' ? 'none' : '').'">';
		foreach($filelist as $dir => $files) {
			$result .= '<tr><td colspan="4"><div class="ofolder">'.$dir.'</div><div class="margintop marginbot">';
			foreach($files as $filename => $file) {
				$result .= '<tr><td><em class="files bold">'.$filename.'</em></td><td style="text-align: right">'.$file[0].'&nbsp;&nbsp;</td><td>'.$file[1].'</td><td><em class="'.$class.'">&nbsp;</em></td></tr>';
			}
		}
		$result .= '</tbody>';
		$resultjs .= '$(\'status_'.$status.'\').style.display=\'none\';';
	}

	$result .= '<script>function showresult(o) {'.$resultjs.'$(\'status_\' + o).style.display=\'\';}</script>';
	showtips('filecheck_tips');
	showboxheader('filecheck_completed');
	echo '<div>'.
		"<em class=\"edited\">{$lang['filecheck_modify']}: $modifiedfiles</em> ".($modifiedfiles > 0 ? "<a href=\"###\" onclick=\"showresult('modify')\">[{$lang['view']}]</a> " : '').
		" &nbsp; <em class=\"del\">{$lang['filecheck_delete']}: $deletedfiles</em> ".($deletedfiles > 0 ? "<a href=\"###\" onclick=\"showresult('del')\">[{$lang['view']}]</a> " : '').
		" &nbsp; <em class=\"unknown\">{$lang['filecheck_unknown']}: $unknownfiles</em> ".($unknownfiles > 0 ? "<a href=\"###\" onclick=\"showresult('add')\">[{$lang['view']}]</a> " : '').
		($doubt > 0 ? "&nbsp;&nbsp;&nbsp;&nbsp;<em class=\"unknown\">{$lang['filecheck_doubt']}: $doubt</em> <a href=\"###\" onclick=\"showresult('doubt')\">[{$lang['view']}]</a> " : '').
		"</div></div><div class=\"boxbody\">";
	showtableheader();
	showsubtitle(['filename', '', 'lastmodified', '']);
	echo $result;
	showtablefooter();
	showboxfooter();

}
	