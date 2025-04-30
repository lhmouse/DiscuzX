<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class editorblock_alert {

	var $version = '1.0.5';
	var $name = 'Alert提示';
	var $available = 1; // 默认启用状态 0:不启用 1:启用
	var $columns = 1; //  默认是否支持多列 0:不支持 1:支持
	var $identifier = 'alert';
	var $description = 'Alert提示内容区块';
	var $filename = 'editorjs-alert';
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
        "align": "left", // 对齐方式
        "message": "content", // 内容
        "type": "primary" // 提示类型
    },
    "id": "zKwG6DQyeb", // 区块id
    "type": "alert" // 区块类型
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
   tools_alert: {
      alert: {
         class: Alert,
         inlineToolbar: true,
         shortcut: 'CMD+SHIFT+A',
         config: {
            defaultType: 'primary',
            messagePlaceholder: '请输入内容...',
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
.cdx-alert {
	position:relative;
	padding:10px;
	border-radius:5px;
	margin-bottom:10px
}
.cdx-alert-primary {
	background-color:#ebf8ff;
	border:1px solid #4299e1;
	color:#2b6cb0
}
.cdx-alert-secondary {
	background-color:#f7fafc;
	border:1px solid #cbd5e0;
	color:#222731
}
.cdx-alert-info {
	background-color:#e6fdff;
	border:1px solid #4cd4ce;
	color:#00727c
}
.cdx-alert-success {
	background-color:#f0fff4;
	border:1px solid #68d391;
	color:#2f855a
}
.cdx-alert-warning {
	background-color:#fffaf0;
	border:1px solid #ed8936;
	color:#c05621
}
.cdx-alert-danger {
	background-color:#fff5f5;
	border:1px solid #fc8181;
	color:#c53030
}
.cdx-alert-light {
	background-color:#fff;
	border:1px solid #edf2f7;
	color:#1a202c
}
.cdx-alert-dark {
	background-color:#2d3748;
	border:1px solid #1a202c;
	color:#d3d3d3
}
.cdx-alert-align-left {
	text-align:left
}
.cdx-alert-align-center {
	text-align:center
}
.cdx-alert-align-right {
	text-align:right
}
.cdx-alert__message {
	outline:none
}
.cdx-alert [contentEditable=true][data-placeholder]::before {
	position:absolute;
	content:attr(data-placeholder);
	color:#707684;
	font-weight:normal;
	opacity:0
}
.cdx-alert [contentEditable=true][data-placeholder]:empty::before {
	opacity:1
}
.cdx-alert [contentEditable=true][data-placeholder]:empty:focus::before {
	opacity:0
}
.ce-popover__item[data-item-name=alert-primary] .ce-popover__item-icon svg #background {
	fill:#ebf8ff;
	stroke:#4299e1
}
.ce-popover__item[data-item-name=alert-primary] .ce-popover__item-icon svg #content {
	fill:#2b6cb0
}
.ce-popover__item[data-item-name=alert-secondary] .ce-popover__item-icon svg #background {
	fill:#f7fafc;
	stroke:#cbd5e0
}
.ce-popover__item[data-item-name=alert-secondary] .ce-popover__item-icon svg #content {
	fill:#222731
}
.ce-popover__item[data-item-name=alert-info] .ce-popover__item-icon svg #background {
	fill:#e6fdff;
	stroke:#4cd4ce
}
.ce-popover__item[data-item-name=alert-info] .ce-popover__item-icon svg #content {
	fill:#00727c
}
.ce-popover__item[data-item-name=alert-success] .ce-popover__item-icon svg #background {
	fill:#f0fff4;
	stroke:#68d391
}
.ce-popover__item[data-item-name=alert-success] .ce-popover__item-icon svg #content {
	fill:#2f855a
}
.ce-popover__item[data-item-name=alert-warning] .ce-popover__item-icon svg #background {
	fill:#fffaf0;
	stroke:#ed8936
}
.ce-popover__item[data-item-name=alert-warning] .ce-popover__item-icon svg #content {
	fill:#c05621
}
.ce-popover__item[data-item-name=alert-danger] .ce-popover__item-icon svg #background {
	fill:#fff5f5;
	stroke:#fc8181
}
.ce-popover__item[data-item-name=alert-danger] .ce-popover__item-icon svg #content {
	fill:#c53030
}
.ce-popover__item[data-item-name=alert-light] .ce-popover__item-icon svg #background {
	fill:#fff;
	stroke:#edf2f7
}
.ce-popover__item[data-item-name=alert-light] .ce-popover__item-icon svg #content {
	fill:#1a202c
}
.ce-popover__item[data-item-name=alert-dark] .ce-popover__item-icon svg #background {
	fill:#2d3748;
	stroke:#1a202c
}
.ce-popover__item[data-item-name=alert-dark] .ce-popover__item-icon svg #content {
	fill:#d3d3d3
}
</style>
EOF;

	}

	function getParser($block = []) {
		global $_G;
		return <<<EOF
<div class="ce-block ce-block--focused" data-id="{id}" [if tunes.anchorTune.anchor=notnull]id="{tunes.anchorTune.anchor}"[/if]>
	<div class="ce-block__content">
		<div class="cdx-alert cdx-alert-{data.type} cdx-alert-align-{data.align}">
			<div class="cdx-alert__message">{data.message}</div>
		</div>
	</div>
</div>
EOF;
	}

}