<?php exit('Access Denied');?>
ALTER TABLE pre_common_member
	MODIFY username char (50) NOT NULL DEFAULT '';

ALTER TABLE pre_ucenter_members
	MODIFY username char (50) NOT NULL DEFAULT '';

ALTER TABLE pre_common_mytask
	MODIFY username char (50) NOT NULL DEFAULT '';

ALTER TABLE pre_common_report
	MODIFY username varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_forum_collection
	MODIFY username varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_forum_collectioncomment
	MODIFY username varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_forum_collectionteamworker
	MODIFY username varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_forum_collectionfollow
	MODIFY username char (50) NOT NULL DEFAULT '';

ALTER TABLE pre_forum_groupuser
	MODIFY username char (50) NOT NULL;

ALTER TABLE pre_forum_pollvoter
	MODIFY username varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_forum_promotion
	MODIFY username char (50) NOT NULL DEFAULT '';

ALTER TABLE pre_forum_ratelog
	MODIFY username char (50) NOT NULL DEFAULT '';

ALTER TABLE pre_forum_threadmod
	MODIFY username char (50) NOT NULL DEFAULT '';

ALTER TABLE pre_home_album
	MODIFY username varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_home_blog
	MODIFY username char (50) NOT NULL DEFAULT '';

ALTER TABLE pre_home_clickuser
	MODIFY username varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_home_docomment
	MODIFY username varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_home_doing
	MODIFY username varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_home_feed
	MODIFY username varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_home_follow
	MODIFY username char (50) NOT NULL DEFAULT '';

ALTER TABLE pre_home_follow
	MODIFY fusername char (50) NOT NULL DEFAULT '';

ALTER TABLE pre_home_follow_feed
	MODIFY username varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_home_follow_feed_archiver
	MODIFY username varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_home_friend
	MODIFY fusername varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_home_friend_request
	MODIFY fusername char (50) NOT NULL DEFAULT '';

ALTER TABLE pre_home_pic
	MODIFY username varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_home_poke
	MODIFY fromusername varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_home_share
	MODIFY username varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_home_show
	MODIFY username varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_home_specialuser
	MODIFY username varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_home_specialuser
	MODIFY opusername varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_home_visitor
	MODIFY vusername char (50) NOT NULL DEFAULT '';

ALTER TABLE pre_portal_topic_pic
	MODIFY username varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_common_card_log
	MODIFY username varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_common_diy_data
	MODIFY username varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_common_failedlogin
	MODIFY username char (50) NOT NULL DEFAULT '';

ALTER TABLE pre_common_invite
	MODIFY fusername char (50) NOT NULL DEFAULT '';

ALTER TABLE pre_common_member_verify_info
	MODIFY username varchar (100) NOT NULL DEFAULT '';

ALTER TABLE pre_common_grouppm
	MODIFY author varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_common_member_crime
	MODIFY operator varchar (50) NOT NULL;

ALTER TABLE pre_common_member_validate
	MODIFY `admin` varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_common_report
	MODIFY opname varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_common_task
	MODIFY prize varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_common_word
	MODIFY `admin` varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_forum_announcement
	MODIFY author varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_forum_collection
	MODIFY lastposter varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_forum_creditslog
	MODIFY fromto char (50) NOT NULL DEFAULT '';

ALTER TABLE pre_forum_debate
	MODIFY umpire varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_forum_forumrecommend
	MODIFY author char (50) NOT NULL;

ALTER TABLE pre_forum_order
	MODIFY `admin` char (50) NOT NULL DEFAULT '';

ALTER TABLE pre_forum_order
	MODIFY `admin` char (50) NOT NULL DEFAULT '';

ALTER TABLE pre_forum_post
	MODIFY author varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_forum_postcomment
	MODIFY author varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_forum_rsscache
	MODIFY author char (50) NOT NULL DEFAULT '';

ALTER TABLE pre_forum_thread
	MODIFY author char (50) NOT NULL DEFAULT '';

ALTER TABLE pre_forum_thread
	MODIFY lastposter char (50) NOT NULL DEFAULT '';

