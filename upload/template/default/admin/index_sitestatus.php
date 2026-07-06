<?php exit('Access Denied'); ?>
<div class="dbox sitestatus-box" id="sitestatus-widget">
	<div class="boxheader">
		{lang home_sitestatus}
	</div>
	<div class="boxbody">
		<!-- 安全评分 -->
		<div class="ss-security">
			<div class="ss-score-circle ss-score-$sitestatus[security_level]">
				<span class="ss-score-num">$sitestatus[security_score]</span>
				<span class="ss-score-label">{lang sitestatus_score}</span>
			</div>
			<!--{if $sitestatus['security_issues']}-->
			<div class="ss-issues">
				<!--{loop $sitestatus['security_issues'] $issue}-->
				<div class="ss-issue-item">
					<span class="ss-issue-dot"></span>
					<span>$issue</span>
				</div>
				<!--{/loop}-->
			</div>
			<!--{else}-->
			<div class="ss-issues ss-issues-good">
				<span class="ss-issue-dot ss-issue-dot-good"></span>
				<span>{lang sitestatus_secure}</span>
			</div>
			<!--{/if}-->
		</div>

		<!-- 资源监控 -->
		<div class="ss-section">
			<div class="section-title">
				<span>{lang sitestatus_resource}</span>
			</div>
			<div class="ss-resource-grid">
				<!--{if $sitestatus['cpu_supported']}-->
				<!-- CPU -->
				<div class="ss-resource-item">
					<div class="ss-resource-info">
						<span class="ss-resource-name">{lang sitestatus_cpu}</span>
						<span class="ss-resource-value">$sitestatus[cpu]%</span>
					</div>
					<div class="ss-progress-bar">
						<div class="ss-progress-fill ss-progress-cpu" style="width: $sitestatus[cpu]%"></div>
					</div>
				</div>
				<!--{/if}-->
				<!--{if $sitestatus['memory_supported']}-->
				<!-- 内存 -->
				<div class="ss-resource-item">
					<div class="ss-resource-info">
						<span class="ss-resource-name">{lang sitestatus_memory}</span>
						<span class="ss-resource-value">$sitestatus[memory]% <small>($sitestatus[memory_used] / $sitestatus[memory_total])</small></span>
					</div>
					<div class="ss-progress-bar">
						<div class="ss-progress-fill ss-progress-memory" style="width: $sitestatus[memory]%"></div>
					</div>
				</div>
				<!--{/if}-->
				<!-- 磁盘 -->
				<div class="ss-resource-item">
					<div class="ss-resource-info">
						<span class="ss-resource-name">{lang sitestatus_disk}</span>
						<!--{if $sitestatus['disk_supported']}-->
						<span class="ss-resource-value">$sitestatus[disk]% <small>($sitestatus[disk_used] / $sitestatus[disk_total])</small></span>
						<!--{else}-->
						<span class="ss-resource-value ss-resource-error">{lang sitestatus_disk_error}</span>
						<!--{/if}-->
					</div>
					<!--{if $sitestatus['disk_supported']}-->
					<div class="ss-progress-bar">
						<div class="ss-progress-fill ss-progress-disk" style="width: $sitestatus[disk]%"></div>
					</div>
					<!--{/if}-->
				</div>
				<!-- 数据库尺寸 -->
				<div class="ss-resource-item">
					<div class="ss-resource-info">
						<span class="ss-resource-name">{lang sitestatus_database_size}</span>
						<span class="ss-resource-value">$sitestatus[dbsize]</span>
					</div>
				</div>
				<!-- 附件尺寸 -->
				<div class="ss-resource-item">
					<div class="ss-resource-info">
						<span class="ss-resource-name">{lang sitestatus_attach_size}</span>
						<span class="ss-resource-value">$sitestatus[attachsize]</span>
					</div>
				</div>
			</div>
		</div>

		<!-- 服务状态 -->
		<div class="ss-section">
			<div class="section-title">
				<span>{lang sitestatus_services}</span>
			</div>
			<div class="ss-service-grid">
				<!-- MySQL -->
				<div class="ss-service-item ss-service-$sitestatus[mysql_status]">
					<div class="ss-service-info">
						<span class="ss-service-name">MySQL</span>
						<span class="ss-service-detail">
							<!--{if $sitestatus['mysql_status'] == 'running'}-->
							{lang sitestatus_threads}: $sitestatus[mysql_threads]
							<!--{else}-->
							{lang sitestatus_error}
							<!--{/if}-->
						</span>
					</div>
					<span class="ss-service-status">
						<!--{if $sitestatus['mysql_status'] == 'running'}-->
						<span class="ss-status-dot ss-status-running"></span>
						<!--{else}-->
						<span class="ss-status-dot ss-status-error"></span>
						<!--{/if}-->
					</span>
				</div>
				<!-- Redis -->
				<div class="ss-service-item ss-service-$sitestatus[redis_status]">
					<div class="ss-service-info">
						<span class="ss-service-name">Redis</span>
						<span class="ss-service-detail">
							<!--{if $sitestatus['redis_status'] == 'running'}-->
							{lang sitestatus_threads}: $sitestatus[redis_threads], {lang sitestatus_memory}: $sitestatus[used_memory]
							<!--{elseif $sitestatus['redis_status'] == 'none'}-->
							{lang sitestatus_not_installed}
							<!--{else}-->
							{lang sitestatus_stopped}
							<!--{/if}-->
						</span>
					</div>
					<span class="ss-service-status">
						<!--{if $sitestatus['redis_status'] == 'running'}-->
						<span class="ss-status-dot ss-status-running"></span>
						<!--{elseif $sitestatus['redis_status'] == 'none'}-->
						<span class="ss-status-dot ss-status-none"></span>
						<!--{else}-->
						<span class="ss-status-dot ss-status-error"></span>
						<!--{/if}-->
					</span>
				</div>
			</div>
		</div>

		<!-- 备份状态 -->
		<div class="ss-section">
			<div class="section-title">
				<span>{lang sitestatus_backup}</span>
			</div>
			<div class="ss-backup-info">
				<div class="ss-backup-item">
					<span class="ss-backup-label">{lang sitestatus_last_backup}</span>
					<span class="ss-backup-value">$sitestatus[backup_last]</span>
				</div>
				<div class="ss-backup-item">
					<span class="ss-backup-label">{lang sitestatus_backup_status}</span>
					<span class="ss-backup-badge ss-backup-$sitestatus[backup_status]">
						<!--{if $sitestatus['backup_status'] == 'normal'}-->
						{lang sitestatus_backup_normal}
						<!--{else}-->
						{lang sitestatus_backup_warning}
						<!--{/if}-->
					</span>
				</div>
			</div>
		</div>
	</div>
</div>
