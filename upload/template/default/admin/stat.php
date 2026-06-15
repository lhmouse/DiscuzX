<?php exit('Access Denied'); ?>

<style>
	*, *::before, *::after { box-sizing: inherit; }
	.d-13 { flex-basis: 10%; }
	.d-23 { flex-basis: 88%; }
	.txt { width: 100px; }
	.keys { padding: 0px !important; line-height: 22px; background: var(--admincp-bgc); overflow: auto; height: 530px; }
	.keys li { padding: 5px 10px; cursor: pointer; }
	.keys li.active { background: var(--admincp-bge); }
	.keys li:hover { background-color: var(--admincp-bge); }
	.keys li:before { font-family: dzicon;content: "\e8b3";font-size: 9px; margin-right: 5px; color: var(--admincp-fd); }
	.keys::-webkit-scrollbar { width: 4px; background-color: var(--admincp-bgf); border-radius: 6px; }
	.keys::-webkit-scrollbar-thumb { background-color: var(--admincp-bge); border-radius: 6px; }
</style>

<div class="drow">
	<div class="dcol d-13">
		<div class="dbox">
			<div class="boxbody keys">
				<ul>
				<!--{loop $keys $k $v}-->
					<li{if $k == $keyenc} class="active" id="currentkey"{/if} onclick="location.href='{ADMINSCRIPT}?action=stat&operation=list&key={$k}';">{$v}</li>
				<!--{/loop}-->
				</ul>
			</div>
		</div>

	</div>
	<div class="dcol d-23">
		<div class="dbox">
			<div class="boxheader">
				<!--{if $isNew}-->
					<a href="{ADMINSCRIPT}?action=stat&operation=setname&key={$keyenc}" class="y">{lang stat_setname}</a>
				<!--{/if}-->
				$keys[$keyenc]
			</div>
			<div class="boxbody">
				<form action="{ADMINSCRIPT}?action=stat&operation=list&key=$keyenc" method="post">
					{lang stat_date}: <input name="primarybegin" type="text" class="txt" value="{$primarybegin}" />- <input name="primaryend" type="text" class="txt" value="{$primaryend}" />
					<!--{loop $options $v}-->
					{$v['name']}: <input name="p[{$v['key']}]" type="text" class="txt" value="{$v['value']}" />
					<!--{/loop}-->
					<input type="submit" class="btn" value="{lang stat_view}" />
					<a href="{ADMINSCRIPT}?action=stat&operation=export&primarybegin=$primarybegin&primaryend=$primaryend&key=$key&type=$type$append">{lang stat_export}</a>
				</form>
				<div class="charts">
					<script src="{STATICURL}js/echarts/echarts.common.min.js"></script>
					<script src="{$_G['setting']['jspath']}stat.js"></script>
					<div id="statchart"></div>
					<script type="text/javascript">
						drawstatchart('{ADMINSCRIPT}?action=stat&operation=chart&primarybegin=$primarybegin&primaryend=$primaryend&key=$key&type=$type$append', 400);
					</script>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
var currentKey = document.getElementById('currentkey');
if (currentKey) {
	var keysContainer = document.querySelector('.keys');
	if (keysContainer) {
		var elementTop = currentKey.offsetTop;
		var containerTop = keysContainer.scrollTop;
		var containerHeight = keysContainer.clientHeight;
		var scrollPosition = elementTop - containerHeight / 2 + currentKey.clientHeight / 2;
		keysContainer.scrollTop = scrollPosition;
	}
}
</script>