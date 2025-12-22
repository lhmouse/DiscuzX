<?php exit('Access Denied');?>
<!--{if $diymode}-->
	<!--{if $_G[setting][homepagestyle]}-->
		<!--{subtemplate home/space_header}-->
		<div id="ct" class="ct2 wp cl">
			<div class="mn">
				<div class="bm">
					<div class="bm_h">
						<h1 class="mt">{lang doing}</h1>
					</div>
				<div class="bm_c">
	<!--{else}-->
		<!--{template common/header}-->
		<div id="pt" class="bm cl">
			<div class="z">
				<a href="./" class="nvhm" title="{lang homepage}">$_G[setting][bbname]</a> <em>&rsaquo;</em>
				<a href="home.php?mod=space&uid=$space[uid]">{$space[username]}</a> <em>&rsaquo;</em>
				<a href="home.php?mod=space&uid=$space[uid]&do=doing&view=me&from=space">{lang doing}</a>
			</div>
		</div>
		<style id="diy_style" type="text/css"></style>
		<div class="wp">
			<!--[diy=diy1]--><div id="diy1" class="area"></div><!--[/diy]-->
		</div>
		<!--{template home/space_menu}-->
		<div id="ct" class="ct1 wp cl">
			<div class="mn">
				<!--[diy=diycontenttop]--><div id="diycontenttop" class="area"></div><!--[/diy]-->
				<div class="bm bw0">
					<div class="bm_c">
	<!--{/if}-->
	<!--{if $space[self] && helper_access::check_module('doing')}--><!--{template home/space_doing_form}--><!--{/if}-->
<!--{else}-->
	<!--{template common/header}-->
	<div id="pt" class="bm cl">
		<div class="z">
			<a href="./" class="nvhm" title="{lang homepage}">$_G[setting][bbname]</a> <em>&rsaquo;</em>
			<a href="home.php?mod=space&do=doing">{lang doing}</a>
		</div>
	</div>

	<style id="diy_style" type="text/css"></style>
	<div class="wp">
		<!--[diy=diy1]--><div id="diy1" class="area"></div><!--[/diy]-->
	</div>
	<div id="ct" class="ct2_a wp cl">
		<!--{if $_G[setting][homestyle]}-->
			<div class="appl">
				<!--{subtemplate common/userabout}-->
			</div>
			<div class="mn pbm">
				<!--{hook/space_doing_top}-->
				<!--[diy=diycontenttop]--><div id="diycontenttop" class="area"></div><!--[/diy]-->
				<div class="bm bw0">
					<!--{if $space[self] && helper_access::check_module('doing')}-->
					<!--{template home/space_doing_form}-->
					<!--{/if}-->
					<!--{hook/space_doing_bottom}-->
					<ul class="tb cl">
						<li$actives[we]><a href="home.php?mod=space&do=$do&view=we"{if !$_G['uid']} onclick="showWindow('login', 'member.php?mod=logging&action=login&guestmessage=yes&referer='+encodeURIComponent(this.href))"{/if}>{lang me_friend_doing}</a></li>
						<li$actives[me]><a href="home.php?mod=space&do=$do&view=me"{if !$_G['uid']} onclick="showWindow('login', 'member.php?mod=logging&action=login&guestmessage=yes&referer='+encodeURIComponent(this.href))"{/if}>{lang doing_view_me}</a></li>
						<li$actives[all]><a href="home.php?mod=space&do=$do&view=all">{lang view_all}</a></li>
					</ul>
				</div>
		<!--{else}-->
			<div class="appl">
				<div class="tbn">
					<h2 class="mt bbda">{lang doing}</h2>
					<ul>
						<li$actives[we]><a href="home.php?mod=space&do=$do&view=we"{if !$_G['uid']} onclick="showWindow('login', 'member.php?mod=logging&action=login&guestmessage=yes&referer='+encodeURIComponent(this.href))"{/if}>{lang me_friend_doing}</a></li>
						<li$actives[me]><a href="home.php?mod=space&do=$do&view=me"{if !$_G['uid']} onclick="showWindow('login', 'member.php?mod=logging&action=login&guestmessage=yes&referer='+encodeURIComponent(this.href))"{/if}>{lang doing_view_me}</a></li>
						<li$actives[all]><a href="home.php?mod=space&do=$do&view=all">{lang view_all}</a></li>
					</ul>
				</div>
			</div>
			<div class="mn pbm">
			<!--[diy=diycontenttop]--><div id="diycontenttop" class="area"></div><!--[/diy]-->
			<!--{if $space[self] && helper_access::check_module('doing')}--><!--{template home/space_doing_form}--><!--{/if}-->
		<!--{/if}-->
		