ALTER TABLE pre_forum_trade
	MODIFY seller char (50) NOT NULL;

ALTER TABLE pre_forum_trade
	MODIFY lastbuyer char (50) NOT NULL;

ALTER TABLE pre_forum_tradecomment
	MODIFY rater char (50) NOT NULL;

ALTER TABLE pre_forum_tradecomment
	MODIFY ratee char (50) NOT NULL;

ALTER TABLE pre_forum_tradelog
	MODIFY seller varchar (50) NOT NULL;

ALTER TABLE pre_forum_tradelog
	MODIFY buyer varchar (50) NOT NULL;

ALTER TABLE pre_forum_warning
	MODIFY operator char (50) NOT NULL;

ALTER TABLE pre_forum_warning
	MODIFY author char (50) NOT NULL;

ALTER TABLE pre_home_comment
	MODIFY author varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_home_notification
	MODIFY author varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_portal_rsscache
	MODIFY author char (50) NOT NULL DEFAULT '';

ALTER TABLE pre_common_adminnote
	MODIFY `admin` varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_common_banned
	MODIFY `admin` varchar (50) NOT NULL DEFAULT '';

ALTER TABLE pre_ucenter_badwords
	MODIFY `admin` varchar (50) NOT NULL default '';

ALTER TABLE pre_ucenter_feeds
	MODIFY username varchar (50) NOT NULL default '';

ALTER TABLE pre_ucenter_admins
	MODIFY username char (50) NOT NULL default '';

ALTER TABLE pre_ucenter_protectedmembers
	MODIFY username char (50) NOT NULL default '';

ALTER TABLE pre_ucenter_protectedmembers
	MODIFY `admin` char (50) NOT NULL default '0';

ALTER TABLE pre_ucenter_mergemembers
	MODIFY username char (50) NOT NULL;

ALTER TABLE `pre_common_member`
	ADD COLUMN loginname char(50) NOT NULL DEFAULT '' AFTER `email`;

UPDATE `pre_common_member` SET `loginname`=`username`;

ALTER TABLE `pre_common_member`
	ADD UNIQUE INDEX loginname (loginname);

DROP TABLE IF EXISTS pre_common_member_username_history;
CREATE TABLE pre_common_member_username_history
(
	username char(50) NOT NULL DEFAULT '',
	uid      mediumint(8) unsigned NOT NULL,
	dateline int(10) unsigned      NOT NULL DEFAULT '0',
	PRIMARY KEY (username),
	KEY      uid (uid)
) ENGINE = InnoDB;

DROP TABLE IF EXISTS pre_common_member_account;
CREATE TABLE pre_common_member_account
(
	id          int(11) unsigned                      NOT NULL AUTO_INCREMENT,
	uid         mediumint(8) unsigned                 NOT NULL,
	atype       tinyint(1)                            NOT NULL,
	account     varchar(255) NOT NULL,
	create_time TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	bindname    varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (id),
	UNIQUE KEY (uid, atype),
	UNIQUE KEY (atype, account)
) ENGINE = InnoDB;

ALTER TABLE `pre_forum_thread`
	ADD COLUMN `summary` varchar(255) NOT NULL DEFAULT '' COMMENT '摘要' AFTER `subject`;

DROP TABLE IF EXISTS `pre_restful_api`;
CREATE TABLE `pre_restful_api`
(
	`baseuri`   varchar(255) NOT NULL COMMENT 'api base uri',
	`ver`       smallint(6) unsigned NOT NULL COMMENT 'ver',
	`name`      varchar(255) NOT NULL COMMENT '名称',
	`copyright` varchar(255) NOT NULL COMMENT '版权',
	`data`      text         NOT NULL COMMENT '配置数据',
	`status`    tinyint(1)           NOT NULL DEFAULT '1' COMMENT '状态',
	`dateline`  int(10) unsigned     NOT NULL COMMENT '创建日期',
	PRIMARY KEY (`baseuri`, `ver`)
) ENGINE = InnoDB;

