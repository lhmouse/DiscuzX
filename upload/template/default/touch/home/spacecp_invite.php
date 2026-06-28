<?php exit('Access Denied');?>
<!--{if $_GET['op'] == 'resend'}-->
<!--{if $_G[inajax]}-->
<form id="resendform_{$id}" name="resendform_{$id}" method="post" autocomplete="off" action="home.php?mod=spacecp&ac=invite&op=resend&id=$id">
	<input type="hidden" name="referer" value="{echo dreferer()}" />
	<input type="hidden" name="resendsubmit" value="true" />
	<input type="hidden" name="formhash" value="{FORMHASH}" />
	<div class="invite-confirm-msg">{lang sure_resend}</div>
	<div class="post_btn">
		<button type="submit" name="btnsubmit" value="true" class="pn btn_pn">{lang resend}</button>
	</div>
</form>
<!--{else}-->
<!--{template common/header}-->
<div class="header cl">
	<div class="mz"><a href="javascript:history.back();"><i class="dm-c-left"></i></a></div>
	<h2>{lang send_mail_again}</h2>
</div>
<form id="resendform_{$id}" name="resendform_{$id}" method="post" autocomplete="off" action="home.php?mod=spacecp&ac=invite&op=resend&id=$id">
	<input type="hidden" name="referer" value="{echo dreferer()}" />
	<input type="hidden" name="resendsubmit" value="true" />
	<input type="hidden" name="formhash" value="{FORMHASH}" />
	<div class="invite-confirm-msg">{lang sure_resend}</div>
	<div class="post_btn">
		<button type="submit" name="btnsubmit" value="true" class="pn btn_pn">{lang resend}</button>
	</div>
</form>
<!--{template common/footer}-->
<!--{/if}-->
<!--{elseif $_GET['op'] == 'delete'}-->
<!--{if $_G[inajax]}-->
<form id="deleteform_{$id}" name="deleteform_{$id}" method="post" autocomplete="off" action="home.php?mod=spacecp&ac=invite&op=delete&id=$id">
	<input type="hidden" name="referer" value="{echo dreferer()}" />
	<input type="hidden" name="deletesubmit" value="true" />
	<input type="hidden" name="formhash" value="{FORMHASH}" />
	<div class="invite-confirm-msg">{lang delete_log_message}</div>
	<div class="post_btn">
		<button type="submit" name="btnsubmit" value="true" class="pn btn_pn btn_pn_red">{lang delete}</button>
	</div>
</form>
<!--{else}-->
<!--{template common/header}-->
<div class="header cl">
	<div class="mz"><a href="javascript:history.back();"><i class="dm-c-left"></i></a></div>
	<h2>{lang delete_log}</h2>
</div>
<form id="deleteform_{$id}" name="deleteform_{$id}" method="post" autocomplete="off" action="home.php?mod=spacecp&ac=invite&op=delete&id=$id">
	<input type="hidden" name="referer" value="{echo dreferer()}" />
	<input type="hidden" name="deletesubmit" value="true" />
	<input type="hidden" name="formhash" value="{FORMHASH}" />
	<div class="invite-confirm-msg">{lang delete_log_message}</div>
	<div class="post_btn">
		<button type="submit" name="btnsubmit" value="true" class="pn btn_pn btn_pn_red">{lang delete}</button>
	</div>
</form>
<!--{template common/footer}-->
<!--{/if}-->
<!--{elseif $_GET['op'] == 'showinvite'}-->
<!--{loop $list $key $url}-->
<div class="invite-code-item">
	<div class="invite-code-row">
		<span class="invite-code-label">{lang invite_link}</span>
		<a href="$url" onclick="setCopy('$url', '{lang copy_invite_link}');return false;">$url</a>
	</div>
	<div class="invite-code-actions">
		<a href="$url" onclick="setCopy('$url', '{lang copy_invite_link}');return false;">{lang copy}</a>
	</div>
</div>
<!--{/loop}-->
<!--{else}-->
<!--{template common/header}-->
<div class="header cl">
	<div class="mz"><a href="javascript:history.back();"><i class="dm-c-left"></i></a></div>
	<h2>{lang invite_friend}</h2>
	<div class="my"><a href="index.php"><i class="dm-house"></i></a></div>
