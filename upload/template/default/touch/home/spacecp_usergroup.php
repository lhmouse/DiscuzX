<?php exit('Access Denied');?>
<!--{template common/header}-->

<!--{if in_array($do, array('buy', 'exit'))}-->
	<!--{template home/spacecp_header}-->
	<!--{hook/spacecp_usergroup_top}-->
	<!--{template home/spacecp_usergroup_header}-->
	<div class="f_c">
	<h3 class="flb">
			<em id="return_$_GET[handlekey]"><!--{if $join}-->{lang memcp_usergroups_joinbuy}<!--{else}-->{lang memcp_usergroups_joinexit}<!--{/if}--></em>
			<!--{if $_G[inajax]}--><span><a href="javascript:;" onclick="hideWindow('$_GET[handlekey]');" class="flbc" title="{lang close}">{lang close}</a></span><!--{/if}-->
		</h3>

		<form id="buygroupform_{$groupid}" name="buygroupform_{$groupid}" method="post" autocomplete="off" action="home.php?mod=spacecp&ac=usergroup&do=$do&groupid=$groupid" onsubmit="ajaxpost(this.id, 'return_$_GET[handlekey]');">
			<!--{if $_G[inajax]}--><input type="hidden" name="handlekey" value="$_GET[handlekey]" /><!--{/if}-->
			<input type="hidden" name="groupsubmit" value="true" />
			<input type="hidden" name="formhash" value="{FORMHASH}" />
			<div class="profile-form usergroup-form">
				<!--{if $join}-->
					<!--{if $group['dailyprice']}-->
						<div class="form-section">
							<div class="form-row">
								<div class="form-label">{lang memcp_usergroups_dailyprice}</div>
								<div class="form-field">$group[dailyprice] {$_G[setting][extcredits][$_G[setting][creditstrans]][unit]}{$_G[setting][extcredits][$_G[setting][creditstrans]][title]}</div>
							</div>
							<div class="form-row">
								<div class="form-label">{lang memcp_usergroups_credit}</div>
								<div class="form-field">$usermaxdays {lang days}</div>
							</div>
							<div class="form-row">
								<div class="form-label">{lang memcp_usergroups_span}</div>
								<div class="form-field">
									<input type="text" name="days" id="days" class="px" size="5" value="$group[minspan]" onblur="change_credits_need(this.value)" onkeyup="change_credits_need(this.value)" /> {lang days}
								</div>
							</div>
							<div class="form-row">
								<div class="form-label">{lang credits_need}{$_G[setting][extcredits][$_G[setting][creditstrans]][title]}</div>
								<div class="form-field"><span id="credits_need"></span> {$_G[setting][extcredits][$_G[setting][creditstrans]][unit]}</div>
							</div>
						</div>
						<div class="form-tip">
							<strong>{lang memcp_usergroups_explain}:</strong>
							<!--{if $join}-->
								{lang memcp_usergroups_join_comment}
							<!--{else}-->
								{lang memcp_usergroups_exit_comment}
							<!--{/if}-->
						</div>
						<script type="text/javascript">
							var dailyprice = $group[dailyprice];
							function change_credits_need(daynum) {
								if(!isNaN(parseInt(daynum))) {
									$('credits_need').innerHTML = parseInt(daynum) * dailyprice;
								} else {
									$('credits_need').innerHTML = '0';
								}
							}
							change_credits_need($group[minspan]);
						</script>
					<!--{else}-->
						<div class="form-tip">
							<strong>{lang memcp_usergroups_explain}:</strong> {lang memcp_usergroups_free_comment}
						</div>
					<!--{/if}-->
				<!--{else}-->
					<div class="form-tip">
						<strong>{lang memcp_usergroups_explain}:</strong>
						<!--{if $group[type] != 'special' || $group[system]=='private'}-->
							{lang memcp_usergroups_admin_exit_comment}
						<!--{elseif $group['dailyprice']}-->
							{lang memcp_usergroups_exit_comment}
						<!--{else}-->
							{lang memcp_usergroups_open_exit_comment}
						<!--{/if}-->
					</div>
				<!--{/if}-->
				<div class="form-submit">
					<button type="submit" name="buysubmit" class="pn" id="buysubmit" value="true"><strong><!--{if $join}-->{lang memcp_usergroups_joinbuy}<!--{else}-->{lang memcp_usergroups_joinexit}<!--{/if}--></strong></button>
				</div>
			</div>
		</form>
	</div>

