<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class helper_forumperm {

	private $formula = '';
	private $permstr = '';

	private $formula_cells = [];


	function __construct($permstr) {
		$this->permstr = $permstr;
	}

	public function check($groupid = 0) {
		if($groupid) {
			return preg_match("/(^|\t)(".$groupid.")(\t|$)/", $this->permstr);
		}

		self::get_group();
		self::get_verify();
		self::get_tag();
		self::get_account();
		self::get_plugin();

		self::init_formula();

		return self::run_formula();
	}

	private function get_group() {
		global $_G;

		$this->formula_cells['g'.$_G['groupid']] = 1;
		$groupterms = dunserialize(getuserprofile('groupterms'));
		foreach(explode("\t", $_G['member']['extgroupids']) as $extgroupid) {
			if($extgroupid = intval(trim($extgroupid))) {
				if($groupterms['ext'][$extgroupid] && $groupterms['ext'][$extgroupid] < TIMESTAMP) {
					continue;
				}
				$this->formula_cells['g'.$extgroupid] = 1;
			}
		}
		$this->formula_cells['group'] = 1;
	}

	private function get_verify() {
		global $_G;

		if($_G['setting']['verify']['enabled']) {
			getuserprofile('verify1');
			foreach($_G['setting']['verify'] as $vid => $verify) {
				if(!$verify['available']) {
					continue;
				}
				if($_G['member']['verify'.$vid] == 1) {
					$this->formula_cells['v'.$vid] = 1;
					$this->formula_cells['verify'] = 1;
				}
			}
		}
	}

	private function get_tag() {
		global $_G;

		static $member_tags = null;
		if($member_tags === null) {
			$member_tags = table_common_tagitem::t()->select($tagperm, $_G['uid'], 'uid');
		}

		foreach($member_tags as $row) {
			$this->formula_cells['t'.$row['tagid']] = 1;
			$this->formula_cells['tag'] = 1;
		}
	}


	private function get_plugin() {
		global $_G;

		if(empty($_G['setting']['plugins']['perm'])) {
			return;
		}

		foreach($_G['setting']['plugins']['perm'] as $k => $v) {
			if(!class_exists($v['class'])) {
				continue;
			}
			$c = new $v['class']();
			if(!method_exists($c, 'fetch_perm')) {
				continue;
			}
			if($c->fetch_perm($_G['uid'])) {
				$this->formula_cells['p_'.$k] = 1;
				$this->formula_cells['plugin_'.$v['pluginid']] = 1;
			}
		}
	}

	private function get_account() {
		global $_G;

		static $member_accounts = null;
		if($member_accounts === null) {
			$member_accounts = table_common_member_account::t()->fetch_all_by_uid($_G['uid'], false);
		}

		foreach($member_accounts as $row) {
			$this->formula_cells['a'.$row['atype']] = 1;
			$this->formula_cells['account'] = 1;
		}
	}

	private function init_formula() {
		if(preg_match("/(^|\t)_formula\[(.+?)\]/", $this->permstr, $r)) {
			$this->permstr = $r[0];
			$this->formula = $r[2];
		} else {
			$this->formula = 'group or tag or verify or account';
		}

		$formulaitems = [];
		foreach(explode("\t", $this->permstr) as $item) {
			if(is_numeric($item)) {
				$formulaitems['g'.$item] = 'g'.$item;
			} elseif(in_array(substr($item, 0, 1), ['a', 't', 'v']) && is_numeric(substr($item, 1))) {
				$formulaitems[$item] = $item;
			} elseif(preg_match('/^_([a|t|v])\[(.+?)\]$/', $item, $r)) {
				$formulaitem = [];
				foreach(explode(',', $r[2]) as $v) {
					$formulaitem[] = $r[1].$v;
					unset($formulaitems[$r[1].$v]);
				}
				$formulaitems[] = implode(' and ', $formulaitem);
			} else if(preg_match('/^p_\w+$/', $item) || preg_match('/^plugin_\w+$/', $item)) {
				$formulaitems[$item] = $item;
			}
		}
		if($formulaitems) {
			$this->formula = implode(' or ', $formulaitems);
		}
	}

	private function run_formula() {
		$formula = [];
		$p = 0;
		$this->formula = str_replace(['(', ')'], [' ( ', ' ) '], $this->formula);
		foreach(explode(' ', $this->formula) as $c) {
			if(!$c) {
				continue;
			} elseif(preg_match('/^(or|and)$/', $c)) {
				$formula[] = str_replace(['or', 'and'], ['||', '&&'], $c);
			} elseif(preg_match('/^(g|t|v|a|o)-?\d+$/', $c) ||
				preg_match('/^(group|tag|verify|account)$/', $c) ||
				preg_match('/^p_\w+$/', $c) || preg_match('/^plugin_\w+$/', $c)) {
				$formula[] = !empty($this->formula_cells[$c]) ? 'TRUE' : 'FALSE';
			} elseif($c == '(') {
				$p++;
				$formula[] = '(';
			} elseif($c == ')') {
				$p--;
				$formula[] = ')';
			} else {
				return false;
			}
		}
		if($p != 0) {
			return false;
		}
		$formulastr = implode(' ', $formula);
		@eval("\$result = ($formulastr) ? TRUE : FALSE;");
		return $result;
	}

}