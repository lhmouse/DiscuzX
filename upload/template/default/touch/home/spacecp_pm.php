<?php exit('Access Denied');?>
<!--{template common/header}-->
<!--{if $op != ''}-->
	<div class="empty-box">
		<h4>{lang user_mobile_pm_error}</h4>
	</div>
<!--{else}-->
<form id="pmform_{$pmid}" name="pmform_{$pmid}" method="post" autocomplete="off" action="home.php?mod=spacecp&ac=pm&op=send&touid=$touid&pmid=$pmid&mobile=2" class="pm-form">
	<input type="hidden" name="referer" value="{echo dreferer();}" />
	<input type="hidden" name="pmsubmit" value="true" />
	<input type="hidden" name="formhash" value="{FORMHASH}" />
	<div class="header cl">
		<div class="mz"><a href="home.php?mod=space&do=pm"><i class="dm-c-left"></i></a></div>
		<h2>{lang send_pm}</h2>
	</div>
	<div class="pm-form-body">
		<!--{if !$touid}-->
		<div class="pm-form-row">
			<div class="pm-form-label"><i class="fico-person fic6 fc-s"></i></div>
			<div class="pm-form-field">
				<input type="text" value="" class="px" autocomplete="off" id="username" name="username" placeholder="{lang addressee}">
			</div>
		</div>
		<!--{/if}-->
		<div class="pm-form-row pm-form-row-message">
			<div class="pm-form-field">
				<textarea class="pt" autocomplete="off" id="sendmessage" name="message" placeholder="{lang thread_content}"></textarea>
			</div>
		</div>
	</div>
	<div class="pm-form-footer">
		<button id="pmsubmit_btn" class="pn btn_pn btn_pn_grey" disabled="disabled">{lang sendpm}</button>
	</div>
</form>
<script type="text/javascript">
	(function() {
		var btn = $('#pmsubmit_btn');
		$('#sendmessage').on('keyup input', function() {
			var obj = $(this);
			if(obj.val()) {
				btn.removeClass('btn_pn_grey').addClass('btn_pn_blue');
				btn.prop('disabled', false);
			} else {
				btn.removeClass('btn_pn_blue').addClass('btn_pn_grey');
				btn.prop('disabled', true);
			}
		});
		var form = $('#pmform_{$pmid}');
		$('#pmsubmit_btn').on('click', function() {
			var obj = $(this);
			if(obj.prop('disabled')) {
				return false;
			}
			$.ajax({
				type:'POST',
				url:form.attr('action') + '&handlekey='+form.attr('id')+'&inajax=1',
				data:form.serialize(),
				dataType:'xml'
			})
			.success(function(s) {
				popup.open(s.lastChild.firstChild.nodeValue);
			})
			.error(function() {
				popup.open('{lang networkerror}', 'alert');
			});
			return false;
		});
	 })();
</script>
<!--{/if}-->
<!--{template common/footer}-->