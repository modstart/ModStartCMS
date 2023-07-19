import {VueManager} from "../lib/vue-manager";

import IconInput from "@ModStartAsset/svue/components/Field/IconInput"

VueManager.Vue.component('icon-input', IconInput)

import ImagesSelector from "@ModStartAsset/svue/components/ImagesSelector.vue"
import ImageSelector from "@ModStartAsset/svue/components/ImageSelector.vue"
import VideoSelector from "@ModStartAsset/svue/components/VideoSelector.vue"
import AudioSelector from "@ModStartAsset/svue/components/AudioSelector.vue"
import FileSelector from "@ModStartAsset/svue/components/FileSelector.vue"
import FilesSelector from "@ModStartAsset/svue/components/FilesSelector.vue"
import CodeEditor from "@ModStartAsset/svue/components/CodeEditor.vue"
import RichEditor from "@ModStartAsset/svue/components/RichEditor.vue"
import ValuesEditor from "@ModStartAsset/svue/components/ValuesEditor.vue"

const setProp = (com, key, value) => {
    if (com.props && (key in com.props)) {
        com.props[key].default = value
    } else {
        for (const c of com.mixins) {
            setProp(c, key, value)
        }
    }
}

const buildFileSelectorDialog = (type) => {
    return (cb) => {
        if (!('__selectorDialogServer' in window)) {
            alert('请先配置 window.__selectorDialogServer')
        }
        window.__selectorDialog = new window.api.selectorDialog({
            server: window.__selectorDialogServer + '/' + type,
            callback: (items) => {
                if (items.length > 0) {
                    cb(items[0].path)
                }
            }
        }).show()
    };
}

const buildFilesSelectorDialog = (type) => {
    return (cb) => {
        if (!('__selectorDialogServer' in window)) {
            alert('请先配置 window.__selectorDialogServer')
        }
        window.__selectorDialog = new window.api.selectorDialog({
            server: window.__selectorDialogServer + '/' + type,
            callback: (items) => {
                cb(items)
            }
        }).show()
    };
}

ImageSelector.props.doSelectCustom.default = buildFileSelectorDialog('image')
ImagesSelector.props.doSelectCustom.default = buildFilesSelectorDialog('image')
FileSelector.props.doSelectCustom.default = buildFileSelectorDialog('file')
FilesSelector.props.doSelectCustom.default = buildFilesSelectorDialog('file')
setProp(VideoSelector, 'doSelectCustom', buildFileSelectorDialog('video'))
setProp(AudioSelector, 'doSelectCustom', buildFileSelectorDialog('audio'))

if (window.__selectorDialogServer) {
    ImageSelector.props.imageDialogUrl.default = window.__selectorDialogServer
}

VueManager.Vue.component("images-selector", ImagesSelector)
VueManager.Vue.component("image-selector", ImageSelector)
VueManager.Vue.component("file-selector", FileSelector)
VueManager.Vue.component("files-selector", FilesSelector)
VueManager.Vue.component("video-selector", VideoSelector)
VueManager.Vue.component("audio-selector", AudioSelector)
VueManager.Vue.component("code-editor", CodeEditor)
VueManager.Vue.component('rich-editor', RichEditor)
VueManager.Vue.component('values-editor', ValuesEditor)

window.__vueManager = VueManager
