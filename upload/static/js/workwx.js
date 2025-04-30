function workWxShare(param) {
	var x = new Ajax('JSON');
	x.get('misc.php?mod=share&' + param, function (s) {
		if(!s) {
			return;
		}
		if(!isWorkWx()) {
			setCopy(s.param.title + '\n' + s.param.desc + '\n\n' + s.param.link, $L('share_copy_notice'));
			return;
		}
		workwxJsSdkCall(function () {
			workwxConf['beta'] = true;
			workwxConf['jsApiList'] = new Array('shareAppMessage');
			wx.config(workwxConf);
			try {
				wx.invoke("shareAppMessage", s.param, function (res) {
					if(res.err_msg == "shareAppMessage:ok") {
					} else {
					}
				});
			} catch(e) {
			}
		});
	});
}

function isWorkWx() {
	var ua = window.navigator.userAgent.toLowerCase();
	if((ua.match(/micromessenger/i) == 'micromessenger') && (ua.match(/wxwork/i) == 'wxwork')) {
		return true;
	} else {
		return false;
	}
}

var workwxConf = {};
function workwxJsSdkCall(call) {
	var x = new Ajax('JSON');
	x.get('misc.php?mod=workwx&ac=JsSdkConf&url=' + escape(workwxUrl), function (s) {
		if(!s) {
			return;
		}
		workwxConf = s;
		call();
	});
}