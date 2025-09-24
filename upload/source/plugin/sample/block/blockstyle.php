<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$blockstyle = [
	[
		'id' => 'style1',
		'blockclass' => 'sample_test2',
		'name' => '[xxx]style',
		'template' => '[loop]<div>{posts} <hr> {field1} <hr> {field2} </div>[/loop]',
	],
];