<!--{elseif $do == 'switch'}-->
	<!--{template home/spacecp_header}-->
	<!--{hook/spacecp_usergroup_top}-->
	<!--{template home/spacecp_usergroup_header}-->
	<div class="f_c">
		<h3 class="flb">
			<em id="return_$_GET[handlekey]">{lang memcp_usergroups_switch}</em>
			<!--{if $_G[inajax]}--><span><a href="javascript:;" onclick="hideWindow('$_GET[handlekey]');" class="flbc" title="{lang close}">{lang close}</a></span><!--{/if}-->
		</h3>
		<form method="post" autocomplete="off" action="home.php?mod=spacecp&ac=usergroup&do=switch&groupid=$groupid">
			<!--{if $_G[inajax]}--><input type="hidden" name="handlekey" value="$_GET[handlekey]" /><!--{/if}-->
			<input type="hidden" name="formhash" value="{FORMHASH}" />
			<div class="profile-form usergroup-form">
				<div class="form-section">
					<div class="form-row">
						<div class="form-label">{lang memcp_usergroups_main_old}</div>
						<div class="form-field">$_G[group][grouptitle]</div>
					</div>
					<div class="form-row">
						<div class="form-label">{lang memcp_usergroups_main_new}</div>
						<div class="form-field">$group[grouptitle]</div>
					</div>
					<div class="form-row">
						<div class="form-label">{lang memcp_usergroups_timelimit}</div>
						<div class="form-field">
							<!--{if $group['grouptype'] == 'member'}-->
								{lang spacecp_usergroup_message2} $group[groupcreditshigher]
							<!--{else}-->
								{lang unlimited}
							<!--{/if}-->
						</div>
					</div>
				</div>
				<div class="form-submit">
					<button type="submit" name="groupsubmit" class="pn" value="true"><strong>{lang memcp_usergroups_switch}</strong></button>
				</div>
			</div>
		</form>
	</div>

<!--{elseif $do == 'forum'}-->
	<!--{template home/spacecp_header}-->
	<!--{hook/spacecp_usergroup_top}-->
	<!--{template home/spacecp_usergroup_header}-->
	<div class="profile-form usergroup-form">
		<!--{if $_G['cache']['forums']}-->
			<div class="perm-grid-header">
				<div class="perm-cell-name">{lang forum}</div>
				<!--{loop $perms $perm}-->
					<div class="perm-cell">$permlang['perms_'.$perm]</div>
				<!--{/loop}-->
			</div>
			<!--{eval $key = 1;}-->
			<!--{loop $_G['cache']['forums'] $fid $forum}-->
				<!--{if $forum['status']}-->
					<div class="perm-grid-row {if $key++%2==0}alt{/if}">
						<div class="perm-cell-name">
							<!--{if $forum['type'] == 'forum'}-->
								<span style="padding-left:15px;">$forum[name]</span>
							<!--{elseif $forum['type'] == 'sub'}-->
								<span style="padding-left:30px;">$forum[name]</span>
							<!--{else}-->
								$forum[name]
							<!--{/if}-->
						</div>
						<!--{loop $perms $perm}-->
							<div class="perm-cell">
								<!--{if !empty($verifyperm[$fid][$perm])}-->
									<!--{if $myverifyperm[$fid][$perm] || $forumperm[$fid][$perm]}-->
										<i class="fico-valid fc-v" title="data_valid"></i>
									<!--{else}-->
										<i class="fico-invalid fc-i" title="data_invalid"></i>
									<!--{/if}-->
								<!--{else}-->
									<!--{if $forumperm[$fid][$perm]}--><i class="fico-valid fc-v" title="data_valid"></i><!--{else}--><i class="fico-invalid fc-i" title="data_invalid"></i><!--{/if}-->
								<!--{/if}-->
							</div>
						<!--{/loop}-->
					</div>
				<!--{/if}-->
			<!--{/loop}-->
		<!--{else}-->
			<p class="emp">{lang memcp_usergroup_unallow}</p>
		<!--{/if}-->
		<div class="form-tip">
			<i class="fico-valid fc-v" title="data_valid"></i> {lang usergroup_right_message1}&nbsp;
			<i class="fico-invalid fc-i" title="data_invalid"></i> {lang usergroup_right_message2}
			<!--{if $_G['setting']['verify']['enabled']}-->
				<!--{echo implode('', $verifyicon)}--> {lang usergroup_right_message3}
			<!--{/if}-->
		</div>
	</div>
	<!--{hook/spacecp_usergroup_bottom}-->

