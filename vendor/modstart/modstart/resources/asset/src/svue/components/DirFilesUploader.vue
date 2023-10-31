<template>
    <div class="pb-dir-files-uploader">
        <label for="selectDirectory"
               class="hover:tw-text-blue-600 tw-cursor-pointer tw-h-6 tw-inline-block tw-relative tw-text-black tw-w-24">
            <input ref="file" type="file" class="tw-opacity-0" webkitdirectory/>
            <div class="tw-absolute tw-inset-0 tw-leading-6 tw-pointer-events-none">
                <i class="iconfont icon-folder"></i>
                选择文件夹
            </div>
        </label>
        <el-dialog :visible.sync="previewShow" append-to-body>
            <div slot="title">
                文件确认
            </div>
            <div slot="footer">
                <el-button type="primary" @click="doSubmit">
                    确认
                </el-button>
            </div>
            <div class="">
                <div class="tw-font-bold tw-pb-3">
                    <span class="tw-pr-3">
                        共 {{ files.length }} 个文件，选中 {{ files.filter(f => f._checked).length }} 个
                    </span>
                    <span class="tw-pr-3">
                        <el-checkbox type="primary"
                                     :indeterminate="files.filter(f => f._checked).length>0 && files.filter(f => f._checked).length < files.length"
                                     :value="files.filter(f => f._checked).length === files.length"
                                     @change="selectAll">全选
                        </el-checkbox>
                    </span>
                </div>
                <div style="max-height:calc(100vh - 320px);"
                     class="tw-bg-gray-100 tw-overflow-auto tw-p-3 tw-rounded ub-border">
                    <div v-for="(f,fIndex) in files">
                        <el-checkbox v-model="f._checked">
                            {{ f.name }}
                        </el-checkbox>
                    </div>
                </div>
            </div>
        </el-dialog>
    </div>
</template>

<script>
let globalIndex = 0;
export default {
    name: "DirFilesUploader",
    props: {
        checkFilter: {
            type: Function,
            default: (name, ext, param) => true
        },
    },
    data() {
        return {
            id: 'dirFilesUploader' + (globalIndex++),
            previewShow: false,
            files: [],
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.init()
        })
    },
    methods: {
        init() {
            this.$refs.file.addEventListener('change', e => {
                const files = e.target.files
                const uploadFiles = Array.from(files).map(f => {
                    const pcs = f.name.split('.')
                    let ext = null
                    if (pcs.length > 1) {
                        ext = pcs.pop()
                    }
                    return {
                        _checked: this.checkFilter(f.name, ext, {
                            file: f
                        }),
                        file: f,
                        ext: ext ? ext.toLowerCase() : null,
                        name: f.name,
                        nameBare: pcs.join('.'),
                    }
                })
                if (!uploadFiles.length) {
                    this.$dialog.tipError('没有选择文件')
                    return
                }
                this.files = uploadFiles
                this.previewShow = true
            });
        },
        selectAll(value) {
            this.files.forEach(f => {
                f._checked = value
            })
        },
        doSubmit() {
            this.$emit('submit', this.files.filter(f => f._checked))
            this.previewShow = false
        }
    }
}
</script>

<style lang="less" scoped>
.pb-dir-files-uploader {
    display: inline-block;
}
</style>
