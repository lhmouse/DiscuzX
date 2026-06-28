<?php exit('Access Denied');?>
<!--{eval $_G['home_tpl_titles'] = array('{lang remind}');}-->
<!--{template common/header}-->
<div class="header cl">
	<div class="mz"><a href="javascript:history.back();"><i class="dm-c-left"></i></a></div>
	<h2>{lang remind}</h2>
	<div class="my"><a href="home.php?mod=space&uid=$_G['uid']&do=profile&mycenter=1"><i class="dm-person"></i></a></div>
</div>
<div class="dhnv flex-box cl">
	<a href="home.php?mod=space&do=pm" class="flex">{lang mypm}<!--{if $newpmcount}--><strong>($newpmcount)</strong><!--{/if}--></a>
	<a href="home.php?mod=space&do=notice" class="flex mon">{lang my}{lang remind}<!--{if $_G['member']['newprompt']}--><strong>($_G['member']['newprompt'])</strong><!--{/if}--></a>
</div>
<!--{if empty($list)}-->
<div class="empty-box">
	<!--{if $new == 1}-->
		<h4>{lang no_new_notice}<a href="home.php?mod=space&do=notice&isread=1">{lang view_old_notice}</a></h4>
	<!--{else}-->
		<h4>{lang no_notice}</h4>
	<!--{/if}-->
</div>
<!--{/if}-->
<!--{if $list}-->
	<div id="notice_ul" class="notice-list">
	<!--{loop $list $key $value}-->
		<div class="notice-list-item" $value['rowid'] notice="$value['id']">
			<div class="notice-list-avatar">
			<!--{if $value['authorid']}-->
				<a href="home.php?mod=space&uid=$value['authorid']"><!--{avatar($value['authorid'],'small')}--></a>
			<!--{else}-->
				<img src="{IMGDIR}/systempm.png" alt="systempm" />
			<!--{/if}-->
			</div>
			<div class="notice-list-info">
				<div class="notice-list-meta">
					<span class="notice-list-time"><!--{date($value['dateline'], 'u')}--></span>
					<a href="home.php?mod=spacecp&ac=common&op=ignore&authorid=$value['authorid']&type=$value['type']&handlekey=addfriendhk_{$value['authorid']}" id="a_note_$value['id']" class="dialog notice-list-shield">{lang shield}</a>
				</div>
				<div class="notice-list-body" style="$value['style']">$value['note']</div>
				<!--{if $value['from_num']}-->
				<div class="notice-list-from">{lang ignore_same_notice_message}</div>
				<!--{/if}-->
			</div>
		</div>
	<!--{/loop}-->
	</div>
	<!--{if $view!='userapp' && $space['notifications']}-->
		<div class="notice-list-ignore"><a href="home.php?mod=space&do=notice&ignore=all">{lang ignore_same_notice_message} <em>&rsaquo;</em></a></div>
	<!--{/if}-->
	<!--{if $multi}--><div class="pgs cl">$multi</div><!--{/if}-->
<!--{/if}-->
<!--{template common/footer}-->