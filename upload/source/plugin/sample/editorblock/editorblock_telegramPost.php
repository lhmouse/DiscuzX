<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
// 类名与文件名需要保持一致
class editorblock_telegramPost {

	var $version = '1.0.5';
	var $name = '联系方式';
	var $available = 1; // 默认启用状态 0:不启用 1:启用
	var $columns = 1; //  默认是否支持多列 0:不支持 1:支持
	var $identifier = 'telegramPost'; // 标识，与editorblock_telegramPost后缀、与tools文件夹下功能区块文件夹名一致
	var $description = '插入联系方式信息区块，可用于插入电话、微信号、QQ号等，添加内容格式为：类型标识(mobile、wechat、qq)/联系人名称/联系方式(电话、微信等)';
	var $filename = 'telegramPost'; // 区块静态文件名，与tools文件夹下功能区块文件夹下js名一致
	var $copyright = '<a href="https://addon.dismall.com/developer-32563.html" target="_blank">云诺</a>';
	var $type = '0'; // 0:数据类型 1:图片类型 2:附件类型

	function __construct() {

	}

	function getsetting() {
		global $_G;
		$settings = [
		];
		return $settings;
	}

	function setsetting(&$blocknew, &$parameters) {
	}

	function getParameter() {
		return <<<EOF
{
    "data": {
   	 	"channelType": "mobile", // mobile、wechat、qq
		"channelName": "云诺",
		"messageId": 123456
	},
    "id": "ZT8S70Q34G", // 区块id
    "type": "telegramPost" // 区块类型
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
   tools_telegramPost: {
      telegramPost: {
         class: TelegramPost,
         tunes: ['anchorTune']
      },
   },
   i18n: {
       messages: {
          tools: {
            'telegramPost': {
                  'English': '中文名称',
            }
          },
          toolNames: {
                'English': '中文名称',
          }
        },
    },
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
    /* margin-bottom: 20px; */
}
.ce-block__content,.ce-toolbar__content {
	/* max-width:calc(100% - 50px) */
	margin-left: auto;
    margin-right: auto;
}


.ce-telegramPost {
    position: relative;
    float: left;
    width: 280px;
    height: auto;
    padding: 15px 10px 15px 20px;
    box-sizing: border-box;
    border: 1px solid #3f9dffa3;
    border-radius: 50px;
    margin: 10px 15px;
}

.ce-telegramPost dl {
    margin: 0
}

.ce-telegramPost dl dt {
    width: 50px;
    height: 50px;
    float: left;
    position: relative;
    margin-right: 12px;
}
.ce-telegramPost dl dt svg {
    width: 100%;
    height: 100%
}
.ce-telegramPost dl dd {
    float: left;
    position: relative;
}
.ce-telegramPost dl dd h3 {
    height: 20px;
    line-height: 20px;
    font-size: 14px;
    margin-top: 5px;
}
.ce-telegramPost dl dd h3 em {
    font-style: normal;
    margin-left: 5px;
    color: #fa5555;
    font-size: 12px;
}
.ce-telegramPost dl dd p.p1 {
    color: #999;
}

.ce-telegramPost dl dd p {
    width: 185px;
    height: 20px;
    line-height: 20px;
    margin-top: 5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
EOF;

	}

	function getParser($block = []) {
		global $_G;
		return <<<EOF
<div class="ce-block ce-block--focused" data-id="{id}" [if tunes.anchorTune.anchor=notnull]id="{tunes.anchorTune.anchor}"[/if]>
	<div class="ce-block__content">
	    <div class="ce-telegramPost ">
            <dl>
                <dt>
                    [if data.channelType=mobile]
                    <svg t="1705559455679" class="icon" viewBox="0 0 1025 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="69723" width="80" height="80"><path d="M511.484254 0.066515C227.987504 0.066515-0.001023 227.399103-0.001023 510.114047c0 282.71392 227.987504 512.917906 511.485277 512.917906 283.461957 0 511.48016-230.203986 511.48016-512.917906C1022.964414 227.399103 794.94621 0.066515 511.484254 0.066515zM762.853281 772.349563l-57.003272 43.768853c-54.072523 16.044418-187.064466 42.271756-352.206644-190.872189-187.063443-252.120135-112.512517-394.946051-84.758406-419.698804l59.904345-42.270732 24.854061 7.306406 100.833523 145.69629-2.901073 23.320125-58.471716 40.803311c-26.28669 21.851681-8.772804 49.541323 10.237155 101.988836 17.543561 30.625508 57.003272 91.816149 83.290985 115.137297 45.297672 37.896098 65.777099 62.684668 96.468099 45.201481l55.505152-39.333844 21.977547 2.871397 105.142665 141.357472L762.853281 772.349563z" fill="#18CC73" p-id="69724"></path></svg>
                    [/if]
                    [if data.channelType=wechat]
                	<svg t="1705558999833" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="47545" width="80" height="80"><path d="M512 1.896C229.452 1.896 0 229.452 0 512s229.452 510.104 512 510.104S1022.104 794.548 1022.104 512 794.548 1.896 512 1.896z m-91.022 629.57c-26.548 0-49.304-5.688-75.852-11.377l-75.852 37.926 22.756-66.37c-54.993-37.926-87.23-87.23-87.23-147.912 0-104.296 98.607-185.837 218.074-185.837 108.089 0 201.007 64.474 219.97 153.6-7.585 0-13.274-1.896-20.859-1.896-104.296 0-185.837 77.748-185.837 172.563 0 15.17 1.896 30.34 7.585 45.511-7.585 3.793-15.17 3.793-22.755 3.793z m322.37 77.749l17.067 54.992-58.785-34.133c-22.756 5.689-43.615 11.378-66.37 11.378-104.297 0-185.838-70.163-185.838-157.393S530.963 424.77 635.26 424.77c98.608 0 185.837 70.163 185.837 159.29 0 47.407-32.237 91.021-77.748 125.155z" fill="#46BB36" p-id="47546"></path><path d="M318.578 379.26c0 17.066 13.274 30.34 30.34 30.34s30.341-13.274 30.341-30.34-13.274-30.341-30.34-30.341-30.341 13.274-30.341 30.34z m235.14 159.288c0 13.274 11.378 24.652 24.652 24.652 13.274 0 24.652-11.378 24.652-24.652 0-13.274-11.378-24.652-24.652-24.652-13.274-1.896-24.651 9.482-24.651 24.652z m-81.54-159.289c0 17.067 13.274 30.341 30.34 30.341 17.067 0 30.341-13.274 30.341-30.34 0-17.067-13.274-30.341-30.34-30.341-17.067 0-30.341 13.274-30.341 30.34zM675.08 538.55c0 13.273 11.378 24.651 24.652 24.651 13.274 0 24.652-11.378 24.652-24.652 0-13.274-11.378-24.652-24.652-24.652-13.274-1.896-24.652 9.482-24.652 24.652z" fill="#46BB36" p-id="47547"></path></svg>
                    [/if]
                    [if data.channelType=qq]
                    <svg t="1705559232025" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="57063" width="80" height="80"><path d="M511.500488 512.499512m-511.500488 0a511.500488 511.500488 0 1 0 1023.000976 0 511.500488 511.500488 0 1 0-1023.000976 0Z" fill="#1BC1FA" p-id="57064"></path><path d="M784.234146 572.440976c8.178014 0 16.273108 0.253752 24.282287 0.728288-16.961436-38.434466-42.247742-69.886751-58.977405-90.331785 2.822244-8.482716 11.30496-56.536788-25.443153-90.453666v-2.827239c0-132.850263-96.103149-231.785647-214.822213-231.785647-118.717065 0-214.82521 96.107145-214.825209 231.785647v2.827239c-33.915879 33.915879-25.437159 81.969951-19.782681 90.453666-25.444152 28.265397-73.49223 87.62343-76.320469 155.461182 0 16.961436 2.827239 45.226833 11.305959 56.531794 11.305959 14.133198 39.570357-2.827239 62.186272-48.054073 5.650482 19.788675 19.78368 53.709549 50.876316 93.281905-50.876316 11.302962-65.009514 62.186271-48.049077 90.450669 11.305959 19.788675 39.570357 33.920874 87.624429 33.920874 78.496343 0 115.815899-19.378076 134.378771-35.711126C492.644901 814.680414 490.520976 800.136617 490.520976 785.233171c0-117.522232 131.500581-212.792195 293.71317-212.792195z" fill="#FFFFFF" p-id="57065"></path><path d="M514.925143 819.204995c5.654478 0 11.309955 2.82624 14.132199 5.649483 16.960437 16.960437 53.709549 39.575352 138.50674 39.575352 48.054072 0 76.320468-16.960437 87.625428-33.920874 16.960437-28.264398 2.827239-79.147707-48.054072-90.450669 31.092636-39.572355 45.225834-73.493229 50.881311-93.281905 19.787676 45.226833 50.881311 62.18727 62.186271 48.054073 2.827239-11.30496 5.650482-39.570357 5.650482-56.531794-1.93511-23.223321-8.508691-45.121936-17.337069-65.128398-8.009179-0.475536-16.104273-0.729288-24.282287-0.729287-162.212589 0-293.713171 95.269963-293.71317 212.792195 0 14.903446 2.123926 29.447243 6.147996 43.485533a88.18688 88.18688 0 0 0 4.122973-3.864226c2.827239-2.823243 8.481717-5.649483 14.133198-5.649483z" fill="#FFFFFF" opacity=".4" p-id="57066"></path></svg>
                    [/if]
                </dt>
                <dd>
                    <h3 class="comm" imagentlist="">{data.channelName}<em></em></h3>
                    <p class="p1"><em>{data.messageId}</em></p>
                </dd>
            </dl>
        </div>
	</div>
</div>
EOF;
	}

}