<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class editorblock_paragraph {

	var $version = '1.0.8';
	var $name = '文本段落(增强版)';
	var $available = 1; // 默认启用状态 0:不启用 1:启用
	var $columns = 1; //  默认是否支持多列 0:不支持 1:支持
	var $identifier = 'paragraph';
	var $description = '文本段落(增强版)内容区块，启用后会自动覆盖默认文本段落区块';
	var $filename = 'paragraph';
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
        "alignment": "left", // 对齐方式
        "text": "content" // 内容
    },
    "id": "ZT8S70Q34G", // 区块id
    "type": "paragraph" // 区块类型
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
   tools_paragraph: {
      paragraph: {
         class: Paragraph,
         inlineToolbar: true,
         config: {
            placeholder: "请输入正文内容, 或点击加号添加功能区块"
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
.ce-paragraph {
    line-height: 1.6em;
    outline: none;
    text-indent: 2em;
}
.ce-paragraph--right {
    text-align: right;
}
.ce-paragraph--center {
    text-align: center;
}
.ce-paragraph--left {
    text-align: left;
}

.ce-paragraph--justify {
    text-align: justify;
}

.ce-paragraph-text-indent {
    text-align: justify;
}
.ce-paragraph[data-placeholder]:empty::before{
  content: attr(data-placeholder);
  color: #707684;
  font-weight: normal;
  opacity: 0;
}

/** Show placeholder at the first paragraph if Editor is empty */
.codex-editor--empty .ce-block:first-child .ce-paragraph[data-placeholder]:empty::before {
  opacity: 1;
}

.codex-editor--toolbox-opened .ce-block:first-child .ce-paragraph[data-placeholder]:empty::before,
.codex-editor--empty .ce-block:first-child .ce-paragraph[data-placeholder]:empty:focus::before {
  opacity: 0;
}

.ce-paragraph p:first-of-type{
    margin-top: 0;
}

.ce-paragraph p:last-of-type{
    margin-bottom: 0;
}


.svg-icon {
    width: 1em;
    height: 1em;
}

.svg-icon path,
.svg-icon polygon,
.svg-icon rect {
    fill: #4691f6;
}

.svg-icon circle {
    stroke: #4691f6;
    stroke-width: 1;
}
</style>
EOF;

	}

	function getParser($block = []) {
		global $_G;
		return <<<EOF
<div class="ce-block ce-block--focused" data-id="{id}" [if tunes.anchorTune.anchor=notnull]id="{tunes.anchorTune.anchor}"[/if]>
	<div class="ce-block__content">
		<div class="ce-paragraph cdx-block ce-paragraph--{data.alignment}">{data.text}</div>
	</div>
</div>
EOF;
	}

}