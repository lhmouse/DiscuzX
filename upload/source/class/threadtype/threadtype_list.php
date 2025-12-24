<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

/*
扩充设置范例：
[
 {"type": "text", "field": "f1", "name": "text1", "width": 80, "maxlen": "2,4"},
 {"type": "radio", "field": "f3", "name": "radio2", "options": [{"name":"A","value":"a","default":true},{"name":"B","value":"b"}]},
 {"type": "checkbox", "field": "f4", "name": "checkbox", "options": [{"name":"C","value":"c","default":true},{"name":"D","value":"d"}]},
 {"type": "select", "field": "f5", "name": "select", "width": 80, "options": [{"name":"C","value":"c"},{"name":"D","value":"d","default":true}]}
 {"template": {"global": "{f1} - {f2} - {f3} - {f4} - {f5}<br>", "viewthread": "{f1} - {f2} - {f3} - {f4} - {f5}<br>"}}
]
*/

class threadtype_list {

	var $name = '自定义列表';

	var $desc = '
<pre>	
[
	{
		"type": "类型", "field": "字段名", "name": "显示名称", "options": [
			{"name":"显示名称","value":"值","default":"默认(bool)"}
			...
		]
	},
	{"template": {"global": "全局模板", "viewthread": "帖子页模板"}}
	...
]
type: 支持 text(文本)、radio(单选)、checkbox(复选)、select(下拉)、color(颜色)
options: type 为 mradio、checkbox、select 时有效
field: 字段变量名称
name: 字段显示名称
width: 显示宽度
maxlen: 最大长度，type 为 text、color 有效
mask: 星号掩码，格式“s,l”，s=起始位，l=长度，type 为 text 有效
template: 模板中用 {字段名} 引用相应的列表字段

【范例】
[
 {"type": "text", "field": "f1", "name": "文本", "width": 80},
 {"type": "radio", "field": "f2", "name": "开关", "options": [{"name":"A","value":"a","default":true},{"name":"B","value":"b"}]},
 {"template": {"global": "{f1} - {f2}", "viewthread": "{f1} - {f2}<br>"}}
]

</pre>
	';

	function show($option, $params) {
		static $header = '';
		if(!$header) {
			$header = '<style type="text/css">
.enhcomp {
	width: auto !important;
}
.enhcomp th {
	width: auto !important;
	text-align: left !important;
	padding: 0 4px 2px 4px !important;
}
.enhcomp td {
	padding-right: 20px !important;
}
.enhcomp label {
	margin-right: 10px;
}
.enhcomp .albumBtn {
	display: inline-block;
	min-width: 26px;
	height: 26px;
	background-image: url('.STATICURL.'image/common/uploadbutton_small_pic.png);
	background-repeat: no-repeat;
	background-position: center top;
	vertical-align: middle;
}
</style>';
			$string = $header;
		} else {
			$string = '';
		}

		$conf = is_string($params) ? json_decode($params, true) : $params;
		$_id = random(5);
		$string .= '<table class="enhcomp" id="'.$_id.'">';
		$string .= '<tr class="header bbda"><th>'.lang('template', 'delete').'</th>';
		foreach($conf as $col) {
			if(!isset($col['field'])) {
				continue;
			}
			$string .= '<th>'.$col['name'].'</th>';
		}
		$string .= '<th>'.lang('forum/template', 'displayorder').'</th></tr>';
		$addCols = '';
		if(!empty($option['value'])) {
			$i = 0;
			foreach($option['value'] as $value) {
				$string .= '<tr><td><input type="checkbox" name="typeoption['.$option['identifier'].'][_del_][]" value="'.$i.'"></td>';
				foreach($conf as $col) {
					if(!isset($col['field'])) {
						continue;
					}
					$var['variable'] = 'typeoption['.$option['identifier'].']';
					$string .= '<td>';
					$string .= $this->_get_input($col, $var, $i, $value[$col['field']] ?? null);
					$string .= '</td>';
				}
				$string .= '<td><input type="text" name="typeoption['.$option['identifier'].'][_order_][]" value="'.$i.'" style="width: 50px;"></td></tr>';
				$i++;
			}
		}

		$c = 1;
		$addCols .= '{_col_0_}<input type="checkbox" name="typeoption['.$option['identifier'].'][_del_][]" value="{_i_}">{/_col_0_}';
		foreach($conf as $col) {
			if(!isset($col['field'])) {
				continue;
			}
			$var['variable'] = 'typeoption['.$option['identifier'].']';
			$s = str_replace('{i}', '{_i_}', $this->_get_input($col, $var, '{i}', null, true));
			$addCols .= '{_col_'.$c.'_}'.$s.'{/_col_'.$c.'_}';
			$c++;
		}
		$addCols .= '{_col_'.$c.'_}<input type="text" name="typeoption['.$option['identifier'].'][_order_][]" value="{_i_}" style="width: 50px;">{/_col_'.$c.'_}';
		$string .= '</table><a href="javascript:;" onclick="threadTypeAddRow(\''.$_id.'\')" class="addtr">'.lang('template', 'add').'</a>';
		$string .= '<script type="text/html" id="row_'.$_id.'">'.$addCols.'</script>';
		return $string;
	}

