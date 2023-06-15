<template>
    <div>
        <DataSelector ref="fileDialog"
                      :url="fileDialogUrl"
                      :category="category"
                      :max="1"
                      :child-key="childKey"
                      @on-select="doFileSelect"
        />
        <!-- 使用如下代码 -->
        <!--<template v-slot:preview="{currentData,doDelete}"> -->
        <!--    {{ currentData }} -->
        <!--    {{ doDelete }} -->
        <!--</template> -->
        <slot name="preview" :currentData="currentData" :doDelete="doDelete">
            <div class="tw-mb-1" v-if="currentData">
                <div class="tw-flex">
                    <div class="tw-flex-grow tw-pr-1" v-if="currentData">
                        <div class="btn btn-block">
                            {{ fileName() }}
                        </div>
                    </div>
                    <div class="tw-flex-shrink-0">
                        <a href="javascript:;" class="btn btn-danger"
                           @click="doDelete()"
                           v-if="currentData">
                            <i class="iconfont icon-trash"></i>
                        </a>
                    </div>
                </div>
            </div>
        </slot>
        <div>
            <a href="javascript:;" class="btn" v-if="galleryEnable" @click="doSelect">
                <i class="iconfont icon-category"></i>
                {{ selectText }}
            </a>
            <div class="tw-inline-block tw-align-top" v-if="uploadEnable">
                <UploadButton :url="fileDialogUrl" :category="category" auto-save @save="onUploadSuccess"/>
            </div>
        </div>
    </div>
</template>

<script>
import DataSelector from "./DataSelector";
import {FieldVModel} from "./../lib/fields-config";
import UploadButton from "./UploadButton";

export default {
    name: "FileSelector",
    mixins: [FieldVModel],
    components: {UploadButton, DataSelector},
    props: {
        fileDialogUrl: {
            type: String,
            default: '/member_data/file_manager'
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
        category: {
            type: String,
            default: 'file',
        }
    },
    data() {
        return {
            previewFile: '',
        }
    },
    methods: {
        fileName() {
            return this.currentData.substring(this.currentData.lastIndexOf('/') + 1)
        },
        onUploadSuccess(data) {
            this.currentData = data.fullPath
        },
        doFileSelect(files) {
            this.currentData = files[0].path
        },
        doDelete() {
            this.currentData = ''
        },
        doSelect() {
            if (null === this.doSelectCustom) {
                this.$refs.fileDialog.show()
            } else {
                this.doSelectCustom(path => {
                    this.currentData = path
                    this.$emit('update', path)
                })
            }
        },
    }
}
</script>
