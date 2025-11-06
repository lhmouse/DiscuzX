<?php

namespace admin;

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

/**
 * showcomponent('setname', 'varname', $value, 'component_perm', 'comment', [
 *      'permtype' => ['group', 'verify', 'account', 'tag', 'org', 'plugin'],    //显示的权限类型，留空为显示全部权限类型
 *      'formula' => true    //按照权限公式表达式方式显示
 * ]);
 */
class component_perm {

	var $name = '权限选择器';

	var $desc = '
<pre>
{	
      "permtype": ["group", "verify", "account", "medal", "magic", "tag", "org", "plugin"],   
      "formula": true
}
permtype: 显示的权限类型，留空为显示全部权限类型
formula: 按照权限公式表达式方式显示
</pre>';

	function show(&$var, &$extra) {
		static $css = null;
		if($css === null) {
			$css = '<style type="text/css">
.component_perm .pbox {
	padding: 10px;
	border: 1px dotted var(--admincp-borderc);
	margin-top: 10px;
	clear: both;
}
.component_perm label {
	float: left;
	width: 200px;
	line-height: 35px;
	cursor: pointer;
}
.component_perm .formula label {
	line-height: 15px !important;
	width: 180px;
}
.component_perm .formula a {
	margin-right: 5px;
}
.component_perm div {
	margin-bottom: 5px;
}
.component_perm .formula div {
	margin-bottom: 5px;
}
.component_perm .formula textarea {
	width: 80%;
}
.component_perm p {
	clear: both;
}
.component_perm .search {
	margin-left: 5px;
	width: 80px;
}
.component_perm.formula .search {
	margin-left: 0;
}
.component_perm .ppreview {
	float: left;
	width: 400px;
	padding: 6px 10px;
	font-size: 100%;
	border: 1px solid;
	border-color: var(--admincp-borderc);
	background: var(--admincp-bgc);
	color: var(--admincp-fa);
	border-radius: 3px;
	margin-bottom: 10px;
}
.component_perm .pswitch {
	float: left;
	margin: 5px 0 0 5px;
}
.component_perm .pdesc {
	clear: both;
}
</style>';
		} else {
			$css = '';
		}
		if(preg_match('/^_formula\[(.+?)\]$/', $var['value'], $r)) {
			$var['value'] = $r[1];
		} else {
			$var['value'] = '';
		}
		$conf = is_string($var['extra']) ? json_decode($var['extra'], true) : $var['extra'];
		$data = [];
		$permarray = ['group', 'verify', 'account', 'plugin', 'tag'];
		foreach($permarray as $permfile) {
			if(!$conf['permtype'] || in_array($permfile, $conf['permtype'])) {
				call_user_func_array([$this, '_gen_'.$permfile], [&$data, $var['value'], $var['variable'], $conf]);
			}
		}

		$cv = $perms = '';
		if(empty($conf['formula'])) {
			$perms .= '<div id="pset_'.$var['variable'].'" class="pbox" style="display: none">';
			foreach($data as $type => $items) {
				$perms .= '<div><p><b>'.$type.'</b>';
				if(isset($items['_'])) {
					$perms .= $items['_'];
					unset($items['_']);
				}
				$perms .= '</p>';
				foreach($items as $item) {
					!empty($item[2]) && $cv .= '['.$item[1].'] ';
					$perms .= '<label><input class="checkbox" name="'.$var['variable'].'[]" onclick="perm_preview(\''.$var['variable'].'\', \''.$item[1].'\', 0)" value="'.$item[0].'" type="checkbox"'.(!empty($item[2]) ? ' checked' : '').' />'.$item[1].'</label>';
				}
				$perms .= '</div><p></p>';
			}
		} else {
			$perms .= '<div id="pset_'.$var['variable'].'" class="pbox formula threadprofilenode" style="display: none">';
			foreach($data as $type => $items) {
				$perms .= '<div><label><b>'.$type.'</b></label>';
				if(isset($items['_'])) {
					$perms .= '<label>'.$items['_'].'</label>';
					unset($items['_']);
				}
				foreach($items as $item) {
					$perms .= '<a href="javascript:;" onclick="perm_add_formula(\''.$var['variable'].'\', \''.$item[0].'\')">'.$item[1].'('.$item[0].')</a> ';
				}
				$perms .= '<p></p></div>';
			}
			$cv = dhtmlspecialchars($var['value']);
			$perms .= '<label><b>'.cplang('forums_edit_perm_formula').'</b></label><textarea rows="6" onkeyup="textareasize(this, 0);perm_preview(\''.$var['variable'].'\', this.value, 1)" onkeydown="textareakey(this, event)" name="'.$var['variable'].'" id="'.$var['variable'].'" cols="50" class="tarea">'.$cv.'</textarea>';
		}
		$current = '<div class="ppreview" id="ppreview_'.$var['variable'].'">'.$cv.'&nbsp;</div>'.
			'<a href="javascript:;" onclick="display(\'pset_'.$var['variable'].'\')" class="pswitch">'.cplang('config').'</a>';
		$description = $var['description'] ? '<div class="pdesc tips2">'.$var['description'].'</div>' : '';
		$var['type'] = $css.'<div class="component_perm">'.$current.$description.$perms.'</div>';
		$var['widemode'] = true;
	}

