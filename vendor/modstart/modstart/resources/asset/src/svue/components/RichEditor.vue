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
    },
    data() {
        return {
            id: null,
            editor: null,
        }
    },
    mounted() {
        this.id = 'RichEditor' + StrUtil.randomString(10)
        this.htmlEditorInit()
    },
    beforeDestroy() {
        if (this.htmlEditor) {
            this.htmlEditor.destroy()
            this.htmlEditor = null
        }
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
            this.htmlEditor = editorCallback(this.id, {
                server: this.server,
                ready: () => {
                    // console.log('htmlEditor.ready', this.htmlEditor)
                    if (this.data) {
                        this.htmlEditor.setContent(this.data)
                    }
                }
            })
            this.htmlEditor.addListener('contentChange', () => {
                // console.log('htmlEditor.contentChange', this.htmlEditor.getContent())
                this.$emit('input', this.htmlEditor.getContent())
            })
        },
    }
}
</script>
<style lang="less">
.pb-rich-editor {

}
</style>
