<template>
    <div>
        <panel-box v-loading="initLoading" :style="{height:height+'px',overflow:'auto',margin:'0px'}">
            <div slot="title">
                <div class="more">
                    <a title="刷新" href="javascript:;" class="ub-text-muted" :loading="initLoading" @click="doInit()"><i
                            class="iconfont icon-refresh"></i></a>
                    &nbsp;&nbsp;
                    <a title="增加根分类" href="javascript:;" class="ub-text-muted" @click="doAddNode({id:0})"><i
                            class="iconfont icon-plus"></i></a>
                </div>
                <slot name="titleSlot">
                    <span style="color: #212C39;font-size:14px;font-weight:600;">{{title}}</span>
                </slot>
            </div>
            <div class="pb-category-editor-selector">
                <div :class="{'node-all':true,active:!id}">
                    <a href="javascript:;" @click="doClear()">全部</a>
                </div>
                <el-tree
                        v-if="tree.length>0"
                        :data="tree"
                        :props="{children:childKey,label:'name'}"
                        default-expand-all
                        :expand-on-click-node="false">
                    <div :class="{node:true,active:id===data.id}" slot-scope="{node,data}">
                        <div class="action">
                            <el-popover
                                    placement="top"
                                    trigger="hover"
                                    popper-class="pc-category-editor-menu"
                            >
                                <a href="javascript:;" @click="() => doAddNode(data)"><span
                                        class="word-style">增加子类</span></a>
                                <a href="javascript:;" @click="() => doEditNode(data)"><span
                                        class="word-style">编辑</span></a>
                                <a href="javascript:;" @click="() => doSortNode(data,'up')"><span
                                        class="word-style">上移</span></a>
                                <a href="javascript:;" @click="() => doSortNode(data,'down')"><span class="word-style">下移</span></a>
                                <a href="javascript:;" @click="() => doDeleteNode(data)"><span
                                        class="word-style">删除</span></a>
                                <span slot="reference">
                                    <i :class="actionEditIconClass"></i>
                                </span>
                            </el-popover>
                        </div>
                        <div class="label" @click="doSelectNode(data)">
                            {{ node.label }}
                            <span class="node-count" v-if="'count' in data">({{data.count}})</span>
                        </div>
                    </div>
                </el-tree>
            </div>
        </panel-box>

        <el-dialog :visible.sync="isEdit" append-to-body>
            <div slot="title">
                {{data.id>0?'修改分类':'增加分类'}}
            </div>
            <div slot="footer">
                <el-button type="primary" :loading="editSubmitLoading" @click="doSubmit">确认</el-button>
            </div>
            <div v-loading="editGetLoading">
                <el-form label-width="100px">
                    <el-form-item label="上级">
                        <el-select v-model="data.pid" placeholder="请选择" style="width:auto;">
                            <el-option v-for="item in selectListForParent"
                                       :key="item.id"
                                       :label="'|-'.repeat(item.level)+item.title"
                                       :value="item.id"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="名称">
                        <el-input style="max-width:20em;" ref="ref" v-model="data.name"></el-input>
                    </el-form-item>
                </el-form>
            </div>
        </el-dialog>

    </div>
</template>

