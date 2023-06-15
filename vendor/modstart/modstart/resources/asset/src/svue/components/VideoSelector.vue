<template>
    <div>
        <FileSelector v-model="currentData"
                      category="video"
                      :upload-enable="uploadEnable"
                      :gallery-enable="galleryEnable"
                      :file-dialog-url="fileDialogUrl"
                      :child-key="childKey"
                      :select-text="selectText"
                      :do-select-custom="doSelectCustom">
        </FileSelector>
    </div>
</template>

<script>
import {FileSelectorMixin} from "./Mixins/FileSelector"
import FileSelector from "./FileSelector"

export default {
    name: "VideoSelector",
    mixins: [FileSelectorMixin],
    components: {FileSelector},
    props: {
        selectText: {
            type: String,
            default: '选择视频',
        },
    },
}
</script>
