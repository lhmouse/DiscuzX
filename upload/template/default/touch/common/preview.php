<?php exit('Access Denied');?>
<!--{template common/header}-->
<div class="preview-page">
    <div id="ct" class="wp cl ptw pbw">
        <div class="preview-wrapper">
            <div class="preview-phone-section">
                <div class="dzphone"><div>
                    <div class="scrtop">
                        <div class="cl"><div class="time"><!--{echo date('H:i')}--></div></div>
                        <div class="siteurl"><!--{$_G['siteurl']}--></div>
                    </div>
                    <div class="phone-loading" id="phoneLoading">
                        <div class="spinner"></div>
                        <span>loading...</span>
                    </div>
                    <iframe id="ifm0" frameborder="0" src="misc.php?mod=mobile&view=true" onload="document.getElementById('phoneLoading').style.display='none';"></iframe>
                    <div class="homebar"></div>
                </div></div>
                <div class="phone-label">Mobile Preview</div>
            </div>
            <div class="preview-qr-section">
                <div class="preview-qr-card">
                    <div class="card-icon"><img src="favicon.ico" alt="site icon" /></div>
                    <h1 class="xw1">{lang login_mobile}</h1>
                    <p class="subtitle">{lang login_mobile_join}</p>
                    <div class="qr-image">
                        <div class="scan-line"></div>
                        <img src="data/cache/$file" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--{template common/footer}-->