	function serialize(&$value) {
		$s = is_array($value) ? implode(' or ', $value) : $value;
		$this->_check($s);
		$value = '_formula['.$s.']';
	}

	private function _check($s) {
		global $_G;

		$check = true;
		$p = 0;
		$permformula = str_replace(['(', ')'], [' ( ', ' ) '], $s);
		$s = '';
		foreach(explode(' ', $permformula) as $c) {
			if(!$c) {
				continue;
			} elseif(preg_match('/^(group|tag|verify|account|medal|magic|org|or|and)$/', $c)) {
				if(in_array($c, ['group', 'tag', 'verify', 'account', 'medal', 'magic', 'org'])) {
					$s .= '$_c[\''.$c.'\'] ';
				} else {
					$s .= $c.' ';
				}
			} elseif(preg_match('/^(g|t|v|a|m|i|o)-?\d+$/', $c)) {
				$s .= '$_c[\''.$c.'\'] ';
			} elseif(preg_match('/^O-?(\d+)\[.*?\]$/', $c)) {
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
			cpmsg('forums_permformula_error', '', 'error');
		}

		$s .= ';';
		$_c = [];
		set_exception_handler(function() {
			cpmsg('forums_permformula_error', '', 'error');
		});
		eval($s);
		restore_exception_handler();
	}

	private function _gen_group(&$data, $value, $variable, $extra) {
		static $groups = null;
		if($groups === null) {
			$groups = [];
			$query = \table_common_usergroup::t()->range_orderby_credit();
			foreach($query as $group) {
				if(!empty($extra['hide']['group']) && in_array($group['groupid'], $extra['hide']['group'])) {
					continue;
				}
				$group['type'] = $group['type'] == 'special' && $group['radminid'] ? 'specialadmin' : $group['type'];
				$groups[$group['type']][] = $group;
			}
		}
		foreach(['member', 'special', 'specialadmin', 'system'] as $type) {
			$tgroups = is_array($groups[$type]) ? $groups[$type] : [];
			if($tgroups) {
				foreach($tgroups as $group) {
					$data[cplang('usergroups_'.$type)][] = ['g'.$group['groupid'], $group['grouptitle'], str_contains(' '.$value.' ', " g{$group['groupid']} ")];
				}
			}
		}
	}

	private function _gen_verify(&$data, $value, $variable, $extra) {
		global $_G;

		foreach($_G['setting']['verify'] as $vid => $verify) {
			if(!empty($extra['hide']['verify']) && in_array($vid, $extra['hide']['verify'])) {
				continue;
			}
			if(!$verify['available']) {
				continue;
			}
			$data[cplang('forums_edit_perm_verify')][] = ['v'.$vid, $verify['title'], str_contains(' '.$value.' ', " v$vid ")];
		}
	}

	private function _gen_account(&$data, $value, $variable, $extra) {
		$interfaces = \account_base::getInterfaces();
		foreach($interfaces as $interface) {
			if(!empty($extra['hide']['account']) && in_array($interface, $extra['hide']['account'])) {
				continue;
			}
			$atype = \account_base::Interfaces_aType[$interface];
			if(empty($atype) && str_starts_with($interface, 'plugin_')) {
				$atype = \account_base::getAccountType(substr($interface, 7));
			}
			if(empty($atype)) {
				continue;
			}
			$data[cplang('account')][] = ['a'.$atype, \account_base::getName($interface), str_contains(' '.$value.' ', " a$atype ")];
		}
	}

	private function _gen_tag(&$data, $value, $variable, $extra) {
		static $tags = null;
		if(empty($extra['formula'])) {
			if($tags === null) {
				$tags = \table_common_tag::t()->fetch_all_by_status(3);
			}
			foreach($tags as $tag) {
				if(!empty($extra['hide']['tag']) && in_array($tag['tagid'], $extra['hide']['tag'])) {
					continue;
				}
				if(str_contains(' '.$value.' ', " t$tag[tagid] ")) {
					$data[cplang('forums_edit_perm_usertag')][] = ['t'.$tag['tagid'], $tag['tagname'], true];
				}
			}
		}
		if(!$tags) {
			return;
		}
		$id = 'st'.random(4);
		$data[cplang('forums_edit_perm_usertag')]['_'] = '<a id="'.$id.'" class="search" href="javascript:;" style="vertical-align: middle" onclick="perm_search(\'tag/'.$variable.'\', \''.$id.'\')">'.cplang('add').'</a>';
	}

	private function _gen_plugin(&$data, $value, $variable, $extra) {
		global $_G;

		foreach($_G['setting']['plugins']['perm'] as $c => $v) {
			$pluginid = $v['pluginid'];
			$data[$_G['setting']['plugins']['name'][$pluginid] ?? $pluginid][] = ['p_'.$c, lang('plugin/'.$pluginid, $v['name']), str_contains(' '.$value.' ', " p_$c ")];
		}
	}

}