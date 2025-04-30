<?php exit('Access Denied');?>
<!--{hook/global_footer_mobile}-->
<div id="mask" style="display:none;"></div>
<!--{if $_G['setting']['statcode']}--><div id="statcode" style="display:none;">{$_G['setting']['statcode']}</div><!--{/if}-->
<!--{if !$nofooter}-->
<div class="foot_height"></div>
<div id="mfoot" class="foot flex-box">
	{cells common/footer/link}
</div>
<!--{/if}-->

<!--{if getgpc('mobilediy')}-->
	<script>mobileDiy.init('{$_G['style']['tpldirectory']}', 'touch/{$_G['style']['tplfile']}', '{echo dsign({$_G['style']['tpldirectory']}.'touch/'.{$_G['style']['tplfile']})}');</script>
<!--{/if}-->

</body>
</html>
<!--{eval updatesession();}-->
<!--{if defined('IN_MOBILE')&&!defined('IN_PREVIEW')}-->
	<!--{eval output();}-->
<!--{else}-->
	<!--{eval output_preview();}-->
<!--{/if}-->