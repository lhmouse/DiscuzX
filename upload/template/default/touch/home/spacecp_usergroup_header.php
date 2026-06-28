<?php exit('Access Denied');?>
<div class="dhnavs_box">
	<div id="dhnavs">
		<div id="dhnavs_li">
			<ul class="swiper-wrapper">
				<li class="swiper-slide<!--{if $activeus[usergroup]}--> mon<!--{/if}-->"><a href="home.php?mod=spacecp&ac=usergroup">{lang my_usergroups}</a></li>
				<li class="swiper-slide<!--{if $activeus[list] || $activeus[expiry]}--> mon<!--{/if}-->"><a href="home.php?mod=spacecp&ac=usergroup&do=list">{lang usergroups_joinbuy}</a></li>
				<li class="swiper-slide<!--{if $activeus[forum]}--> mon<!--{/if}-->"><a href="home.php?mod=spacecp&ac=usergroup&do=forum">{lang my}{$_G['setting']['navs'][2]['navname']}{lang rights}</a></li>
			</ul>
		</div>
	</div>
</div>
<script>initdhnav();</script>