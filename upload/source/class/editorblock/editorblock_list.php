<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class editorblock_list {

	var $version = '1.0.3';
	var $name = '列表';
	var $available = 1; // 默认启用状态 0:不启用 1:启用
	var $columns = 1; //  默认是否支持多列 0:不支持 1:支持
	var $identifier = 'list';
	var $description = '列表区块';
	var $filename = 'list';
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
    "data" : {
        "style" : "unordered",
        "items" : [
            "This is a block-styled editor",
            "Clean output data",
            "unordered"
        ]
    },
    "id": "ZT8S70Q34G", // 区块id
    "type": "list" // 区块类型
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
   tools_list: {
      list: {
		  class: List,
		  inlineToolbar: true,
		  config: {
			defaultStyle: 'unordered'
		  },
          tunes: ['anchorTune', 'hideTune']
	  },
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
.cdx-list {
    margin: 0;
    padding-left: 40px;
    outline: none;
}

    .cdx-list__item {
        padding: 5.5px 0 5.5px 3px;
        line-height: 1.6em;
    }

    .cdx-list--unordered {
        list-style: disc;
    }

	.cdx-list--unordered .cdx-list__item {
		list-style-type:disc;
		list-style-position:inside;
    }
    
    .cdx-list--ordered {
        list-style: decimal;
    }
    
    .cdx-list--ordered .cdx-list__item {
		list-style-type:decimal;
		list-style-position:inside;
    }

    .cdx-list-settings {
        display: flex;
    }

    .cdx-list-settings .cdx-settings-button {
            width: 50%;
        }
</style>
EOF;

	}

	function getParser($block = []) {
		global $_G;
		return <<<EOF
<div class="ce-block ce-block--focused" data-id="{id}" [if tunes.anchorTune.anchor=notnull]id="{tunes.anchorTune.anchor}"[/if]>
    <div class="ce-block__content">
    	[if data.style=ordered]
        <ol class="cdx-block cdx-list cdx-list--{data.style}">
        	[loopobject data.items]
                <li class="cdx-list__item">{loopobjectdata}</li>
            [/loopobject]
        </ol>
        [/if]
        [if data.style=unordered]
        <ul class="cdx-block cdx-list cdx-list--{data.style}">
        	[loopobject data.items]
                <li class="cdx-list__item">{loopobjectdata}</li>
            [/loopobject]
        </ul>
        [/if]
    </div>
</div>
EOF;
	}

}