<!--{elseif $do == 'expiry' || $do == 'list'}-->
	<!--{template home/spacecp_header}-->
	<!--{hook/spacecp_usergroup_top}-->
	<!--{template home/spacecp_usergroup_header}-->
	<div class="profile-form usergroup-form">
		<!--{if $do == 'expiry'}-->
			<div class="notice">{lang usergroup_expired}</div>
		<!--{/if}-->
		<div class="list-row list-row-header">
			<div class="list-cell">{lang usergroup}</div>
			<div class="list-cell">{lang memcp_usergroups_dailyprice}</div>
			<div class="list-cell">{lang memcp_usergroups_credit}</div>
			<div class="list-cell">{lang group_expiry_time}</div>
			<div class="list-cell list-cell-action"></div>
		</div>
		<!--{if $expirylist}-->
			<!--{loop $expirylist $groupid $group}-->
				<div class="list-row">
					<div class="list-cell">
						<a href="home.php?mod=spacecp&ac=usergroup&gid=$groupid">$group[grouptitle]</a>
					</div>
					<div class="list-cell">
						<!--{if $_G['cache']['usergroups'][$groupid]['pubtype'] == 'buy' && $group[dailyprice]}-->
							$group[dailyprice] {$_G[setting][extcredits][$_G[setting][creditstrans]][unit]}{$_G[setting][extcredits][$_G[setting][creditstrans]][title]}
						<!--{elseif $_G['cache']['usergroups'][$groupid]['pubtype'] == 'free'}-->
							{lang free}
						<!--{/if}-->
					</div>
					<div class="list-cell"><!--{if $group[usermaxdays]}-->$group[usermaxdays] {lang days}<!--{/if}--></div>
					<div class="list-cell">$group[time]</div>
					<div class="list-cell list-cell-action">
						<!--{if (is_array($extgroupids) && in_array($groupid, $extgroupids)) || $groupid == $_G['groupid']}-->
							<!--{if $groupid != $_G['groupid']}-->
								<!--{if !$group[noswitch]}-->
									<a href="home.php?mod=spacecp&ac=usergroup&do=switch&groupid=$groupid&handlekey=switchgrouphk" class="button-link">{lang memcp_usergroups_set_main}</a>
								<!--{/if}-->
								<!--{if !$group['maingroup']}-->
									<!--{if $_G['cache']['usergroups'][$groupid]['pubtype'] == 'buy'}-->
										<a href="home.php?mod=spacecp&ac=usergroup&do=buy&groupid=$groupid&handlekey=buygrouphk" class="button-link">{lang renew}</a>
									<!--{/if}-->
									<a href="home.php?mod=spacecp&ac=usergroup&do=exit&groupid=$groupid&handlekey=exitgrouphk" class="button-link button-link-danger">{lang memcp_usergroups_exit}</a>
								<!--{/if}-->
							<!--{else}-->
								<!--{if $_G['cache']['usergroups'][$groupid]['pubtype'] == 'buy'}-->
									<a href="home.php?mod=spacecp&ac=usergroup&do=buy&groupid=$groupid&handlekey=buygrouphk" class="button-link">{lang renew}</a>
								<!--{/if}-->
								<span class="tag tag-primary">{lang main_usergroup}</span>
							<!--{/if}-->
						<!--{elseif $_G['cache']['usergroups'][$groupid]['pubtype'] == 'free'}-->
							<a href="home.php?mod=spacecp&ac=usergroup&do=buy&groupid=$groupid&handlekey=buygrouphk" class="button-link">{lang free_buy}</a>
						<!--{elseif $_G['cache']['usergroups'][$groupid]['pubtype'] == 'buy'}-->
							<a href="home.php?mod=spacecp&ac=usergroup&do=buy&groupid=$groupid&handlekey=buygrouphk" class="button-link">{lang memcp_usergroups_buy}</a>
						<!--{/if}-->
					</div>
				</div>
			<!--{/loop}-->
		<!--{else}-->
			<p class="emp">{lang memcp_usergroup_unallow}</p>
		<!--{/if}-->
	</div>
	<!--{hook/spacecp_usergroup_bottom}-->