DROP TABLE IF EXISTS `pre_restful_app`;
CREATE TABLE `pre_restful_app`
(
	`appid`    int(10) unsigned NOT NULL COMMENT 'appid',
	`secret`   varchar(255) NOT NULL COMMENT 'secret',
	`name`     varchar(255) NOT NULL COMMENT '名称',
	`data`     text         NOT NULL COMMENT '配置数据',
	`status`   tinyint(1)       NOT NULL DEFAULT '1' COMMENT '状态',
	`dateline` int(10) unsigned NOT NULL COMMENT '创建日期',
	PRIMARY KEY (`appid`)
) ENGINE = InnoDB;

DROP TABLE IF EXISTS `pre_restful_permission`;
CREATE TABLE `pre_restful_permission`
(
	`appid`    int(10) unsigned     NOT NULL COMMENT 'appid',
	`uri`      varchar(255)         NOT NULL COMMENT 'api uri',
	`ver`      smallint(6) unsigned NOT NULL COMMENT 'ver',
	`isbase`   tinyint(1)           NOT NULL COMMENT '是否基础 uri',
	`freq`     int(10) unsigned     NOT NULL COMMENT '频率',
	`dateline` int(10) unsigned     NOT NULL COMMENT '创建日期',
	PRIMARY KEY (`appid`, `uri`, `ver`),
	KEY `isbase` (`isbase`)
) ENGINE = InnoDB;

DROP TABLE IF EXISTS pre_restful_stat;
CREATE TABLE pre_restful_stat
(
	`appid`    int(10) unsigned NOT NULL COMMENT 'appid',
	`uri`      varchar(255)     NOT NULL COMMENT 'api uri with ver',
	`daytime`  int(10) unsigned NOT NULL DEFAULT '0',
	`request`  int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`appid`, `uri`, `daytime`),
	KEY daytime (daytime)
) ENGINE = InnoDB;

