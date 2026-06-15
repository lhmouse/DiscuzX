<?php exit('Access Denied'); ?>

<!--{eval
$colors = [
    '21,91,213' => '{lang admincolor_classic}',
    '26,176,76' => '{lang admincolor_green}',
    '118,65,226' => '{lang admincolor_purple}',
    '222,42,144' => '{lang admincolor_pink}',
    '8,164,232' => '{lang admincolor_blue}',
    '242,122,12' => '{lang admincolor_orange}',
    '96,108,118' => '{lang admincolor_gray}',
    '12,210,196' => '{lang admincolor_cyan}',
    '216,24,36' => '{lang admincolor_red}',
    '228,182,38' => '{lang admincolor_yellow}',
];
}-->

<div class="adminColor">
	<h1><span class="coloricon"></span>{lang admincolor}</h1>

	<form method="post" id="adminColor_form" action="{ADMINSCRIPT}?action=misc&operation=admincolor" target="adminColor_target">
		<fieldset data-cssvar="--root-base-value">
			<!--{loop $colors $color $name}-->
			<div data-color="{$color}" {if $color == $_G['adminColor']['--root-base-value']} style="border-color: rgb({$color})" class="active"{/if}>
				<em style="background: rgb({$color})"></em>{$name}
			</div>
			<!--{/loop}-->
			<input type="text" title="{lang admincolor_tips}" id="--root-base-value" name="--root-base-value" onkeydown="testColor()" onkeyup="testColor(this.value)" value="{$_G['adminColor']['--root-base-value']}" />
		</fieldset>
		<input type="hidden" name="formhash" value="{FORMHASH}"/>
		<input name="submit" type="submit" value="{lang save}" />
	</form>
</div>

<script>
var lastObj = new Array();
document.querySelectorAll('#adminColor_form fieldset').forEach(function(item) {
	var cssvar = item.dataset.cssvar;
	item.querySelectorAll('div').forEach(function(div) {
		var color = div.dataset.color;
		div.onclick = function () {
			setValue(this, cssvar, color);
		};
	});
	lastObj[cssvar] = item.querySelector('div.active');
});

function setValue(obj, cssvar, v) {
	if(lastObj[cssvar]) {
		lastObj[cssvar].style.borderColor = '#BBB';
	}
	lastObj[cssvar] = obj;
	obj.style.borderColor = 'rgb(' + v + ')';
	$(cssvar).value = v;
	switchColor();
}
var _t = null;
function testColor(v) {
	if(!v) {
		clearTimeout(_t);
		return;
	}
	if(!v.match(/^\d+,\d+,\d+$/)) {
		return;
	}
	_t = setTimeout(function() {
		switchColor();
	}, 500);
}
</script>