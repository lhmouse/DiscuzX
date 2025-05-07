<?php exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="Cache-control" content="{if $_G['setting']['mobile']['mobilecachetime'] > 0}{$_G['setting']['mobile']['mobilecachetime']}{else}no-cache{/if}" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
<meta name="format-detection" content="telephone=no" />
<title><!--{if !empty($navtitle)}-->$navtitle - <!--{/if}--><!--{if empty($nobbname)}--> $_G['setting']['bbname'] - <!--{/if}--> {lang waptitle} - Powered by Discuz!</title>
<meta name="keywords" content="{if !empty($metakeywords)}{echo dhtmlspecialchars($metakeywords)}{/if}" />
<meta name="description" content="{if !empty($metadescription)}{echo dhtmlspecialchars($metadescription)} {/if},$_G['setting']['bbname']" />
<!--{hook/global_meta_mobile}-->
$_G['setting']['seohead']
<base href="{$_G['siteurl']}" />
<script type="text/javascript">var STYLEID = '{STYLEID}', STATICURL = '{STATICURL}', IMGDIR = '{IMGDIR}', VERHASH = '{VERHASH}', charset = '{CHARSET}', discuz_uid = '{$_G['uid']}', cookiepre = '{$_G['config']['cookie']['cookiepre']}', cookiedomain = '{$_G['config']['cookie']['cookiedomain']}', cookiepath = '{$_G['config']['cookie']['cookiepath']}', showusercard = '{$_G['setting']['showusercard']}', attackevasive = '{$_G['config']['security']['attackevasive']}', disallowfloat = '{$_G['setting']['disallowfloat']}', creditnotice = '<!--{if $_G['setting']['creditnotice']}-->$_G['setting']['creditnames']<!--{/if}-->', defaultstyle = '$_G['style']['defaultextstyle']', REPORTURL = '$_G['currenturl_encode']', SITEURL = '$_G['siteurl']', JSPATH = '$_G['setting']['jspath']';</script>
<link rel="stylesheet" href="{STATICURL}image/mobile/style.css?{VERHASH}" type="text/css" media="all">
<link rel="stylesheet" href="{STATICURL}image/mobile/font/dzmicon.css?{VERHASH}" type="text/css" media="all">
<link rel="stylesheet" type="text/css" href="{$_G['setting']['csspath']}{STYLEID}_css_mobile_diy.css?{VERHASH}" />
<script src="{STATICURL}js/mobile/jquery.min.js?{VERHASH}"></script>
<script src="{STATICURL}js/mobile/common.js?{VERHASH}" charset="{CHARSET}"></script>
<script src="{STATICURL}js/swiper/swiper-bundle.min.js?{VERHASH}"></script>
{cell account/js}
<style>
	body {
		--dz-BG-body:{if $_G['style']['touch_style_bodybg']}$_G['style']['touch_style_bodybg']{else}#EEEEEE{/if};
		--dz-BG-color:{if $_G['style']['touch_style_color']}$_G['style']['touch_style_color']{else}#2B7ACD{/if};
		--dz-BG-0:{if $_G['style']['touch_style_bg0']}$_G['style']['touch_style_bg0']{else}#FFFFFF{/if};
		--dz-BG-1:{if $_G['style']['touch_style_bg1']}$_G['style']['touch_style_bg1']{else}#333333{/if};
		--dz-BG-2:{if $_G['style']['touch_style_bg2']}$_G['style']['touch_style_bg2']{else}#FF5656{/if};
		--dz-BG-3:{if $_G['style']['touch_style_bg3']}$_G['style']['touch_style_bg3']{else}#FF9C00{/if};
		--dz-BG-4:{if $_G['style']['touch_style_bg4']}$_G['style']['touch_style_bg4']{else}#B3CC0D{/if};
		--dz-BG-5:{if $_G['style']['touch_style_bg5']}$_G['style']['touch_style_bg5']{else}#F3F3F3{/if};
		--dz-BG-6:{if $_G['style']['touch_style_bg6']}$_G['style']['touch_style_bg6']{else}#CCCCCC{/if};
		--dz-BG-n:{if $_G['style']['touch_style_bgn']}$_G['style']['touch_style_bgn']{else}#A0C8EA{/if};
		--dz-FC-color:{if $_G['style']['touch_style_color']}$_G['style']['touch_style_color']{else}#2B7ACD{/if};
		--dz-FC-fff:{if $_G['style']['touch_style_tfff']}$_G['style']['touch_style_tfff']{else}#FFFFFF{/if};
		--dz-FC-333:{if $_G['style']['touch_style_t333']}$_G['style']['touch_style_t333']{else}#333333{/if};
		--dz-FC-666:{if $_G['style']['touch_style_t666']}$_G['style']['touch_style_t666']{else}#666666{/if};
		--dz-FC-777:{if $_G['style']['touch_style_t777']}$_G['style']['touch_style_t777']{else}#777777{/if};
		--dz-FC-888:{if $_G['style']['touch_style_t888']}$_G['style']['touch_style_t888']{else}#888888{/if};
		--dz-FC-999:{if $_G['style']['touch_style_t999']}$_G['style']['touch_style_t999']{else}#999999{/if};
		--dz-FC-aaa:{if $_G['style']['touch_style_taaa']}$_G['style']['touch_style_taaa']{else}#AAAAAA{/if};
		--dz-FC-bbb:{if $_G['style']['touch_style_tbbb']}$_G['style']['touch_style_tbbb']{else}#BBBBBB{/if};
		--dz-FC-ccc:{if $_G['style']['touch_style_tccc']}$_G['style']['touch_style_tccc']{else}#CCCCCC{/if};
		--dz-FC-ddd:{if $_G['style']['touch_style_tddd']}$_G['style']['touch_style_tddd']{else}#DDDDDD{/if};
		--dz-FC-nnn:{if $_G['style']['touch_style_tnnn']}$_G['style']['touch_style_tnnn']{else}#7DA0CC{/if};
		--dz-FC-light:{if $_G['style']['touch_style_tlight']}$_G['style']['touch_style_tlight']{else}#FF9C00{/if};
		--dz-FC-a:{if $_G['style']['touch_style_ta']}$_G['style']['touch_style_ta']{else}#F26C4F{/if};
		--dz-FC-v:{if $_G['style']['touch_style_tv']}$_G['style']['touch_style_tv']{else}#7CBE00{/if};		
		--dz-FC-t:transparent;		
		--dz-BOR-ed:{if $_G['style']['touch_style_border']}$_G['style']['touch_style_border']{else}#EDEDED{/if};
	}
	<!--{if $_GET['diy'] != 'yes' && $_GET['mobilediy'] != 'yes'}-->.discuz_diy .area {background:none;min-height:0}<!--{/if}-->
	<!--{if $_G['style']['touch_style_addcss']}-->$_G['style']['touch_style_addcss']<!--{/if}-->
</style>
</head>
<body id="{$_G['basescript']}" class="pg_{CURMODULE} discuz_diy">
<!--{hook/global_header_mobile}-->
<div id="append_parent"></div>