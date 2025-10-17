<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class editor {

	private static $r_dzTpl = [
		"/[\n\r\t]*\{block\/(\d+?)\}[\n\r\t]*/i",
		"/[\n\r\t]*\{blockdata\/(\d+?)\}[\n\r\t]*/i",
		"/[\n\r\t]*\{ad\/(.+?)\}[\n\r\t]*/i",
		"/[\n\r\t]*\{ad\s+([a-zA-Z0-9_\[\]]+)\/(.+?)\}[\n\r\t]*/i",
		"/[\n\r\t]*\{date\((.+?)\)\}[\n\r\t]*/i",
		"/[\n\r\t]*\{avatar\((.+?)\)\}[\n\r\t]*/i",
		"/[\n\r\t]*\{eval\}\s*(\<\!\-\-)*(.+?)(\-\-\>)*\s*\{\/eval\}[\n\r\t]*/is",
		"/[\n\r\t]*\{eval\s+(.+?)\s*\}[\n\r\t]*/is",
		"/[\n\r\t]*\{csstemplate\}[\n\r\t]*/is",
		"/\{(\\\$[a-zA-Z0-9_\-\>\[\]\'\"\$\.\x7f-\xff]+)\s(or|\?\?)\s([a-zA-Z0-9\']+)\}/s",
		"/\{(\\\$[a-zA-Z0-9_\-\>\[\]\'\"\$\.\x7f-\xff]+)\}/s",
		'/\{hook\/(\w+?)(\s+(.+?))?\}/i',
		"/((\\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*(\-\>)?[a-zA-Z0-9_\x7f-\xff]*)(\[[a-zA-Z0-9_\-\.\"\'\[\]\$\x7f-\xff]+\])*)/s",
		"/\<\?\=\<\?\=((\\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*(\-\>)?[a-zA-Z0-9_\x7f-\xff]*)(\[[a-zA-Z0-9_\-\.\"\'\[\]\$\x7f-\xff]+\])*)\?\>\?\>/s",
		"/[\n\r\t]*\{template\s+([a-z0-9_:\/]+)\}[\n\r\t]*/is",
		"/[\n\r\t]*\{template\s+(.+?)\}[\n\r\t]*/is",
		"/[\n\r\t]*\{echo\s+(.+?)\}[\n\r\t]*/is",
		"/([\n\r\t]*)\{if\s+(.+?)\}([\n\r\t]*)/is",
		"/([\n\r\t]*)\{elseif\s+(.+?)\}([\n\r\t]*)/is",
		'/\{else\}/i',
		'/\{\/if\}/i',
		"/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\}[\n\r\t]*/is",
		"/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}[\n\r\t]*/is",
		'/\{\/loop\}/i',
		"/[\n\r\t]*\{block\s+([a-zA-Z0-9_\[\]']+)\}(.+?)\{\/block\}/is",
	];
	private static $r_php = [
		'/<\?/',
		'/\?>/',
	];
	private static $r_js = [
		'/javascrit:/i',
		'/<script(.*?)>/i',
		'/\s+on(?!ly\b)([a-zA-Z]+)/i',
	];

	public static function checkTemplate($template) {
		$template = preg_replace('/\<\!\-\-\{(.+?)\}\-\-\>/s', "{\\1}", $template);
		foreach(self::$r_dzTpl as $r) {
			if(preg_match($r, $template)) {
				return false;
			}
		}
		foreach(self::$r_php as $r) {
			if(preg_match($r, $template)) {
				return false;
			}
		}
		foreach(self::$r_js as $r) {
			if(preg_match($r, $template)) {
				return false;
			}
		}
		return true;
	}

	public static function toArray($content) {
		$content = json_decode($content, true);
		return $content;
	}

	public static function getBlockTemplate($type) {
		$editorblock_parser = $editorblock_style = '';
		$editorblock = memory('get', 'editorblock_'.$type);
		if($editorblock && $editorblock['parser'] && $editorblock['style']) {
			$editorblock_parser = $editorblock['parser'];
			$editorblock_style = $editorblock['style'];
		} else {
			$editorblock = table_common_editorblock::t()->fetch_by_block_class($type);
			$editorblock_parser = $editorblock['parser'];
			$editorblock_style = $editorblock['style'];
			memory('set', 'editorblock_'.$type, $editorblock);
		}
		return [$editorblock_parser, $editorblock_style];
	}

	public static function getBlockParser($tpl, $block) {
		$parser = $tpl;
		return $parser;
	}

	public static function parser($content) {
		global $_G;
		$blocksData = self::toArray($content);
		$parserData = '';
		$styleData = ['types' => [], 'style' => ''];
		foreach($blocksData['blocks'] as $key => $value) {
			list($editorblock_parser, $editorblock_style) = self::getBlockTemplate($value['type']);
			// 是否用户回帖可见
			$allowParser = true;
			if($value['tunes']['hideTune']['hide']) {
				$authorreplyexist = null;
				if($_G['uid']) {
					$authorreplyexist = table_forum_post::t()->fetch_pid_by_tid_authorid($_G['tid'], $_G['uid']);
				}
				if(!$authorreplyexist) {
					$allowParser = false;
				}
			}
			if(!$allowParser) {
				$parser = '<div class="locked">'.($_G['uid'] ? $_G['username'] : lang('forum/template', 'guest')).lang('forum/template', 'post_hide_reply_hidden_text').'<a href="forum.php?mod=post&action=reply&fid='.$_G['fid'].'&tid='.$_G['tid'].'" onclick="showWindow(\'reply\', this.href)">'.lang('forum/template', 'reply').'</a></div>';
			} else {
				$parser = (new editorBlock($editorblock_parser, $value))->replace();
			}

			// 锚点解析
			$anchorparse = explode(',', getglobal('setting/anchorparse')) ?? [];
			if(in_array($value['type'], $anchorparse)) {
				$parser = preg_replace_callback('/<a\s+href=[\'"]#([^\'"]+)[\'"](.*?)>/s', function($m) {
					$href = $m[1];
					// 在<a>标签中添加onclick属性
					$onclick = "javascript: document.getElementById('".$href."').scrollIntoView();return false;";
					return '<a href="#'.$href.'" onclick="'.$onclick.'"'.$m[2].'>';
				}, $parser);
			}
			// 追加
			$parserData .= $parser;
			if(!in_array($value['type'], $styleData['types'])) {
				$styleData['types'][] = $value['type'];
				$styleData['style'] .= $editorblock_style;
			}
		}
		return [$parserData, $styleData['style']];
	}

}

