<template>
    <div>
        <DataSelector ref="fileDialog"
                      :url="fileDialogUrl"
                      category="file"
                      :child-key="childKey"
                      :max="max-currentData.length"
                      @on-select="doFileSelect"
        />
        <div class="pb-files-selector">
            <draggable v-model="currentData" handle=".handle">
                <transition-group>
                    <div class="item"
                         draggable="true"
                         v-for="(item,itemIndex) in currentData"
                         :key="'a'+itemIndex">
                        <div class="tw-flex">
                            <div class="tw-flex-grow">
                                <div class="btn btn-block">
                                    {{ fileName(item) }}
                                </div>
                            </div>
                            <div class="tw-pl-1">
                                <a href="javascript:;" class="btn btn-danger"
                                   @click="doDelete(itemIndex)">
                                    <i class="iconfont icon-trash"></i>
                                </a>
                            </div>
                            <div class="tw-pl-1" v-if="dragEnable">
                                <a href="javascript:;" class="btn handle">
                                    <i class="iconfont icon-move"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </transition-group>
            </draggable>
            <div>
                <a href="javascript:;" class="btn" v-if="galleryEnable" @click="doSelect">
                    <i class="iconfont icon-category"></i>
                    {{ selectText }}
                </a>
                <div class="tw-inline-block tw-align-top" v-if="uploadEnable">
                    <UploadButton :url="fileDialogUrl" category="file" auto-save @save="onUploadSuccess"/>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="less" scoped>
.pb-files-selector {
    .item {
        margin-bottom: 0.2rem;
    }
}
</style>

<script>
import DataSelector from "./DataSelector";
import draggable from 'vuedraggable'
import {FieldVModel} from "../lib/fields-config";
import UploadButton from "./UploadButton";

export default {
    name: "FilesSelector",
    mixins: [FieldVModel],
    components: {DataSelector, UploadButton, draggable},
    props: {
        data: {
            type: Array,
            default: []
        },
        fileDialogUrl: {
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
        childKey: {
            type: String,
            default: '_child',
        },
        selectText: {
            type: String,
            default: '选择文件',
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
        dragEnable: {
            type: Boolean,
            default: true,
        }
    },
    data() {
        return {
            previewFile: '',
        }
    },
    methods: {
        fileName(file) {
            return file.substring(file.lastIndexOf('/') + 1)
        },
        doFileSelect(files) {
            files.forEach(o => {
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
            this.previewFile = this.currentData[index]
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
