<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!empty($_G['setting']['i18n'][$_GET['key']])) {
	dsetcookie('i18n', $_GET['key'], 86400 * 365);
} else {
	dsetcookie('i18n', 'default', 86400 * 365);
}

dsetcookie('d_i18n', '', -1);

dheader('location: '.dreferer());