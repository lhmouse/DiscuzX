<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class editorblock_codeflask {

	var $version = '1.0.3';
	var $name = '代码';
	var $available = 1; // 默认启用状态 0:不启用 1:启用
	var $columns = 1; //  默认是否支持多列 0:不支持 1:支持
	var $identifier = 'codeflask';
	var $description = '代码区块';
	var $filename = 'codeflask';
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
            "id": "mAPnw-xzCD",
            "type": "codeflask",
            "data": {
                "code": "// Hello World\n{\"aa\": \"bb\"}",
                "language": "json",
                "showlinenumbers": true,
                "length": 2,
            }
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
   tools_codeflask: {
      codeflask: {
         class : editorjsCodeflask,
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
    position: relative;
}

.editorjs-codeFlask_Wrapper {
    height: 200px;
	border: 1px solid #dcdfe6;
	border-radius: 5px;
	background-color: #f0f2f5;
    margin-bottom: 10px;
}

.editorjs-codeFlask_Wrapper .codeflask {
	border-radius: 5px;
	background: none;
}


.editorjs-codeFlask_Wrapper .editorjs-codeFlask_LangDisplay {
	position: absolute;
	height: 20px;
	line-height: 20px;
	font-size: 10px;
	color: #999;
	background-color: #dcdfe6;
	padding: 5px;
	padding-left: 10px;
	padding-right: 25px;
	right: 0;
	bottom: 0;
	border-bottom-right-radius: 5px;
	border-top-left-radius: 5px;
}

.editorjs-codeFlask_Wrapper .codeflask.codeflask--has-line-numbers:before{
    background-color: #dcdfe6;
}


  .codeflask {
    position: absolute;
    width: 100%;
    height: 100%;
    overflow: hidden;
  }

  .codeflask, .codeflask * {
    box-sizing: border-box;
  }

  .codeflask__pre {
    pointer-events: none;
    z-index: 3;
    overflow: hidden;
  }

  .codeflask__textarea {
    background: none;
    border: none;
    color: #fff;
    z-index: 1;
    resize: none;
    font-family: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, Courier, monospace;
    -webkit-appearance: pre;
    caret-color: #111;
    z-index: 2;
    width: 100%;
    height: 100%;
  }

  .codeflask--has-line-numbers .codeflask__textarea {
    width: calc(100% - 40px);
  }

  .codeflask__code {
    display: block;
    font-family: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, Courier, monospace;
    overflow: hidden;
  }

  .codeflask__flatten {
    padding: 10px;
    font-size: 13px;
    line-height: 20px;
    white-space: pre;
    position: absolute;
    top: 0;
    left: 0;
    overflow: auto;
    margin: 0 !important;
    outline: none;
    text-align: left;
  }

  .codeflask--has-line-numbers .codeflask__flatten {
    width: calc(100% - 40px);
    left: 40px;
  }

  .codeflask__line-highlight {
    position: absolute;
    top: 10px;
    left: 0;
    width: 100%;
    height: 20px;
    background: rgba(0,0,0,0.1);
    z-index: 1;
  }

  .codeflask__lines {
    padding: 10px 4px;
    font-size: 12px;
    line-height: 20px;
    font-family: 'Cousine', monospace;
    position: absolute;
    left: 0;
    top: 0;
    width: 40px;
    height: 100%;
    text-align: right;
    color: #999;
    z-index: 2;
  }

  .codeflask__lines__line {
    display: block;
  }

  .codeflask.codeflask--has-line-numbers {
    padding-left: 40px;
  }

  .codeflask.codeflask--has-line-numbers:before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 40px;
    height: 100%;
    background: #eee;
    z-index: 1;
  }
  
</style>
EOF;

	}

	function getParser($block = []) {
		global $_G;
		return <<<EOF
<div class="ce-block ce-block--focused" data-id="{id}" [if tunes.anchorTune.anchor=notnull]id="{tunes.anchorTune.anchor}"[/if]>
    <div class="ce-block__content">
        <div class="editorjs-codeFlask_Wrapper">
            <div class="editorjs-codeFlask_Editor" id="codeflask-{id}">
                
            </div>
            <div class="editorjs-codeFlask_LangDisplay">{data.language}</div>
        </div>
    </div>
</div>
[codeflask id,data.language,data.length,data.code,{STATICURL}js/editorjs/tools/codeflask/codeflask150.min.js?{VERHASH}]
EOF;
	}

}