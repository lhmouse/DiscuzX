<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_sample {

	function common() {
		//echo lang('plugin/sample', 'english');
	}

}

class mobileplugin_sample extends plugin_sample {

}

// 编辑器新嵌入点

class plugin_sample_forum {

	function post_editor_body_output() {
		//return '';
	}

}

class plugin_sample_home {

	function spacecp_blog_editor_body_output() {
		//return '';
	}

}

class plugin_sample_portal {

	function portalcp_article_editor_body() {
		//return '';
	}

}