<?php

// 邮件发送插件
// 邮件设置>邮件发送方式，选择“通过插件发送(插件控制发送流程)”，“插件标识及脚本标识”填写“sample:mail”

class mailsend_mail {

	public function sendmail($toemail, $subject, $message = '', $from = '') {
		// 发送邮件
		runlog('SMTP', "--", 0);
		return true;
	}

}