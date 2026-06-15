<?php exit('Access Denied'); ?>
<div class="dbox">
	<div class="boxheader">
		<a href="misc.php?mod=ranklist&type=thread&view=replies&orderby=thisweek" target="_blank" style="float: right">{lang stats_more}</a>
		{lang ranklist_hotthread}
	</div>
	<div class="boxbody hotthreads-list">
		<!--{loop $threadlist $thread}-->
		<div class="ht-item">
			<span class="ht-rank<!--{if $thread['rank'] <= 3}--> top<!--{/if}-->">$thread['rank']</span>
			<a class="ht-subject" href="forum.php?mod=viewthread&tid=$thread['tid']" target="_blank">{$thread['subject']}</a>
			<!--{if $thread['forum']}--><span class="ht-forum">{$thread['forum']}</span><!--{/if}-->
			<span class="ht-replies">$thread['replies']</span>
		</div>
		<!--{/loop}-->
	</div>
</div>
