<template>
    <div>
        <script ref="editor" :id="id" :name="id" type="text/plain"></script>
    </div>
</template>

<script>
import {FieldInputMixin, FieldVModel} from "@ModStartAsset/svue/lib/fields-config";
import {StrUtil} from "@ModStartAsset/svue/lib/util"

export default {
    name: "RichEditor",
    mixins: [FieldInputMixin, FieldVModel],
    props: {
        editorOption: {
            type: Object,
            default: () => {
                return {}
            },
        },
    },
    data() {
        return {
            id: null,
            editor: null,
            editorReady: false,
            ignoreChangedContent: null,
        }
    },
    watch: {
        data: {
            handler(n, o) {
                if (this.ignoreChangedContent && this.ignoreChangedContent === n) {
                    this.ignoreChangedContent = null
                    return
                }
                this.setContent(n)
            },
            immediate: true,
        },
    },
    mounted() {
        this.id = StrUtil.randomString()
        $(this.$refs.editor).attr('id', this.id)
        this.$nextTick(() => {
            this.editor = MS.editor.basic(this.id, {
                ready: () => {
                    this.editorReady = true;
                    // console.log('editor.ready', this.editor)
                    //TODO 该段代码会导致插入图片时不能插入正确的位置
                    // $(this.editor.container).click((e) => {
                    //    e.stopPropagation()
                    //    this.editor.setContent(this.currentData)
                    //})
                },
            }, Object.assign({
                zIndex: 10000,
            }, this.editorOption))
            this.editor.on('contentchange', () => {
                const content = this.editor.getContent()
                console.log('RichEditor.contentchange',content);
                this.currentData = content
                this.ignoreChangedContent = content
            })
        })
    },
    beforeDestroy() {
        if (this.editor) {
            this.editor.destroy()
            this.editor = null
        }
    },
    methods: {
        setContent(content) {
            if (!this.editorReady) {
                setTimeout(() => {
                    this.setContent(content)
                }, 100)
                return
            }
            // console.log('editor.setContent', content)
            this.editor.setContent(content)
        }
    }
}
</script>
