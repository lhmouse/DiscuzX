<?php

//自定义支付通道 基础类

namespace sample;

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

// sample 仅实现了 pay 方法，更多参见 /class/pay

class pay_test extends \pay_base {

	public function callback($data, $order) {
		print_r([$data, $order]);
	}

	public function pay($order) {
		global $_G;
		$pay_url = $_G['siteurl'].'?app=plugin&id=sample:pay_notify&out_biz_no='.$order['out_biz_no'].'&trade_no=NO'.time().'&referer='.rawurlencode(dreferer());

		echo <<<EOF
<script type="text/javascript">
		top.location.href = '{$pay_url}';
</script>
EOF;
exit;

	}

}
