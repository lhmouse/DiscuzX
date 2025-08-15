<?php exit('Access Denied');?>
<!--{template common/header}-->

<style id="diy_style" type="text/css"></style>
<!--[diy=diynavtop]--><div id="diynavtop" class="area"></div><!--[/diy]-->
<div id="pt" class="bm cl">
	<div class="z">
		<a href=".." class="nvhm" title="{lang homepage}">$_G[setting][bbname]</a><em>&raquo;</em><a href="forum.php">{$_G[setting][navs][2][navname]}</a>$navigation
	</div>
</div>

<div id="wp" class="pl">
	<div id="threadlist" {if $_G['uid']} style="position: relative;"{/if}>
		<!--{if $quicksearchlist && !$_GET['archiveid']}-->
			<div class="bm"><!--{subtemplate forum/search_sortoption}--></div>
		<!--{/if}-->
		$sorttemplate['header']
		$sorttemplate['body']
		$sorttemplate['footer']
	</div>
</div>

<br class="cl">

<div id="pgt" class="bm bw0 pgs cl">
	<a href="javascript:;" onclick="location.href='forum.php?mod=post&action=newthread&fid=$_G[fid]';return false;" class="pgsbtn">{lang send_posts}</a>
</div>

<!--{template common/footer}-->

