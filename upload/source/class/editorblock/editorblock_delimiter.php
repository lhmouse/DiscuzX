<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class editorblock_delimiter {

	var $version = '1.0.1';
	var $name = '分隔符';
	var $available = 1; // 默认启用状态 0:不启用 1:启用
	var $columns = 1; //  默认是否支持多列 0:不支持 1:支持
	var $identifier = 'delimiter';
	var $description = '分隔符';
	var $filename = 'delimiter';
	var $copyright = '<a href="https://addon.dismall.com/developer-32563.html" target="_blank">云诺</a>';
	var $type = '0'; // 0:数据类型 1:图片类型 2:附件类型

	function __construct() {

	}

	function getsetting() {
		global $_G;
		$settings = [];
		return $settings;
	}

	function setsetting(&$blocknew, &$parameters) {
	}

	function getParameter() {
		return <<<EOF
{
    "data": {
    },
    "id": "ZT8S70Q34G", // 区块id
    "type": "delimiter" // 区块类型
}
EOF;
	}

	/*
	 * 结构(左顶头)：
	 * 	{
	 * 		tools_$identifier: {
	 * 			$identifier: {
	 * 				...
	 * 			}
	 * 		}
	 * 	}
	 */
	function getConfig() {
		return <<<EOF
{
   tools_delimiter: {
      delimiter: Delimiter,
   }
}
EOF;
	}


	function getI18n() {
		return <<<EOF

EOF;
	}

	function getStyle() {
		return <<<EOF
<style type="text/css">
.ce-block {
    margin-bottom: 20px;
}
.ce-block__content,.ce-toolbar__content {
	/* max-width:calc(100% - 50px) */
	margin-left: auto;
    margin-right: auto;
}
.ce-delimiter {
    line-height: 1.6em;
    width: 100%;
    text-align: center;
}

.ce-delimiter:before {
    display: inline-block;
    content: "***";
    font-size: 30px;
    line-height: 65px;
    height: 30px;
    letter-spacing: 0.2em;
}
</style>
EOF;

	}

	function getParser($block = []) {
		global $_G;
		return <<<EOF
<div class="ce-block ce-block--focused" data-id="{id}">
	<div class="ce-block__content">
		<div class="ce-delimiter cdx-block"></div>
	</div>
</div>
EOF;
	}

}