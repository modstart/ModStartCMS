<template>
    <div class="pb-rich-editor">
        <script :id="id" :name="id" type="text/plain"></script>
    </div>
</template>

<script>
import {StrUtil} from './../lib/util';

export default {
    name: 'RichEditor',
    props: {
        data: {
            type: String,
            default: ''
        },
        type: {
            type: String,
            default: 'basic',
        },
        server: {
            type: String,
            default: '/member_data/ueditor'
        },
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
    mounted() {
        this.id = 'RichEditor' + StrUtil.randomString(10)
        this.htmlEditorInit()
    },
    beforeDestroy() {
        if (this.editor) {
            this.editor.destroy()
            this.editor = null
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
    methods: {
        htmlEditorInit() {
            if (!MS || !MS.editor) {
                alert('UEditor插件未加载')
                return
            }
            let editorCallback = MS.editor.basic
            switch (this.type) {
                case 'simple':
                    editorCallback = MS.editor.simple
                    break
            }
            this.editor = editorCallback(this.id, {
                server: this.server,
                ready: () => {
                    this.editorReady = true
                }
            }, Object.assign({
                zIndex: 10000,
            }, this.editorOption))
            this.setContent(this.data)
            this.editor.addListener('contentChange', () => {
                // console.log('editor.contentChange', this.editor.getContent())
                const content = this.editor.getContent()
                this.ignoreChangedContent = content
                this.$emit('input', content)
            })
        },
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
<style lang="less">
.pb-rich-editor {

}
</style>
