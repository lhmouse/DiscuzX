<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

checkpermission('dbimport');

if(!(submitcheck('deletesubmit', 1) && !empty($_GET['formhash']) && $_GET['formhash'] == formhash())) {

	$exportlog = $exportziplog = $exportsize = $exportzipsize = $exportfiletime = $exportzipfiletime = [];
	if(is_dir(DISCUZ_DATA.'./'.$backupdir)) {
		$dir = dir(DISCUZ_DATA.'./'.$backupdir);
		while($entry = $dir->read()) {
			$entry = DISCUZ_DATA.'./'.$backupdir.'/'.$entry;
			if(is_file($entry)) {
				if(preg_match('/\.sql$/i', $entry)) {
					$filesize = filesize($entry);
					$filemtime = filemtime($entry);
					$fp = fopen($entry, 'rb');
					$identify = explode(',', base64_decode(preg_replace('/^# Identify:\s*(\w+).*/s', "\\1", fgets($fp, 256))));
					fclose($fp);
					$key = preg_replace('/^(.+?)(\-\d+)\.sql$/i', '\\1', basename($entry));
					$exportlog[$key][$identify[4]] = [
						'version' => $identify[1],
						'type' => $identify[2],
						'method' => $identify[3],
						'volume' => $identify[4],
						'filename' => str_replace(DISCUZ_ROOT, '', $entry),
						'dateline' => $filemtime,
						'size' => $filesize
					];
					$exportsize[$key] += $filesize;
					$exportfiletime[$key] = $filemtime;
				} elseif(preg_match('/\.zip$/i', $entry)) {
					$key = preg_replace('/^(.+?)(\-\d+)\.zip$/i', '\\1', basename($entry));
					$filesize = filesize($entry);
					$filemtime = filemtime($entry);
					$exportziplog[$key][] = [
						'type' => 'zip',
						'filename' => str_replace(DISCUZ_ROOT, '', $entry),
						'size' => $filesize,
						'dateline' => $filemtime
					];
					$exportzipsize[$key] += $filesize;
					$exportzipfiletime[$key] = $filemtime;
				}
			}
		}
		$dir->close();
		if(!empty($exportlog)) {
			array_multisort($exportfiletime, SORT_DESC, SORT_STRING, $exportlog);
		}
		if(!empty($exportziplog)) {
			array_multisort($exportzipfiletime, SORT_DESC, SORT_STRING, $exportziplog);
		}
	} else {
		cpmsg('database_export_dest_invalid', '', 'error');
	}

	$restore_url = $_G['siteurl'].'data/restore.php';

	shownav('founder', 'nav_db', 'nav_db_import');
	showsubmenu('nav_db', [
		['nav_db_export', 'db&operation=export', 0],
		['nav_db_import', 'db&operation=import', 1],
		['nav_db_runquery', 'db&operation=runquery', 0],
		['nav_db_optimize', 'db&operation=optimize', 0],
		['nav_db_dbcheck', 'db&operation=dbcheck', 0]
	]);
	/*search={"nav_db":"action=db&operation=export","nav_db_import":"action=db&operation=import"}*/
	showtips('db_import_tips');
	showtableheader('db_import');
	showtablerow('', ['colspan="9" class="tipsblock"'], [cplang('do_import_option', ['restore_url' => $restore_url])]);
	/*search*/

	showformheader('db&operation=import');
	showtitle('db_export_file');
	showsubtitle(['', 'filename', 'version', 'time', 'type', 'size', 'db_method', 'db_volume', '']);

	$datasiteurl = $_G['siteurl'].'data/';

	foreach($exportlog as $key => $val) {
		$info = $val[1];
		$info['dateline'] = is_int($info['dateline']) ? dgmdate($info['dateline']) : $lang['unknown'];
		$info['size'] = sizecount($exportsize[$key]);
		$info['volume'] = count($val);
		$info['method'] = $info['method'] == 'multivol' ? $lang['db_multivol'] : $lang['db_shell'];
		$datafile_server = '.'.$info['filename'];
		showtablerow('', '', [
			"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"".$key."\">",
			"<a href=\"javascript:;\" onclick=\"display('exportlog_$key')\">".basename($info['filename']).'</a>',
			$info['version'],
			$info['dateline'],
			$lang['db_export_'.$info['type']],
			$info['size'],
			$info['method'],
			"<a href=\"javascript:;\" onclick=\"display('exportlog_$key')\">".$info['volume'].'</a>',
			"<a class=\"act\" href=\"".$datasiteurl."restore.php?operation=import&from=server&datafile_server=$datafile_server&importsubmit=yes\"".($info['version'] != $_G['setting']['version'] ? " onclick=\"return confirm('{$lang['db_import_confirm']}');\"" : " onclick=\"return confirm('{$lang['db_import_confirm_sql']}');\"")." class=\"act\" target=\"_blank\">{$lang['import']}</a>"
		]);
		echo '<tbody id="exportlog_'.$key.'" style="display:none">';
		foreach($val as $info) {
			$info['dateline'] = is_int($info['dateline']) ? dgmdate($info['dateline']) : $lang['unknown'];
			$info['size'] = sizecount($info['size']);
			showtablerow('', '', [
				'',
				"<a href=\"{$info['filename']}\">".substr(strrchr($info['filename'], '/'), 1).'</a>',
				$info['version'],
				$info['dateline'],
				'',
				$info['size'],
				'',
				'',
				''
			]);
		}
		echo '</tbody>';
	}

	foreach($exportziplog as $key => $val) {
		sort($val);//修改 确保-1.zip排前面,才会自动解压-2.zip
		$info = $val[0];
		$info['volume'] = count($val);
		$info['dateline'] = is_int($info['dateline']) ? dgmdate($info['dateline']) : $lang['unknown'];
		$info['size'] = sizecount($exportzipsize[$key]);
		$info['method'] = $info['method'] == 'multivol' ? $lang['db_multivol'] : $lang['db_zip'];
		$datafile_server = '.'.$info['filename'];
		showtablerow('', '', [
			"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"".basename($info['filename'])."\">",
			"<a href=\"javascript:;\" onclick=\"display('exportlog_zip_$key')\">".basename($info['filename']).'</a>',
			'',
			$info['dateline'],
			($info['volume'] > 1 ? $lang['db_multivol'] : '').$lang['db_export_'.$info['type']],
			$info['size'],
			$info['method'],
			"<a href=\"javascript:;\" onclick=\"display('exportlog_zip_$key')\">".$info['volume'].'</a>',
			"<a href=\"".$datasiteurl."restore.php?operation=importzip&datafile_server=$datafile_server&importsubmit=yes\"  onclick=\"return confirm('{$lang['db_import_confirm_zip']}');\" class=\"act\" target=\"_blank\">{$lang['db_import_unzip']}</a>"
		]);
		echo '<tbody id="exportlog_zip_'.$key.'" style="display:none">';
		foreach($val as $info) {
			$info['dateline'] = is_int($info['dateline']) ? dgmdate($info['dateline']) : $lang['unknown'];
			$info['size'] = sizecount($info['size']);
			showtablerow('', '', [
				'',
				"<a href=\"{$info['filename']}\">".substr(strrchr($info['filename'], '/'), 1).'</a>',
				$info['version'],
				$info['dateline'],
				'',
				$info['size'],
				'',
				'',
				''
			]);
		}
		echo '</tbody>';
	}

	showsubmit('deletesubmit', 'submit', 'del');
	showformfooter();

	showtablefooter();

} else {
	if(is_array($_GET['delete'])) {
		foreach($_GET['delete'] as $filename) {
			$type = '.sql';
			if(str_contains($filename, '-1.zip')) {
				$type = '.zip';
				$filename = str_replace('-1.zip', '', $filename);
			}
			$file_path = './data/'.$backupdir.'/'.str_replace(['/', '\\'], '', $filename);
			if(is_file($file_path)) {
				@unlink($file_path);
			} else {
				$i = 1;
				while(1) {
					$file_path = './data/'.$backupdir.'/'.str_replace(['/', '\\'], '', $filename.'-'.$i.$type);
					if(is_file($file_path)) {
						@unlink($file_path);
						$i++;
					} else {
						break;
					}
				}
			}
		}
		cpmsg('database_file_delete_succeed', 'action=db&operation=import', 'succeed');
	} else {
		cpmsg('database_file_delete_invalid', '', 'error');
	}
}
	