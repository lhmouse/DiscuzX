<?php exit('Access Denied');?>
<!--{template common/header}-->
    <script type="text/javascript" src="{$_G[setting][iconfont]}?{VERHASH}"></script>
	<!--{subtemplate home/spacecp_header}-->

		<div class="profile-form account-form">
			<div class="form-section-title">{lang action_account_title_security}</div>
			<div class="form-section">
                <!--{if $_G['setting']['security_rename']}-->
				<div class="form-row">
					<div class="form-label">{lang action_account_security_type_rename}</div>
					<div class="form-field">
						<div class="account-value">
							{$_G['member']['username']}
							<span class="account-action">
								<!--{if getuserprofile('extcredits'.$creditExtra) < $_G['setting']['chgusername']['credits_pay'] || ($_G['member']['credits'] < $_G['setting']['chgusername']['credits_threshold'] && !in_array($_G['member']['groupid'], (array)$_G['setting']['chgusername']['credits_unlimit_group']))}-->
								<span style="color: #646464;">{lang action_account_operate_chg}</span>
								<!--{else}-->
								<a href="home.php?mod=spacecp&ac=account&op=verify&method=chgusername&formhash={FORMHASH}" style="color: green;" class="dialog">{lang action_account_operate_chg}</a>
								<!--{/if}-->
							</span>
						</div>
						<p class="d xs1 xg1">
							{lang action_account_security_type_rename_comment}
							<!--{if $_G['setting']['chgusername']['max_times'] > 0}-->
								, {lang action_account_security_rename_numberoftimes1} <!--{echo ($_G['setting']['chgusername']['max_times'] - table_common_member_username_history::t()->count_by_uid($_G['uid']));}--> {lang action_account_security_rename_numberoftimes2}
							<!--{/if}-->
							<!--{if $_G['setting']['chgusername']['credits_threshold'] > 0}-->
								, {lang action_account_security_rename_credits_low1} {$_G['setting']['chgusername']['credits_threshold']} {lang action_account_security_rename_credits_low2}
							<!--{/if}-->
							<!--{if $_G['setting']['chgusername']['credits_pay'] > 0}-->
								,
								{lang action_account_security_rename_credits_pay_low} {$_G['setting']['chgusername']['credits_pay']} {$extcredit['unit']} {$extcredit['title']}
							<!--{/if}-->
						</p>
					</div>
				</div>
                <!--{/if}-->
                <!--{if $_G[member][loginname] != $_G[member][username]}-->
				<div class="form-row">
					<div class="form-label">{lang loginname}</div>
					<div class="form-field">{$_G['member']['loginname']}</div>
				</div>
                <!--{/if}-->
                <!--{if $_G['setting']['security_password']}-->
				<div class="form-row">
					<div class="form-label">{lang action_account_security_type_password}</div>
					<div class="form-field">
						<div class="account-value">
							******
							<span class="account-action">
								<a href="home.php?mod=spacecp&ac=account&op=verify&method=chgpassword&formhash={FORMHASH}" style="color: green;" class="dialog">{lang action_account_operate_password_modify}</a>
							</span>
						</div>
						<p class="d xs1 xg1">{lang action_account_security_type_password_comment}</p>
						<!--{if $_G['member']['freeze'] == 1}-->
							<strong class="xi1">{lang freeze_pw_tips}</strong>
						<!--{/if}-->
						<!--{if $pwdexpire == 1}-->
							<p>{lang pwdexpire_1}</p>
						<!--{elseif $pwdexpire == 2}-->
							<p><strong class="xi1">{lang pwdexpire_2}</strong></p>
						<!--{/if}-->
					</div>
				</div>
                <!--{/if}-->
                <!--{if $_G['setting']['security_question']}-->
				<div class="form-row">
					<div class="form-label">{lang action_account_security_type_question}</div>
					<div class="form-field">
						<div class="account-value">
							<span class="d xs1 xg1">{lang action_account_security_type_question_comment}</span>
							<span class="account-action">
								<a href="home.php?mod=spacecp&ac=account&op=verify&method=chgquestion&formhash={FORMHASH}" style="color: green;" class="dialog">{lang action_account_operate_chg}</a>
							</span>
						</div>
					</div>
				</div>
                <!--{/if}-->
                <!--{if $_G['setting']['security_email']}-->
				<div class="form-row">
					<div class="form-label">{lang action_account_security_type_email}</div>
					<div class="form-field">
						<div class="account-value">
							<!--{if $_G['member']['email']}-->
								<!--{eval $email_arr = explode('@', $_G['member']['email']);}-->
								<!--{eval echo substr($email_arr[0], 0, 3).'****' . '@' . $email_arr[1]}-->
								<!--{if $_G['member']['emailstatus']}-->
									<span style="color: green;">({lang action_account_status_active})</span>
								<!--{else}-->
									<a href="home.php?mod=spacecp&ac=account&op=verifyemail&method=resend&formhash={FORMHASH}" style="color: red;">({lang action_account_operate_email_active})</a>
								<!--{/if}-->
							<!--{else}-->
								{lang action_account_security_type_data_empty}
							<!--{/if}-->
							<span class="account-action">
								<a href="home.php?mod=spacecp&ac=account&op=verify&method=chgemail&formhash={FORMHASH}" style="color: green;" class="dialog">{lang action_account_operate_chg}</a>
							</span>
						</div>
						<p class="d xs1 xg1">{lang action_account_security_type_email_comment}</p>
						<!--{if $_G['member']['freeze'] == 2}-->
							<strong class="xi1 xs1">{lang freeze_email_tips}</strong>
						<!--{/if}-->
					</div>
				</div>
                <!--{/if}-->
                <!--{if $_G['setting']['security_mobile']}-->
				<div class="form-row">
					<div class="form-label">{lang action_account_security_type_mobile}</div>
					<div class="form-field">
						<div class="account-value">
							<!--{if $_G['member']['secmobile']}-->
								<!--{if $_G['member']['secmobicc']}-->+{$_G['member']['secmobicc']} <!--{/if}-->
								<!--{eval echo substr($_G['member']['secmobile'], 0, 3).'****'.substr($_G['member']['secmobile'], -3);}-->
							<!--{else}-->
								{lang action_account_security_type_data_empty}
							<!--{/if}-->
							<span class="account-action">
								<!--{if !$_G['member']['secmobile']}-->
									<a href="home.php?mod=spacecp&ac=account&op=verify&method=bindmobile&formhash={FORMHASH}" style="color: green;" class="dialog">{lang action_account_operate_bind}</a>
								<!--{else}-->
									<a href="home.php?mod=spacecp&ac=account&op=verify&method=unbindmobile&formhash={FORMHASH}" style="color: red;" class="dialog">{lang action_account_operate_unbind}</a>
								<!--{/if}-->
							</span>
						</div>
						<p class="d xs1 xg1">{lang action_account_security_type_mobile_comment}</p>
					</div>
				</div>
                <!--{/if}-->
                <!--{if $_G['setting']['security_logoff']}-->
				<div class="form-row">
					<div class="form-label">{lang action_account_security_type_logoff}</div>
					<div class="form-field">
						<div class="account-value">
							<span class="d xs1 xg1">{lang action_account_security_type_logoff_comment}</span>
							<span class="account-action">
								<a href="home.php?mod=spacecp&ac=account&op=verify&method=logoff&formhash={FORMHASH}" style="color: green;" class="dialog">{lang action_account_operate_logoff_set}</a>
							</span>
						</div>
					</div>
				</div>
                <!--{/if}-->
                <!--{if $_G['member']['freeze'] == 2 || $_G['member']['freeze'] == -1}-->
				<div class="form-row">
					<div class="form-label">{lang action_account_security_type_freeze}</div>
					<div class="form-field">
						<div class="account-value">
							<span class="account-action">
								<a href="home.php?mod=spacecp&ac=account&op=verify&method=freeze&formhash={FORMHASH}" onclick="showWindow('security_verify', this.href, 'get', 0);return false;" style="color: green;" class="dialog">{lang action_account_security_type_freeze_reason_submit}</a>
							</span>
						</div>
						<!--{if $_G['member']['freeze'] == 2}--><span class="d xs1 xg1">{lang freeze_reason_comment}</span>
						<!--{elseif $_G['member']['freeze'] == -1}-->
							<p class="xs1"><strong class="xi1">{lang freeze_admincp_tips}</strong></p>
						<!--{/if}-->
					</div>
				</div>
                <!--{/if}-->
			</div>
            <!--{if $list}-->
			<div class="form-section-title">{lang action_account_title_third_login_method}</div>
			<div class="form-section">
				<!--{eval $i = 0;}-->
				<!--{loop $list $key $value}-->
				<!--{eval $i++;}-->
				<div class="form-row third-account-row {if $i % 2 == 0}alt{/if}">
					<div class="form-label">
						<span class="account-icon">{cell account/icon}</span>
						<span class="account-name">{$value[1]}</span>
					</div>
					<div class="form-field">
						<!--{if !empty($account_list[$value[0]]['account'])}-->
							<div class="account-value">
								<span class="bind-name">{$account_list[$value[0]]['bindname']}</span>
								<span class="account-action">
									<a href="home.php?mod=spacecp&ac=account&op=unbind&method={$value[0]}&formhash={FORMHASH}" style="color: red;">{lang action_account_operate_unbind}</a>
								</span>
							</div>
						<!--{else}-->
							<div class="account-value">
								<span class="d xs1 xg1">{lang action_account_status_unbind}</span>
								<span class="account-action">
									<a style="color: green;" href="login.php?method={$value[0]}&formhash={FORMHASH}">{lang action_account_operate_bind}</a>
								</span>
							</div>
						<!--{/if}-->
					</div>
				</div>
				<!--{/loop}-->
			</div>
            <!--{/if}-->
		</div>
<!--{template common/footer}-->