class editorBlock {
	var $html = '';
	var $block = [];

	/*
	 * [loop data.items]
	 *   <li>{text}</li>
	 * [/loop]
	 */
	const rLoop = '/\[loop ([a-zA-Z\.]+)\](.+?)\[\/loop\]/s';
	const rLoopIndex = '/\[loopindex\]/s';
	const rLoopObject = '/\[loopobject ([a-zA-Z\.]+)\](.+?)\[\/loopobject\]/s';
	const rLoopObjectData = '/\{loopobjectdata\}/';
	const rVar = '/\{([a-zA-Z\.]+)\}/';
	/*
	 * [if data.ok]
	 *   {text} or string
	 * [/if]
	 */
	const rIf = '/\[if ([a-zA-Z\.]+)=(.+?)\](.*?)\[\/if\]/s';
	/*
	 * [url data.url]
	 */
	const rUrl = '/\[url ([a-zA-Z\.]+)\]/s';
	/*
	 * [url data.url]
	 */
	const rAttach = '/\[attach ([a-zA-Z\.]+)\]/s';
	/*
	* [column blocks]
	*/
	const rColumn = '/\[column ([a-zA-Z\.]+)\]/s';
	/*
	 * [script path]
	 */
	const rScript = '/\[script (.+?)\]/s';
	/*
	* [codeflask id,language,code,jspath]
	*/
	const rCodeflask = '/\[codeflask ([a-zA-Z\.]+),([a-zA-Z\.]+),([a-zA-Z\.]+),([a-zA-Z\.]+),(.+?)\]/s';

	public function __construct($html, $block) {
		$this->html = $html;
		$this->block = $block;
	}

	public function replace() {
		//$this->html = preg_replace_callback(self::rScript, array($this, '_script'), $this->html);
		$this->html = preg_replace_callback(self::rCodeflask, [$this, '_codeflask'], $this->html);
		$this->html = preg_replace_callback(self::rLoop, [$this, '_loop'], $this->html);
		$this->html = preg_replace_callback(self::rLoopObject, [$this, '_object'], $this->html);
		$this->html = preg_replace_callback(self::rVar, [$this, '_var'], $this->html);
		$this->html = preg_replace_callback(self::rUrl, [$this, '_url'], $this->html);
		$this->html = preg_replace_callback(self::rAttach, [$this, '_attach'], $this->html);
		$this->html = preg_replace_callback(self::rIf, [$this, '_if'], $this->html);
		return $this->html;
	}

