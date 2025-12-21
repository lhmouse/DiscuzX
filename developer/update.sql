ALTER TABLE `pre_forum_collection`
	ADD COLUMN cover tinyint(1) unsigned NOT NULL DEFAULT '0';

ALTER TABLE `pre_forum_collection`
	ADD COLUMN icon tinyint(1) unsigned NOT NULL DEFAULT '0';


ALTER TABLE `pre_common_tag`
	MODIFY `tagname` char (50) NOT NULL DEFAULT '';

ALTER TABLE `pre_common_tag`
ADD COLUMN `related_count` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '关联数据数量' AFTER `status`,
ADD COLUMN `hot_score` float NOT NULL DEFAULT '0' COMMENT '近期热度值' AFTER `related_count`,
ADD COLUMN `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间' AFTER `hot_score`,
ADD COLUMN `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间' AFTER `created_at`,
ADD KEY `idx_hot_score` (`hot_score`);

ALTER TABLE `pre_common_tagitem`
ADD COLUMN `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关联时间' AFTER `idtype`,
ADD KEY `idx_created_at` (`created_at`);

INSERT INTO pre_common_block_style (blockclass, `name`, template, `hash`, getpic, getsummary, makethumb, settarget, `fields`) VALUES('html_misctag', '[内置]标签模版', 'a:9:{s:3:\"raw\";s:361:\"<!-- 热门标签模块 -->\r\n<div class=\"tag-cloud-module\">\r\n	<div class=\"tag-cloud-container\">\r\n		[loop]\r\n		<a href=\"{url}\"\r\n		   title=\"{title} ({related_count}篇内容)\"\r\n		   class=\"tag-cloud-item tag-size-{size_level} tag-color-{color_level}\"\r\n		   data-count=\"{related_count}\"\r\n		   data-hot=\"{hot_score}\">\r\n			{title}\r\n		</a>\r\n		[/loop]\r\n	</div>\r\n</div>\";s:6:\"footer\";s:0:\"\";s:6:\"header\";s:0:\"\";s:9:\"indexplus\";a:0:{}s:5:\"index\";a:0:{}s:9:\"orderplus\";a:0:{}s:5:\"order\";a:0:{}s:8:\"loopplus\";a:0:{}s:4:\"loop\";s:224:\"<a href=\"{url}\"\r\n		   title=\"{title} ({related_count}篇内容)\"\r\n		   class=\"tag-cloud-item tag-size-{size_level} tag-color-{color_level}\"\r\n		   data-count=\"{related_count}\"\r\n		   data-hot=\"{hot_score}\">\r\n			{title}\r\n		</a>\";}', '391cb72a', 0, 0, 0, 0, 'a:6:{i:0;s:3:\"url\";i:1;s:5:\"title\";i:2;s:13:\"related_count\";i:3;s:10:\"size_level\";i:4;s:11:\"color_level\";i:5;s:9:\"hot_score\";}');

INSERT INTO pre_common_setting VALUES ('seohead_mobile','');

ALTER TABLE `pre_portal_article_title` DROP `tag`;
ALTER TABLE `pre_portal_article_title` ADD `tags` VARCHAR(255) NOT NULL AFTER `click8`;

ALTER TABLE `pre_forum_threadtype` ADD super text NOT NULL;

INSERT INTO pre_restful_source (sourceid, name, url) VALUES (1, 'Discuz! Team', 'https://api.witframe.com/discuzrestful');

ALTER TABLE pre_common_credit_log_field
	ADD COLUMN dateline int(10) unsigned NOT NULL DEFAULT 0;

ALTER TABLE pre_common_credit_log_field
	ADD INDEX dateline (dateline);

ALTER TABLE pre_common_credit_log_field
	ADD COLUMN ac_extcredits1 int(10)          NOT NULL,
    	ADD COLUMN ac_extcredits2 int(10)          NOT NULL,
    	ADD COLUMN ac_extcredits3 int(10)          NOT NULL,
    	ADD COLUMN ac_extcredits4 int(10)          NOT NULL,
    	ADD COLUMN ac_extcredits5 int(10)          NOT NULL,
    	ADD COLUMN ac_extcredits6 int(10)          NOT NULL,
    	ADD COLUMN ac_extcredits7 int(10)          NOT NULL,
    	ADD COLUMN ac_extcredits8 int(10)          NOT NULL;

UPDATE pre_common_credit_log_field f
	JOIN pre_common_credit_log l ON f.logid = l.logid
	SET f.dateline = l.dateline;

ALTER TABLE pre_forum_forum
	ADD COLUMN editormode tinyint(1) NOT NULL DEFAULT '-1';

ALTER TABLE pre_common_credit_log_field
	CHANGE COLUMN logid logid int(10) unsigned NOT NULL;

ALTER TABLE pre_common_credit_log_field
	ADD COLUMN uid mediumint(8) unsigned NOT NULL DEFAULT '0' AFTER logid;

ALTER TABLE pre_common_credit_log_field
	ADD INDEX uid (uid);

UPDATE pre_common_credit_log_field f
	JOIN pre_common_credit_log l ON f.logid = l.logid
	SET f.uid = l.uid;

ALTER TABLE `pre_common_usergroup_field`
	ADD COLUMN `fields` json;

ALTER TABLE `pre_forum_forumfield`
	ADD COLUMN `fields` json;