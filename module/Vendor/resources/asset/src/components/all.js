import ImagesSelector from "@ModStartAsset/svue/components/ImagesSelector.vue"
import ImageSelector from "@ModStartAsset/svue/components/ImageSelector.vue"
import VideoSelector from "@ModStartAsset/svue/components/VideoSelector.vue"
import AudioSelector from "@ModStartAsset/svue/components/AudioSelector.vue"
import FileSelector from "@ModStartAsset/svue/components/FileSelector.vue"
import FilesSelector from "@ModStartAsset/svue/components/FilesSelector.vue"
import CodeEditor from "@ModStartAsset/svue/components/CodeEditor.vue"
import RichEditor from "./RichEditor"
import NameValueListEditor from "./NameValueListEditor"
import SmartLink from "@ModStartAsset/svue/components/SmartLink"
import SmartCaptcha from "@ModStartAsset/svue/components/SmartCaptcha"
import SmartVerify from "@ModStartAsset/svue/components/SmartVerify"

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

export default (Vue) => {
    Vue.component("images-selector", ImagesSelector)
    Vue.component("image-selector", ImageSelector)
    Vue.component("file-selector", FileSelector)
    Vue.component("files-selector", FilesSelector)
    Vue.component("video-selector", VideoSelector)
    Vue.component("audio-selector", AudioSelector)
    Vue.component("code-editor", CodeEditor)
    Vue.component('rich-editor', RichEditor)
    Vue.component('name-value-list-editor', NameValueListEditor)
    Vue.component("smart-link", SmartLink)
    Vue.component("smart-captcha", SmartCaptcha)
    Vue.component("smart-verify", SmartVerify)
}

// VueManager.Vue.component('icon-selector', require('./../components/IconSelector').default)
// VueManager.Vue.component('image-link-editor', require('./../components/ImageLinkEditor').default)
// VueManager.Vue.component('text-link-editor', require('./../components/TextLinkEditor').default)
// VueManager.Vue.component('text-link-list-editor', require('./../components/TextLinkListEditor').default)
// VueManager.Vue.component('group-text-link-list-editor', require('./../components/GroupTextLinkListEditor').default)
// VueManager.Vue.component('ratio-editor', require('./../components/RatioEditor').default)
// VueManager.Vue.component('link-selector', require('./../components/LinkSelector').default)
