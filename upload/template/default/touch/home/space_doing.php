<?php exit('Access Denied'); ?>
<!--{template common/header}-->

<style type="text/css">
	/* �ֻ����¼ͼƬչʾ��ʽ */
	.doing_images {
		display: flex;
		flex-wrap: wrap;
		margin: 10px 0;
		gap: 8px;
	}

	.doing_image_item {
		flex: 0 0 auto;
		width: 80px;
		height: 80px;
		overflow: hidden;
		border-radius: 6px;
		background-color: var(--dz-BG-0);
		border: 1px solid var(--dz-BOR-ed);
		transition: all 0.2s ease;
	}

	.doing_image_item:hover {
		transform: scale(1.02);
		box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
	}

	.doing_image_link {
		display: block;
		width: 100%;
		height: 100%;
		overflow: hidden;
	}

	.doing_image {
		width: 100%;
		height: 100%;
		object-fit: cover;
		transition: transform 0.2s ease;
	}

	.doing_image_link:hover .doing_image {
		transform: scale(1.05);
	}

	/* ���䲻ͬ��Ļ�ߴ� */
	@media (max-width: 375px) {
		.doing_image_item {
			width: 70px;
			height: 70px;
			gap: 6px;
		}
	}

	@media (max-width: 320px) {
		.doing_image_item {
			width: 60px;
			height: 60px;
			gap: 5px;
		}
	}
</style>

<script type="text/javascript">
    // 点赞功能
    document.addEventListener('DOMContentLoaded', function() {
        var recommendBtns = document.querySelectorAll('.doing_recommend_btn');
        for (let i = 0; i < recommendBtns.length; i++) {
            recommendBtns[i].addEventListener('click', function() {
                // 防止重复点击
                if (this.classList.contains('disabled')) {
                    return;
                }
                
                var doid = this.getAttribute('data-doid');
                var btn = this;
                var countElem = this.querySelector('.recommend_count');
                var iconElem = this.querySelector('i');
                
                // 添加加载状态
                btn.classList.add('disabled');
                
                // 使用fetch API处理JSON响应
                fetch('home.php?mod=space&do=doing&op=recommend&doid=' + doid)
                    .then(function(response) {
                        return response.json();
                    })
                    .then(function(data) {
                        // 移除加载状态
                        btn.classList.remove('disabled');
                        
                        if (data && data.message === 'doing_recommend_success') {
                            // 更新点赞状态
                            btn.setAttribute('data-status', data.status);
                            
                            // 更新点赞数
                            countElem.innerHTML = data.count;
                            
                            // 更新图标
                            if (parseInt(data.status) === 1) {
                                iconElem.className = 'icon-recommend';
                            } else {
                                iconElem.className = 'icon-recommend-o';
                            }
                        }
                    })
                    .catch(function(error) {
                        // 移除加载状态
                        btn.classList.remove('disabled');
                    });
            });
        }
    });
</script>

<div class="header cl">
	<div class="mz"><a href="javascript:history.back();"><i class="dm-c-left"></i></a></div>
	<h2>{lang doing}</h2>
</div>

<div class="dhnv flex-box cl">
	<a href="home.php?mod=space&do=$do&view=we" class="flex{if $_GET['view'] == 'we'} mon{/if}">{lang me_friend_doing}</a>
	<a href="home.php?mod=space&do=$do&view=me" class="flex{if $_GET['view'] == 'me'} mon{/if}">{lang doing_view_me}</a>
	<a href="home.php?mod=space&do=$do&view=all" class="flex{if $_GET['view'] == 'all'} mon{/if}">{lang view_all}</a>
</div>

