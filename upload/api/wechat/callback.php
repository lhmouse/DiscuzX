<?php

const ALLOWGUEST = true;
const IN_API = true;

require_once '../../source/class/class_core.php';

$discuz = C::app();
$discuz->init_cron = false;
$discuz->init_session = false;
$discuz->init();

(new account_wechat())->getLoginUser();