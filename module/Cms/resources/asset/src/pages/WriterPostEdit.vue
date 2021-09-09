<template>
    <div class="ub-panel">
        <div class="head">
            <div class="title">编辑文章</div>
        </div>
        <div class="body" v-loading="loading">
            <div class="tw-p-1">
                <div class="row">
                    <div class="col-md-3">
                        <MemberPostCategorySelector v-model="data.categoryId"></MemberPostCategorySelector>
                    </div>
                    <div class="col-md-9">
                        <el-input v-model="data.title" placeholder="标题"></el-input>
                    </div>
                </div>
            </div>
            <div class="tw-p-1">
                <div class="pb-content-editor">
                    <div class="item" :class="{show:data.contentType===CmsEditorType.RICH_TEXT.value}">
                        <script id="htmlEditor" name="htmlEditor" type="text/plain"></script>
                    </div>
                    <div class="item" :class="{show:data.contentType===CmsEditorType.MARKDOWN.value}">
                        <textarea name="markdownEditor" id="markdownEditor"></textarea>
                    </div>
                </div>
            </div>
            <div class="tw-p-1">
                标签
                <el-input v-model="data.tags"></el-input>
                <div class="ub-text-muted">多个标签使用逗号分隔</div>
            </div>
            <div class="tw-p-1">
                原创
                <el-switch v-model="data.isOriginal"></el-switch>
            </div>
            <div class="tw-p-1">
                <a href="javascript:;" class="btn btn-primary" @click="doSubmit">保存</a>
            </div>
        </div>
    </div>
</template>

<script>
    import MemberPostCategorySelector from "../components/MemberPostCategorySelector";
    import {CmsEditorType} from "../lib/constant";
    import {BeanUtil} from "@ModStartAsset/svue/lib/util";

    export default {
        name: "WriterPostEdit",
        components: {MemberPostCategorySelector},
        data() {
            return {
                CmsEditorType,
                loading: true,
                setting: {
                    cmsEditorType: 0,
                },
                data: {
                    id: window.__data.id,
                    categoryId: 0,
                    title: '',
                    contentType: 0,
                    content: '',
                    isOriginal: false,
                    tags: '',
                },
                htmlEditor: null,
                markdownEditor: null,
            }
        },
        mounted() {
            this.htmlEditor = window.api.editor.basic('htmlEditor')
            this.markdownEditor = window.api.editorMarkdown.basic('markdownEditor')
            // window.htmlEditor = this.htmlEditor
            // window.markdownEditor = this.markdownEditor
            this.doLoadSetting(() => {
                this.doLoad()
            })
        },
        methods: {
            doLoadSetting(cb) {
                this.$api.post(this.$url.api('writer/setting/get'), {id: this.data.id}, res => {
                    BeanUtil.update(this.setting, res.data)
                    cb()
                })
            },
            doLoad() {
                this.loading = true
                if (this.data.id) {
                    this.$api.post(this.$url.api('writer/post/get'), {id: this.data.id}, res => {
                        BeanUtil.update(this.data, res.data.memberPost)
                        this.loading = false
                        switch (this.data.contentType) {
                            case CmsEditorType.RICH_TEXT.value:
                                this.htmlEditor.setContent(this.data.content)
                                break
                            case CmsEditorType.MARKDOWN.value:
                                this.markdownEditor.value(this.data.content)
                                break
                            default:
                                this.$dialog.tipError('错误的文章类型')
                                break
                        }
                    }, res => {
                        this.loading = false
                    })
                } else {
                    this.data.contentType = this.setting.cmsEditorType
                    this.loading = false
                }
            },
            doSubmit() {
                let data = BeanUtil.clone(this.data)
                switch (data.contentType) {
                    case CmsEditorType.RICH_TEXT.value:
                        data.content = this.htmlEditor.getContent()
                        break
                    case CmsEditorType.MARKDOWN.value:
                        data.content = this.markdownEditor.value()
                        break
                    default:
                        this.$dialog.tipError('错误的文章类型')
                        return
                }
                this.$dialog.loadingOn()
                this.$api.post(this.$url.api('writer/post/edit'), data, res => {
                    this.$dialog.loadingOff()
                    this.$dialog.tipSuccess('保存成功')
                    setTimeout(() => {
                        window.location.href = this.$url.web('writer/post')
                    }, 3000)
                }, res => {
                    this.$dialog.loadingOff()
                })
            }
        }
    }
</script>

<style lang="less">
    .pb-content-editor {
        .item {
            visibility: hidden;
            height: 0;

            &.show {
                visibility: visible;
                height: auto;
            }
        }
    }
</style>