</div>
<!--{if $allowinvite}-->
<form method="post" id="newinvite" autocomplete="off" action="home.php?mod=spacecp&ac=invite&appid=$appid&ref">
	<!--{if $config[inviteaddcredit] || $config[invitedaddcredit]}-->
	<div class="invite-info">
		{lang friend_invite_success}
		<!--{if $config[invitedaddcredit]}-->{lang you_get} <strong class="fc-n">$config[invitedaddcredit]</strong> {lang unit}{$credittitle},<!--{/if}-->
		<!--{if $config[inviteaddcredit]}-->{lang friend_get} <strong class="fc-n">$config[inviteaddcredit]</strong> {lang unit}{$credittitle},<!--{/if}-->
		{lang go_nuts}
	</div>
	<!--{/if}-->
	<!--{if $flist}-->
	<div class="invite-section cl">
		<div class="invite-section-title">{lang invited_friend}</div>
		<div class="invite-friend-list">
		<!--{if $invitedcount < 24}-->
			<!--{loop $flist $key $value}-->
			<a href="home.php?mod=space&uid=$value[fuid]" class="invite-friend-item">
				<div class="invite-friend-avatar"><!--{avatar($value['fuid'], 'small')}--></div>
				<div class="invite-friend-name">$value[fusername]</div>
			</a>
			<!--{/loop}-->
		<!--{else}-->
			<!--{eval $mod='';}-->
			<!--{loop $flist $key $value}-->
			<!--{eval $mod=$mod ? ', ' : '';}-->
			<a href="home.php?mod=space&uid=$value[fuid]">$mod$value[fusername]</a>
			<!--{/loop}-->
		<!--{/if}-->
		</div>
	</div>
	<!--{/if}-->
	<!--{if $maillist}-->
	<div class="invite-section cl">
		<div class="invite-section-title">{lang no_invite_friend_email}</div>
		<div class="invite-mail-list">
		<!--{loop $maillist $key $value}-->
			<div class="invite-mail-item" id="sendmail_$value[id]_li">
				<div class="invite-mail-info">
					<div class="invite-mail-email">$value[email]</div>
				</div>
				<div class="invite-mail-actions">
					<a href="home.php?mod=spacecp&ac=invite&op=resend&id=$value[id]" title="{lang resend}">{lang resend}</a>
					<a href="javascript:;" title="{lang link}" onclick="setCopy('$value[url]', '{lang copy_invite_link}');return false;">{lang link}</a>
					<a href="home.php?mod=spacecp&ac=invite&op=delete&id=$value[id]" title="{lang delete}">{lang delete}</a>
				</div>
			</div>
			<!--{/loop}-->
		</div>
	</div>
	<!--{/if}-->
	<div class="invite-section cl">
		<div class="invite-section-title">{lang friend_invite_link}</div>
		<div class="invite-desc">{lang friend_invite_message}</div>
		<!--{if $creditnum && $list}-->
		<div class="invite-desc">{lang click_link_copy}</div>
		<!--{/if}-->
		<div class="invite-code-list" id="invitelist">
		<!--{if $list}-->
			<!--{loop $list $key $url}-->
			<div class="invite-code-item">
				<div class="invite-code-row">
					<span class="invite-code-label">{lang invite_link}</span>
					<a href="$url" onclick="setCopy('$url', '{lang copy_invite_link}');return false;">$url</a>
				</div>
				<div class="invite-code-actions">
					<a href="$url" onclick="setCopy('$url', '{lang copy_invite_link}');return false;">{lang copy}</a>
				</div>
			</div>
			<!--{/loop}-->
		<!--{else}-->
			<div class="empty-box">
				<h4>{lang no_invitation_code}</h4>
			</div>
		<!--{/if}-->
		</div>
		<div class="invite-credit-info">
			{lang invitation_code_spend}{$extcredits[title]} <strong class="fc-n">$creditnum</strong> $extcredits[unit]
			( {lang you_have}{$extcredits[title]} <strong id="haveallcredit">{$space[$creditkey]}</strong> $extcredits[unit] )
			<!--{if $space[$creditkey] < $creditnum}-->
			<a href="home.php?mod=spacecp&ac=credit" class="fc-a">{lang credit_recharge}</a>
			<!--{/if}-->
		</div>
		<!--{if $_G['group']['maxinviteday']}-->
		<div class="invite-tip">{lang max_invite_day_message}</div>
		<!--{/if}-->
		<div class="invite-form-row invite-form-row-inline">
			<input type="number" name="invitenum" value="1" class="px" id="invitenum" placeholder="{lang buy_nums}" />
			<button type="submit" name="invitesubmit_btn" value="true" class="pn btn_pn">{lang get_invitation_code}</button>
		</div>
		<!--{if !$creditnum}-->
		<div class="invite-desc">{lang copy_invite_manage}</div>
		<div class="invite-url-box">
			<div class="invite-url-text">$inviteurl</div>
			<a href="$inviteurl" onclick="setCopy('$inviteurl', '{lang copy_invite_link}');return false;" class="invite-url-copy">{lang copy}</a>
		</div>
		<!--{/if}-->
	</div>
	<input type="hidden" name="handlekey" value="newinvite" />
	<input type="hidden" name="invitesubmit" value="true" />
	<input type="hidden" name="formhash" value="{FORMHASH}" />
