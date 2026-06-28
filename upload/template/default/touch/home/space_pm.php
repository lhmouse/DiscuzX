<?php exit('Access Denied');?>
<!--{eval $_G['home_tpl_titles'] = array('{lang pm}');}-->
<!--{template common/header}-->
<!--{if in_array($filter, array('privatepm')) || in_array($_GET['subop'], array('view'))}-->
	<!--{if in_array($filter, array('privatepm'))}-->
	<div class="header cl">
		<div class="mz"><a href="javascript:history.back();"><i class="dm-c-left"></i></a></div>
		<h2>{lang mypm}</h2>
		<div class="my"><a href="home.php?mod=spacecp&ac=pm"><i class="dm-edit"></i></a></div>
	</div>
	<div class="dhnv flex-box cl">
		<a href="home.php?mod=space&do=pm" class="flex mon">{lang mypm}<!--{if $newpmcount}--><strong>($newpmcount)</strong><!--{/if}--></a>
		<a href="home.php?mod=space&do=notice" class="flex">{lang my}{lang remind}<!--{if $_G['member']['newprompt']}--><strong>($_G['member']['newprompt'])</strong><!--{/if}--></a>
	</div>
	<div id="pmlist" class="pm-list">
		<!--{loop $list $key $value}-->
		<a href="{if $value['touid']}home.php?mod=space&do=pm&subop=view&touid=$value['touid']{else}home.php?mod=space&do=pm&subop=view&plid={$value['plid']}&type=1{/if}" class="pm-list-item">
			<div class="pm-list-avatar">
				<!--{if $value['pmtype'] == 2}-->
				<img src="{STATICURL}image/common/grouppm.png" alt="" />
				<!--{else}-->
				<!--{avatar($value['touid'] ? $value['touid'] : ($value['lastauthorid'] ? $value['lastauthorid'] : $value['authorid']), 'small')}-->
				<!--{/if}-->
				<!--{if $value['new']}--><span class="pm-list-badge">$value['pmnum']</span><!--{/if}-->
			</div>
			<div class="pm-list-info">
				<div class="pm-list-meta">
					<span class="pm-list-name">
					<!--{if $value['touid']}-->
						<!--{if $value['msgfromid'] == $_G['uid']}-->
							{lang me}{lang you_to} {$value['tousername']}
						<!--{else}-->
							{$value['tousername']}
						<!--{/if}-->
					<!--{elseif $value['pmtype'] == 2}-->
						{lang chatpm_author}: $value['firstauthor']
					<!--{/if}-->
					</span>
					<span class="pm-list-time"><!--{date($value['dateline'], 'u')}--></span>
				</div>
				<div class="pm-list-preview">
					<!--{if $value['pmtype'] == 2}-->[{lang chatpm}]<!--{if $value['subject']}-->$value['subject']<!--{/if}--><!--{/if}--><!--{if $value['pmtype'] == 2 && $value['lastauthor']}-->$value['lastauthor'] : $value['message']<!--{else}-->$value['message']<!--{/if}-->
				</div>
			</div>
		</a>
		<!--{/loop}-->
	</div>
	<!--{elseif in_array($_GET['subop'], array('view'))}-->
		<!--{eval $msguser = $tousername;}-->
		<div class="header cl">
			<div class="mz"><a href="javascript:history.back();"><i class="dm-c-left"></i></a></div>
			<h2>{if $chatpmmember}{lang viewmypm}{else}{lang taking_with_user}{/if}</h2>
			<div class="my"><a href="index.php"><i class="dm-house"></i></a></div>
		</div>
		<div class="msgbox b_m">
			<!--{if !$list}-->
				<div class="empty-box">
					<h4>{lang no_corresponding_pm}</h4>
				</div>
			<!--{else}-->
				<!--{loop $list $key $value}-->
					<!--{subtemplate home/space_pm_node}-->
				<!--{/loop}-->
				$multi
				<div id="dumppage" style="display:none">
			<!--{/if}-->
		</div>
		<form id="pmform" class="pmform" name="pmform" method="post" action="home.php?mod=spacecp&ac=pm&op=send&pmid=$pmid&daterange=$daterange&pmsubmit=yes&mobile=2" >
			<input type="hidden" name="formhash" value="{FORMHASH}" />
			<!--{if !$touid}-->
			<input type="hidden" name="plid" value="$plid" />
			<!--{else}-->
			<input type="hidden" name="touid" value="$touid" />
			<!--{/if}-->
			<div class="foot_height"></div>
			<div class="foot msg_post flex-box">
				<input type="text" value="" class="flex px" autocomplete="off" id="replymessage" name="message" placeholder="{lang mobsendpm}" />
				<input type="button" name="pmsubmit" id="pmsubmit" class="formdialog pns" value="{lang sendpm}" />
			</div>
		</form>
		<!--{eval $nofooter = true;}-->
	<!--{/if}-->
<!--{else}-->
	<div class="empty-box">
		<h4>{lang user_mobile_pm_error}</h4>
	</div>
<!--{/if}-->
<!--{template common/footer}-->
