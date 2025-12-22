<?php exit('Access Denied');?>
<style type="text/css">
/* 手机版记录发布表单样式 */
#moodfm {
    background: var(--dz-BG-0);
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    margin-bottom: 15px;
}

#mood_addform {
    margin: 0;
}

.moodfm_post {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.moodfm_text textarea {
    width: 100%;
    min-height: 100px;
    padding: 12px;
    border: 1px solid var(--dz-BOR-ed);
    border-radius: 6px;
    resize: vertical;
    font-size: 16px;
    line-height: 1.5;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
    box-sizing: border-box;
    background: var(--dz-BG-0);
    color: var(--dz-FC-333);
}

.moodfm_text textarea:focus {
    outline: none;
    border-color: var(--dz-BG-color);
    box-shadow: 0 0 0 2px rgba(0, 122, 255, 0.2);
}

/* 图片上传区域 */
.specialpost {
    margin: 0;
    padding: 0;
    list-style: none;
}

.upload-main {
    padding: 10px 0;
    display: flex;
    align-items: flex-start;
    flex-wrap: wrap;
}

.image-main {
    width: 100%;
    display: block;
    overflow: hidden;
}

#multipic_img {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 10px;
}

.image-list {
    position: relative;
    float: left;
    width: 70px;
    height: 70px;
}

.image-upload {
    width: 100%;
    height: 100%;
    position: relative;
    border: 1px dashed var(--dz-BOR-ed);
    border-radius: 6px;
    background: var(--dz-BG-0);
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.image-upload:hover {
    border-color: var(--dz-BG-color);
    background: rgba(0, 122, 255, 0.05);
}

.image-upload:before {
    top: 50%;
    left: 50%;
    height: 2px;
    content: "";
    width: 20px;
    position: absolute;
    background: var(--dz-FC-bbb);
    transform: translateX(-50%);
    z-index: 1;
}

.image-upload:after {
    top: 50%;
    left: 50%;
    width: 2px;
    content: "";
    height: 20px;
    position: absolute;
    background: var(--dz-FC-bbb);
    transform: translateY(-50%);
    z-index: 1;
}

.image-upload:hover:before,
.image-upload:hover:after {
    background: var(--dz-BG-color);
}

/* 文件输入框样式 */
.image-upload input[type="file"],
.image-upload input.file {
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    opacity: 0 !important;
    cursor: pointer !important;
    z-index: 2 !important;
    font-size: 100px !important;
    padding: 0 !important;
    margin: 0 !important;
    border: none !important;
    outline: none !important;
    background: transparent !important;
}

/* 上传按钮样式 */
.image-upload-mp {
    width: 70px !important;
    height: 70px !important;
    position: relative !important;
    border: 1px dashed var(--dz-BOR-ed) !important;
    border-radius: 6px !important;
    background: var(--dz-BG-0) !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    float: left !important;
    margin-right: 10px !important;
    margin-bottom: 10px !important;
}

/* 图片预览样式 */
.previewbigpic {
    position: relative;
    overflow: hidden;
    margin-right: 10px;
    margin-bottom: 10px;
    width: 70px;
    height: 70px;
    border: 1px solid var(--dz-BOR-ed);
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--dz-BG-0);
    transition: all 0.2s ease;
}

.previewbigpic:hover {
    border-color: var(--dz-BG-color);
    box-shadow: 0 2px 8px rgba(0, 122, 255, 0.15);
}

.previewbigpic img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
/* 表单布局优化 */
.moodfm_f {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-top: 15px;
}

.moodfm_signature {
    font-size: 14px;
    color: var(--dz-FC-666);
}

.moodfm_signature input[type="checkbox"] {
    margin-right: 8px;
}

.moodfm_btn {
    width: 100%;
}

.moodfm_btn .button {
    width: 100%;
    font-size: 16px;
    border-radius: 6px;
    background-color: var(--dz-BG-color);
    color: var(--dz-FC-fff);
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.moodfm_btn .button:hover {
    background-color: var(--dz-BG-color-hover);
    box-shadow: 0 4px 12px rgba(0, 122, 255, 0.3);
}
</style>

<div id="moodfm" class="moodfm">
	<form method="post" autocomplete="off" id="mood_addform" action="home.php?mod=spacecp&ac=doing&view=$_GET['view']" enctype="multipart/form-data">
		<div class="moodfm_post">
			<div class="moodfm_text">
				<textarea name="message" id="message" class="xg1" placeholder="{$defaultstr}" rows="3"></textarea>
			</div>
			
			<!-- 图片上传区域 -->
			<div class="specialpost s_clear">
				<li class="upload-main">
					<div class="image-main" id="MultiPicList">
						<div id="multipic_img"></div>
						<div id="multipic_btn" class="image-list image-upload image-upload-mp">
							<div class="file_pic"></div>
							<input name="photos[]" type="file" class="file" id="multipic_sel" multiple="multiple"
								accept=".jpg,.jpeg,.png,image/jpeg,image/png">
						</div>
					</div>
				</li>
			</div>
			
			<div class="moodfm_f">
				<div class="moodfm_btn">
					<button type="submit" name="add" id="add" class="pgsbtn button">{lang publish}</button>
				</div>
			</div>
		</div>
		<input type="hidden" name="addsubmit" value="true" />
		<input type="hidden" name="refer" value="$theurl" />
		<input type="hidden" name="topicid" value="$topicid" />
		<input type="hidden" name="formhash" value="{FORMHASH}" />
	</form>
</div>

<script type="text/javascript" reload="1">
	listenup();

	function listenup() {
		// 检查是否支持FileReader API
		if (typeof FileReader === 'undefined') {
			console.error('当前浏览器不支持FileReader API，无法实现图片预览功能。');
		} else {
			// 为id为'multipic_sel'的元素添加change事件监听器
			document.getElementById('multipic_sel').addEventListener('change', function(event) {
				// 获取用户选择的所有文件
				const files = event.target.files;
				// 获取图片显示容器
				const imgContainer = document.getElementById('multipic_img');

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
								// 设置div的样式
								imgWrapper.className = 'previewbigpic z';
								// 添加数据属性，标识原始文件索引
								imgWrapper.setAttribute('data-file-index', index);

								// 将图片添加到div中
								imgWrapper.append(img);
								// 调整图片大小以适应容器
								img.style.width = '100%';
								img.style.height = '100%';
								img.style.objectFit = 'cover'; // 保持图片比例并覆盖容器

								// 将包裹图片的div添加到显示容器中
								imgContainer.append(imgWrapper);
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

	var MultiPicUploaded = 0;
	var mpimgmax = 12;
	var mpimgmax_low = mpimgmax - 1;
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
	}

	function preview_pic(obj) {
		var hlthumb = obj.parentNode.childNodes[0];
		zoom(hlthumb, hlthumb.src);
	}
</script>