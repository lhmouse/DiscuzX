// json editor undo config
const config = {
    shortcuts: {
        undo: 'CMD+Z',
        redo: 'CMD+SHIFT+Z'
    }
};

/**
 * To initialize the Editor, create a new instance with configuration object
 * @see docs/installation.md for mode details
 */
var editor = new EditorJS({
    /**
     * Enable/Disable the read only mode
     */
    readOnly: false,
    /** 启用自动对焦 */
    autofocus : true,

    /**
     * Wrapper of Editor
     */
    holder: 'editorjs',

    /**
     * Common Inline Toolbar settings
     * - if true (or not specified), the order from 'tool' property will be used
     * - if an array of tool names, this order will be used
     */
    // inlineToolbar: ['link', 'marker', 'bold', 'italic', 'list'],
    // inlineToolbar: true,

    /**
     * Tools list
     */
    tools: main_tools,
    /*
    toolbar: {
        // 这里设置需要显示的按钮
        buttons: ['header', 'bold', 'italic', 'underline', 'strike', 'code', 'link', 'image']
    },
     */
    i18n: {
        messages: {
            tools: {
                image: {
                    'Upload an image': '上传图片',
                },
                attaches: {
                    'Select file to upload': '请选择一个要上传的视频/音频/文件',
                },
                hyperlink: {
                    'Save': '保存',
                    'Select target': '选择链接方式',
                    'Select rel': '选择Rel'
                },
                list: {
                    "Ordered": "有序列表",
                    "Unordered": "无序列表"
                },
                anchorTune: {
                    'Anchor': '锚点ID'
                }
            },
            toolNames: {
                "Text": "文本段落",
                "Heading": "标题",
                "ImageTool": "图片",
                "Image": "图片",
                "Video": "视频",
                "Audio": "音频",
                "Attach": "文件",
                "Caption": "描述",
                "NestedList": "多级列表",
                "List": "列表",
                "Warning": "警示信息",
                "Checklist": "多选列表",
                "Quote": "引言",
                "Code": "代码",
                "raw": "原始HTML",
                "Delimiter": "分隔符",
                "Table": "表格",
                "Link": "链接",
                "Marker": "标记",
                "Bold": "粗体",
                "Italic": "斜体",
                "InlineCode": "内联码",
                "Download Markdown": "导出Markdown",
                "Import Markdown": "导入Markdown",
                "Alert": "提示",
                "Columns": "多列",
                "Attachment": "视频/音频/文件",
                "Hyperlink": "超链接",
                'Anchor': '锚点ID',
                'Embed': '多媒体资源嵌入',
            },

            /**
             * Section allows to translate Block Tunes
             */
            blockTunes: {
                /**
                 * Each subsection is the i18n dictionary that will be passed to the corresponded Block Tune plugin
                 * The name of a plugin should be equal the name you specify in the 'tunes' section for that plugin
                 *
                 * Also, there are few internal block tunes: "delete", "moveUp" and "moveDown"
                 */
                "delete": {
                    "Delete": "删除",
                    "Click to delete": "确认删除？",
                },
                "moveUp": {
                    "Move up": "向上移动"
                },
                "moveDown": {
                    "Move down": "向下移动"
                },
                "anchorTune": {
                    'Anchor': '锚点ID'
                },
                "image": {
                    'With border': '带边框',
                    'Stretch image': '拉伸图像',
                    'With background': '带背景色'
                }
            },
        }
    },

    /**
     * This Tool will be used as default
     */
    // defaultBlock: 'paragraph',

    /**
     * Initial Editor data
     */
    data: content,
    onReady: function(){
        console.log("Delaying Save to launch Column Editors")

        new Undo({ editor, config });
        new DragDrop(editor);

        setTimeout(() => {
            //saveButton.click();
        },2000)

    },
    onChange: function(e) {
        console.log(e)
        // console.log('something changed');
    }
});

/**
 * Saving button
 */
const saveButton = document.getElementById('saveButton');
/**
 * Submit button
 */
const submitButton = document.getElementById('submitButton');


/**
 * Saving
 */
saveButton.addEventListener('click', function () {
    var postsave = document.getElementById("postsave");
    postsave.value = 1;
    saveContent();
});

/**
 * Submit
 */
submitButton.addEventListener('click', function () {
    saveContent();
});

function saveContent() {
    editor.save()
        .then((savedData) => {
            console.log(JSON.stringify(savedData, null, 4));
            var postform = document.getElementById("postform");
            var subject = document.getElementById("subject");
            if(subject.value == '' || subject.value == undefined) {
                editor.notifier.show({
                    message: '请输入标题',
                    style: 'error',
                    // time: 30
                });
                event.stopPropagation();
                return false;
            }
            var seccodeverify = document.getElementsByName("seccodeverify");
            if(seccodeverify[0] != undefined) {
                if(seccodeverify[0].value == '' || seccodeverify[0].value == undefined) {
                    editor.notifier.show({
                        message: '请输入验证码',
                        style: 'error',
                        // time: 30
                    });
                    event.stopPropagation();
                    return false;
                }
            }
            var content = document.getElementById("content");
            if(savedData.blocks == '' || savedData.blocks == undefined) {
                editor.notifier.show({
                    message: '请输入正文内容',
                    style: 'error',
                    // time: 30
                });
                event.stopPropagation();
                return false;
            }
            content.value = JSON.stringify(savedData);

            var mobileeditor = document.getElementById("mobileeditor");
            if(mobileeditor.value != 1 && mobileeditor.value != '1') {
                postform.onsubmit();
            }

        })
        .catch((error) => {
            console.error('保存失败', error);
        });
}

function succeedhandle_postform(url, msg, param) {
    editor.notifier.show({
        message: '发布成功',
        style: 'success'
    });
    window.location.href = url;
}