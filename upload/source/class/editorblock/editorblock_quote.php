<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class editorblock_quote {

	var $version = '1.0.1';
	var $name = '引言';
	var $available = 1; // 默认启用状态 0:不启用 1:启用
	var $columns = 1; //  默认是否支持多列 0:不支持 1:支持
	var $identifier = 'quote';
	var $description = '引言';
	var $filename = 'quote';
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
        "text" : "引言内容.",
        "caption" : "引言描述",
        "alignment" : "left"
    }
    "id": "ZT8S70Q34G", // 区块id
    "type": "quote" // 区块类型
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
   tools_quote: {
        quote: {
		  class: Quote,
		  inlineToolbar: true,
		  shortcut: 'CMD+SHIFT+O',
		  config: {
			quotePlaceholder: '请输入引言内容',
			captionPlaceholder: '请输入引言作者',
		  },
          tunes: ['anchorTune']
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
.cdx-quote-icon svg {
  transform: rotate(180deg);
}

.cdx-quote {
  margin: 0;
      background-color: #f8f8f8;
    padding: 16px;
    border-radius: 10px;
}

.cdx-quote__text {
  min-height: 30px;
  margin-bottom: 10px;
}

.cdx-quote__caption {
	margin-top: 20px;
}

.cdx-quote__caption-left {
	text-align: left;
}

.cdx-quote__caption-center {
	text-align: center;
}

.cdx-quote__caption-right {
	text-align: right;
}

.cdx-quote [contentEditable=true][data-placeholder]::before{
  position: absolute;
  content: attr(data-placeholder);
  color: #707684;
  font-weight: normal;
  opacity: 0;
}

.cdx-quote [contentEditable=true][data-placeholder]:empty::before {
  opacity: 1;
}

.cdx-quote [contentEditable=true][data-placeholder]:empty:focus::before {
  opacity: 0;
}


.cdx-quote-settings {
  display: flex;
}

.cdx-quote-settings .cdx-settings-button {
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
                <blockquote class="cdx-block cdx-quote">
                    <div class="cdx-input cdx-quote__text">
                    {data.text}
                    </div>
                    <div class="cdx-input cdx-quote__caption cdx-quote__caption-{data.alignment}">
                    {data.caption}
                    </div>
                </blockquote>
            </div>
</div>
EOF;
	}

}