	function __get_maskvalue($value, $mask) {
		if(!empty($mask)) {
			list($s, $l) = explode(',', $mask);
			if($s > 0 && $l > 0) {
				$e = substr($value, intval($s) + intval($l));
				$value = component_list . phpsubstr($value, 0, $s) . str_repeat('*', $l) .$e;
			}
		}
		return $value;
	}

	function _get_input($col, $var, $i, $value = null, $default = false) {
		$widthstr = $col['width'] ? ' style="width:'.$col['width'].'px !important;"' : '';
		switch($col['type']) {
			case 'text':
				$append = '';
				if($value !== null && !empty($col['mask'])) {
					$mvalue = dhtmlspecialchars(authcode(json_encode([$value, $col['mask']]), 'ENCODE'));
					$value = $this->__get_maskvalue($value, $col['mask']);
					$append = '<input type="hidden" name="'.$var['variable'].'['.$i.']['.$col['field'].'._mask_]" value="'.$mvalue.'" />';
				}
				$paramstr = $value !== null ? ' value="'.dhtmlspecialchars($value).'"' : '';
				$paramstr .= $col['maxlen'] ? ' maxlength="'.$col['maxlen'].'"' : '';
				return '<input name="'.$var['variable'].'['.$i.']['.$col['field'].']" type="text"'.$paramstr.$widthstr.' />'.$append;
			case 'radio':
				$str = '';
				foreach($col['options'] as $opt) {
					$checked = $value !== null && $opt['value'] == $value || $default && $opt['default'] ? ' checked' : '';
					$str .= '<label><input name="'.$var['variable'].'['.$i.']['.$col['field'].']" type="radio" value="'.$opt['value'].'"'.$checked.' />'.$opt['name'].'</label>';
				}
				return $str;
			case 'checkbox':
				$str = '';
				foreach($col['options'] as $opt) {
					$checked = $value !== null && in_array($opt['value'], $value) || $default && $opt['default'] ? ' checked' : '';
					$str .= '<label><input name="'.$var['variable'].'['.$i.']['.$col['field'].'][]" type="checkbox" value="'.$opt['value'].'"'.$checked.' />'.$opt['name'].'</label>';
				}
				return $str;
			case 'select':
				$str = '<select name="'.$var['variable'].'['.$i.']['.$col['field'].']"'.$widthstr.'>';
				foreach($col['options'] as $opt) {
					$selected = $value !== null && $opt['value'] == $value || $default && $opt['default'] ? ' selected' : '';
					$str .= '<option value="'.$opt['value'].'"'.$selected.'>'.$opt['name'].'</option>';
				}
				return $str.'</select>';
			case 'album':
				$paramstr = $value !== null ? ' value="'.dhtmlspecialchars($value).'"' : '';
				$str = '<input name="'.$var['variable'].'['.$i.']['.$col['field'].']" type="text"'.$paramstr.$widthstr.' />';
				return $str.'<a href="javascript:;" onclick="openAlbumWindow(this.previousElementSibling)" class="albumBtn"></a>';
		}
	}

	function view($viewtype, $option, $params, $value) {
		$params = json_decode($params, true);
		$tpl = '';
		foreach($params as $param) {
			if(isset($param['template'][$viewtype])) {
				$tpl = $param['template'][$viewtype];
				break;
			}
		}
		if(!$tpl) {
			return '';
		}
		$string = '';
		foreach($value as $v) {
			$row = $tpl;
			foreach($params as $param) {
				if(!isset($param['field'])) {
					continue;
				}
				$value = isset($v[$param['field']]) ? (is_array($v[$param['field']]) ? implode(',', $v[$param['field']]) : $v[$param['field']]) : '';
				$row = str_replace('{'.$param['field'].'}', $value, $row);
			}
			$string .= $row;
		}
		return $string;
	}

	function serialize($params, &$value) {
		if(!empty($value['_del_'])) {
			foreach($value['_del_'] as $i) {
				unset($value[$i]);
			}
			unset($value['_del_']);
		}
		$valuenew = [];
		if(!empty($value['_order_'])) {
			$value['_order_'] = array_flip($value['_order_']);
			ksort($value['_order_']);
			foreach($value['_order_'] as $k) {
				if(!isset($value[$k])) {
					continue;
				}
				foreach($value[$k] as $_k => $_v) {
					if(str_ends_with($_k, '._mask_')) {
						$mvalue = json_decode(authcode($_v, 'DECODE'));
						if($mvalue) {
							$_ik = substr($_k, 0, -7);
							if($this->__get_maskvalue($mvalue[0], $mvalue[1]) == $value[$k][$_ik]) {
								$value[$k][$_ik] = $mvalue[0];
							}
							unset($value[$k][$_k]);
						}
					}
				}
				$valuenew[] = $value[$k];
			}
			$value = $valuenew;
		}

		$value = array_values($valuenew);
		$value = json_encode($value);
	}

	function unserialize($params, &$value) {
		$value = json_decode($value, 1);
	}

}