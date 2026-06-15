<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$days = 30 * 86400;
$begin = dgmdate(time() - $days, 'Ymd');
$end = dgmdate(time(), 'Ymd');

// Data column groupings for chart series
$groups = [
	'login'     => ['login', 'mobilelogin'],
	'register'  => ['register'],
	'thread'    => ['thread', 'poll', 'activity', 'reward', 'debate', 'trade'],
	'post'      => ['post'],
	'social'    => ['doing', 'docomment', 'blog', 'blogcomment', 'pic', 'piccomment', 'share', 'sharecomment'],
	'interact'  => ['sendpm', 'addfriend', 'friend'],
];

// Load all stat data for the date range
$data = [];
foreach(table_common_stat::t()->fetch_all_stat($begin, $end, '*') as $value) {
	$data[substr($value['daytime'], 4, 4)] = $value;
}

// Build graph data series
$graph = [];
foreach($groups as $gk => $cols) {
	$graph[$gk] = '';
}

$count = 0;
$xaxis = '';
for($i = time() - $days; $i <= time(); $i += 86400) {
	$md = dgmdate($i, 'md');
	$xaxis .= "<value xid='$count'>".$md.'</value>';
	$row = $data[$md] ?? [];

	foreach($groups as $gk => $cols) {
		$num = 0;
		foreach($cols as $col) {
			$num += intval($row[$col] ?? 0);
		}
		$graph[$gk] .= "<value xid='$count'>".$num.'</value>';
	}
	$count++;
}

// Build XML response
$xml = '<'."?xml version=\"1.0\" encoding=\"utf-8\"?>";
$xml .= '<chart><xaxis>';
$xml .= $xaxis;
$xml .= '</xaxis><graphs>';

$gid = 0;
foreach($graph as $key => $values) {
	$title = lang('spacecp', "do_stat_$key");
	if($title == '') {
		continue;
	}
	$xml .= "<graph gid='$gid' title='".$title."'>";
	$xml .= $values;
	$xml .= '</graph>';
	$gid++;
}
$xml .= '</graphs></chart>';

@header('Expires: -1');
@header('Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0', FALSE);
@header('Pragma: no-cache');
@header('Content-type: application/xml; charset=utf-8');
echo $xml;
exit();