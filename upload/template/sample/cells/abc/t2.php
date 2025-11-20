<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

/*
 * 可自定义的细胞模板
 * 模板调用方法：{cells abc_t2}
 */
class abc_t2 {

	public static $name = '可自定义的细胞模板范例';
	public static $useage = '{cells abc_t2}';
	public static $cellList = array(
		'abc/loop_start' => '循环体开始 (必须包含)',
		'abc/subject' => '标题',
		'abc/loop_end' => '循环体结束 (必须包含)',
	);
	public static $requireList = array(
		'abc/loop_start',
		'abc/loop_end',
	);

	public static $used = array(
	);

	public static function getDefault($type = 0) {
		if(!$type) {
			return <<<EOF
<div class="tl bm bmw" style="position: relative;">
	<div class="bm_c threadlist">
		<table cellspacing="0" cellpadding="0">
			{cell abc/loop_start}		  
			<tbody>
			<tr>
			<td>
			    {cell abc/subject}	
			</td>
			</tr>
			</tbody>
			{cell abc/loop_end}
		</table>
	</div>
</div>

EOF;
		} else {
			return <<<EOF
<div class="threadlist_box mt10 cl">
	<div class="threadlist cl">
		<ul>
		{cell abc/loop_start}
			<li class="list">
				{cell abc/subject}
			</li>
		{cell abc/loop_end}
		</ul>
	</div>
</div>
EOF;
		}
	}

}



