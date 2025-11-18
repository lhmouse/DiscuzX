<?php exit('Access Denied');?>

<!--  Load Editor.js's Css -->
<link rel="stylesheet" type="text/css" href="{STATICURL}js/editorjs/editorjs.css?{VERHASH}" />
<!--{if $_GET['action'] == 'edit' && !empty($postinfo['noticetrimstr_html'])}-->
$postinfo['noticetrimstr_html']
<!--{/if}-->
<div class="json-editor" xmlns="http://www.w3.org/1999/html">
		<input type="hidden" name="inajax" id="inajax" value="1" />
		<input type="hidden" name="message" id="{$editorid}_textarea" value="" />
		<input type="hidden" name="contentType" id="contentType" value="json" />
		<input type="hidden" name="contentEditor" id="contentEditor" value="jsonEditor" />
		<input type="hidden" name="content" id="content" value="" />
		<!--{if $_GET['action'] == 'edit'}-->
		<input type="hidden" name="noticetrimstr" id="noticetrimstr" value="{$postinfo['noticetrimstr']}" />
		<!--{/if}-->
		<input type="hidden" name="handlekey" id="handlekey" value="postform" />
		<!--{template forum/jsoneditor_toolbar}-->
		<div class="json-editor__content _json-editor__content--small">
			<div id="editorjs"></div>
		</div>
		<div class="json-editor__output">
			<pre class="json-editor__output-content" id="output"></pre>
		</div>
</div>

<!-- 常量 -->
<script type="text/javascript">
    const editorid = '{$editorid}';
    const editor_fid = "{$_G['fid']}";
    const editor_uid = "{$_G['uid']}";
    const editor_hash = "{echo md5(substr(md5($_G['config']['security']['authkey']), 8).$_G['uid'])}";
    const editor_remote_attachurl = "{$_G['setting']['ftp']['attachurl']}";
    const editor_attachurl = "{$_G['setting']['attachurl']}";
    // EDITOR_TOOLS
    let EDITOR_TOOLS = {};
    // first define the tools to be made avaliable in the columns
    let column_tools = {};
    // next define the tools in the main block
    // Warning - Dont just use main_tools - you will probably generate a circular reference
    let main_tools = {};
    let i18n_tools = {};
    let content = "";
    <!--{if $_GET[action] == 'edit'}-->
    content = {
        blocks: {$postinfo['content']}
    };
    <!--{/if}-->
</script>

<!-- Load Editor.js's Core -->
<script src="{STATICURL}js/editorjs/editorjs.umd.js?{VERHASH}"></script>

<!-- Load Ajax Core -->
<script src="{STATICURL}js/editorjs/ajax.js?{VERHASH}"></script>
<script src="{STATICURL}js/editorjs/util.js?{VERHASH}"></script>

<!-- Initialization -->
<script src="{STATICURL}js/editorjs/tools/editorjs-drag-drop/editorjs-drag-drop.js?{VERHASH}"></script><!-- editorjs-drag-drop.js -->
<script src="{STATICURL}js/editorjs/tools/editorjs-undo/editorjs-undo.js?{VERHASH}"></script><!-- editorjs-undo.js -->
<script src="{STATICURL}js/editorjs/tools/anchor/anchor.js?{VERHASH}"></script><!-- anchor.js -->
<script src="{STATICURL}js/editorjs/tools/hide/hide.js?{VERHASH}"></script><!-- hide.js -->

<!-- Load Tools -->
<!--{loop $editorblocks $eblock}-->
<script src="$eblock['jspath']?{VERHASH}"></script>
<!--{/loop}-->
<script type="text/javascript">
    let column_available = false;
    EDITOR_TOOLS = Object.assign(EDITOR_TOOLS, {
        tools_anchor: {
            anchorTune: AnchorTune
        },
	tools_hide: {
	    hideTune: HideTune
	}
    });
    main_tools = Object.assign(main_tools, EDITOR_TOOLS.tools_anchor);
    column_tools = Object.assign(column_tools, EDITOR_TOOLS.tools_anchor);
    main_tools = Object.assign(main_tools, EDITOR_TOOLS.tools_hide);
    column_tools = Object.assign(column_tools, EDITOR_TOOLS.tools_hide);
    <!--{loop $editorblocks $eblock}-->
    EDITOR_TOOLS = Object.assign(EDITOR_TOOLS, $eblock['config']);
    <!--{if $eblock['available'] && $eblock['columns']}-->
    column_tools = Object.assign(column_tools, EDITOR_TOOLS.tools_$eblock['identifier']);
    <!--{/if}-->
    <!--{if $eblock['identifier'] == 'columns' && $eblock['available']}-->
    column_available = true;
    <!--{/if}-->
    main_tools = Object.assign(main_tools, EDITOR_TOOLS.tools_$eblock['identifier']);
    if (EDITOR_TOOLS.i18n !== undefined) {
	    i18n_tools = mergeObjects(i18n_tools, EDITOR_TOOLS.i18n);
    }
    <!--{/loop}-->
    // 多列
    if(column_available && Object.keys(column_tools).length !== 0) {
        const tools_columns = {
            columns : {
                class : editorjsColumns,
                config : {
                    EditorJsLibrary : EditorJS, //ref EditorJS - This means only one global thing
                    tools : column_tools,
                }
            },
        }
        main_tools = Object.assign(main_tools, tools_columns);
    }
</script>
<!-- Initialization -->
<script src="{STATICURL}js/editorjs/init_content.js?{VERHASH}"></script>

<!--  Load icon -->
<script src="{STATICURL}js/iconfont.js?{VERHASH}"></script>
<style type="text/css">
    .icon {
        width: 1em; height: 1em;
        vertical-align: -0.15em;
        fill: currentColor;
        overflow: hidden;
    }
</style>