<template>
    <div>
        <script ref="editor" :id="id" :name="id" type="text/plain"></script>
    </div>
</template>

<script>
    import {FieldInputMixin, FieldVModel} from "@ModStartAsset/svue/lib/fields-config";
    import {StrUtil} from "@ModStartAsset//svue/lib/util"

    export default {
        name: "RichEditor",
        mixins: [FieldInputMixin, FieldVModel],
        data() {
            return {
                id: null,
                editor: null,
                ignoreChange: false,
            }
        },
        watch: {
            data: {
                handler(n, o) {
                    // console.log('data.change', this.ignoreChange, n)
                    if (this.ignoreChange) {
                        this.ignoreChange = false
                        return
                    }
                    this.setContent(n)
                    this.currentData = n
                },
                immediate: true,
            },
        },
        mounted() {
            this.id = StrUtil.randomString()
            $(this.$refs.editor).attr('id', this.id)
            this.$nextTick(() => {
                this.editor = window.api.editor.basic(this.id, {
                    ready: () => {
                        // console.log('editor.ready', this.editor)
                        //TODO 该段代码会导致插入图片时不能插入正确的位置
                        // $(this.editor.container).click((e) => {
                        //    e.stopPropagation()
                        //    this.editor.setContent(this.currentData)
                        //})
                    },
                },{
                    zIndex: 10000,
                })
                this.editor.on('contentchange', () => {
                    this.currentData = this.editor.getContent()
                    this.ignoreChange = true
                })
            })
        },
        methods: {
            setContent(content) {
                if (!this.editor || !this.editor.body) {
                    setTimeout(() => {
                        this.setContent(content)
                    }, 100)
                    return
                }
                // console.log('editor.setContent', content)
                setTimeout(() => {
                    this.editor.setContent(content)
                }, 100)
            }
        }
    }
</script>

<style scoped>

</style>
