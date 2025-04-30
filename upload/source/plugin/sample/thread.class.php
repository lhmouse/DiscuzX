<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class threadplugin_sample {
	var $name = 'XX主题';            //主题类型名称
	var $iconfile = 'icon.gif';        //发布主题链接中的前缀图标
	var $buttontext = '发布xx主题';    //发帖时按钮文字

	function newthread($fid) {
		return '';
	}

	function newthread_submit($fid) {
	}

	function newthread_submit_end($fid, $tid) {
	}

	function editpost($fid, $tid) {
		return '';
	}

	function editpost_submit($fid, $tid) {
	}

	function editpost_submit_end($fid, $tid) {
	}

	function newreply_submit_end($fid, $tid) {
	}

	function viewthread($tid) {
		return '';
	}
}
