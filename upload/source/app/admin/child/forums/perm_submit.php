<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!$multiset) {
	if(!checkformulaperm($_GET['formulapermnew'])) {
		cpmsg('forums_formulaperm_error', '', 'error', ['frame' => $multiset]);
	}
	$_GET['formulapermnew'] = trim($_GET['formulapermnew']);
	$formulapermary[0] = $_GET['formulapermnew'];
	$formulapermary[1] = preg_replace(
		['/(digestposts|posts|threads|oltime|extcredits[1-8])/', '/(regdate|regday|regip|lastip|buyercredit|sellercredit|field\d+)/'],
		["getuserprofile('\\1')", "\$memberformula['\\1']"],
		$_GET['formulapermnew']);
	$formulapermary['message'] = $_GET['formulapermmessagenew'];
} else {
	$formulapermary = dunserialize($forum['formulaperm']);
}
$formulapermary['medal'] = $_GET['medalnew'];
$formulapermary['users'] = $_GET['formulapermusersnew'];
$formulapermary['viewtype'] = $_GET['viewtypenew'];
if(!empty($_GET['viewtypenew'])) {
	$formulapermary['viewtype_gids'] = $_GET['viewperm'];
	$formulapermary['viewtype_fid'] = $fid;
}
$_GET['formulapermnew'] = serialize($formulapermary);

foreach($perms as $perm) {
	$_GET[''.$perm.'new'] = is_array($_GET[''.$perm]) && !empty($_GET[''.$perm]) ? "\t".implode("\t", $_GET[''.$perm])."\t" : '';
	$mpermval = [];
	if(!empty($_GET['maccount'][$perm])) {
		foreach($_GET[''.$_GET['maccount'][$perm]] as $row) {
			$mpermtype = substr($row, 0, 1);
			if($mpermtype == 'a') {
				$mpermval[$mpermtype][] = substr($row, 1);
			}
		}
	}
	if(!empty($_GET['mverify'][$perm])) {
		foreach($_GET[''.$_GET['mverify'][$perm]] as $row) {
			$mpermtype = substr($row, 0, 1);
			if($mpermtype == 'v') {
				$mpermval[$mpermtype][] = substr($row, 1);
			}
		}
	}
	if(!empty($_GET['mtag'][$perm])) {
		foreach($_GET[''.$_GET['mtag'][$perm]] as $row) {
			$mpermtype = substr($row, 0, 1);
			if($mpermtype == 't') {
				$mpermval[$mpermtype][] = substr($row, 1);
			}
		}
	}
	foreach($mpermval as $mtype => $val) {
		$_GET[''.$perm.'new'] .= '_'.$mtype.'['.implode(',', $val).']'."\t";
	}

	if(!empty($_GET['permformula'][$perm])) {
		$check = true;
		$p = 0;
		$permformula = str_replace(['(', ')'], [' ( ', ' ) '], $_GET['permformula'][$perm]);
		$s = '';
		foreach(explode(' ', $permformula) as $c) {
			if(!$c) {
				continue;
			} elseif(preg_match('/^(group|tag|verify|account|org|or|and)$/', $c)) {
				if(in_array($c, ['group', 'tag', 'verify', 'account', 'org'])) {
					$s .= '$_c[\''.$c.'\'] ';
				} else {
					$s .= $c.' ';
				}
			} elseif(preg_match('/^(g|t|v|a|o)-?\d+$/', $c)) {
				$s .= '$_c[\''.$c.'\'] ';
			} elseif(str_starts_with($c, 'p_') && isset($_G['setting']['plugins']['perm'][substr($c, 2)])) {
				$s .= '$_c[\''.$c.'\'] ';
			} elseif(str_starts_with($c, 'plugin_') && in_array(substr($c, 7), $_G['setting']['plugins']['available'])) {
				$s .= '$_c[\''.$c.'\'] ';
			} elseif($c == '(') {
				$s .= '( ';
				$p++;
			} elseif($c == ')') {
				$s .= ') ';
				$p--;
			} else {
				$check = false;
			}
		}

		if(!$check || $p != 0) {
			cpmsg('forums_permformula_error', '', 'error', ['frame' => $multiset]);
		}

		$_c = [];
		set_exception_handler(function() {
			cpmsg('forums_permformula_error', '', 'error', ['frame' => $GLOBALS['multiset']]);
		});
		@eval("\$result = ($s) ? TRUE : FALSE;");
		restore_exception_handler();

		$_GET[''.$perm.'new'] .= '_formula['.$_GET['permformula'][$perm].']'."\t";
	}
}