<!--{/if}-->
		
		<!--{if $searchkey}-->
			<p class="tbmu">{lang doing_search_record} <span style="color: red; font-weight: 700;">$searchkey</span> {lang doing_record_list}</p>
		<!--{/if}-->
		
		<!--{if $dolist}-->
			<div class="xld {if empty($diymode)}xlda{/if}">
			<!--{loop $dolist $dv}-->
			<!--{eval $doid = $dv['doid'];}-->
			<!--{eval $_GET['key'] = $key = random(8);}-->
				<dl id="{$key}dl{$doid}" class="pbn bbda cl">
					<!--{if empty($diymode)}--><dd class="m avt"><a href="home.php?mod=space&uid=$dv[uid]" c="1"><!--{avatar($dv['uid'], 'small')}--></a></dd><!--{/if}-->
					<dd class="{if empty($diymode)}ptm{else}ptw{/if} xs2">
					<!--{if empty($diymode)}--><a href="home.php?mod=space&uid=$dv[uid]">$dv[username]</a>: <!--{/if}--><span>$dv[message]</span> <!--{if $dv[status] == 1}--> <span style="font-weight: bold;">({lang moderate_need})</span><!--{/if}-->
					</dd>
					<!--{if $dv['attachments']}-->
					<dd class="ptm">
						<div class="doing_images">
							<!--{loop $dv['attachments'] $attach}-->
							<!--{if $attach['isimage']}-->
							<div class="doing_image_item">
								<a href="javascript:;" class="doing_image_link ">
									<img src="{if $attach['remote']}remote{else}{$_G['setting']['attachurl']}{/if}doing/{$attach['attachment']}" 
										zoomfile="{if $attach['remote']}remote{else}{$_G['setting']['attachurl']}{/if}doing/{$attach['attachment']}" 
										file="{if $attach['remote']}remote{else}{$_G['setting']['attachurl']}{/if}doing/{$attach['attachment']}"  
										onclick="zoom(this, this.src, 0, 0, 0)" 
										alt="" 
										class="doing_image zoom"
										id="aimg_$attach[aid]"
										aid="$attach[aid]" />
								</a>
							</div>
							<!--{/if}-->
							<!--{/loop}-->
						</div>
					</dd>
					<!--{/if}-->
					<!--{if $dv['body_template']}-->
					<div class="ec cl">
						<div class="d quote">
							<blockquote id="quote_{$dv['id']}">$dv[body_template]</blockquote>
						</div>
					</div>
					<!--{/if}-->
					<!--{eval $list = $clist[$doid];}-->
					<dd class="cmt brm" id="{$key}_$doid"{if empty($list) || !$showdoinglist[$doid]} style="display:none;"{/if}>
						<span id="{$key}_form_{$doid}_0"></span>
						<!--{template home/space_doing_li}-->
					</dd>
					<dd class="ptn xg1">
					<span class="y"><!--{date($dv['dateline'], 'u')}--></span>
					<!--{if helper_access::check_module('doing')}-->
					<a href="javascript:;" onclick="docomment_form($doid, 0, '$key')">{lang reply}</a><span class="pipe">|</span>
					<!--{/if}-->
					<!--{if $dv[uid]==$_G[uid]}--><a href="home.php?mod=spacecp&ac=doing&op=delete&doid=$doid&id=$dv[id]&handlekey=doinghk_{$doid}_$dv[id]" id="{$key}_doing_delete_{$doid}_{$dv[id]}" onclick="showWindow(this.id, this.href, 'get', 0);">{lang delete}</a><!--{/if}-->
					
					<!-- 点赞功能 -->
					<span class="pipe">|</span>
					<a href="javascript:;" class="doing_recommend_btn" data-doid="$doid" data-status="<!--{if $dv['recommendstatus']}-->1<!--{else}-->0<!--{/if}-->">
						<i class="<!--{if $dv['recommendstatus']}-->icon-recommend<!--{else}-->icon-recommend-o<!--{/if}-->"></i> 
						<span class="recommend_count">$dv[recomends]</span>
					</a>
					
					<!--{if checkperm('managedoing')}-->
					<span class="pipe">IP: $dv[ip]:$dv[port]</span>
					<!--{/if}-->
				</dd>
				</dl>
			<!--{/loop}-->
			<!--{if $pricount}-->
				<p class="mtm">{lang hide_doing}</p>
			<!--{/if}-->
			</div>
			<!--{if $multi}-->
			<div class="pgs cl mtm">$multi</div>
			<!--{elseif $_GET[highlight]}-->
			<div class="pgs cl mtm"><div class="pg"><a href="home.php?mod=space&do=doing&view=me">{lang viewmore}</a></div></div>
			<!--{/if}-->
		<!--{else}-->
			<p class="emp">{lang doing_no_replay}<!--{if $space[self]}-->{lang doing_now}<!--{/if}--></p>
		<!--{/if}-->
		
		<!--{if !$_G[setting][homepagestyle]}--><!--[diy=diycontentbottom]--><div id="diycontentbottom" class="area"></div><!--[/diy]--><!--{/if}-->

		<!--{if $diymode}-->
					</div>
				</div>
			<!--{if $_G[setting][homepagestyle]}-->
			</div>
			<div class="sd">
				<!--{subtemplate home/space_userabout}-->
			<!--{/if}-->
		<!--{/if}-->
		</div>
	</div>

