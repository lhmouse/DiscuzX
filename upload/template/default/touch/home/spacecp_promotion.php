<?php exit('Access Denied');?>
<!--{template common/header}-->
	<!--{template home/spacecp_header}-->
	<!--{hook/spacecp_promotion_top}-->
	<!--{if $_G['setting']['creditspolicy']['promotion_visit'] || $_G['setting']['creditspolicy']['promotion_register']}-->
		<div class="profile-form promotion-form">
			<!-- �ƹ�˵�� -->
			<div class="promotion-info">
				<!--{if $_G['setting']['creditspolicy']['promotion_visit']}-->
					<div class="promotion-info-item">
						{lang post_promotion_url}
					</div>
				<!--{/if}-->
				<!--{if $_G['setting']['creditspolicy']['promotion_register']}-->
					<div class="promotion-info-item">
						<!--{if $_G['setting']['creditspolicy']['promotion_visit']}-->
							{lang post_promotion_reg}
						<!--{else}-->
							{lang post_promotion}
						<!--{/if}-->
					</div>
				<!--{/if}-->
			</div>

			<!-- ��ʽһ -->
			<div class="form-section">
				<div class="form-section-title">{lang mode_one}</div>
				<div class="form-row promotion-row-block">
					<div class="form-label">{lang post_promotion_url1}</div>
					<div class="form-field">
						<input type="text" class="px" onclick="this.select();setCopy('{$copystr}'+'\n'+this.value, '{lang promotion_url_copied}');" value="$_G[siteurl]?fromuid=$_G[uid]" readonly />
						<button type="button" class="pn" onclick="setCopy('{$copystr}'+'\n'+'$_G[siteurl]?fromuid=$_G[uid]', '{lang promotion_url_copied}');"><em>{lang copy}</em></button>
					</div>
				</div>
				<div class="form-row promotion-row-block">
					<div class="form-label">{lang post_promotion_url2}</div>
					<div class="form-field">
						<input type="text" class="px" onclick="this.select();setCopy('{$copystr}'+'\n'+this.value, '{lang promotion_url_copied}');" value="$_G[siteurl]?fromuser={echo rawurlencode($_G[username])}" readonly />
						<button type="button" class="pn" onclick="setCopy('{$copystr}'+'\n'+'$_G[siteurl]?fromuser={echo rawurlencode($_G[username])}', '{lang promotion_url_copied}');"><em>{lang copy}</em></button>
					</div>
				</div>
			</div>

			<!-- ��ʽ�� -->
			<div class="form-section">
				<div class="form-section-title">{lang mode_two}</div>
				<div class="form-tip">
					{lang mode_two_desc}
				</div>
			</div>
		</div>
	<!--{/if}-->
	<!--{hook/spacecp_promotion_bottom}-->
<!--{template common/footer}-->