<template>
    <div>
        <textarea style="visibility:hidden;" ref="editor"></textarea>
        <DataSelector ref="imageDialog"
                      :url="imageDialogUrl"
                      category="image"
                      :max="999"
                      :child-key="childKey"
                      @on-select="doImageSelect"
        />
    </div>
</template>

<script>
    import {DomUtil, StrUtil} from "../lib/util"
    import $ from 'jquery'
    import DataSelector from "./DataSelector";
    import {PopupManager} from 'element-ui/lib/utils/popup'

    let TinyMCE = null
    let TinyMCEInit = false

    export default {
        name: "TinyMCEEditor",
        model: {
            prop: 'data',
            event: 'TinyMCEEditorEvent'
        },
        props: {
            childKey: {
                type: String,
                default: '_child',
            },
            data: {
                type: String,
                default: ''
            },
            imageDialogUrl: {
                type: String,
                default: 'member_data/select_dialog'
            },
            toolbarItems: {
                type: Array,
                default: () => {
                    return [
                        "code", "undo", "redo", "removeformat", "fullscreen", "visualblocks",
                        "bold", "italic", "underline", "strikethrough", "subscript", "superscript", "forecolor", "backcolor",
                        "formatselect", "bullist", "numlist", "imagedialog", "media",
                        "hr", "link", "unlink", "table",
                        "alignleft", "aligncenter", "alignright", "alignjustify", "alignnone",
                        // "styleselect",
                        // "fontselect", "fontsizeselect",
                        // "newdocument", "cut", "copy", "paste", "outdent", "indent", "blockquote",
                        // "openlink", "visualaid", "insert",
                        // "quickimage",
                        // "charmap", "pastetext", "print", "preview", "anchor", "pagebreak", "spellchecker",
                        // "searchreplace", "visualblocks", "visualchars",
                        // "help", "fullscreen", "insertdatetime",
                        // "nonbreaking", "save", "cancel",
                        // "tabledelete", "tablecellprops", "tablemergecells", "tablesplitcells", "tableinsertrowbefore", "tableinsertrowafter",
                        // "tabledeleterow", "tablerowprops", "tablecutrow", "tablecopyrow", "tablepasterowbefore", "tablepasterowafter", "tableinsertcolbefore", "tableinsertcolafter", "tabledeletecol",
                        // "rotateleft", "rotateright", "flipv", "fliph", "editimage", "imageoptions", "fullpage", "ltr", "rtl", "emoticons", "template",
                        // "restoredraft", "insertfile", "a11ycheck", "toc", "quicktable", "quicklink",
                    ]
                }
            },
            readonly: {
                type: Boolean,
                default: false
            },
            editorOption: {
                type: Object,
                default: () => {
                    return {}
                }
            }
        },
        components: {DataSelector},
        data() {
            return {
                id: '',
                editor: null,
            }
        },
        mounted() {
            DomUtil.loadScript('/static/tinymce/tinymce.min.js').then(() => {
                TinyMCE = window.tinymce
                TinyMCEInit = true
            })

            const me = this
            this.id = 'TinyMCEEditor_' + StrUtil.randomString(10)
            $(this.$refs.editor).attr('id', this.id)
            const init = () => {
                if (!TinyMCEInit) {
                    setTimeout(init, 100)
                    return
                }
                TinyMCE.PluginManager.add('imagedialog', function (editor, url) {
                    editor.ui.registry.addButton('imagedialog', {
                        text: '<i class="iconfont icon-images" style="font-size:large;"></i>',
                        onAction: function () {
                            me.$refs.imageDialog.show()
                        }
                    });
                    return {};
                });
                TinyMCE.init($.extend({
                    selector: '#' + this.id,
                    branding: false,
                    menubar: false,
                    language: 'zh_CN',
                    image_caption: false,
                    image_description: false,
                    media_alt_source: false,
                    readonly: this.readonly,
                    video_template_callback: function (data) {
                        return `<iframe src="${data.source1}" width="${data.width}" height="${data.height}"></iframe>`
                    },
                    audio_template_callback: function (data) {
                        return '<audio controls>' + '\n<source src="' + data.source1 + '"' + (data.source1mime ? ' type="' + data.source1mime + '"' : '') + ' />\n' + '</audio>';
                    },
                    plugins: [
                        "hr", "lists", "link", "image", "visualblocks", "code", "fullscreen", "media", "table", "textcolor", "imagedialog",
                    ],
                    toolbar: me.toolbarItems.join(' '),
                    setup: function (editor) {
                        me.editor = editor
                        editor.on('change', function (e) {
                            me.$emit('TinyMCEEditorEvent', editor.getContent())
                        });
                        editor.on('init', function (e) {
                            editor.setContent(me.data)
                            // 统一使用 Element UI 的 ZIndex 管理器
                            $(editor.editorContainer).on('click', '.tox-toolbar button', e => {
                                $('.tox-tinymce-aux').css({zIndex: PopupManager.nextZIndex()});
                            })
                        })
                    }
                }, this.editorOption))
            }
            init()
        },
        methods: {
            doImageSelect(files) {
                files.forEach(o => {
                    this.editor.insertContent(`<p><img src="${o.path}" /></p>`)
                })
            },
            setContentSafe(content) {
                if (!this.editor) {
                    setTimeout(() => this.setContentSafe(content), 10)
                    return
                }
                if (content !== this.editor.getContent()) {
                    this.editor.setContent(content)
                }
            }
        },
        watch: {
            data: {
                handler(newValue, oldValue) {
                    this.setContentSafe(newValue)
                },
                immediate: true,
            },
            readonly(newValue, oldValue) {
                this.editor.setMode(newValue ? 'readonly' : 'design')
            }
        }
    }
</script>