</form>
<script type="text/javascript">
	(function() {
		var form = $('#newinvite');
		var submitBtn = form.find('button[type="submit"]');
		form.on('submit', function(e) {
			e.preventDefault();
			if(submitBtn.prop('disabled')) return false;
			submitBtn.prop('disabled', true);
			$.ajax({
				type: 'POST',
				url: form.attr('action') + '&inajax=1',
				data: form.serialize(),
				dataType: 'xml'
			})
			.success(function(s) {
				var msg = s.lastChild.firstChild.nodeValue;
				if(msg.indexOf('ajaxerror') != -1) {
					popup.open(msg, 'alert');
				} else {
					popup.open('{lang invitecode_succeed_title}', 'alert');
					setTimeout(function() {
						location.reload();
					}, 1000);
				}
			})
			.error(function() {
				popup.open('{lang networkerror}', 'alert');
			})
			.complete(function() {
				submitBtn.prop('disabled', false);
			});
			return false;
		});
	})();
</script>
<!--{if $_G['group']['allowmailinvite']}-->
<form method="post" id="emailinviteform" autocomplete="off" action="home.php?mod=spacecp&ac=invite&type=mail&appid=$appid&ref">
	<input type="hidden" name="formhash" value="{FORMHASH}" />
	<div class="invite-section">
		<div class="invite-section-title">{lang send_invitation_email}<!--{if $appid}-->{lang friend_play_together}$appinfo[appname]<!--{/if}--></div>
		<div class="invite-desc">{lang send_invitation_email_message}</div>
		<div class="invite-form-row">
			<div class="invite-form-label">{lang friend_email_address}</div>
			<textarea name="email" id="email" rows="3" class="pt" placeholder="{lang friend_email_address}"></textarea>
		</div>
		<div class="invite-form-row">
			<div class="invite-form-label">{lang friend_to_say}</div>
			<input type="text" name="saymsg" id="saymsg" class="px" placeholder="{lang friend_to_say}" onkeyup="showPreview(this.value, 'sayPreview')">
		</div>
		<div class="post_btn">
			<button type="submit" name="emailinvite" value="true" class="pn btn_pn">{lang invite}</button>
		</div>
	</div>
	<div class="invite-section">
		<div class="invite-section-title">{lang preview_invitation}</div>
		<div class="invite-preview-box">
			<div class="invite-preview-avatar">{$mailvar[avatar]}</div>
			<div class="invite-preview-content">
				<h4>{lang hi_iam_invite_you}</h4>
				<p>{lang become_friend_message}</p>
				<p>{lang invite_add_note}:</p>
				<p id="sayPreview" class="invite-preview-note"></p>
				<h4>{lang click_link_become_friend}:</h4>
				<p class="invite-preview-link">{$inviteurl}</p>
				<h4>{lang have_account_view_homepage}</h4>
				<p class="invite-preview-link">{$mailvar[siteurl]}home.php?mod=space&uid=$mailvar[uid]</p>
			</div>
		</div>
	</div>
</form>
<!--{/if}-->
<!--{else}-->
<div class="empty-box">
	<h4>{lang no_right_invite_friend}</h4>
</div>
<!--{/if}-->
<!--{/if}-->
<!--{if !$_G[inajax]}-->
<!--{template common/footer}-->
<!--{/if}-->