<script>
    const $ = require('jquery')
    import PanelBox from "./../layouts/Panel/PanelBox"
    import PanelBoxBody from "./../layouts/Panel/PanelBoxBody"

    export default {
        name: "CategoryEditorSelector",
        model: {
            prop: 'id',
            event: 'update'
        },
        components: {PanelBox, PanelBoxBody},
        props: {
            id: {
                type: Number,
                default: 0
            },
            idv: {
                type: Number,
                default: 0,
            },
            urlBase: {
                type: String,
                default: ''
            },
            title: {
                type: String,
                default: '分类列表'
            },
            param: {
                type: Object,
                default: () => {
                    return {}
                }
            },
            height: {
                type: Number,
                default: 100
            },
            childKey: {
                type: String,
                default: '_child',
            },
            actionEditIconClass: {
                type: String,
                default: 'iconfont icon-list'
            }
        },
        data() {
            return {
                doHover: 1,
                initLoading: true,
                editSubmitLoading: false,
                editGetLoading: false,
                tree: [],
                selectListForParent: [],
                isEdit: false,
                data: {
                    pid: 0,
                    id: 0,
                    name: '',
                    roleIds: [],
                }
            }
        },
        mounted() {
            this.doInit()
        },
        watch: {
            id(newValue, oldValue) {
                // console.log('value-change')
            },
            param: {
                handler(newValue, oldValue) {
                    if (JSON.stringify(newValue) !== JSON.stringify(oldValue)) {
                        this.doInit()
                    }
                },
                deep: true,
                immediate: true,
            }
        },
        methods: {
            doInit() {
                this.initLoading = true
                this.$api.post(`${this.urlBase}/all`, $.extend({}, this.param), res => {
                    this.tree = res.data.tree
                    this.selectListForParent = res.data.selectListForParent
                    this.$emit('update-tree', res.data.tree)
                    this.$emit('update-list-for-parent', res.data.selectListForParent)
                    this.$emit('update-data')
                    this.isEdit = false
                    this.initLoading = false
                }, res => {
                    this.initLoading = false
                })
            },
            doAddNode(data) {
                this.data.pid = data.id
                this.data.id = 0
                this.data.name = ''
                this.data.roleIds = []
                this.isEdit = true
                this.$nextTick(() => this.$refs.ref.focus())
            },
            doEditNode(data) {
                this.editGetLoading = true
                this.$api.post(`${this.urlBase}/get`, $.extend({id: data.id}, this.param), res => {
                    this.data.pid = res.data.pid
                    this.data.id = res.data.id
                    this.data.name = res.data.name
                    this.data.roleIds = res.data.roleIds
                    this.editGetLoading = false
                    this.isEdit = true
                    this.$nextTick(() => this.$refs.ref.focus())
                })
            },
            doDeleteNode(data) {
                this.$dialog.confirm('确认删除？', () => {
                    this.$api.post(`${this.urlBase}/delete`, $.extend({id: data.id}, this.param), () => {
                        this.$dialog.tipSuccess('操作成功')
                        this.doInit()
                    })
                })
            },
            doSortNode(data, direction) {
                this.$api.post(`${this.urlBase}/sort/${direction}`, $.extend({id: data.id}, this.param), () => {
                    this.$dialog.tipSuccess('操作成功')
                    this.doInit()
                })
            },
            doSelectNode(data) {
                this.$emit('update', data.id)
            },
            doClear() {
                this.$emit('update', 0)
            },
            doSubmit() {
                this.editSubmitLoading = true
                this.$api.post(`${this.urlBase}/edit`, $.extend(this.data, this.param), res => {
                    this.editSubmitLoading = false
                    this.isEdit = false
                    this.$dialog.tipSuccess('操作成功')
                    this.doInit()
                }, res => {
                    this.editSubmitLoading = false
                })
            },
        }
    }
</script>

<style lang="less">


    .pc-category-editor-menu {
        min-width: 80px;
        width: 80px;
        font-size: var(--font-size-small, 10px);
        padding: 5px;

        a {
            color: #999;
            display: block;
            text-align: center;
            line-height: 20px;

            &:hover {
                color: #6B9BE8;
            }
        }
    }

    .pb-category-editor-selector {
        margin-top: -5px;

        .node-all {
            line-height: 25px;
            border-bottom: 1px dotted #CCC;

            a {
                color: #333;
            }

            &.active {
                a {
                    color: var(--color-primary, #419488);
                }
            }
        }

        .node-count {
            color: #999;
            font-size: var(--font-size-small, 10px);
            position: absolute;
            top: 0px;
            right: 0px;
        }

        .el-tree-node__content {
            height: 32px;
            border-radius: 3px;

            .node {
                line-height: 30px;
                border-bottom: 1px dotted #CCC;
                flex: 1;
                overflow: hidden;

                &:hover {
                    .action {
                        visibility: visible;
                    }
                }

                &.active {
                    color: var(--color-primary, #419488);
                }

                .action {
                    line-height: 30px;
                    float: right;
                    visibility: hidden;

                    a {
                        padding: 0px 3px;
                    }
                }

                .label {
                    line-height: 30px;
                    overflow: hidden;
                    margin-right: 25px;
                    text-overflow: ellipsis;
                    position: relative;
                    padding-right: 20px;
                }
            }
        }
    }
</style>
