<?php exit('Access Denied');?>
<div id="moodfm" class="moodfm">
	<form method="post" autocomplete="off" id="mood_addform" action="home.php?mod=spacecp&ac=doing&view=$_GET['view']" enctype="multipart/form-data">
		<div class="moodfm_post">
			<div class="moodfm_text">
				<textarea name="message" id="message" class="xg1" placeholder="{$defaultstr}" rows="3"></textarea>
			</div>
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
		if (typeof FileReader === 'undefined') {
		} else {
			document.getElementById('multipic_sel').addEventListener('change', function(event) {
				const files = event.target.files;
				const imgContainer = document.getElementById('multipic_img');

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
                                const removeDiv = document.createElement('i');
								removeDiv.className = 'fico-error';
								removeDiv.onclick = function() {
									MultiPicDel(this);
								};
								imgWrapper.append(img);
                                imgWrapper.append(removeDiv);
								img.style.width = '100%';
								img.style.height = '100%';
								img.style.objectFit = 'cover';
								imgContainer.append(imgWrapper);
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