<template>
    <div>
        <DataSelector ref="fileDialog"
                      :url="fileDialogUrl"
                      category="file"
                      :child-key="childKey"
                      :max="max-datav.length"
                      @on-select="doFileSelect"
        />
        <div class="pb-files-selector">
            <draggable v-model="datav" handle=".handle">
                <transition-group>
                    <div class="item"
                         draggable="true"
                         v-for="(item,itemIndex) in datav"
                         :key="itemIndex+'a'">
                        <div class="file-cover">{{fileName(item)}}</div>
                        <div class="mask">
                            <a href="javascript:;" @click="doPreview(itemIndex)">
                                <i class="iconfont icon-zoom-in"></i>
                            </a>
                            <a href="javascript:;" @click="doDelete(itemIndex)">
                                <i class="iconfont icon-trash"></i>
                            </a>
                            <a href="javascript:;" class="handle">
                                <i class="iconfont icon-move"></i>
                            </a>
                        </div>
                    </div>
                </transition-group>
                <template v-if="datav.length===0 && mini">
                    <a href="javascript:;" @click="doSelect">
                        <i class="iconfont icon-file"></i>
                        文件
                    </a>
                </template>
                <template v-if="datav.length>0 || !mini">
                    <div class="item" slot="footer" v-if="datav.length<max">
                        <a href="javascript:;" class="plus" @click="doSelect">
                            <i class="iconfont icon-plus"></i>
                        </a>
                    </div>
                </template>
            </draggable>
        </div>
    </div>
</template>

<style lang="less" scoped>
    .pb-files-selector {
        .item {
            overflow: hidden;
            background-color: #fff;
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            border: 1px solid #c0ccda;
            border-radius: 3px;
            box-sizing: border-box;
            position: relative;
            width: 100%;
            height: 30px;
            display: inline-block;
            .plus {
                line-height: 30px;
                text-align: center;
                color: #999;
                display: block;
            }
            .file-cover {
                color: #999;
                padding: 0 5px;
            }
            &:hover {
                .mask {
                    display: block;
                }
            }
            .mask {
                background: rgba(0, 0, 0, 0.5);
                color: #FFF;
                text-align: right;
                display: none;
                position: absolute;
                top: 0px;
                right: 0px;
                bottom: 0px;
                left: 0px;
                a {
                    color: #CCC;
                    display: inline-block;
                    line-height: 30px;
                    padding: 0 5px;
                    &:hover {
                        color: #FFF;
                    }
                }
            }
        }
    }
</style>

<script>
    import DataSelector from "./DataSelector";
    import draggable from 'vuedraggable'

    export default {
        name: "FilesSelector",
        components: {DataSelector, draggable},
        model: {
            prop: 'data',
            event: 'FilesSelectorEvent'
        },
        props: {
            data: {
                type: Array,
                default: []
            },
            fileDialogUrl: {
                type: String,
                default: 'member_data/file_manager'
            },
            max: {
                type: Number,
                default: 999
            },
            mini: {
                type: Boolean,
                default: false,
            },
            childKey: {
                type: String,
                default: '_child',
            },
            doSelectCustom: {
                type: Function,
                default: null,
            }
        },
        data() {
            return {
                datav: [],
                previewFile: '',
            }
        },
        mounted() {
            this.datav = this.data
        },
        watch: {
            data(newValue, oldValue) {
                if (JSON.stringify(newValue) != JSON.stringify(this.datav)) {
                    this.datav = newValue
                }
            },
            datav(newValue, oldValue) {
                this.$emit('FilesSelectorEvent', this.datav)
            }
        },
        methods: {
            fileName(file) {
                return file.substring(file.lastIndexOf('/') + 1)
            },
            doFileSelect(files) {
                files.forEach(o => {
                    this.datav.push(o.path)
                })
            },
            doDelete(index) {
                this.datav.splice(index, 1)
            },
            doPreview(index) {
                this.previewFile = this.datav[index]
            },
            doSelect() {
                if (null === this.doSelectCustom) {
                    this.$refs.fileDialog.show()
                } else {
                    this.doSelectCustom(items => {
                        this.doFileSelect(items)
                    })
                }
            },
        }
    }
</script>
