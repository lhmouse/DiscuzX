<?php exit('Access Denied');?>
<!--{subtemplate common/header_ajax}-->
<style>
	.dList {
		width:350px;height:400px;overflow:auto;float: left;
		border-right: 1px solid #EEE;
	}
	.dList p {
		line-height: 30px;
		border-bottom: 1px dotted #EEE;
	}
	.mList {
		width:150px;height:400px;overflow:auto;float: left;
	}
	.mList p {
		line-height: 30px;
		border-bottom: 1px dotted #EEE;
	}
</style>
<div class="fcontent">
	<h3 class="flb">
		<em id="return_$handlekey">$wTitle</em>
		<span>
			<a href="javascript:;" class="flbc" onclick="hideWindow('$_GET['handlekey']')" title="{lang close}">{lang close}</a>
		</span>
	</h3>
	<form id="wxPushForm" method="post" action="misc.php?mod=workwx&infloat=yes&$param" onsubmit="return false;">
		<div class="cl">
			<div class="c flb dList">
				成员搜索: <input id="mSearch" class="px" style="width: 70%" onkeyup="searchMember(this.value)">
				$departmentStr
			</div>
			<div class="c flb mList" id="selected">
				<p id="mAll"><label class="vm"><img src="{STATICURL}image/workwx/folder.png" /> 全体成员</label></p>
			</div>
			$appendHtml
		</div>
		<div class="o pns">
			<button type="button" onclick="submitSend()" name="pushsubmit" id="pushsubmit" value="ture" class="pn pnc"><span>$wBtn</span></button>
			<div class="z">$wDesc</div>
		</div>
		<input type="hidden" name="handlekey" value="$_GET[handlekey]" />
		<input type="hidden" name="formhash" value="{FORMHASH}"/>
	</form>
</div>
<script type="text/javascript" reload="1">
	var addItems = new Array();
	function submitSend() {
		showDialog('确定要发送消息？', 'confirm', '消息', function() {
			ajaxpost('wxPushForm', 'return_$_GET[handlekey]', 'return_$_GET[handlekey]', 'onerror');
		}, 1);
	}

	var searchT = null;
	function searchMember(kw) {
		clearTimeout(searchT);
		searchT = setTimeout(function () {
			selectPart($('p' + rPartId), rPartId, kw, 0);
		}, 500)
	}
	function selectPart(obj, partId, kw, pl) {
		if(pl > 0 && $('members_' + partId).innerHTML != '') {
			$('members_' + partId).innerHTML = '';
			obj.innerHTML = '<img class="vm" src="{STATICURL}image/common/tree_plus.gif" />';
			return;
		}
		$('mSearch').style.display = '';
		var x = new Ajax('JSON');
		x.get('misc.php?mod=workwx&ac=GetUserList&dId='+partId + '&kw='+kw, function(s) {
			var list = '';
			for (var k in s) {
				list += '<p><label style="margin-left: ' + (pl + 30) + 'px" onclick="addSelect(\'toUser\', \'' + s[k].userid + '\', this)"> <img class="vm" src="{STATICURL}image/workwx/people.png" /> ' + s[k].name +
					'</label></p>';
			}
			$('members_' + partId).innerHTML = list;
			if(obj && list != '') {
				obj.innerHTML = '<img class="vm" src="{STATICURL}image/common/tree_minus.gif" />'
			}
		});
	}

	function addSelect(name, value, obj) {
		if(addItems[name + '_' + value]) {
			return;
		}
		addItems[name + '_' + value] = 1;
		$('mAll').style.display = 'none';
		$('selected').innerHTML += '<p><span class="y" style="cursor: pointer" onclick="removeSelect(this, \'' + name + '_' + value + '\')">x</span><label><input name="' + name + '[]" value="' + value + '" type="hidden" checked /> ' + obj.innerHTML + '</label></p>';
	}

	function removeSelect(obj, itemKey) {
		delete addItems[itemKey];
		if(Object.keys(addItems).length == 0) {
			$('mAll').style.display = '';
		}
		obj.parentNode.outerHTML = '';
	}

	function succeedhandle_$_GET['handlekey'](url, msg) {
		hideWindow('$_GET['handlekey']');
		showDialog(msg, 'notice');
	}
</script>
<!--{subtemplate common/footer_ajax}-->