<div class="doing_list threadlist_box cl">
	<!--{if helper_access::check_module('doing')}-->
	<!--{template home/space_doing_form}-->
	<!--{/if}-->
	<div class="doing_list_box threadlist cl">
		<!--{if $dolist}-->
		<ul>
			<!--{loop $dolist $dv}-->
			<li class="doing_list_li list cl">
				<!--{eval $doid = $dv['doid'];}-->
				<!--{eval $_GET['key'] = $key = random(8);}-->
				<div class="threadlist_top cl">
					<!--{if empty($diymode)}-->
					<a href="home.php?mod=space&uid=$dv['uid']&do=profile" class="avatar mimg z">
						<!--{avatar($dv['uid'],'small')}-->
					</a>
					<!--{/if}-->
					<div class="muser cl">
						<h3>
							<!--{if empty($diymode)}-->
							<a href="home.php?mod=space&uid=$dv['uid']&do=profile" id="author_$value['cid']" class="mmc">$dv['username']</a>
							<!--{/if}-->
						</h3>
						<div class="mtime">
					<span><!--{date($dv['dateline'], 'u')}--></span>
					<div class="doing_listgl y">
						<!--{if $dv['uid']==$_G['uid']}-->
						<a href="home.php?mod=spacecp&ac=doing&op=delete&doid=$doid&id=$dv['id']&handlekey=doinghk_{$doid}_$dv['id']" id="{$key}_doing_delete_{$doid}_{$dv['id']}" class="y doing_gl{if $_G['uid']} dialog{/if}">{lang delete}</a>
						<!--{/if}-->
						<!--{if helper_access::check_module('doing')}-->
						<a href="home.php?mod=spacecp&ac=doing&op=docomment&handlekey=msg_0&doid=$doid&id=0&key=$key" class="y doing_gl{if $_G['uid']} dialog{/if}">{lang reply}</a>
						<!--{/if}-->
						
						<!-- 点赞功能 -->
						<a href="javascript:;" class="y doing_gl doing_recommend_btn" data-doid="$doid" data-status="<!--{if $dv['recommendstatus']}-->1<!--{else}-->0<!--{/if}-->">
							<i class="<!--{if $dv['recommendstatus']}-->icon-recommend<!--{else}-->icon-recommend-o<!--{/if}-->"></i>
							<span class="recommend_count">$dv[recomends]</span>
						</a>
					</div>
				</div>
					</div>
				</div>
				<div id="comment_$value['cid']" class="do_comment{if $value['magicflicker']} magicflicker{/if}">$dv[message]<!--{if $dv['status'] == 1}--> <span style="font-weight: bold;">({lang moderate_need})</span><!--{/if}--></div>

				<!-- ͼƬչʾ���� -->
				<!--{if $dv['attachments']}-->
				<div class="doing_images">
					<!--{loop $dv['attachments'] $attach}-->
					<!--{if $attach['isimage']}-->
					<div class="doing_image_item">
						<a href="javascript:;" class="doing_image_link" onclick="zoom(this, '{if $attach['remote']}remote{else}{$_G['setting']['attachurl']}{/if}doing/{$attach['attachment']}')">
							<img src="{if $attach['remote']}remote{else}{$_G['setting']['attachurl']}{/if}doing/{$attach['attachment']}" alt="" class="doing_image" />
						</a>
					</div>
					<!--{/if}-->
					<!--{/loop}-->
				</div>
				<!--{/if}-->

				<div id="{$key}dl{$doid}" class="do_comment">
					<!--{eval $list = $clist[$doid];}-->
					<div class="quote" id="{$key}_$doid" {if empty($list) || !$showdoinglist[$doid]} style="display:none;" {/if}>
						<span id="{$key}_form_{$doid}_0"></span>
						<!--{template home/space_doing_li}-->
					</div>

			</li>
			<!--{/loop}-->
			<!--{if $multi}-->
			<div class="pgs cl mtm">$multi</div>
			<!--{/if}-->
		</ul>
		<!--{else}-->
		<div class="threadlist_box mt10 cl">
			<h4>{lang doing_no_replay}</h4>
		</div>
		<!--{/if}-->
	</div>
</div>

<!--{template common/footer}-->