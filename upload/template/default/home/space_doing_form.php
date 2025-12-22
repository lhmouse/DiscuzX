<?php exit('Access Denied'); ?>
<!--{if $_G[inajax] && $type}-->
<h3 class="flb">
	<em id="return_$_GET[handlekey]">{lang share}</em>
	<!--{if $_G[inajax]}--><span><a href="javascript:;" onclick="hideWindow('$_GET[handlekey]');" class="flbc" title="{lang close}">{lang close}</a></span><!--{/if}-->
</h3>
<!--{/if}-->

<script type="text/javascript">
	var msgstr = '$defaultstr';

	function handlePrompt(type) {
		var msgObj = $('message');
		if (type) {
			if (msgObj.value == msgstr) {
				msgObj.value = '';
				msgObj.className = 'xg2';
			}
			if ($('message_menu')) {
				if ($('message_menu').style.display == 'block') {
					//showFace('message', 'message', msgstr);
				}
			}
			if (BROWSER.firefox || BROWSER.chrome) {
				//showFace('message', 'message', msgstr);
			}
		} else {
			if (msgObj.value == '') {
				msgObj.value = msgstr;
				msgObj.className = 'xg1';
			}
		}
	}
</script>

<div id="moodfm">
	<form method="post" autocomplete="off" id="mood_addform" action="home.php?mod=spacecp&ac=doing&view=$_GET['view']" onsubmit="if($('message').value == msgstr){showError('{lang content_isnull}');return false;} return check_submit();" enctype="multipart/form-data">
		<div class="moodfm_container">
			<!--{if $type}-->
			<p class="mbn cl">
				<span class="y xg1">{lang share_count}&nbsp;&nbsp;</span>
				{lang share_description}:
			</p>
			<!--{/if}-->
			<div class="moodfm_row">
				<div id="mood_statusinput" class="moodfm_input">
					<textarea name="message" id="message" class="xg1" onfocus="handlePrompt(1);" onblur="handlePrompt(0);" onkeyup="strLenCalc(this, 'maxlimit')" onkeydown="ctrlEnter(event, 'add');" rows="4">$defaultstr</textarea>
					<div class="moodfm_f">
						<div id="return_doing" class="xi1 xw1"></div>
						<span class="y">{lang doing_maxlimit_char}</span>
					</div>
				</div>
			</div>
			<!--{if !$type}-->
			<div class="image-main" id="MultiPicList" style="display:none;">
				<div id="multipic_img"></div>
				<div id="multipic_btn" class="image-list image-upload image-upload-mp">
					<div class="file_pic"></div>
					<input name="photos[]" type="file" class="file" id="multipic_sel" multiple="multiple"
						accept=".jpg,.jpeg,.png,image/jpeg,image/png">
				</div>
			</div>
			<!--{else}-->
			<ul id="share_preview" class="el mtm cl 1">
				<!--{eval $value = $arr;}-->
				<!--{template home/space_share_li}-->
			</ul>
			<!--{/if}-->
			<div class="moodfm_div">
				<div class="specialpost s_clear">
					<!--{if !$type}-->
					<a href="javascript:;" id="moodfm_pic"><i class="fico-image fic8 fc-s fnmr vm" ></i></a>
					<!--{/if}-->
				</div>
				<div class="moodfm_btn">
					<!--{if $commentcable[$type]}-->
					<label><input type="checkbox" class="pc z" name="iscomment" value="1"/><!--{if $type == 'thread'}-->{lang post_add_inonetime}<!--{else}-->{lang comment_add_inonetime}<!--{/if}--></label>
					<!--{/if}-->
					<button type="submit" name="add" id="add" class="pgsbtn" onsubmit="check_submit();"><strong>{lang publish}</strong></button>
				</div>
			</div>
		</div>
		<!--{if $type}--><input type="hidden" name="type" value="$type" /><!--{/if}-->
		<!--{if $id}--><input type="hidden" name="id" value="$id" /><!--{/if}-->
		<input type="hidden" name="addsubmit" value="true" />
		<input type="hidden" name="refer" value="$theurl" />
		<input type="hidden" name="topicid" value="$topicid" />
		<input type="hidden" name="formhash" value="{FORMHASH}" />
	</form>
</div>

<script type="text/javascript" reload="1">
	listenup();
	var MultiPicUploaded = 0;
	var mpimgmax = 12;
	var mpimgmax_low = mpimgmax - 1;
	function listenup() {
		if (typeof FileReader === 'undefined') {

		} else {
			var moodPicBtn = document.getElementById('moodfm_pic');
			var fileInput = document.getElementById('multipic_sel');
			var multiPicList = document.getElementById('MultiPicList');
			
			if (moodPicBtn && fileInput) {
				moodPicBtn.addEventListener('click', function(e) {
					e.preventDefault();
					fileInput.click(); 
				});
			}
			document.getElementById('multipic_sel').addEventListener('change', function(event) {
				const files = event.target.files;
				const imgContainer = document.getElementById('multipic_img');
				if (files.length > 0 && multiPicList.style.display === 'none') {
					multiPicList.style.display = '';
				}
				for (let i = 0; i < files.length; i++) {
					const file = files[i];
					if (file) {
						(function(currentFile, index) {
							const reader = new FileReader();
							reader.onload = function(e) {
								const img = new Image();
								img.src = e.target.result;
								const imgWrapper = document.createElement('div');
								imgWrapper.className = 'previewbigpic z';
								imgWrapper.setAttribute('data-file-index', index);
								const removeDiv = document.createElement('div');
								removeDiv.className = 'flbc';
								removeDiv.onclick = function() {
									MultiPicDel(this);
								};
								imgWrapper.append(img);
								imgWrapper.append(removeDiv);
								img.style.width = '100%';
								img.style.objectFit = 'cover';
								imgContainer.append(imgWrapper);
								MultiPicUploaded++;
								console.log(i);
							};
							reader.onerror = function() {
							};
							reader.readAsDataURL(currentFile);
						})(file);
					}
				}
				document.getElementById("multipic_sel").style.display = "none";
				document.getElementById("multipic_sel").removeAttribute("id");
				const newbtn = document.createElement('input');
				newbtn.type = 'file';
				newbtn.name = 'photos[]';
				newbtn.id = 'multipic_sel';
				newbtn.className = "file";
				newbtn.multiple = "multiple";
				newbtn.accept = ".jpg,.jpeg,.png,image/jpeg,image/png";
				document.getElementById('multipic_btn').append(newbtn);
				listenup();
			});
		}
	}

	if (MultiPicUploaded >= mpimgmax_low) {
		document.querySelector('.image-upload-mp').style.display = 'none';
	}

	function MultiPicDel(obj) {
		var oldAid = obj.getAttribute('dataid');
		console.log(oldAid);
		MultiPicUploaded--;
		obj.parentNode.remove();
		
		if (MultiPicUploaded < mpimgmax) {
			document.querySelector('.image-upload-mp').style.display = '';
		}
		if (MultiPicUploaded <= 0) {
			document.getElementById('MultiPicList').style.display = 'none';
			MultiPicUploaded = 0; 
		}
	}

	function preview_pic(obj) {
		var hlthumb = obj.parentNode.childNodes[0];
		zoom(hlthumb, hlthumb.src);
	}
</script>