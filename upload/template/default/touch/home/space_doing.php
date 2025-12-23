<?php exit('Access Denied'); ?>
<!--{template common/header}-->
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        var recommendBtns = document.querySelectorAll('.doing_recommend_btn');
        for (let i = 0; i < recommendBtns.length; i++) {
            recommendBtns[i].addEventListener('click', function() {
                if (this.classList.contains('disabled')) {
                    return;
                }
                
                var doid = this.getAttribute('data-doid');
                var btn = this;
                var countElem = this.querySelector('.recommend_count');
                var iconElem = this.querySelector('i');

                btn.classList.add('disabled');
                fetch('home.php?mod=space&do=doing&op=recommend&doid=' + doid)
                    .then(function(response) {
                        return response.json();
                    })
                    .then(function(data) {
                        btn.classList.remove('disabled');
                        
                        if (data && data.message === 'doing_recommend_success') {
                            btn.setAttribute('data-status', data.status);
                            countElem.innerHTML = data.count;
                        
                            if (parseInt(data.status) === 1) {
                                iconElem.className = 'fico-thumbup fc-i';
                            } else {
                                iconElem.className = 'fico-thumbup fc-s';
                            }
                        } else {
                            console.error('error:', data);
                        }
                    })
                    .catch(function(error) {
                        btn.classList.remove('disabled');
                        console.error('error:', error);
                    });
            });
        }
    });
</script>

<div class="header cl">
	<div class="mz"><a href="javascript:history.back();"><i class="dm-c-left"></i></a></div>
	<h2>{lang doing}</h2>
	<div class="my"><a href="home.php?mod=spacecp&ac=doing"><i class="dm-edit"></i></a></div>
</div>

<div class="dhnv flex-box cl">
	<a href="home.php?mod=space&do=$do&view=we" class="flex{if $_GET['view'] == 'we'} mon{/if}">{lang me_friend_doing}</a>
	<a href="home.php?mod=space&do=$do&view=me" class="flex{if $_GET['view'] == 'me'} mon{/if}">{lang doing_view_me}</a>
	<a href="home.php?mod=space&do=$do&view=all" class="flex{if $_GET['view'] == 'all'} mon{/if}">{lang view_all}</a>
</div>

<div class="doing_list threadlist_box cl">
	<div class="doing_list_box threadlist cl">
		<!--{if $dolist}-->
		<!--{loop $dolist $dv}-->
		<!--{eval $doid = $dv['doid'];}-->
		<!--{eval $_GET['key'] = $key = random(8);}-->
		<div class="doing_card">
			<div class="doing_card_content">
				<div class="doing_card_top">
					<div class="doing_card_top_left">
						<div class="hl-author-icon">
							<span class="mio-avatar-badge">
								<a href="home.php?mod=space&uid=$dv['uid']&do=profile" class="mio-avatar">
									<!--{avatar($dv['uid'],'small')}-->
								</a>
							</span>
						</div>
						<div class="doing_card_info">
							<div class="doing_card_info_top">
								<a href="home.php?mod=space&uid=$dv['uid']&do=profile" id="author_$value['cid']" class="doing_card_info_name">$dv['username']</a>
							</div>
						</div>

					</div>
				</div>
				<div class="doing_card_text">
					<div class="doing_card_text_textcontent">
						<div id="comment_$value['cid']" class="newmessage{if $value['magicflicker']} magicflicker{/if}">$dv[message]<!--{if $dv['status'] == 1}--> <span style="font-weight: bold;">({lang moderate_need})</span><!--{/if}--></div>
						<!--{if $dv['attachments']}-->
						<div class="doing_card_piclistbox">
							<div class="doing_card_piclist">
								<!--{loop $dv['attachments'] $attach}-->
								<!--{if $attach['isimage']}-->
								<div class="doing_card_piclist_item "><img lass="hl_noloadimage lazy lazy-fade-in" width="300" height="300" src="{$attach['thumb']}" data-src="{if $attach['remote']}{$_G['setting']['ftp']['attachurl']}{else}{$_G['setting']['attachurl']}{/if}doing/{$attach['attachment']}"></div>
								<!--{/if}-->
								<!--{/loop}-->
							</div>
						</div>
						<!--{/if}-->
					</div>
				</div>
				<!--{if $dv['body_template']}-->
				<div class="doing_card_more cl">
					<div class="d quote">
						<blockquote id="quote_{$dv['id']}">$dv[body_template]</blockquote>
					</div>
				</div>
				<!--{/if}-->
				<div class="doing_card_bottom">
					<div class="doing_card_bottom_left">
						<span class="doing_card_info-bottom-info"><!--{date($dv['dateline'], 'u')}--></span>
					</div>
					<div class="doing_card_bottom_right">
						<a href="javascript:;" class="recommend doing_recommend_btn" data-doid="$doid" data-status="<!--{if $dv['recommendstatus']}-->1<!--{else}-->0<!--{/if}-->">
							<i class="<!--{if $dv['recommendstatus']}-->fico-thumbup fc-i<!--{else}-->fico-thumbup fc-s<!--{/if}-->"></i>
							<span class="bottom_num recommend_count">$dv[recomends]</span>
						</a>
						<!--{if helper_access::check_module('doing')}-->
						<a href="home.php?mod=spacecp&ac=doing&op=docomment&handlekey=msg_0&doid=$doid&id=0&key=$key" class="recommend{if $_G['uid']} dialog{/if}">
							<i class="dm-chat-s"></i>
							<span class="bottom_num">{lang reply}</span>
						</a>
						<!--{/if}-->
						
						
						<!--{if $dv['uid']==$_G['uid']}-->
						<a href="home.php?mod=spacecp&ac=doing&op=delete&doid=$doid&id=$dv['id']&handlekey=doinghk_{$doid}_$dv['id']" id="{$key}_doing_delete_{$doid}_{$dv['id']}" class="recommend{if $_G['uid']} dialog{/if}">
							<i class="dm-delete"></i>
							<span class="bottom_num">{lang delete}</span>
						</a>
						<!--{/if}-->
					</div>
				</div>
			</div>
			<div id="{$key}dl{$doid}" class="doing_card_comment">
					<!--{eval $list = $clist[$doid];}-->
					<div class="doing_card_quote" id="{$key}_$doid" {if empty($list) || !$showdoinglist[$doid]} style="display:none;" {/if}>
						<span id="{$key}_form_{$doid}_0"></span>
						<!--{template home/space_doing_li}-->
					</div>
				</div>
		</div>
		<!--{/loop}-->
		
	
			
			<!--{if $multi}-->
			<div class="pgs cl mtm">$multi</div>
			<!--{/if}-->
		
		<!--{else}-->
		<div class="threadlist_box mt10 cl">
			<h4>{lang doing_no_replay}</h4>
		</div>
		<!--{/if}-->
	</div>
</div>

<!--{template common/footer}-->