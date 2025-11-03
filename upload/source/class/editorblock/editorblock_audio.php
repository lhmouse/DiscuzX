<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class editorblock_audio {

	var $version = '1.0.8';
	var $name = '音频';
	var $available = 1; // 默认启用状态 0:不启用 1:启用
	var $columns = 1; //  默认是否支持多列 0:不支持 1:支持
	var $identifier = 'audio';
	var $description = '音频区块';
	var $filename = 'audio';
	var $copyright = '<a href="https://addon.dismall.com/developer-32563.html" target="_blank">云诺</a>';
	var $type = '4'; // 0:数据类型 1:图片类型 2:附件类型 3:视频类型 4:音频类型 5:文件类型

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
            "id": "ls9YCBJ7V7",
            "type": "audio",
            "data": {
                "file": {
                        "aid": 1,
                        "remote": 0,
                	"directory" => "forum",
                        "url": "202312/26/151439rv17ot1mgatw1121.mp3"
                },
                "caption": "desc",
                "withBorder": false,
                "stretched": false,
                "withBackground": false
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
   tools_audio: {
        audio: {
            class: AudioTool,
            config: {
                endpoints: {
                    byFile: 'misc.php?mod=swfupload&action=swfupload&operation=jsoneditorupload&fid='+editor_fid, // Your backend file uploader endpoint
                    byUrl: 'misc.php?mod=swfupload&action=swfupload&operation=jsoneditorupload&fid='+editor_fid, // Your endpoint that provides uploading by Url
                },
                field: 'Filedata',
                types: 'audio/*',
                additionalRequestData: {
                    'uid': editor_uid,
                    'hash': editor_hash,
                },
                remote_attachurl: editor_remote_attachurl,
                attachurl: editor_attachurl,
                captionPlaceholder: '描述信息',
                buttonContent: '请选择需要上传的音频（MP3）',
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
.audio-tool {
  --bg-color: #cdd1e0;
  --front-color: #388ae5;
  --border-color: #e8e8eb;

}

  .audio-tool__audio {
    border-radius: 3px;
    overflow: hidden;
    margin-bottom: 10px;
  }

  .audio-tool__audio-picture {
      max-width: 100%;
      vertical-align: bottom;
      display: block;
      margin-left: auto;
      margin-right: auto;
    }

  .audio-tool__audio-preloader {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      background-size: cover;
      margin: auto;
      position: relative;
      background-color: var(--bg-color);
      background-position: center center;
    }

  .audio-tool__audio-preloader::after {
        content: "";
        position: absolute;
        z-index: 3;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        border: 2px solid var(--bg-color);
        border-top-color: var(--front-color);
        left: 50%;
        top: 50%;
        margin-top: -30px;
        margin-left: -30px;
        animation: audio-preloader-spin 2s infinite linear;
        box-sizing: border-box;
      }

  .audio-tool__caption[contentEditable="true"][data-placeholder]::before {
      position: absolute !important;
      content: attr(data-placeholder);
      color: #707684;
      font-weight: normal;
      display: none;
    }

  .audio-tool__caption[contentEditable="true"][data-placeholder]:empty::before {
        display: block;
      }

  .audio-tool__caption[contentEditable="true"][data-placeholder]:empty:focus::before {
        display: none;
      }

  .audio-tool--empty .audio-tool__audio {
      display: none;
    }

  .audio-tool--empty .audio-tool__caption, .audio-tool--loading .audio-tool__caption {
      display: none;
    }

  .audio-tool .cdx-button {
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .audio-tool .cdx-button svg {
      height: auto;
      margin: 0 6px 0 0;
    }

  .audio-tool--filled .cdx-button {
      display: none;
    }

  .audio-tool--filled .audio-tool__audio-preloader {
        display: none;
      }

  .audio-tool--loading .audio-tool__audio {
      min-height: 200px;
      display: flex;
      border: 1px solid var(--border-color);
      background-color: #fff;
    }

  .audio-tool--loading .audio-tool__audio-picture {
        display: none;
      }

  .audio-tool--loading .cdx-button {
      display: none;
    }

  /**
   * Tunes
   * ----------------
   */

  .audio-tool--withBorder .audio-tool__audio {
      border: 1px solid var(--border-color);
    }

  .audio-tool--withBackground .audio-tool__audio {
      padding: 15px;
      background: var(--bg-color);
    }

  .audio-tool--withBackground .audio-tool__audio-picture {
        max-width: 60%;
        margin: 0 auto;
      }

  .audio-tool--stretched .audio-tool__audio-picture {
        width: 100%;
      }

  .audio-tool__caption {
		text-align: center;
		font-size: 14px;
		color: #a3a3a3;
	}
@keyframes audio-preloader-spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
</style>
EOF;

	}

	function getParser($block = []) {
		global $_G;
		return <<<EOF
<div class="ce-block ce-block--focused" data-id="{id}" [if tunes.anchorTune.anchor=notnull]id="{tunes.anchorTune.anchor}"[/if]>
    <div class="ce-block__content">
        <div class="cdx-block audio-tool audio-tool--filled [if data.withBorder=1]audio-tool--withBorder[/if] [if data.stretched=1]audio-tool--stretched[/if] [if data.withBackground=1]audio-tool--withBackground[/if]">
            <div class="audio-tool__audio">
                <div class="audio-tool__audio-preloader" style=""></div>
                <audio class="audio-tool__audio-picture" src="[url data.file.url,data.file.remote,data.file.directory]" type="audio/mpeg" [if data.autoplay=1]autoplay[/if] [if data.loop=1]loop[/if] [if data.muted=1]muted[/if] [if data.controls=1]controls[/if] title="{data.caption}" alt="{data.caption}" />
            </div>
            <div class="cdx-input audio-tool__caption" data-placeholder="{data.caption}">{data.caption}</div>
        </div>
    </div>
</div>
EOF;
	}

}