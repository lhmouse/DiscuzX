<?php exit('Access Denied'); ?>
<div class="dbox">
	<div class="boxheader">
		<a href="misc.php?mod=stat&op=trend" target="_blank" style="float: right">{lang stats_more}</a>
		{lang stats_trend}
	</div>
	<div class="boxbody">
		<div class="stat_box cl">
			<div class="statitem ico01">{lang stats_main_members}
				<em>{lang stats_main_today} +{echo number_format($statvars['membersaddtoday'])}</em>
				<span title="{lang stats_main_members} {echo number_format($statvars['members'])}"><a href="{ADMINSCRIPT}?action=members&operation=search&submit=yes" target="_blank">{echo number_format($statvars['members'])}</a></span>
			</div>
			<div class="statitem ico02">{lang stats_main_onlinemembers}
				<em>{lang stats_main_registers} {echo number_format($statvars['onlinemembers'])}</em>
				<span title="{lang stats_main_onlinemembers} {echo number_format($statvars['online'])}"><a href="{ADMINSCRIPT}?action=members&operation=search&sid_noempty=yes&submit=yes" target="_blank">{echo number_format($statvars['online'])}</a></span>
			</div>
			<div class="statitem ico04">{lang stats_main_posts}
				<em>{lang stats_main_todays_newposts} +{echo number_format($statvars['postsaddtoday'])}</em>
				<span title="{lang stats_main_posts} {echo number_format($statvars['posts'])}">{echo number_format($statvars['posts'])}</span>
			</div>
			<div class="statitem ico05">{lang stats_main_threads_count}
				<em>{lang stats_main_rpt} $statvars['threadreplyavg']</em>
				<span title="{lang stats_main_threads_count} {echo number_format($statvars['threads'])}"><a href="{ADMINSCRIPT}?action=threads" target="_blank">{echo number_format($statvars['threads'])}</a></span>
			</div>
			<div class="statitem ico03">{lang stats_main_posters}
				<em>{lang stats_main_ratio} $statvars['mempostpercent']%</em>
				<span title="{lang stats_main_posters} {echo number_format($statvars['mempost'])}"><a href="{ADMINSCRIPT}?action=members&operation=search&posts_low=1&submit=yes" target="_blank">{echo number_format($statvars['mempost'])}</a></span>
			</div>
			<div class="statitem ico06">{lang stats_main_forums_count}
				<em>{lang stats_main_nppd} {echo number_format($statvars['postsaddavg'])}</em>
				<span title="{lang stats_main_forums_count} {echo number_format($statvars['forums'])}"><a href="{ADMINSCRIPT}?action=forums" target="_blank">{echo number_format($statvars['forums'])}</a></span>
			</div>
			<div class="statitem ico07">{lang stats_main_board_activity}
				<em>{lang stats_main_nmpd} {echo number_format($statvars['membersaddavg'])}</em>
				<span title="{lang stats_main_board_activity} {echo number_format($statvars['activeindex'])}">{echo number_format($statvars['activeindex'])}</span>
			</div>
			<div class="statitem ico08">{lang stats_main_posts_avg}
				<em>{lang stats_main_admins} {echo number_format($statvars['admins'])}</em>
				<span title="{lang stats_main_posts_avg} $statvars['mempostavg']">$statvars['mempostavg']</span>
			</div>
		</div>
		<div class="stat_detail">
			<div class="stat_detail_item">
				<label>{lang stats_main_topposter}</label>
				<!--{if $statvars['bestmem']}-->
				<value><a href="{ADMINSCRIPT}?action=members&operation=search&username={echo rawurlencode($statvars['bestmem'])}" target="_blank">$statvars['bestmem']</a> ({echo number_format($statvars['bestmemposts'])})</value>
				<!--{else}-->
				<value>-</value>
				<!--{/if}-->
			</div>
			<div class="stat_detail_item">
				<label>{lang stats_main_hot_forum}</label>
				<value><a href="forum.php?mod=forumdisplay&fid=$statvars['hotforum']['fid']" target="_blank">$statvars['hotforum']['name']</a></value>
			</div>
			<div class="stat_detail_item">
				<label>{lang stats_main_nonposters}</label>
				<value>{echo number_format($statvars['memnonpost'])}</value>
			</div>
		</div>
		<div class="charts">
			<script src="{STATICURL}js/echarts/echarts.common.min.js"></script>
			<script src="{$_G['setting']['jspath']}stat.js"></script>
			<div id="statchart"></div>
			<script type="text/javascript">
				drawstatchart('{ADMINSCRIPT}?action=index&operation=chart', 300);
			</script>
		</div>
	</div>
</div>