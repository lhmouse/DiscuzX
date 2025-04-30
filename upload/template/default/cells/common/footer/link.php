<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class common_footer_link {

	public static $name = '手机底部导航';

	public static $useage = "
选择上面链接插入或者复制下面代码到模板中自行调整：<br /><br />
<textarea style='width: 90%;height: 100px;' readonly>
&lt;a href=&quot;...&quot; class=&quot;flex&quot;&gt;
  &lt;span class=&quot;foot-ico&quot;&gt;
    &lt;img src=&quot;{STATICURL}image/mobile/touch/space.svg&quot; /&gt;
  &lt;/span&gt;
&lt;span class=&quot;foot-txt&quot;&gt;测试&lt;/span&gt;
&lt;/a&gt;</textarea>";

	public static $cellList = array(
		'common/footer/link_home' => '首页链接',
		'common/footer/link_info' => '资讯链接',
		'common/footer/link_post' => '发帖链接',
		'common/footer/link_find' => '发现链接',
		'common/footer/link_my' => '我的链接',
	);

	public static $requireList = array();

	public static $used = array();

	public static $mobileonly = true;

	public static function getDefault($type = 1) {
		return <<<EOF
{cell common/footer/link_home}
{cell common/footer/link_info}
{cell common/footer/link_post}
{cell common/footer/link_find}
{cell common/footer/link_my}
EOF;

	}

}
