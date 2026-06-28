<?php exit('Access Denied');?>
<!--{template common/header}-->
<!--{subtemplate home/spacecp_header}-->
	<!--{if $validate}-->
		<p class="tbmu mb10">{lang validator_comment}</p>
		<form action="member.php?mod=regverify" method="post" autocomplete="off" class="validate-form profile-form">
			<input type="hidden" value="{FORMHASH}" name="formhash" />
			<div class="form-section">
				<div class="form-row">
					<div class="form-label">{lang validator_remark}</div>
					<div class="form-field">$validate[remark]</div>
				</div>
				<div class="form-row">
					<div class="form-label">{lang validator_message}</div>
					<div class="form-field">
						<input type="text" class="px" name="regmessagenew" value="" />
					</div>
				</div>
			</div>
			<div class="form-submit">
				<button type="submit" name="verifysubmit" value="true" class="pn pnc"><strong>{lang validator_submit}</strong></button>
			</div>
		</form>
	<!--{else}-->
		<!--{if $operation == 'password'}-->
			<script type="text/javascript" src="{$_G[setting][jspath]}register.js?{VERHASH}"></script>
			<p class="pt10 mb10 form-tip">
				<!--{if !$_G['member']['freeze']}-->
					<!--{if empty($_G['setting']['connect']['allow']) || !$conisregister}-->{lang old_password_comment}<!--{elseif $wechatuser}-->{lang wechat_config_newpassword_comment}<!--{else}-->{lang connect_config_newpassword_comment}<!--{/if}-->
				<!--{elseif $_G['member']['freeze'] == 1}-->
					<strong class="xi1">{lang freeze_pw_tips}</strong>
				<!--{elseif $_G['member']['freeze'] == 2}-->
					<strong class="xi1">{lang freeze_email_tips}</strong>
				<!--{elseif $_G['member']['freeze'] == -1}-->
					<strong class="xi1">{lang freeze_admincp_tips}</strong>
				<!--{/if}-->
			</p>
			<form action="home.php?mod=spacecp&ac=profile" method="post" autocomplete="off" class="password-form profile-form">
				<input type="hidden" value="{FORMHASH}" name="formhash" />
				<div class="form-section">
					<!--{if empty($_G['setting']['connect']['allow']) || !$conisregister}-->
						<div class="form-row">
							<div class="form-label"><span class="rq" title="{lang required}">*</span>{lang old_password}</div>
							<div class="form-field">
								<input type="password" name="oldpassword" id="oldpassword" class="px" />
							</div>
						</div>
					<!--{/if}-->
					<div class="form-row">
						<div class="form-label">{lang new_password}</div>
						<div class="form-field">
							<input type="password" name="newpassword" id="newpassword" class="px" />
							<p class="d" id="chk_newpassword">{lang memcp_profile_passwd_comment}</p>
						</div>
					</div>
					<div class="form-row">
						<div class="form-label">{lang new_password_confirm}</div>
						<div class="form-field">
							<input type="password" name="newpassword2" id="newpassword2" class="px" />
							<p class="d" id="chk_newpassword2">{lang memcp_profile_passwd_comment}</p>
						</div>
					</div>
					<div class="form-row" id="contact"<!--{if isset($_GET[from]) && $_GET[from] == 'contact'}--> style="background-color: {$_G['style']['specialbg']};"<!--{/if}-->>
						<div class="form-label">{lang email}</div>
						<div class="form-field">
							<input type="text" name="emailnew" id="emailnew" value="$space[email]" class="px" />
							<p class="d">
								<!--{if $_G['member']['freeze'] == 2}-->
									<p class="xi1">{lang freeze_email_tips}</p>
								<!--{elseif empty($space['newemail'])}-->
									{lang email_been_active}
								<!--{else}-->
									$acitvemessage
								<!--{/if}-->
							</p>
							<!--{if $_G['setting']['regverify'] == 1 && (($_G['group']['grouptype'] == 'member' && in_array($_G['adminid'], array(0, -1))) || $_G['groupid'] == 8) || $_G['member']['freeze']}--><p class="d">{lang memcp_profile_email_comment}</p><!--{/if}-->
						</div>
					</div>
					<div class="form-row form-row-secmobile">
						<div class="form-label">{lang secmobile}</div>
						<div class="form-field">
							<input type="text" name="secmobiccnew" id="secmobiccnew" value="$space[secmobicc]" class="px" style="width: 50px;" />
							<input type="text" name="secmobilenew" id="secmobilenew" value="$space[secmobile]" class="px" />
							<p class="d">{lang memcp_profile_secmobile_comment} $_G['setting']['smsdefaultcc']</p>
						</div>
					</div>
					<!--{if $_G['setting']['smsstatus']}-->
					<div class="form-row form-row-seccode">
						<div class="form-label">{lang secmobseccode}</div>
						<div class="form-field">
							<input type="text" name="secmobseccodenew" id="secmobseccodenew" value="" class="px" />
							<button type="button" name="secmobseccodesendnew" id="secmobseccodesendnew" value="true" class="pn pnc" onclick="memcp_sendsecmobseccode();"><strong>{lang send}</strong></button>
							<p class="d">{lang memcp_profile_secmobseccode_comment}</p>
						</div>
					</div>
					<!--{/if}-->

					<!--{if $_G['member']['freeze'] == 2 || $_G['member']['freeze'] == -1}-->
					<div class="form-row">
						<div class="form-label">{lang freeze_reason}</div>
						<div class="form-field">
							<textarea rows="3" cols="80" name="freezereson" class="pt">$space[freezereson]</textarea>
							<!--{if $_G['member']['freeze'] == 2}--><p class="d" id="chk_newpassword2">{lang freeze_reason_comment}</p><!--{/if}-->
							<!--{if $_G['member']['freeze'] == -1}--><p class="d" id="chk_newpassword2">{lang freeze_reason_admincp_comment}</p><!--{/if}-->
						</div>
					</div>
					<!--{/if}-->

					<!--{if ($_G['member']['freeze'] == 2 || $_G['member']['freeze'] == -1) && !empty($space[freezemodremark])}-->
					<div class="form-row">
						<div class="form-label">{lang freeze_remark}</div>
						<div class="form-field">
							<textarea rows="3" cols="80" name="freezemodremark" class="pt" disabled="disabled">$space[freezemodremark]</textarea>
							<p class="d" id="chk_newpassword2">{lang freeze_remark_comment}</p>
						</div>
					</div>
					<!--{/if}-->

					<div class="form-row form-row-question">
						<div class="form-label">{lang security_question}</div>
						<div class="form-field">
							<select name="questionidnew" id="questionidnew">
								<option value="" selected>{lang memcp_profile_security_keep}</option>
								<option value="0">{lang security_question_0}</option>
								<option value="1">{lang security_question_1}</option>
								<option value="2">{lang security_question_2}</option>
								<option value="3">{lang security_question_3}</option>
								<option value="4">{lang security_question_4}</option>
								<option value="5">{lang security_question_5}</option>
								<option value="6">{lang security_question_6}</option>
								<option value="7">{lang security_question_7}</option>
							</select>
							<p class="d">{lang memcp_profile_security_comment}</p>
						</div>
					</div>

					<div class="form-row">
						<div class="form-label">{lang security_answer}</div>
						<div class="form-field">
							<input type="text" name="answernew" id="answernew" class="px" />
							<p class="d">{lang memcp_profile_security_answer_comment}</p>
						</div>
					</div>
				</div>
				<!--{if $secqaacheck || $seccodecheck}-->
					<!--{eval $sectpl = '<div class="form-section"><div class="form-row"><div class="form-label"><sec></div><div class="form-field"><sec><p class="d"><sec></p></div></div></div>';}-->
					<!--{subtemplate common/seccheck}-->
				<!--{/if}-->
				<div class="form-submit">
					<button type="submit" name="pwdsubmit" value="true" class="pn pnc"><strong>{lang save}</strong></button>
				</div>
				<input type="hidden" name="passwordsubmit" value="true" />
			</form>
			<script type="text/javascript">
				var strongpw = new Array();
				<!--{if $_G['setting']['strongpw']}-->
					<!--{loop $_G['setting']['strongpw'] $key $val}-->
					strongpw[$key] = $val;
					<!--{/loop}-->
				<!--{/if}-->
				var pwlength = <!--{if $_G['setting']['pwlength']}-->$_G['setting']['pwlength']<!--{else}-->0<!--{/if}-->;
				checkPwdComplexity($('newpassword'), $('newpassword2'), true);
			</script>
			<!--{if $_G['setting']['smsstatus']}-->
			<script type="text/javascript">
			function memcp_sendsecmobseccode() {
				memcp_svctype = 1;
				memcp_secmobicc = getID("secmobiccnew").value;
				memcp_secmobile = getID("secmobilenew").value;
				return sendsecmobseccode(memcp_svctype, memcp_secmobicc, memcp_secmobile);
			}
			</script>
			<!--{/if}-->
		<!--{else}-->
			<!--{hook/spacecp_profile_top}-->
			<!--{subtemplate home/spacecp_profile_nav}-->
				<!--{if $vid}-->
				<p class="tbms mt10 {if !$showbtn}tbms_r{/if}"><!--{if $showbtn}-->{lang spacecp_profile_message1}<!--{else}-->{lang spacecp_profile_message2}<!--{/if}--></p>
				<!--{/if}-->
			<iframe id="frame_profile" name="frame_profile" style="display: none"></iframe>
			<form action="{if $operation != 'plugin'}home.php?mod=spacecp&ac=profile&op=$operation{else}home.php?mod=spacecp&ac=plugin&op=profile&id=$_GET[id]{/if}" method="post" enctype="multipart/form-data" autocomplete="off"<!--{if $operation != 'plugin'}--> target="frame_profile"<!--{/if}--> onsubmit="clearErrorInfo();" class="profile-form">
				<input type="hidden" value="{FORMHASH}" name="formhash" />
				<!--{if !empty($_GET[vid])}-->
				<input type="hidden" value="$_GET[vid]" name="vid" />
				<!--{/if}-->
				<div class="form-section" id="profilelist">
					<div class="form-row form-row-readonly">
						<div class="form-label">{lang username}</div>
						<div class="form-field">
							$_G[member][username]<!--{if $_G[member][loginname] != $_G[member][username]}--> ({lang loginname}: $_G[member][loginname])<!--{/if}-->
						</div>
					</div>
				<!--{loop $settings $key $value}-->
				<!--{if $value[available]}-->
					<div class="form-row" id="tr_$key">
						<div class="form-label" id="th_$key"><!--{if $value[required]}--><span class="rq" title="{lang required}">*</span><!--{/if}-->$value[title]</div>
						<div class="form-field" id="td_$key">
							$htmls[$key]
						</div>
						<!--{if !$vid}-->
						<div class="form-privacy">
							<select name="privacy[$key]">
								<option value="0"<!--{if isset($privacy[$key]) && $privacy[$key] == "0"}--> selected="selected"<!--{/if}-->>{lang open_privacy}</option>
								<option value="1"<!--{if isset($privacy[$key]) && $privacy[$key] == "1"}--> selected="selected"<!--{/if}-->>{lang friend_privacy}</option>
								<option value="3"<!--{if isset($privacy[$key]) && $privacy[$key] == "3"}--> selected="selected"<!--{/if}-->>{lang secrecy}</option>
							</select>
						</div>
						<!--{else}-->
						<input type="hidden" name="privacy[$key]" value="3" />
						<!--{/if}-->
					</div>
				<!--{/if}-->
				<!--{/loop}-->
				<!--{if $allowcstatus && is_array($allowitems) && in_array('customstatus', $allowitems)}-->
					<div class="form-row">
						<div class="form-label" id="th_customstatus">{lang permission_basic_status}</div>
						<div class="form-field" id="td_customstatus">
							<input type="text" value="$space[customstatus]" name="customstatus" id="customstatus" class="px" />
							<div class="form-error" id="showerror_customstatus"></div>
						</div>
					</div>
				<!--{/if}-->
				<!--{if $_G['group']['maxsigsize'] && is_array($allowitems) && in_array('sightml', $allowitems)}-->
					<div class="form-row">
						<div class="form-label" id="th_sightml">{lang personal_signature}</div>
						<div class="form-field" id="td_sightml">
							<div class="tedt">
								<div class="area">
									<textarea rows="3" cols="80" name="sightml" id="sightmlmessage" class="pt" onkeydown="ctrlEnter(event, 'profilesubmitbtn');">$space[sightml]</textarea>
								</div>
							</div>
						</div>
					</div>
				<!--{/if}-->

				<!--{if $operation == 'contact'}-->
					<div class="form-row form-row-readonly">
						<div class="form-label" id="th_sightml">Email</div>
						<div class="form-field" id="td_sightml">
							$space[email]&nbsp;(<a href="home.php?mod=spacecp&ac=profile&op=password&from=contact#contact">{lang modify}</a>)
						</div>
					</div>
				<!--{/if}-->

				<!--{if $operation == 'plugin'}-->
					<!--{eval include(template($_GET['id']));}-->
				<!--{/if}-->
				<!--{hook/spacecp_profile_extra}-->
				<!--{if $showbtn}-->
					<div class="form-submit">
						<input type="hidden" name="profilesubmit" value="true" />
						<button type="submit" name="profilesubmitbtn" id="profilesubmitbtn" value="true" class="pn pnc"><strong>{lang save}</strong></button>
						<span id="submit_result" class="rq"></span>
					</div>
				<!--{/if}-->
				</div>
				<!--{hook/spacecp_profile_bottom}-->
			</form>
			<script type="text/javascript">
				function show_error(fieldid, extrainfo) {
					var elem = getID('th_'+fieldid);
					if(elem) {
						elem.className = "rq";
						fieldname = elem.innerHTML;
						extrainfo = (typeof extrainfo == "string") ? extrainfo : "";
						getID('showerror_'+fieldid).innerHTML = "{lang check_date_item} " + extrainfo;
						getID(fieldid).focus();
					}
				}
				function show_success(message) {
					message = message == '' ? '{lang update_date_success}' : message;
					popup.open(message, 'alert');
				}
				function clearErrorInfo() {
					var spanObj = getID('profilelist').getElementsByTagName("div");
					for(var i in spanObj) {
						if(typeof spanObj[i].id != "undefined" && spanObj[i].id.indexOf("_")) {
							var ids = explode('_', spanObj[i].id);
							if(ids[0] == "showerror") {
								spanObj[i].innerHTML = '';
								getID('th_'+ids[1]).className = '';
							}
						}
					}
				}
			</script>
		<!--{/if}-->
	<!--{/if}-->
</div>

<!--{template common/footer}-->
