<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class cos_tencent extends oss_base {
	function __construct($oss_id, $oss_key, $oss_bucket, $oss_endpoint, $oss_url, $bucket_url) {
	}

	public function setCors() {
	}


	public function testOSS() {
	}

	public function RefererConfig() {
	}

	public function getImgStyle($style) {
	}

	public function isObject($object) {
	}

	public function getFilesList($prefix = '', $marker = '', $limit = 100, $delimiter = '') {
	}

	public function uploadData($data, $object, $Acl = null) {
	}

	public function uploadFile($file, $object, $Acl = null) {
	}

	public function setAcl($object, $Acl = null) {
	}

	public function getPolicy($dir, $object, $length = 1048576000) {
	}

	public function signUrl($object, $filename = '', $e = 3600) {
	}

	public function renameObject($oldObject, $newObject, $MimeType = null) {
	}

	public function downFile($file, $object) {
	}

	public function deleteFile($objects) {
	}

	public function getCallback($object) {
	}

}