<!--{else}-->
	<!--{template home/spacecp_header}-->
	<!--{hook/spacecp_usergroup_top}-->
	<!--{template home/spacecp_usergroup_header}-->
	<!--{eval
		$permtype = array(0 => '{lang permission_menu_normaloptions}', 1 => '{lang permission_modoptions_name}');
	}-->
	<div class="profile-form usergroup-form">
		<!-- ͷ����Ϣ��Ƭ -->
		<div class="ug-header-cards">
			<div class="ug-header-card">
				<div class="ug-header-title">{lang my_main_usergroup}</div>
				<div class="ug-header-body">
					<div class="ug-header-group">$maingroup[grouptitle]</div>
					<div class="ug-header-stars"><!--{echo showstars($_G['cache']['usergroups'][$maingroup['groupid']]['stars']);}--></div>
					<div class="ug-header-credit">{lang credits}: $space[credits]</div>
				</div>
			</div>
			<!--{if $group}-->
				<!--{if $switchtype == 'user'}--><!--{eval $cid = 1;$tlang = '{lang usergroup_group1}';}--><!--{/if}-->
				<!--{if $switchtype == 'upgrade'}--><!--{eval $cid = 2;$tlang = '{lang usergroup_group2}';}--><!--{/if}-->
				<!--{if $switchtype == 'admin'}--><!--{eval $cid = 3;$tlang = '{lang usergroup_group3}';}--><!--{/if}-->
				<div class="ug-header-card">
					<div class="ug-header-title">$tlang</div>
					<div class="ug-header-body">
						<div class="ug-header-group">$currentgrouptitle</div>
						<div class="ug-header-stars"><!--{echo showstars($_G['cache']['usergroups'][$group['groupid']]['stars']);}--></div>
						<div class="ug-header-credit">
							<!--{if $group['grouptype'] == 'member'}-->
								<!--{eval $v = $group['groupcreditshigher'] - $_G['member']['credits'];}-->
								<!--{if $_G['group']['grouptype'] == 'member' && $v > 0}-->
									{lang spacecp_usergroup_message1} $v
								<!--{else}-->
									{lang spacecp_usergroup_message2} $group[groupcreditshigher]
								<!--{/if}-->
							<!--{/if}-->
						</div>
						<div class="ug-header-actions">
							<!--{if isset($publicgroup[$group['groupid']]) && $group['groupid'] != $_G['groupid'] && $publicgroup[$group['groupid']]['allowsetmain']}-->
								<a href="home.php?mod=spacecp&ac=usergroup&do=switch&groupid=$group['groupid']&gid=$_GET['gid']&handlekey=switchgrouphk" class="button-link">{lang memcp_usergroups_set_main}</a>
							<!--{/if}-->
							<!--{if (is_array($extgroupids) && in_array($group['groupid'], $extgroupids)) && $switchmaingroup && $group['grouptype'] == 'special' && $group['groupid'] != $_G['groupid']}-->
								<!--{if $_G['cache']['usergroups'][$group['groupid']]['pubtype'] == 'buy'}-->
									<a href="home.php?mod=spacecp&ac=usergroup&do=buy&groupid=$group['groupid']&gid=$_GET['gid']&handlekey=buygrouphk" class="button-link">{lang renew}</a>
								<!--{/if}-->
								<a href="home.php?mod=spacecp&ac=usergroup&do=exit&groupid=$group['groupid']&gid=$_GET['gid']&handlekey=exitgrouphk" class="button-link button-link-danger">{lang memcp_usergroups_exit}</a>
							<!--{/if}-->
							<!--{if $group['grouptype']=='special' && $group['groupid'] != $_G['groupid'] && array_key_exists($group['groupid'], $publicgroup) && !$publicgroup[$group['groupid']]['allowsetmain']}-->
								<a href="home.php?mod=spacecp&ac=usergroup&do=buy&groupid=$group['groupid']&gid=$_GET['gid']&handlekey=buygrouphk" class="button-link">{lang memcp_usergroups_buy}</a>
							<!--{/if}-->
							<!--{if isset($groupterms['ext']) && is_array($groupterms['ext']) && array_key_exists($group['groupid'], $groupterms['ext'])}-->
								<span class="notice">{lang memcp_usergroups_timelimit}: <!--{date($groupterms['ext'][$group['groupid']])}--></span>
							<!--{/if}-->
						</div>
					</div>
				</div>
			<!--{/if}-->
		</div>

		<!-- Ȩ�޶Աȱ�� - ������Ⱦ -->
		<div class="perm-compare-table">
			<!-- ����Ȩ�� -->
			<div class="perm-compare-section">
				<div class="perm-compare-section-title">{lang permission_menu_normaloptions}</div>
				<!-- �û������� -->
				<div class="perm-compare-row">
					<div class="perm-compare-name">{lang user_level}</div>
					<div class="perm-compare-val"><!--{echo showstars($_G['cache']['usergroups'][$maingroup['groupid']]['stars']);}--></div>
					<!--{if $group}-->
						<div class="perm-compare-val"><!--{echo showstars($_G['cache']['usergroups'][$group['groupid']]['stars']);}--></div>
					<!--{/if}-->
				</div>
				<!--{loop $bperms $key $perm}-->
					<div class="perm-compare-row {if $key%2==0}alt{/if}">
						<div class="perm-compare-name">$permlang['perms_'.$perm]</div>
						<div class="perm-compare-val">
							<!--{if $perm == 'creditshigher' || $perm == 'readaccess' || $perm == 'maxpmnum'}-->$maingroup[$perm]
							<!--{elseif $perm == 'allowsearch'}-->
								<!--{if $maingroup['allowsearch'] == '0'}-->{lang permission_basic_disable_sarch}
								<!--{elseif $maingroup['allowsearch'] == '1'}-->{lang permission_basic_search_title}
								<!--{else}-->{lang permission_basic_search_content}<!--{/if}-->
							<!--{else}-->
								<!--{if $maingroup[$perm] >= 1}--><i class="fico-valid fc-v" title="data_valid"></i><!--{else}--><i class="fico-invalid fc-i" title="data_invalid"></i><!--{/if}-->
							<!--{/if}-->
						</div>
						<!--{if $group}-->
							<div class="perm-compare-val">
								<!--{if $perm == 'creditshigher' || $perm == 'readaccess' || $perm == 'maxpmnum'}-->$group[$perm]
								<!--{elseif $perm == 'allowsearch'}-->
									<!--{if $group['allowsearch'] == '0'}-->{lang permission_basic_disable_sarch}
									<!--{elseif $group['allowsearch'] == '1'}-->{lang permission_basic_search_title}
									<!--{else}-->{lang permission_basic_search_content}<!--{/if}-->
								<!--{else}-->
									<!--{if $group[$perm] >= 1}--><i class="fico-valid fc-v" title="data_valid"></i><!--{else}--><i class="fico-invalid fc-i" title="data_invalid"></i><!--{/if}-->
								<!--{/if}-->
							</div>
						<!--{/if}-->
					</div>
				<!--{/loop}-->
			</div>

			<!-- ������� -->
			<div class="perm-compare-section">
				<div class="perm-compare-section-title">{lang permission_menu_post}</div>
				<!--{loop $pperms $key $perm}-->
					<div class="perm-compare-row {if $key%2==0}alt{/if}">
						<div class="perm-compare-name">$permlang['perms_'.$perm]</div>
						<div class="perm-compare-val">
							<!--{if in_array($perm, array('maxsigsize', 'maxbiosize'))}-->$maingroup[$perm] {lang bytes}
							<!--{elseif $perm == 'allowrecommend'}-->
								<!--{if $maingroup[allowrecommend] > 0}-->+$maingroup[allowrecommend]<!--{else}--><i class="fico-invalid fc-i" title="data_invalid"></i><!--{/if}-->
							<!--{elseif in_array($perm, array('allowat', 'allowcreatecollection'))}--><!--{echo intval($maingroup[$perm])}-->
							<!--{else}-->
								<!--{if $maingroup[$perm] == 1 || (in_array($perm, array('raterange', 'allowcommentpost')) && !empty($maingroup[$perm]))}--><i class="fico-valid fc-v" title="data_valid"></i><!--{else}--><i class="fico-invalid fc-i" title="data_invalid"></i><!--{/if}-->
							<!--{/if}-->
						</div>
						<!--{if $group}-->
							<div class="perm-compare-val">
								<!--{if in_array($perm, array('maxsigsize', 'maxbiosize'))}-->$group[$perm] {lang bytes}
								<!--{elseif $perm == 'allowrecommend'}-->
									<!--{if $group[allowrecommend] > 0}-->+$group[allowrecommend]<!--{else}--><i class="fico-invalid fc-i" title="data_invalid"></i><!--{/if}-->
								<!--{elseif in_array($perm, array('allowat', 'allowcreatecollection'))}--><!--{echo intval($group[$perm])}-->
								<!--{else}-->
									<!--{if $group[$perm] == 1 || (in_array($perm, array('raterange', 'allowcommentpost')) && !empty($group[$perm]))}--><i class="fico-valid fc-v" title="data_valid"></i><!--{else}--><i class="fico-invalid fc-i" title="data_invalid"></i><!--{/if}-->
								<!--{/if}-->
							</div>
						<!--{/if}-->
					</div>
				<!--{/loop}-->
			</div>

			<!-- �ռ���� -->
			<div class="perm-compare-section">
				<div class="perm-compare-section-title">{lang about_space}</div>
				<!--{loop $sperms $key $perm}-->
					<div class="perm-compare-row {if $key%2==0}alt{/if}">
						<div class="perm-compare-name">$permlang['perms_'.$perm]</div>
						<div class="perm-compare-val">
							<!--{if in_array($perm, array('maxspacesize', 'maximagesize'))}-->
								<!--{if $maingroup[$perm]}-->$maingroup[$perm]<!--{else}-->{lang permission_attachment_nopermission}<!--{/if}-->
							<!--{elseif in_array($perm, array('allowblog', 'allowupload', 'allowshare', 'allowdoing', 'allowpoke', 'allowclick', 'allowcomment'))}-->
								<!--{if $maingroup[$perm] == 1}--><i class="fico-valid fc-v" title="data_valid"></i><!--{else}--><i class="fico-invalid fc-i" title="data_invalid"></i><!--{/if}-->
							<!--{else}-->
								$maingroup[$perm]
							<!--{/if}-->
						</div>
						<!--{if $group}-->
							<div class="perm-compare-val">
								<!--{if in_array($perm, array('maxspacesize', 'maximagesize'))}-->
									<!--{if $group[$perm]}-->$group[$perm]<!--{else}-->{lang permission_attachment_nopermission}<!--{/if}-->
								<!--{elseif in_array($perm, array('allowblog', 'allowupload', 'allowshare', 'allowdoing', 'allowpoke', 'allowclick', 'allowcomment'))}-->
									<!--{if $group[$perm] == 1}--><i class="fico-valid fc-v" title="data_valid"></i><!--{else}--><i class="fico-invalid fc-i" title="data_invalid"></i><!--{/if}-->
								<!--{else}-->
									$group[$perm]
								<!--{/if}-->
							</div>
						<!--{/if}-->
					</div>
				<!--{/loop}-->
			</div>

			<!-- ������� -->
			<div class="perm-compare-section">
				<div class="perm-compare-section-title">{lang permission_menu_attachment}</div>
				<!--{loop $aperms $key $perm}-->
					<div class="perm-compare-row {if $key%2==0}alt{/if}">
						<div class="perm-compare-name">$permlang['perms_'.$perm]</div>
						<div class="perm-compare-val">
							<!--{if in_array($perm, array('maxattachsize', 'maxsizeperday', 'attachextensions'))}-->
								<!--{if $perm == 'attachextensions'}-->
									<!--{if $maingroup[$perm]}--><p class="nwp">$maingroup[$perm]</p><!--{else}--><i class="fico-invalid fc-i" title="data_invalid"></i><!--{/if}-->
								<!--{else}-->
									<!--{if $maingroup[$perm]}-->$maingroup[$perm]<!--{else}-->{lang permission_attachment_nopermission}<!--{/if}-->
								<!--{/if}-->
							<!--{elseif in_array($perm, array('allowgetattach', 'allowgetimage', 'allowpostattach', 'allowpostimage', 'allowsetattachperm'))}-->
								<!--{if $maingroup[$perm] == 1}--><i class="fico-valid fc-v" title="data_valid"></i><!--{else}--><i class="fico-invalid fc-i" title="data_invalid"></i><!--{/if}-->
							<!--{else}-->
								<!--{if $maingroup[$perm]}-->$maingroup[$perm]<!--{else}-->{lang permission_attachment_nopermission}<!--{/if}-->
							<!--{/if}-->
						</div>
						<!--{if $group}-->
							<div class="perm-compare-val">
								<!--{if in_array($perm, array('maxattachsize', 'maxsizeperday', 'attachextensions'))}-->
									<!--{if $perm == 'attachextensions'}-->
										<!--{if $group[$perm]}--><p class="nwp">$group[$perm]</p><!--{else}--><i class="fico-invalid fc-i" title="data_invalid"></i><!--{/if}-->
									<!--{else}-->
										<!--{if $group[$perm]}-->$group[$perm]<!--{else}-->{lang permission_attachment_nopermission}<!--{/if}-->
									<!--{/if}-->
								<!--{elseif in_array($perm, array('allowgetattach', 'allowgetimage', 'allowpostattach', 'allowpostimage', 'allowsetattachperm'))}-->
									<!--{if $group[$perm] == 1}--><i class="fico-valid fc-v" title="data_valid"></i><!--{else}--><i class="fico-invalid fc-i" title="data_invalid"></i><!--{/if}-->
								<!--{else}-->
									<!--{if $group[$perm]}-->$group[$perm]<!--{else}-->{lang permission_attachment_nopermission}<!--{/if}-->
								<!--{/if}-->
							</div>
						<!--{/if}-->
					</div>
				<!--{/loop}-->
			</div>
		</div>
	</div>
	<!--{hook/spacecp_usergroup_bottom}-->
<!--{/if}-->

<!--{template common/footer}-->