DROP TABLE IF EXISTS `pre_common_log`;
CREATE TABLE `pre_common_log`
(
	`id`           bigint unsigned AUTO_INCREMENT COMMENT '日志ID',
	`uid`          mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '被记录用户UID',
	`loginname`    char(50)     NOT NULL DEFAULT '' COMMENT '被记录用户登录名',
	`username`     char(50)     NOT NULL DEFAULT '' COMMENT '被记录用户用户名',
	`type`         varchar(255) NOT NULL DEFAULT '' COMMENT '操作类型标识',
	`data`         json         NOT NULL COMMENT '详细数据',
	`operationuid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '执行操作用户UID',
	`source`       varchar(255) NOT NULL DEFAULT '' COMMENT '操作来源 PC|Mobile|APP|Minapp',
	`device`       json         NOT NULL COMMENT '操作者设备相关信息',
	`record`       varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
	`dateline`     bigint unsigned       NOT NULL DEFAULT '0' COMMENT '记录时间',
	PRIMARY KEY (`id`),
	KEY            uid (uid),
	KEY            dateline (dateline),
	KEY            `type` (`type`)
) ENGINE = InnoDB;

INSERT INTO pre_common_setting
	VALUES ('log', 'a:25:{s:13:"clearlogstime";s:1:"0";s:7:"illegal";s:1:"1";s:3:"ban";s:1:"0";s:4:"mods";s:1:"1";s:2:"cp";s:1:"1";s:5:"error";s:1:"1";s:8:"sendmail";s:1:"0";s:4:"SMTP";s:1:"0";s:4:"rate";s:1:"0";s:4:"warn";s:1:"1";s:6:"credit";s:1:"1";s:5:"magic";s:1:"1";s:5:"medal";s:1:"1";s:6:"invite";s:1:"1";s:7:"payment";s:1:"1";s:3:"pmt";s:1:"0";s:13:"crime_delpost";s:1:"1";s:14:"crime_warnpost";s:1:"1";s:13:"crime_banpost";s:1:"1";s:14:"crime_banspeak";s:1:"1";s:14:"crime_banvisit";s:1:"1";s:15:"crime_banstatus";s:1:"1";s:12:"crime_avatar";s:1:"1";s:13:"crime_sightml";s:1:"1";s:18:"crime_customstatus";s:1:"1";}');

DROP TABLE IF EXISTS pre_common_emaillog;
CREATE TABLE pre_common_emaillog
(
	`logid`     int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
	`uid`       mediumint(8) unsigned NOT NULL COMMENT '用户UID',
	`emailtype` int(10) NOT NULL DEFAULT '0' COMMENT '邮件类型 0:验证码类 1:通知类',
	`svctype`   int(10) NOT NULL DEFAULT '0' COMMENT '业务类型 0:第三方业务 1:系统级邮件验证 2:系统级消息验证',
	`status`    int(10) NOT NULL DEFAULT '0' COMMENT '状态 0:未验证',
	`verify`    int(10) NOT NULL DEFAULT '0' COMMENT '验证次数',
	`email`     varchar(255) NOT NULL DEFAULT '' COMMENT '邮箱',
	`ip`        varchar(45)  NOT NULL DEFAULT '' COMMENT 'IP',
	`port`      smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '端口',
	`content`   text         NOT NULL COMMENT '内容',
	`dateline`  int(10) unsigned NOT NULL DEFAULT '0' COMMENT '时间',
	PRIMARY KEY (`logid`),
	KEY         dateline (`email`, `dateline`),
	KEY         uid (uid)
) ENGINE=InnoDB;

ALTER TABLE `pre_common_usergroup`
	ADD COLUMN upgroupid smallint(6) unsigned DEFAULT '0';

ALTER TABLE `pre_common_usergroup`
	ADD INDEX upgroupid (upgroupid);

DROP TABLE IF EXISTS `pre_common_editorblock`;
CREATE TABLE `pre_common_editorblock`
(
	`blockid`     int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '块id',
	`available`   tinyint(1) NOT NULL DEFAULT '0' COMMENT '启用状态',
	`columns`     tinyint(1) NOT NULL DEFAULT '0' COMMENT '多列支持',
	`type`        int(10) NOT NULL DEFAULT '0' COMMENT '块类型 0:数据类型 1:图片类型 2:附件类型',
	`sort`        int(10) NOT NULL DEFAULT '0' COMMENT '块显示顺序',
	`name`        varchar(255) NOT NULL DEFAULT '' COMMENT '块名称',
	`version`     varchar(255) NOT NULL DEFAULT '' COMMENT '块版本号',
	`identifier`  varchar(255) NOT NULL DEFAULT '' COMMENT '块英文标识',
	`description` varchar(255) NOT NULL DEFAULT '' COMMENT '块描述',
	`class`       varchar(255) NOT NULL DEFAULT '0' COMMENT '块类标识',
	`parser`      mediumtext   NOT NULL COMMENT '解析模板',
	`style`       mediumtext   NOT NULL COMMENT 'CSS样式',
	`config`      text         NOT NULL COMMENT '块配置参数',
	`filename`    varchar(255) NOT NULL DEFAULT '' COMMENT 'js文件名称',
	`i18n`        text         NOT NULL COMMENT '块语言变量',
	`parameters`  text         NOT NULL COMMENT '块自定义参数',
	`plugin`      varchar(255) NOT NULL DEFAULT '' COMMENT '插件标识 系统区块为空',
	`filemtime`   int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件最后修改时间',
	`copyright`   varchar(255) NOT NULL DEFAULT '' COMMENT '作者',
	PRIMARY KEY (blockid)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `pre_forum_post`
	ADD COLUMN `content` JSON DEFAULT NULL COMMENT 'JSON内容' AFTER `message`;

INSERT INTO pre_common_setting
	VALUES ('editormodetype', '0');

ALTER TABLE `pre_forum_post`
	ADD COLUMN `source` JSON DEFAULT NULL COMMENT '文章来源' AFTER `content`;

ALTER TABLE `pre_forum_post`
	ADD COLUMN `original` tinyint(1) NOT NULL DEFAULT '0' COMMENT '原创标识' AFTER `subject`;

DROP TABLE IF EXISTS `pre_restful_source`;
CREATE TABLE `pre_restful_source`
(
	`sourceid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
	`name`     varchar(255) NOT NULL COMMENT '名称',
	`url`      varchar(255) NOT NULL COMMENT '源URL',
	PRIMARY KEY (`sourceid`)
) ENGINE = InnoDB;

