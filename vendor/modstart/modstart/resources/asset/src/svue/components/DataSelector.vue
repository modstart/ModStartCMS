<template>
    <div class="pb-data-selector" v-if="permission['View']">
        <el-dialog :visible="visible || 'flat'===mode" :custom-class="'pb-data-selector-mode-'+mode"
                   width="60%" @close="()=>{this.visible=false}"
                   append-to-body>
            <div slot="title">
                <span>{{ L('Please Select') }}</span>
            </div>
            <div slot="footer">
                <el-button type="primary" @click="doSubmit">
                    <i class="iconfont icon-check-simple"></i>
                    {{ L('Confirm') }}
                </el-button>
            </div>
            <div style="border-bottom:1px solid #EEE;padding-bottom:7px;">
                <div class="pb-data-upload-button" v-if="permission['Upload']">
                    <UploadButton :url="url" :category="category" styles="queue" @success="onUploadButtonSuccess"/>
                </div>
                <el-button v-if="smallWindow" @click="menuShow=!menuShow">
                    <i class="iconfont icon-list"></i>
                </el-button>
                <el-button :type="tab==='list'?'primary':''" @click="tab='list'">
                    <i class="iconfont icon-category"></i> {{ L('File Gallery') }}
                </el-button>
                <el-button :type="tab==='input'?'primary':''" @click="tab='input'">
                    <i class="iconfont icon-edit"></i> {{ L('Custom Link') }}
                </el-button>
                <span class="tw-ml-4" v-if="max>1 && tab==='list' && listChecked.length>1">
                    <el-checkbox v-model="reverseSelectOrder">{{ L('Reverse Select Order') }}</el-checkbox>
                </span>
            </div>
            <div class="pb-data-selector-gallery" :class="{'small-window':smallWindow,'menu-show':menuShow}"
                 v-show="tab==='list'">
                <div class="pb-data-selector-category" v-loading="categoryLoading">
                    <div class="action">
                        <a v-if="permission['Add/Edit']"
                           href="javascript:;" :title="L('Add Category')"
                           :class="{active:currentCategoryId>=0}"
                           @click="doCategoryAdd">
                            <i class="iconfont icon-plus"></i>
                        </a>
                        <a v-if="permission['Add/Edit']"
                           href="javascript:;" :title="L('Edit Category')"
                           :class="{active:currentCategoryId>0}"
                           @click="doCategoryEdit">
                            <i class="iconfont icon-sign"></i>
                        </a>
                        <a v-if="permission['Delete']"
                           href="javascript:;" :title="L('Delete Category')"
                           :class="{active:currentCategoryId>0}"
                           @click="doCategoryDelete">
                            <i class="iconfont icon-trash"></i>
                        </a>
                    </div>
                    <div class="data">
                        <el-input prefix-icon="el-icon-search" :placeholder="L('Filter')"
                                  v-model="categoryFilter">
                        </el-input>
                        <el-tree ref="$categoryTreeAll"
                                 node-key="id"
                                 :data="categoryTreeAll" :props="{children: childKey,label: 'name'}"
                                 :highlight-current="true" :default-expand-all="true"
                                 :filter-node-method="categoryFilterCallback"
                                 @node-click="doCategorySelect"></el-tree>
                    </div>
                    <div class="mask" @click="menuShow=false"></div>
                </div>
                <div class="pb-data-selector-list" v-loading="categoryLoading">
                    <div class="action">
                        <a v-if="permission['Delete']"
                           href="javascript:;" :title="L('Delete')"
                           :class="{active:activeFileOperate}"
                           @click="doFileDelete">
                            <i class="iconfont icon-trash"></i>
                            {{ L('Delete') }}
                        </a>
                        <a v-if="permission['Add/Edit']"
                           href="javascript:;" :title="L('Edit')"
                           :class="{active:activeFileOperate}"
                           @click="doFileEdit">
                            <i class="iconfont icon-sign"></i>
                            {{ L('Edit') }}
                        </a>
                        <a href="javascript:;" :title="L('Copy Link')"
                           :class="{active:listChecked.length>0}"
                           v-clipboard:copy='listChecked.map(o=>o.fullPath).join("\n")'
                           v-clipboard:success="$onCopySuccess">
                            <i class="iconfont icon-copy"></i>
                            {{ L('Copy Link') }}
                        </a>
                    </div>
                    <div class="records" style="min-height:5rem;" v-loading="listLoading">
                        <div class="ub-empty" v-if="!listLoading && records.length===0">
                            {{ L('No Records') }}
                        </div>
                        <el-row :gutter="10">
                            <el-col :xs="12" :sm="6" :md="4" v-for="(listItem,listIndex) in records" :key="listIndex">
                                <div class="item" :class="{active:listItem.checked}">
                                    <div @click="doClickItem(listIndex)">
                                        <ImageCover v-if="isImage(listItem)"
                                                    :src="listItem.fullPath"></ImageCover>
                                        <div class="file-cover" v-else>
                                            <div class="text">
                                                <div class="label">
                                                    {{ listItem.type.toUpperCase() }}{{ L('File(s)') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="title">{{ listItem.filename }}</div>
                                    <div class="action">
                                        <a :href="listItem.fullPath"
                                           class="tw-mx-2"
                                           target="_blank" rel="noreferrer">
                                            <i class="iconfont icon-link-alt"></i>
                                        </a>
                                        <a href="javascript:;" class="tw-mx-2"
                                           :title="L('Copy Link')"
                                           v-clipboard:copy="listItem.fullPath"
                                           v-clipboard:success="$onCopySuccess">
                                            <i class="iconfont icon-copy"></i>
                                        </a>
                                    </div>
                                    <i class="check iconfont icon-checked"></i>
                                </div>
                            </el-col>
                        </el-row>
                    </div>
                    <div class="page" v-if="!listLoading && records.length>0">
                        <el-pagination
                            @current-change="doList"
                            :current-page.sync="page"
                            :page-size="pageSize"
                            layout="total,prev,pager,next"
                            :total="total">
                        </el-pagination>
                    </div>
                </div>
            </div>
            <div v-if="tab==='input'" class="tw-pt-4">
                <el-form label-width="80px">
                    <el-form-item :label="L('Url')">
                        <el-input v-model="input.path" style="width:80%;"></el-input>
                    </el-form-item>
                </el-form>
            </div>
        </el-dialog>
        <el-dialog
            :title="categoryEdit.id>0?L('Edit Category'):L('Add Category')"
            :visible.sync="categoryVisible"
            append-to-body
            width="30%">
            <el-form label-width="80px">
                <el-form-item :label="L('Parent')">
                    <el-select v-model="categoryEdit.pid" :placeholder="L('Please Select')">
                        <el-option
                            v-for="item in categoryListParent"
                            :key="item.id"
                            :label="item.title"
                            :value="item.id">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item :label="L('Name')">
                    <el-input v-model="categoryEdit.title" style="width:80%;"></el-input>
                </el-form-item>
            </el-form>
            <span slot="footer" class="dialog-footer">
                <el-button :loading="categoryEditLoading"
                           size="mini" type="primary"
                           @click="doCategoryEditSubmit">
                    {{ L('Confirm') }}
                </el-button>
              </span>
        </el-dialog>
        <el-dialog
            :title="L('Edit File')"
            :visible.sync="fileVisible"
            append-to-body
            width="30%">
            <el-form label-width="80px">
                <el-form-item :label="L('Category')">
                    <el-select v-model="fileEdit.categoryId" :placeholder="L('Please Select')">
                        <el-option
                            v-for="item in categoryListAll"
                            :key="item.id"
                            :label="item.title"
                            :value="item.id">
                        </el-option>
                    </el-select>
                </el-form-item>
            </el-form>
            <span slot="footer" class="dialog-footer">
                <el-button :loading="fileEditLoading"
                           size="mini" type="primary"
                           @click="doFileEditSubmit">{{ L('Confirm') }}</el-button>
              </span>
        </el-dialog>
    </div>
</template>

<script>
import UploadButton from "./UploadButton";
import ImageCover from "./DataSelector/ImageCover"
import {Dialog} from "../lib/dialog";
import {JsonUtil} from "../lib/util";

export default {
    name: "DataSelector",
    components: {UploadButton, ImageCover},
    props: {
        mode: {
            type: String,
            default: 'dialog'
        },
        url: {
            type: String,
            default: ''
        },
        category: {
            type: String,
            default: ''
        },
        min: {
            type: Number,
            default: 1,
        },
        max: {
            type: Number,
            default: 1,
        },
        childKey: {
            type: String,
            default: '_child',
        },
        permission: {
            type: Object,
            default: () => {
                return {
                    'View': true,
                    'Upload': true,
                    'Delete': true,
                    'Add/Edit': true
                }
            }
        }
    },
    data() {
        return {
            visible: false,
            tab: 'list',
            currentCategoryId: 0,
            fileVisible: false,
            smallWindow: $(window).width() < 600,
            menuShow: false,
            fileEdit: {
                categoryId: 0,
            },
            fileEditLoading: false,
            categoryLoading: false,
            categoryVisible: false,
            categoryListAll: [],
            categoryListParent: [],
            categoryEdit: {
                id: 0,
                pid: 0,
                title: '',
            },
            categoryEditLoading: false,
            categoryTreeAll: [],
            categoryTreeParent: [],
            categoryFilter: '',
            records: [],
            listLoading: false,
            page: 1,
            pageSize: 10,
            total: 0,
            input: {
                path: '',
            },
            reverseSelectOrder: false,
        }
    },
    watch: {
        categoryFilter(val) {
            this.$refs.$categoryTreeAll.filter(val)
        },
    },
    computed: {
        apiUrl() {
            return this.url + '/' + this.category
        },
        listChecked() {
            return this.records.filter(o => o.checked)
        },
        listCheckedIds() {
            return this.listChecked.map(o => o.id
            )
        },
        activeFileOperate() {
            return this.listChecked.length > 0 && this.listChecked.filter(f => f.id > 0).length === this.listChecked.length
        },
    },
    mounted() {
        if ('flat' === this.mode) {
            if (this.permission['View']) {
                this.doCategoryList()
            }
        }
    },
    methods: {
        categoryFilterCallback(value, data) {
            if (!value) return true
            return data.name.indexOf(value) !== -1
        },
        isImage(file) {
            return ['jpg', 'png', 'gif', 'jpeg', 'webp'].includes(file.type)
        },
        show() {
            this.visible = true
            this.doCategoryList()
        },
        hide() {
            this.visible = false
        },
        doClickItem(index) {
            const item = this.records[index]
            if (item.checked) {
                item.checked = false
                return
            }
            const records = this.listChecked
            if (records.length < this.max) {
                item.checked = true
                return
            }
            if (this.max === 1) {
                this.records.map(o => o.checked = false)
                item.checked = true
                return
            }
            Dialog.tipError(this.L('Select %d item(s) at most', this.max))
        },
        doFileEdit() {
            if (!this.activeFileOperate) {
                return
            }
            this.fileEdit.categoryId = this.currentCategoryId
            this.fileVisible = true
        },
        doFileEditSubmit() {
            const ids = this.listCheckedIds;
            if (ids.length === 0) {
                return;
            }
            this.fileEditLoading = true
            this.$api.post(this.apiUrl, JsonUtil.extend(this.fileEdit, {
                id: ids.join(','),
                action: 'fileEdit'
            }), res => {
                Dialog.tipSuccess(this.L('Edit Success'))
                this.fileEditLoading = false
                this.fileVisible = false
                this.doList()
            }, res => {
                this.fileEditLoading = false
            })
        },
        doFileDelete() {
            if (!this.activeFileOperate || !this.listCheckedIds.length) {
                return;
            }
            Dialog.confirm(this.L('Confirm Delete ?'), () => {
                Dialog.loadingOn()
                this.$api.post(this.apiUrl, JsonUtil.extend({id: this.listCheckedIds.join(',')}, {action: 'fileDelete'}), res => {
                    Dialog.loadingOff()
                    Dialog.tipSuccess(this.L('Delete Success'))
                    this.doList(1)
                }, res => {
                    Dialog.loadingOff()
                })
            })
        },
        doCategoryAdd() {
            if (this.currentCategoryId < 0) {
                return;
            }
            this.categoryEdit.id = 0
            this.categoryEdit.pid = this.currentCategoryId
            this.categoryEdit.title = ''
            this.categoryVisible = true
            this.categoryEditLoading = false
        },
        doCategoryEdit() {
            if (this.currentCategoryId <= 0) {
                return;
            }
            let category = this.categories.filter(o => o.id === this.currentCategoryId
            )
            if (category.length !== 1) {
                return;
            }
            category = category[0]
            this.categoryEdit.id = category.id
            this.categoryEdit.pid = category.pid
            this.categoryEdit.title = category.name
            this.categoryVisible = true
            this.categoryEditLoading = false
        },
        doCategoryDelete() {
            if (this.currentCategoryId <= 0) {
                return;
            }
            Dialog.confirm(this.L('Confirm Delete ?'), () => {
                this.$api.post(this.apiUrl, JsonUtil.extend({id: this.currentCategoryId}, {action: 'categoryDelete'}), res => {
                    Dialog.tipSuccess(this.L('Delete Success'))
                    this.currentCategoryId = 0
                    this.doCategoryList()
                })
            })
        },
        doCategoryEditSubmit() {
            this.categoryEditLoading = true
            this.$api.post(this.apiUrl, JsonUtil.extend(this.categoryEdit, {action: 'categoryEdit'}), res => {
                Dialog.tipSuccess(this.L('Add Success'))
                this.categoryEditLoading = false
                this.categoryVisible = false
                this.currentCategoryId = 0
                this.doCategoryList()
            }, res => {
                this.categoryEditLoading = false
            })
        },
        doCategoryList() {
            this.categoryLoading = true
            this.$api.post(this.apiUrl, {action: 'category'}, res => {
                this.categoryLoading = false
                this.categoryTreeAll = res.data.categoryTreeAll
                this.categoryListAll = res.data.categoryListAll
                this.categoryTreeParent = res.data.categoryTreeParent
                this.categoryListParent = res.data.categoryListParent
                this.categories = res.data.categories
                this.$nextTick(() => {
                    this.$refs.$categoryTreeAll.setCurrentKey(this.currentCategoryId)
                })
                this.doList(1)
            }, res => {
                this.categoryLoading = false
            })
        },
        doCategorySelect(data) {
            this.menuShow = false
            this.currentCategoryId = data.id
            this.doList(1)
        },
        doList(page) {
            page = page || this.page
            this.page = page
            this.listLoading = true
            this.$api.post(this.apiUrl, {
                action: 'list',
                page: page,
                categoryId: this.currentCategoryId
            }, res => {
                this.tab = 'list'
                let records = res.data.records
                records.map(o => (o.checked = false, o))
                this.records = records
                this.total = res.data.total
                this.pageSize = res.data.pageSize
                this.$nextTick(() => {
                    this.listLoading = false
                })
            })
        },
        onUploadButtonSuccess(file) {
            this.$api.post(this.apiUrl, {
                action: 'save',
                path: file.path,
                name: file.name,
                size: file.size,
                categoryId: this.currentCategoryId
            }, res => {
                this.doList(1)
            })
        },
        doSubmit() {
            if (this.tab === 'input') {
                if (!this.input.path) {
                    Dialog.tipError(this.L('Please Input'))
                    return
                }
                const records = []
                records.push({
                    category: this.category,
                    name: '',
                    path: this.input.path,
                    fullPath: this.input.path,
                    type: '',
                })
                this.$emit('on-select', records)
                this.visible = false
                return
            }
            let records = this.listChecked.map(o => {
                    return {
                        category: o.category,
                        filename: o.filename,
                        path: o.path,
                        fullPath: o.fullPath,
                        type: o.type,
                    }
                }
            )
            if (records.length < this.min) {
                Dialog.tipError(this.L('Select %d item(s) at least', this.min))
                return
            } else if (records.length > this.max) {
                Dialog.tipError(this.L('Select %d item(s) at most', this.max))
                return
            }
            if (this.reverseSelectOrder) {
                records.reverse()
            }
            this.$emit('on-select', records)
            this.visible = false
        }
    }
}
</script>

<style lang="less">

.pb-data-selector-gallery {
    padding: 10px 10px 10px 210px;
    background: #FFF;

    &.small-window {
        padding-left: 10px;

        .pb-data-selector-category {
            left: -200px;

            .mask {
                content: '';
                display: none;
                position: fixed;
                background: rgba(0, 0, 0, 0.5);
                right: 0;
                top: 45px;
                bottom: 0;
                z-index: 10000;
                left: 200px;
            }
        }

        &.menu-show {
            .pb-data-selector-category {
                left: 0px;

                .mask {
                    display: block;
                }
            }
        }
    }
}

.pb-data-selector-category {
    width: 200px;
    border-right: 1px solid #EEE;
    padding: 10px;
    overflow: auto;
    background: #FFF;

    .action {
        border-bottom: 1px solid #EEE;
        padding: 10px;
        overflow: hidden;

        a {
            display: block;
            width: 33%;
            float: left;
            text-align: center;
            color: var(--color-muted);
            text-decoration: none;

            &:hover {
                color: var(--color-primary, #419488);
            }

            &.active {
                color: var(--color-primary, #419488);
            }
        }
    }
}

.pb-data-selector-mode-dialog {
    .el-dialog__header {
        padding: 10px 10px 0;

        .el-dialog__headerbtn {
            top: 10px;
        }
    }

    .el-dialog__body {
        padding: 10px;
    }

    .pb-data-selector-gallery {
        position: relative;
    }

    .pb-data-selector-category {
        position: absolute;
        left: 0;
        top: 0px;
        bottom: 0px;
    }

    .pb-data-upload-button {
        position: absolute;
        right: 0.5rem;
        top: 1.9rem;
        z-index: 999;
    }
}


.pb-data-selector-mode-flat {
    width: 100% !important;
    margin: 0 !important;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: auto !important;
    bottom: 0;

    .el-dialog__header {
        display: none;
    }

    .el-dialog__body {
        position: fixed;
        left: 0;
        top: 0;
        bottom: 50px;
        right: 0;
        overflow: auto;
    }

    .el-dialog__footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: #FFF;
        box-shadow: 0 0 5px #EEE;
    }

    .pb-data-selector-gallery {
        position: fixed;
        top: 51px;
        right: 0;
        left: 0;
        bottom: 50px;
        overflow: auto;
    }

    .pb-data-selector-category {
        z-index: 1000;
        position: fixed;
        top: 51px;
        left: 0;
        bottom: 50px;
    }

    .pb-data-upload-button {
        position: absolute;
        right: 0.5rem;
        top: 0.5rem;
        z-index: 999;
    }
}

.pb-data-selector-list {
    font-size: var(--font-size, 0.65rem);

    & > .action {
        border-bottom: 1px solid #EEE;
        padding: 10px;
        overflow: hidden;

        & > a {
            display: inline-block;
            padding: 0 20px;
            text-align: center;
            color: var(--color-muted);
            text-decoration: none;

            &:hover {
                color: var(--color-primary, #419488);
            }

            &.active {
                color: var(--color-primary, #419488);
            }
        }
    }

    .records {
        margin-top: 10px;

        .item {
            margin: 0 0 10px 0;
            border-radius: 3px;
            position: relative;
            border: 1px solid #EEE;

            .file-cover {
                position: relative;
                overflow: hidden;
                display: block;

                .text {
                    position: absolute;
                    top: 0;
                    right: 0;
                    bottom: 0;
                    left: 0;
                    text-align: center;
                    color: #CCC;
                    display: flex;
                    align-items: center;

                    .label {
                        flex-grow: 1;
                        font-size: 1.5rem;
                    }
                }

                &:after {
                    content: '';
                    margin-top: 100%;
                    overflow: hidden;
                    display: block;
                }
            }

            &.active {
                border-color: var(--color-primary, #419488);

                .check {
                    display: block;
                }
            }

            .pb-cover {
                border-color: #FFF;
            }

            .check {
                position: absolute;
                top: 0px;
                right: 0px;
                color: var(--color-primary, #419488);
                width: 30px;
                height: 30px;
                line-height: 30px;
                font-size: 20px;
                text-align: center;
                display: none;
            }

            .title {
                text-align: center;
                font-size: 12px;
                color: #999;
                height: 20px;
                overflow: hidden;
                width: 100%;
                text-overflow: ellipsis;
                border-top: 1px solid #EEE;
                padding: 5px 0;
                box-sizing: content-box;
            }

            .action {
                text-align: center;
                border-top: 1px dashed #EEE;
                padding: 5px 0;
                text-decoration: none;

                a {
                    display: inline-block;
                    color: #999;

                    i {
                        font-size: 12px;
                    }
                }
            }
        }
    }

    .page {
        text-align: center;
    }
}
</style>
