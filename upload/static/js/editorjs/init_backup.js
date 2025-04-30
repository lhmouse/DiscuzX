// json editor undo config
const config = {
    shortcuts: {
        undo: 'CMD+Z',
        redo: 'CMD+SHIFT+Z'
    }
};
// EDITOR_TOOLS
const EDITOR_TOOLS = {
    // 标题
    tools_header: {
        header: {
            class: Header,
            config: {
                placeholder: '请输入标题...',
                levels: [1, 2, 3, 4, 5, 6],
                defaultLevel: 3,
                defaultAlignment: 'left'
            }
        }
    },
    // 弹窗提示
    tools_alert: {
        alert: {
            class: Alert,
            inlineToolbar: true,
            shortcut: 'CMD+SHIFT+A',
            config: {
                defaultType: 'primary',
                messagePlaceholder: '请输入内容...',
            },
        },
    },
    // 段落
    tools_paragraph: {
        paragraph: {
            class: Paragraph,
            inlineToolbar: true,
            config: {
                placeholder: "请输入正文内容..."
            }
        },
    },
    // 分隔线
    tools_delimiter: {
        delimiter: Delimiter,
    },
    // 图片
    tools_image: {
        image: {
            class: ImageTool,
            config: {
                endpoints: {
                    byFile: 'misc.php?mod=swfupload&action=swfupload&operation=jsoneditorupload&fid='+editor_fid, // Your backend file uploader endpoint
                    byUrl: 'misc.php?mod=swfupload&action=swfupload&operation=jsoneditorupload&fid='+editor_fid, // Your endpoint that provides uploading by Url
                },
                field: 'Filedata',
                additionalRequestData: {
                    'uid': editor_uid,
                    'hash': editor_hash,
                }
            }
        },
    },
    // ImageGallery
    tools_imageGallery: {
        imageGallery: ImageGallery,
    },
    // 有序列表
    tools_list: {
        list: {
            class: List,
            inlineToolbar: true,
            shortcut: 'CMD+SHIFT+L'
        },
    },
    // 无序列表
    tools_nestedlist: {
        nestedlist: {
            class: NestedList,
            inlineToolbar: true,
            shortcut: 'CMD+SHIFT+I'
        },
    },
    // 多选
    tools_checklist: {
        checklist: {
            class: Checklist,
            inlineToolbar: true,
        },
    },
    // 引用
    tools_quote: {
        quote: {
            class: Quote,
            inlineToolbar: true,
            config: {
                quotePlaceholder: 'Enter a quote',
                captionPlaceholder: 'Quote\'s author',
            },
            shortcut: 'CMD+SHIFT+O'
        },
    },
    // 警示
    tools_warning: {
        warning: Warning,
    },
    // 标记
    tools_marker: {
        marker: {
            class:  Marker,
            shortcut: 'CMD+SHIFT+M'
        },
    },
    // 代码
    tools_code: {
        code: {
            class:  CodeTool,
            shortcut: 'CMD+SHIFT+C'
        },
    },
    // 标准HTML
    tools_raw: {
        raw: RawTool,
    },
    // 内链码
    tools_inlineCode: {
        inlineCode: {
            class: InlineCode,
            shortcut: 'CMD+SHIFT+C'
        },
    },
    // 链接
    tools_linkTool: {
        linkTool: LinkTool,
    },
    // 嵌入
    tools_embed: {
        embed: {
            class: Embed,
            config: {
                services: {
                    youtube: true,
                    coub: true
                }
            }
        },
    },
    // 表格
    tools_table: {
        table: {
            class: Table,
            inlineToolbar: true,
            shortcut: 'CMD+ALT+T'
        },
    },
    // markdown 导入导出
    tools_markdown: {
        markdownParser: MDParser,
        markdownImporter: MDImporter,
    },
    // TextVariantTune
    tools_tvt: {
        tvt: TextVariantTune
    },
};
// first define the tools to be made avaliable in the columns
let column_tools = {};
column_tools = Object.assign(column_tools, EDITOR_TOOLS.tools_header);
column_tools = Object.assign(column_tools, EDITOR_TOOLS.tools_alert);
column_tools = Object.assign(column_tools, EDITOR_TOOLS.tools_paragraph);
column_tools = Object.assign(column_tools, EDITOR_TOOLS.tools_delimiter);
column_tools = Object.assign(column_tools, EDITOR_TOOLS.tools_image);
column_tools = Object.assign(column_tools, EDITOR_TOOLS.tools_table);

// next define the tools in the main block
// Warning - Dont just use main_tools - you will probably generate a circular reference
let main_tools = {};
main_tools = Object.assign(main_tools, EDITOR_TOOLS.tools_header);
main_tools = Object.assign(main_tools, EDITOR_TOOLS.tools_alert);
main_tools = Object.assign(main_tools, EDITOR_TOOLS.tools_paragraph);
main_tools = Object.assign(main_tools, EDITOR_TOOLS.tools_table);
main_tools = Object.assign(main_tools, EDITOR_TOOLS.tools_delimiter);
main_tools = Object.assign(main_tools, EDITOR_TOOLS.tools_image);
// main_tools = Object.assign(main_tools, EDITOR_TOOLS.tools_imageGallery);
main_tools = Object.assign(main_tools, EDITOR_TOOLS.tools_list);
main_tools = Object.assign(main_tools, EDITOR_TOOLS.tools_nestedlist);
main_tools = Object.assign(main_tools, EDITOR_TOOLS.tools_checklist);
main_tools = Object.assign(main_tools, EDITOR_TOOLS.tools_quote);
main_tools = Object.assign(main_tools, EDITOR_TOOLS.tools_warning);
main_tools = Object.assign(main_tools, EDITOR_TOOLS.tools_marker);
main_tools = Object.assign(main_tools, EDITOR_TOOLS.tools_code);
main_tools = Object.assign(main_tools, EDITOR_TOOLS.tools_raw);
main_tools = Object.assign(main_tools, EDITOR_TOOLS.tools_inlineCode);
main_tools = Object.assign(main_tools, EDITOR_TOOLS.tools_linkTool);
main_tools = Object.assign(main_tools, EDITOR_TOOLS.tools_embed);
main_tools = Object.assign(main_tools, EDITOR_TOOLS.tools_markdown);
main_tools = Object.assign(main_tools, EDITOR_TOOLS.tools_tvt);
// 多列
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
                }
            },
            toolNames: {
                "Text": "文本段落",
                "Heading": "标题",
                "ImageTool": "图片",
                "NestedList": "无序列表",
                "List": "有序列表",
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
                    "Delete": "删除"
                },
                "moveUp": {
                    "Move up": "向上移动"
                },
                "moveDown": {
                    "Move down": "向下移动"
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
    // data: {},
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
 * Saving
 */
saveButton.addEventListener('click', function () {
    editor.save()
        .then((savedData) => {
            console.log(savedData);
            var postform = document.getElementById("postform");
            var subject = document.getElementById("subject");
            if(subject.value == '' || subject.value == undefined) {
                editor.notifier.show({
                    message: '请输入标题',
                    style: 'error',
                    // time: 30
                });
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
                    return false;
                }
            }
            var content = document.getElementById("content");
            content.value = JSON.stringify(savedData);

            postform.onsubmit();
        })
        .catch((error) => {
            console.error('Saving error', error);
        });
});

function succeedhandle_postform(url, msg, param) {
    editor.notifier.show({
        message: '发布成功',
        style: 'success'
    });
    window.location.href = url;
}