DROP TABLE IF EXISTS pre_common_admincp_menu_platform;
CREATE TABLE pre_common_admincp_menu_platform
(
	platform varchar(255) NOT NULL DEFAULT 'system',
	menu     text         NOT NULL COMMENT '菜单内容',
	PRIMARY KEY (platform)
) ENGINE = InnoDB;

ALTER TABLE `pre_common_member_profile`
	ADD COLUMN fields json NOT NULL AFTER `field8`;

ALTER TABLE `pre_common_member_profile_history`
	ADD COLUMN fields json NOT NULL AFTER `field8`;

DROP TABLE IF EXISTS pre_common_stylevar_extra;
CREATE TABLE pre_common_stylevar_extra
(
	stylevarid   mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	styleid      smallint(6) unsigned  NOT NULL DEFAULT '0',
	displayorder tinyint(3)            NOT NULL DEFAULT '0',
	title        varchar(100) NOT NULL DEFAULT '',
	description  varchar(255) NOT NULL DEFAULT '',
	variable     varchar(40)  NOT NULL DEFAULT '',
	`type`       varchar(20)  NOT NULL DEFAULT 'text',
	`value`      text         NOT NULL,
	extra        text         NOT NULL,
	PRIMARY KEY (stylevarid),
	KEY          styleid (styleid)
) ENGINE = InnoDB;

ALTER TABLE pre_common_stylevar
	CHANGE COLUMN stylevarid stylevarid mediumint(8) unsigned NOT NULL AUTO_INCREMENT;

ALTER TABLE pre_common_pluginvar
	CHANGE COLUMN displayorder displayorder int (10) NOT NULL DEFAULT '0';

ALTER TABLE pre_common_stylevar_extra
	CHANGE COLUMN displayorder displayorder int (10) NOT NULL DEFAULT '0';

ALTER TABLE pre_common_style
	ADD COLUMN version varchar(20) NOT NULL DEFAULT '';

ALTER TABLE `pre_forum_post`
	ADD COLUMN `bestanswer` tinyint(1) NOT NULL COMMENT '最佳答案',
	ADD INDEX (`bestanswer`);

ALTER TABLE pre_common_member_profile_setting
	ADD COLUMN encrypt tinyint(1) NOT NULL DEFAULT '0';

INSERT INTO pre_common_member_profile_setting
	VALUES('fields', 1, 1, 0, '更多自定义资料', '', 0, 0, 0, 0, 0, 0, 0, 'json', 0, '', '', 0);

ALTER TABLE pre_common_usergroup
	ADD COLUMN creditsformula varchar(255) NOT NULL DEFAULT '';

ALTER TABLE `pre_common_admincp_menu_platform`
	ADD displayorder smallint(6) NOT NULL DEFAULT '0';

ALTER TABLE `pre_common_pluginvar`
	MODIFY `type` varchar (255) NOT NULL DEFAULT 'text';

ALTER TABLE `pre_common_stylevar_extra`
	MODIFY `type` varchar (255) NOT NULL DEFAULT 'text';

DROP TABLE IF EXISTS pre_common_session;
CREATE TABLE pre_common_session
(
	sid          char(6)               NOT NULL DEFAULT '',
	ip           varchar(45)           NOT NULL DEFAULT '',
	uid          mediumint(8) unsigned NOT NULL DEFAULT '0',
	username     char(50)              NOT NULL DEFAULT '',
	groupid      smallint(6) unsigned  NOT NULL DEFAULT '0',
	invisible    tinyint(1)            NOT NULL DEFAULT '0',
	`action`     tinyint(3) unsigned   NOT NULL DEFAULT '0',
	lastactivity int(10) unsigned      NOT NULL DEFAULT '0',
	lastolupdate int(10) unsigned      NOT NULL DEFAULT '0',
	fid          mediumint(8) unsigned NOT NULL DEFAULT '0',
	tid          int(10) unsigned      NOT NULL DEFAULT '0',
	UNIQUE KEY sid (sid),
	KEY uid (uid)
) ENGINE = InnoDB;