	private function _codeflask($m, $value = null) {
		$path = $m[5];
		if(str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, 'ftp://')) {
			return '';
		}
		$path = str_replace('{STATICURL}', getglobal('staticurl'), $path);
		$path = str_replace('{VERHASH}', getglobal('style/verhash'), $path);
		$id = $this->_value($m[1], $value);
		$language = $this->_value($m[2], $value);
		$n_count = $this->_value($m[3], $value) ?? 0;
		$code = $this->_value($m[4], $value);
		$code = str_replace('`', '\`', $code);
		$code = dhtmlspecialchars($code);
		$rand = time().random(5);
		$script = <<<EOF
<script type="application/javascript" src="$path"></script>
<script type="application/javascript">
			const editorElem$rand = document.getElementById('codeflask-$id');
			const flask$rand = new CodeFlask(editorElem$rand, {
				language: '$language',
				lineNumbers: true,
				styleParent: this.shadowRoot,
				rtl: false,
				readonly: true
			});
            		var code$rand = `$code`;
            		code$rand = code$rand.replace(/&amp;/g, "&").replace(/&lt;/g, "<").replace(/&gt;/g, ">").replace(/&quot;/g, '"').replace(/&#039;/g, "'"); 
			flask$rand.addLanguage('$language', Prism.languages['$language']);
			flask$rand.onUpdate((code) => {
				// do something with code here.
				// this will trigger whenever the code
				// in the editor changes.
			    // console.log(code)
			});
			// flask.updateCode('云诺');
			// This will also trigger .onUpdate()
			flask$rand.updateCode(code$rand);

			const currentCode$rand = flask$rand.getCode();
            
		            var coderow = parseInt('$n_count');
			    if (coderow === undefined || coderow !== coderow || coderow === 0) {
				    coderow = flask$rand.lineNumber;
			    }
		            if (coderow > 10 && coderow < 20) {
		                editorElem$rand.parentElement.style.height = '300px';
		            } else if (coderow >= 20) {
		                editorElem$rand.parentElement.style.height = '500px';
		            }
			//console.log({currentCode$rand})    
</script>
EOF;
		return $script;
	}

	private function _object($m, $value = null) {
		$t2 = '';
		if($m[1] != 'null' && $m[1] != null) {
			$value = $this->_value($m[1], $value);
		}
		if(is_object($value)) {
			$jsonString = json_encode($value);
			$array = json_decode($jsonString, true);
		} elseif(is_array($value)) {
			$array = $value;
		} else {
			return $m[2];
		}
		foreach($array as $ad) {
			$t2 .= preg_replace(self::rLoopObjectData, $ad, $m[2]);
		}
		return $t2;
	}

	private function _if($m, $value = null) {
		$result = $this->_value($m[1], $value);
		if(!$result) {
			return '';
		} else {
			if((string)$m[2] == 'notnull') {
				$content = preg_replace_callback(self::rVar, function($m) {
					return $this->_var($m);
				}, $m[3]);
				return !empty($content) ? $content : $m[3];
			} elseif((string)$result != (string)$m[2]) {
				return '';
			} else {
				$content = preg_replace_callback(self::rVar, function($m) {
					return $this->_var($m);
				}, $m[3]);
				return !empty($content) ? $content : $m[3];
			}
		}
	}

	private function _url($m, $value = null) {
		$url = $this->_value($m[1], $value);
		if(!$url) {
			return '';
		} else {
			if(str_contains($url, 'http')) {
				return $url;
			} else {
				return getglobal('siteurl').$url;
			}
		}
	}

	private function _attach($m, $value = null) {
		global $_G;
		$aid = $this->_value($m[1], $value);
		if(!$aid) {
			return '';
		} else {
			return aidencode($aid, 0, $_G['tid']);
		}
	}

	private function _script($m, $value = null) {
		$path = $m[1];
		if(str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, 'ftp://')) {
			return '';
		}
		$path = str_replace('{STATICURL}', getglobal('staticurl'), $path);
		$path = str_replace('{VERHASH}', getglobal('style/verhash'), $path);

		return '<script src="'.$path.'"></script>';
	}

	private function _column($m, $value = null) {
		$result = $this->_value($m[1], $value);
		if(!$result) {
			return '';
		} else {
			return $m[2];
		}
	}

	private function _loop($m) {
		$nodes = $this->_value($m[1]);
		if(!is_array($nodes)) {
			return $m[0];
		}
		$s = '';
		foreach($nodes as $index => $node) {
			$t = preg_replace(self::rLoopIndex, $index, $m[2]);
			$t = preg_replace_callback(self::rLoopObject, function($m) use ($node) {
				return $this->_object($m, $node);
			}, $t);
			$t = preg_replace_callback(self::rVar, function($m) use ($node) {
				return $this->_var($m, $node);
			}, $t);
			$t = preg_replace_callback(self::rIf, function($m) use ($node) {
				return $this->_if($m, $node);
			}, $t);
			$s .= preg_replace_callback(self::rColumn, function($m) use ($node) {
				$data = ['blocks' => []];
				$data['blocks'] = $this->_var($m, $node);
				list($parserData, $styleData) = editor::parser(json_encode($data));
				return $parserData.$styleData;
			}, $t);
		}
		return $s;
	}

	private function _var($m, $value = null) {
		$v = $this->_value($m[1], $value);
		//return $v !== false ? $v : $m[0];
		return $v !== false ? $v : '';
	}

	private function _value($var, $value = null) {
		$e = explode('.', $var);
		$value = $value ?? $this->block;
		foreach($e as $_v) {
			if(!isset($value[$_v])) {
				return false;
			}
			$value = $value[$_v];
		}
		return $value;
	}
}