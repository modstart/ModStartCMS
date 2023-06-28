<template>
    <div class="pb-code-editor">
        <div :id="id" style="width:100%;height:100px;">{{ currentData }}</div>
    </div>
</template>

<script>
import {FieldVModel} from "../lib/fields-config";

export default {
    name: "CodeEditor",
    mixins: [FieldVModel],
    props: {
        mode: {
            type: String,
            // 目前支持 json, css
            default: 'json',
        },
        minLines: {
            type: Number,
            default: 5
        },
        maxLines: {
            type: Number,
            default: 100
        }
    },
    data() {
        return {
            id: 'CodeEditor' + Math.random().toString(36).substr(2),
            editor: null,
        }
    },
    watch: {
        mode() {
            this.updateMode()
        }
    },
    mounted() {
        MS.util.loadScript(this.$url.cdn('asset/vendor/ace/ace.js'), () => {
            this.init()
        })
    },
    methods: {
        init() {
            if (!window.ace) {
                setTimeout(() => {
                    this.init()
                }, 100)
                return
            }

            const editor = window.ace.edit(this.id);
            // editor.setTheme("ace/theme/monokai");
            editor.setOptions({
                minLines: this.minLines,
                maxLines: this.maxLines
            })
            editor.session.on('change', () => {
                // console.log('CodeEditor.changed', editor.session.getValue());
                this.currentData = editor.session.getValue();
            })
            this.editor = editor
            this.updateMode()
        },
        updateMode() {
            if (!this.editor) {
                setTimeout(() => {
                    this.updateMode()
                }, 100)
                return
            }
            this.editor.session.setMode("ace/mode/" + this.mode);
        }
    }
}
</script>

<style lang="less" scoped>
.pb-code-editor {
    border: 1px solid var(--color-body-line);
    border-radius: 0.2rem;

    /deep/ .ace_editor {
        border: none;
        border-radius: 0.1rem;
    }
}
</style>
