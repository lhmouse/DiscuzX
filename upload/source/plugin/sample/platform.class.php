<?php

// source/plugin/xxx/platform.class.php
// 平台菜单 method 调用的文件

class platform_sample {

	public static function test() {
		return true;
	}

	public static function testList() {
		return [
			['菜单c', 'plugin_sample:c'],
			[cplang('区域'), '', 1],
			['菜单d', 'plugin_sample:d'],
			['远程链接', 'https://www.discuz.vip', 'target="_blank"'],
			[cplang('区域'), '', 2],
		];
	}

}