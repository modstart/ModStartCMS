<template>
    <div>
        <DataSelector ref="imageDialog"
                      :url="imageDialogUrl"
                      category="image"
                      :max="max-datav.length"
                      :child-key="childKey"
                      @on-select="doImageSelect"
        />
        <el-dialog :visible.sync="previewVisible">
            <img width="100%" :src="previewImage"/>
        </el-dialog>
        <div class="pb-images-selector">
            <draggable v-model="datav" handle=".handle">
                <transition-group>
                    <div class="item" draggable="true" v-for="(item,itemIndex) in datav"
                         :key="itemIndex+'a'" :style="{backgroundImage:'url('+item+')'}">
                        <div class="mask">
                            <a href="javascript:;" @click="doPreview(itemIndex)">
                                <i class="iconfont icon-zoom-in"></i>
                            </a>
                            <a href="javascript:;" @click="doDelete(itemIndex)">
                                <i class="iconfont icon-trash"></i>
                            </a>
                            <br>
                            <a href="javascript:;" class="handle">
                                <i class="iconfont icon-move"></i>
                            </a>
                        </div>
                    </div>
                </transition-group>
                <template v-if="datav.length===0 && mini">
                    <a href="javascript:;" @click="doSelect">
                        <i class="iconfont icon-image"></i>
                        图片
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

<script>
    import DataSelector from "./DataSelector";
    import draggable from 'vuedraggable'

    export default {
        name: "ImagesSelector",
        components: {DataSelector, draggable},
        model: {
            prop: 'data',
            event: 'ImagesSelectorEvent'
        },
        props: {
            data: {
                type: Array,
                default: []
            },
            imageDialogUrl: {
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
                previewVisible: false,
                previewImage: '',
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
                this.$emit('ImagesSelectorEvent', this.datav)
            }
        },
        methods: {
            doImageSelect(items) {
                items.forEach(o => {
                    this.datav.push(o.path)
                })
            },
            doDelete(index) {
                this.datav.splice(index, 1)
            },
            doPreview(index) {
                this.previewImage = this.datav[index]
                this.previewVisible = true
            },
            doSelect() {
                if (null === this.doSelectCustom) {
                    this.$refs.imageDialog.show()
                } else {
                    this.doSelectCustom(items => {
                        this.doImageSelect(items)
                    })
                }
            },
        }
    }
</script>

<style lang="less" scoped>
    .pb-images-selector {
        .item {
            overflow: hidden;
            background-color: #fff;
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            border: 1px solid #c0ccda;
            border-radius: 6px;
            box-sizing: border-box;
            width: 60px;
            height: 60px;
            margin: 0 8px 8px 0;
            display: inline-block;
            .plus {
                line-height: 60px;
                text-align: center;
                color: #999;
                display: block;
            }
            &:hover {
                .mask {
                    display: block;
                }
            }
            .mask {
                background: rgba(0, 0, 0, 0.5);
                color: #FFF;
                text-align: center;
                display: none;
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
