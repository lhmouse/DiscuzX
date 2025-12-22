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
		// 检查是否支持FileReader API
		if (typeof FileReader === 'undefined') {
			console.error('当前浏览器不支持FileReader API，无法实现图片预览功能。');
		} else {
			var moodPicBtn = document.getElementById('moodfm_pic');
			var fileInput = document.getElementById('multipic_sel');
			var multiPicList = document.getElementById('MultiPicList');
			
			if (moodPicBtn && fileInput) {
				moodPicBtn.addEventListener('click', function(e) {
					e.preventDefault();
					fileInput.click(); // 触发文件选择对话框
				});
			}
			// 为id为'multipic_sel'的元素添加change事件监听器
			document.getElementById('multipic_sel').addEventListener('change', function(event) {
				// 获取用户选择的所有文件
				const files = event.target.files;
				// 获取图片显示容器
				const imgContainer = document.getElementById('multipic_img');
				if (files.length > 0 && multiPicList.style.display === 'none') {
					multiPicList.style.display = '';
				}
				// 遍历所有文件
				for (let i = 0; i < files.length; i++) {
					const file = files[i];
					if (file) {
						// 创建一个FileReader对象
						(function(currentFile, index) {
							const reader = new FileReader();
							// 当文件读取完成时触发的回调函数
							reader.onload = function(e) {
								// 创建一个新的Image对象
								const img = new Image();
								// 将读取到的文件内容（DataURL）赋值给img元素的src属性
								img.src = e.target.result;
								// 创建一个div元素用于包裹图片
								const imgWrapper = document.createElement('div');
								// 设置div的样式，这里假设固定宽度为200px，高度为200px
								imgWrapper.className = 'previewbigpic z';
								// 添加数据属性，标识原始文件索引
								imgWrapper.setAttribute('data-file-index', index);
								// 添加删除按钮
								const removeDiv = document.createElement('div');
								removeDiv.className = 'flbc';
								removeDiv.onclick = function() {
									MultiPicDel(this);
								};

								//imgContainer.append("<div class=\"previewbigpic z\" ><img src=\"" + img.src + "\" width=\"100\" /><div class=\"remove\" onclick=\"MultiPicDel(this)\"></div><span class=\"preview\" onclick=\"preview_pic(this)\"> <img src=\"template/discuz_newdim/static/svg/preview.svg\"/>预览</span></div>");
								// 创建一个隐藏的 input 元素用于存储文件
								//const fileInput = document.createElement('input');
								//fileInput.type = 'file';
								//fileInput.name = 'image[]';
								//fileInput.id = 'image_'+i;
								//fileInput.style.display = 'none';
								//document.getElementById('image_'+i).files = [currentFile];
								//fileInput.files = [currentFile];
								console.log(currentFile);
								// 将图片添加到div中
								imgWrapper.append(img);
								imgWrapper.append(removeDiv);
								// 调整图片大小以适应容器
								img.style.width = '100%';
								img.style.objectFit = 'cover'; // 保持图片比例并覆盖容器

								// 将包裹图片的div添加到显示容器中
								imgContainer.append(imgWrapper);
								// 将文件 input 元素添加到表单中
								MultiPicUploaded++;
								//imgContainer.append(fileInput);
								console.log(i);
							};
							// 当文件读取出错时触发的回调函数
							reader.onerror = function() {
								console.error('文件读取出错，请检查文件格式或权限。');
							};
							// 以DataURL格式读取文件
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
		//cappendAttachDel(oldAid);
		MultiPicUploaded--;
		obj.parentNode.remove();
		
		if (MultiPicUploaded < mpimgmax) {
			document.querySelector('.image-upload-mp').style.display = '';
		}
		if (MultiPicUploaded <= 0) {
			document.getElementById('MultiPicList').style.display = 'none';
			MultiPicUploaded = 0; // 重置计数器
		}
	}

	function preview_pic(obj) {
		var hlthumb = obj.parentNode.childNodes[0];
		// console.log(hlthumb);
		zoom(hlthumb, hlthumb.src);
	}
</script>