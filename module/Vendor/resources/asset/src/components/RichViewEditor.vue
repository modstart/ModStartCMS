<template>
    <div class="pb-rich-view-editor" :class="{'active':editing}">
        <div>
            <div ref="editorContainer"></div>
            <div v-if="editing" class="tw-pt-1">
                <a href="javascript:;"
                   @click="doConfirm"
                   class="btn btn-block btn-round btn-sm btn-primary">
                    <i class="iconfont icon-check-simple"></i> 确认
                </a>
            </div>
        </div>
        <div v-if="!editing" @click="doRichViewEdit"
             class="tw-overflow-auto"
             :style="{maxHeight:maxHeight}">
            <div class="ub-html" v-if="modelValue" v-html="modelValue"></div>
            <div class="ub-html tw-text-gray-400" v-else>
                <p>
                    <i class="iconfont icon-edit"></i>
                    点击编辑内容
                </p>
            </div>
        </div>
        <div v-if="richViewEditorShow"
             style="visibility:hidden;height:1px;width:1px;opacity:0;position:absolute;top:0;right:0;"
             id="richViewEditorPlaceholder">
            <script id="richViewEditor" name="richViewEditor" type="text/plain"></script>
        </div>
    </div>
</template>

<script>
import {VModelMixin} from "@ModStartAsset/svue/lib/fields-config";

let DefaultServer = '';
if (window.__msAdminRoot) {
    DefaultServer = window.__msAdminRoot + 'data/ueditor';
}
export default {
    name: "RichViewEditor",
    mixins: [VModelMixin],
    props: {
        server: {
            type: String,
            default: DefaultServer,
        },
        maxHeight: {
            type: String,
            default: '10em',
        }
    },
    data() {
        return {
            richViewEditorShow: false,
            editing: false,
        }
    },
    mounted() {
        this.richViewEditorInit();
    },
    methods: {
        richViewEditorInit() {
            if (window.__richViewEditor) {
                return;
            }
            this.richViewEditorShow = true;
            window.__richViewEditor = true;
            this.$nextTick(() => {
                window.__richViewEditorPlaceholder = document.getElementById('richViewEditorPlaceholder');
                window.__richViewEditor = window.api.editor.basic('richViewEditor', {
                    server: this.server,
                    ready: () => {
                        window.__richViewEditor.setContent(window.__richViewEditorValue || '');
                        window.__richViewEditor.focus(true)
                    }
                })
            });
        },
        doRichViewEdit() {
            if (this.editing) {
                return;
            }
            if (window.__richViewEditorEnd) {
                window.__richViewEditorEnd();
            }
            window.__richViewEditorEnd = () => {
                this.doConfirm()
            };
            this.editing = true;
            window.__richViewEditorValue = this.modelValue;
            $(this.$refs.editorContainer).html(window.__richViewEditor.container.parentNode);
            // this.htmlEditor.unsetFloating()
            window.__richViewEditor.fireEvent("beforefullscreenchange", true)
            window.__richViewEditor.reset()
        },
        doConfirm() {
            this.editing = false;
            this.modelValue = window.__richViewEditor.getContent();
            $(window.__richViewEditorPlaceholder).append(window.__richViewEditor.container.parentNode);
            window.__richViewEditorEnd = null;
        }
    }
}
</script>
<style lang="less">
.pb-rich-view-editor {
    border: 1px dashed #DDD;
    min-height: 1em;
    border-radius: 0.25rem;
    cursor: pointer;
    padding: 0.1rem;

    &:hover, &.active {
        border-color: var(--color-primary);
    }
}
</style>