<!--{if !$_G[setting][homepagestyle]}-->
	<div class="wp mtn">
		<!--[diy=diy3]--><div id="diy3" class="area"></div><!--[/diy]-->
	</div>
<!--{/if}-->
<script type="text/javascript">
    window.onload = function() {
        var doingList = document.querySelectorAll('.doing_images');
        for (var i = 0; i < doingList.length; i++) {
            var images = doingList[i].querySelectorAll('.doing_image');
            if (images.length > 1) {
                var groupId = 'doing_group_' + i;
                if (typeof zoomgroup === 'undefined') {
                    zoomgroup = {};
                }
                if (typeof aimgcount === 'undefined') {
                    aimgcount = {};
                }
                aimgcount[groupId] = [];
                for (var j = 0; j < images.length; j++) {
                    var imgId = images[j].id;
                    if (imgId) {
                        zoomgroup[imgId] = groupId;
                        aimgcount[groupId].push(imgId.replace('aimg_', ''));
                    }
                }
            }
        }
        
        // 点赞功能
        var recommendBtns = document.querySelectorAll('.doing_recommend_btn');
        for (let k = 0; k < recommendBtns.length; k++) {
            recommendBtns[k].addEventListener('click', function() {
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
                    .then(response => response.json())
                    .then(data => {
                        // 移除加载状态
                        btn.classList.remove('disabled');
                        
                        if (data && data.message === 'doing_recommend_success') {
                            // 更新点赞状态
                            btn.setAttribute('data-status', data.status);
                            
                            // 更新点赞数
                            countElem.innerHTML = data.count;
                            
                            // 更新图标
                            if (data.status === 1) {
                                iconElem.className = 'icon-recommend';
                            } else {
                                iconElem.className = 'icon-recommend-o';
                            }
                        }
                    })
                    .catch(error => {
                        // 移除加载状态
                        btn.classList.remove('disabled');
                    });
            });
        }
    };
</script>
<style type="text/css">
	.doing_images {
		display: flex;
		flex-wrap: wrap;
		margin: 5px 0;
		gap: 5px;
	}

	.doing_image_item {
		flex: 0 0 auto;
		width: 80px;
		height: 80px;
		overflow: hidden;
		border-radius: 4px;
		background-color: #f5f5f5;
	}

	.doing_image {
		width: 100%;
		height: 100%;
		object-fit: cover;
		transition: transform 0.2s ease;
	}

	.doing_image:hover {
		transform: scale(1.05);
	}

	.doing_image_link {
		display: block;
		width: 100%;
		height: 100%;
	}
</style>
<!--{template common/footer}-->