INSERT INTO pre_common_setting VALUES ('editorfids','a:1:{i:0;s:0:"";}');
INSERT INTO pre_common_setting VALUES ('editorgroupid','a:1:{i:0;s:0:"";}');
INSERT INTO pre_common_setting VALUES ('iconfont','static/js/iconfont.js');
INSERT INTO pre_common_setting VALUES ('regemail','1');
INSERT INTO pre_common_setting VALUES ('security_email', '1');
INSERT INTO pre_common_setting VALUES ('security_logoff', '0');
INSERT INTO pre_common_setting VALUES ('security_mobile', '0');
INSERT INTO pre_common_setting VALUES ('security_password', '1');
INSERT INTO pre_common_setting VALUES ('security_question', '1');
INSERT INTO pre_common_setting VALUES ('security_rename', '1');
INSERT INTO pre_common_setting VALUES ('security_verify', 'a:0:{}');

ALTER TABLE pre_common_smslog ADD INDEX status (`status`, `dateline`, `uid`);
ALTER TABLE pre_common_smslog ADD INDEX dateline2 (`dateline`);

ALTER TABLE pre_forum_thread
	ADD INDEX displayorder_dateline (fid, displayorder, dateline);
ALTER TABLE pre_forum_thread
	ADD INDEX typeid_dateline (fid, typeid, displayorder, dateline);
ALTER TABLE pre_forum_thread
	ADD INDEX displayorder_replies (fid, displayorder, replies);
ALTER TABLE pre_forum_thread
	ADD INDEX typeid_replies (fid, typeid, displayorder, replies);
ALTER TABLE pre_forum_thread
	ADD INDEX displayorder_views (fid, displayorder, views);
ALTER TABLE pre_forum_thread
	ADD INDEX typeid_views (fid, typeid, displayorder, views);
ALTER TABLE pre_forum_thread
	ADD INDEX displayorder_recommends (fid, displayorder, recommends);
ALTER TABLE pre_forum_thread
	ADD INDEX typeid_recommends (fid, typeid, displayorder, recommends);
ALTER TABLE pre_forum_thread
	ADD INDEX displayorder_heats (fid, displayorder, heats);
ALTER TABLE pre_forum_thread
	ADD INDEX typeid_heats (fid, typeid, displayorder, heats);

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

ALTER TABLE `pre_portal_article_title` DROP `tag`;
ALTER TABLE `pre_portal_article_title` ADD `tags` VARCHAR(255) NOT NULL AFTER `click8`;

ALTER TABLE `pre_forum_threadtype` ADD super text NOT NULL;

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

ALTER TABLE pre_forum_forum
	ADD COLUMN editormode tinyint(1) NOT NULL DEFAULT '-1';

UPDATE pre_common_credit_log_field f
	JOIN pre_common_credit_log l ON f.logid = l.logid
	SET f.dateline = l.dateline;

ALTER TABLE pre_common_credit_log_field
	CHANGE COLUMN logid logid int(10) unsigned NOT NULL;

ALTER TABLE pre_common_credit_log_field
	ADD COLUMN uid mediumint(8) unsigned NOT NULL DEFAULT '0' AFTER logid;

ALTER TABLE pre_common_credit_log_field
	ADD INDEX uid (uid);

UPDATE pre_common_credit_log_field f
	JOIN pre_common_credit_log l ON f.logid = l.logid
	SET f.uid = l.uid;

INSERT INTO pre_restful_source (sourceid, name, url) VALUES (1, 'Discuz! Team', 'https://api.witframe.com/discuzrestful');

ALTER TABLE `pre_common_usergroup_field`
	ADD COLUMN `fields` json;

ALTER TABLE `pre_forum_forumfield`
	ADD COLUMN `fields` json;