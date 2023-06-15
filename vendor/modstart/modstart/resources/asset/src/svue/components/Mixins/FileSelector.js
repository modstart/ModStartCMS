import {FieldVModel} from "../../lib/fields-config";

export const FileSelectorMixin = {
    mixins: [FieldVModel],
    props: {
        data: {
            type: String,
            default: ''
        },
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
    },
}
