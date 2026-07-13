<?php exit('Access Denied'); ?>
<div class="dbox todobox">
	<div class="boxheader">
		<!--{if $totalcount}-->
		<span class="todo-badge">$totalcount</span>
		<!--{/if}-->
		{lang home_todo}
	</div>
	<div class="boxbody">
		<!-- 待办统计汇总 -->
		<!--{if $totalcount}-->
		<div class="todo-summary">
			<div class="todo-stat-item">
				<span class="stat-icon icon-users"></span>
				<span class="stat-label">{lang todo_category_users}</span>
				<span class="stat-value"><a href="{ADMINSCRIPT}?action=moderate&operation=members">$membersmod</a></span>
			</div>
			<div class="todo-stat-item">
				<span class="stat-icon icon-content"></span>
				<span class="stat-label">{lang todo_category_content}</span>
				<span class="stat-value">$contentmod</span>
			</div>
			<div class="todo-stat-item">
				<span class="stat-icon icon-other"></span>
				<span class="stat-label">{lang todo_category_other}</span>
				<span class="stat-value">$othermod</span>
			</div>
		</div>
		<!--{/if}-->

		<!-- 用户审核 -->
		<!--{if $membersmod}-->
		<div class="todo-section todo-section-users">
			<div class="section-title">
				<i class="dzicon todo-icon-users"></i>
				<span>{lang todo_section_users}</span>
			</div>
			<div class="todo-items">
				<div class="todo-item">
					<a href="{ADMINSCRIPT}?action=moderate&operation=members" class="todo-link">
						<span class="todo-text">{lang home_mod_members}</span>
						<span class="todo-count">$membersmod</span>
					</a>
				</div>
			</div>
		</div>
		<!--{/if}-->

		<!-- 内容审核 -->
		<!--{if $threadsmod || $postsmod || $blogsmod || $doingsmod || $picturesmod || $sharesmod || $commentsmod || $articlesmod || $articlecommentsmod || $topiccommentsmod}-->
		<div class="todo-section todo-section-content">
			<div class="section-title">
				<i class="dzicon todo-icon-content"></i>
				<span>{lang todo_section_content}</span>
			</div>
			<div class="todo-items">
				<!--{if $threadsmod}-->
				<div class="todo-item">
					<a href="{ADMINSCRIPT}?action=moderate&operation=threads&dateline=all" class="todo-link">
						<span class="todo-text">{lang home_mod_threads}</span>
						<span class="todo-count">$threadsmod</span>
					</a>
				</div>
				<!--{/if}-->
				<!--{if $postsmod}-->
				<div class="todo-item">
					<a href="{ADMINSCRIPT}?action=moderate&operation=replies&dateline=all" class="todo-link">
						<span class="todo-text">{lang home_mod_posts}</span>
						<span class="todo-count">$postsmod</span>
					</a>
				</div>
				<!--{/if}-->
				<!--{if $blogsmod}-->
				<div class="todo-item">
					<a href="{ADMINSCRIPT}?action=moderate&operation=blogs&dateline=all" class="todo-link">
						<span class="todo-text">{lang home_mod_blogs}</span>
						<span class="todo-count">$blogsmod</span>
					</a>
				</div>
				<!--{/if}-->
				<!--{if $doingsmod}-->
				<div class="todo-item">
					<a href="{ADMINSCRIPT}?action=moderate&operation=doings&dateline=all" class="todo-link">
						<span class="todo-text">{lang home_mod_doings}</span>
						<span class="todo-count">$doingsmod</span>
					</a>
				</div>
				<!--{/if}-->
				<!--{if $picturesmod}-->
				<div class="todo-item">
					<a href="{ADMINSCRIPT}?action=moderate&operation=pictures&dateline=all" class="todo-link">
						<span class="todo-text">{lang home_mod_pictures}</span>
						<span class="todo-count">$picturesmod</span>
					</a>
				</div>
				<!--{/if}-->
				<!--{if $sharesmod}-->
				<div class="todo-item">
					<a href="{ADMINSCRIPT}?action=moderate&operation=shares&dateline=all" class="todo-link">
						<span class="todo-text">{lang home_mod_shares}</span>
						<span class="todo-count">$sharesmod</span>
					</a>
				</div>
				<!--{/if}-->
				<!--{if $commentsmod}-->
				<div class="todo-item">
					<a href="{ADMINSCRIPT}?action=moderate&operation=comments&dateline=all" class="todo-link">
						<span class="todo-text">{lang home_mod_comments}</span>
						<span class="todo-count">$commentsmod</span>
					</a>
				</div>
				<!--{/if}-->
				<!--{if $articlesmod}-->
				<div class="todo-item">
					<a href="{ADMINSCRIPT}?action=moderate&operation=articles&dateline=all" class="todo-link">
						<span class="todo-text">{lang home_mod_articles}</span>
						<span class="todo-count">$articlesmod</span>
					</a>
				</div>
				<!--{/if}-->
				<!--{if $articlecommentsmod}-->
				<div class="todo-item">
					<a href="{ADMINSCRIPT}?action=moderate&operation=articlecomments&dateline=all" class="todo-link">
						<span class="todo-text">{lang home_mod_articlecomments}</span>
						<span class="todo-count">$articlecommentsmod</span>
					</a>
				</div>
				<!--{/if}-->
				<!--{if $topiccommentsmod}-->
				<div class="todo-item">
					<a href="{ADMINSCRIPT}?action=moderate&operation=topiccomments&dateline=all" class="todo-link">
						<span class="todo-text">{lang home_mod_topiccomments}</span>
						<span class="todo-count">$topiccommentsmod</span>
					</a>
				</div>
				<!--{/if}-->
			</div>
		</div>
		<!--{/if}-->

		<!-- 其他待办 -->
		<!--{if $medalsmod || $groupmod || $reportcount || $threadsdel || !empty($verify) || $errcredits}-->
		<div class="todo-section todo-section-other">
			<div class="section-title">
				<i class="dzicon todo-icon-other"></i>
				<span>{lang todo_section_other}</span>
			</div>
			<div class="todo-items">
				<!--{if $medalsmod}-->
				<div class="todo-item">
					<a href="{ADMINSCRIPT}?action=medals&operation=mod" class="todo-link">
						<span class="todo-text">{lang home_mod_medals}</span>
						<span class="todo-count">$medalsmod</span>
					</a>
				</div>
				<!--{/if}-->
				<!--{if $groupmod}-->
				<div class="todo-item">
					<a href="{ADMINSCRIPT}?action=group&operation=mod" class="todo-link">
						<span class="todo-text">{lang home_mod_wait}</span>
						<span class="todo-count">$groupmod</span>
					</a>
				</div>
				<!--{/if}-->
				<!--{if $reportcount}-->
				<div class="todo-item todo-item-urgent">
					<a href="{ADMINSCRIPT}?action=report" class="todo-link">
						<span class="todo-text">{lang home_mod_reports}</span>
						<span class="todo-count todo-count-urgent">$reportcount</span>
					</a>
				</div>
				<!--{/if}-->
				<!--{if $threadsdel}-->
				<div class="todo-item">
					<a href="{ADMINSCRIPT}?action=recyclebin" class="todo-link">
						<span class="todo-text">{lang home_del_threads}</span>
						<span class="todo-count">$threadsdel</span>
					</a>
				</div>
				<!--{/if}-->
				<!--{loop $verify $n $row}-->
				<div class="todo-item">
					<a href="{ADMINSCRIPT}?action=verify&operation=verify&do={$n}" class="todo-link">
						<span class="todo-text">{lang home_mod_verify_prefix}{$row[0]}</span>
						<span class="todo-count">{$row[1]}</span>
					</a>
				</div>
				<!--{/loop}-->
				<!--{if $errcredits}-->
				<div class="todo-item todo-item-error">
					<a href="{ADMINSCRIPT}?action=logs&operation=credit&srch_operation=ERR" class="todo-link">
						<span class="todo-text">{lang home_err_credits}</span>
						<span class="todo-count todo-count-error">{$errcredits}</span>
					</a>
				</div>
				<!--{/if}-->
			</div>
		</div>
		<!--{/if}-->
	</div>
</div>