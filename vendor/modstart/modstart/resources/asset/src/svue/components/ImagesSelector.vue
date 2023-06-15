<template>
    <div>
        <DataSelector ref="imageDialog"
                      :url="imageDialogUrl"
                      category="image"
                      :max="max-currentData.length"
                      :child-key="childKey"
                      @on-select="doImageSelect"
        />
        <el-dialog :visible.sync="previewVisible">
            <img width="100%" :src="previewImage"/>
        </el-dialog>
        <div class="tw-mb-1" v-if="currentData.length>0">
            <div class="ub-images-selector">
                <draggable v-model="currentData" handle=".handle">
                    <transition-group>
                        <div class="item" draggable="true" v-for="(item,itemIndex) in currentData"
                             :key="itemIndex+'a'">
                            <div class="tools">
                                <a href="javascript:;" class="action close" @click="doDelete(itemIndex)"><i
                                    class="iconfont icon-close"></i></a>
                                <a href="javascript:;" class="action preview" @click="doPreview(itemIndex)"><i
                                    class="iconfont icon-eye"></i></a>
                            </div>
                            <div class="cover ub-cover-1-1" :style="{backgroundImage:`url(${item})`}"></div>
                        </div>
                    </transition-group>
                </draggable>
            </div>
        </div>
        <div>
            <a href="javascript:;" class="btn" v-if="galleryEnable" @click="doSelect">
                <i class="iconfont icon-category"></i>
                {{ selectText }}
            </a>
            <div class="tw-inline-block tw-align-top" v-if="uploadEnable">
                <UploadButton :url="imageDialogUrl" category="image" auto-save @save="onUploadSuccess"/>
            </div>
        </div>
    </div>
</template>

<script>
import DataSelector from "./DataSelector";
import draggable from 'vuedraggable'
import UploadButton from "./UploadButton";
import {FieldVModel} from "../lib/fields-config";

export default {
    name: "ImagesSelector",
    mixins: [FieldVModel],
    components: {DataSelector, UploadButton, draggable},
    props: {
        data: {
            type: Array,
            default: []
        },
        imageDialogUrl: {
            type: String,
            default: '/member_data/file_manager'
        },
        max: {
            type: Number,
            default: 999
        },
        mini: {
            type: Boolean,
            default: false,
        },
        selectText: {
            type: String,
            default: '选择图片',
        },
        childKey: {
            type: String,
            default: '_child',
        },
        doSelectCustom: {
            type: Function,
            default: null,
        },
        uploadEnable: {
            type: Boolean,
            default: false,
        },
        galleryEnable: {
            type: Boolean,
            default: true,
        },
    },
    data() {
        return {
            previewVisible: false,
            previewImage: '',
        }
    },
    methods: {
        doImageSelect(items) {
            items.forEach(o => {
                this.currentData.push(o.path)
            })
        },
        onUploadSuccess(data) {
            this.currentData.push(data.fullPath)
        },
        doDelete(index) {
            this.currentData.splice(index, 1)
        },
        doPreview(index) {
            this.previewImage = this.currentData[index]
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

    }
}
</style>
