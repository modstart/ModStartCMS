import ImagesSelector from "@ModStartAsset/svue/components/ImagesSelector.vue"
import ImageSelector from "@ModStartAsset/svue/components/ImageSelector.vue"
import VideoSelector from "@ModStartAsset/svue/components/VideoSelector.vue"
import FileSelector from "@ModStartAsset/svue/components/FileSelector.vue"
import FilesSelector from "@ModStartAsset/svue/components/FilesSelector.vue"

const AudioSelector = Object.assign({}, FileSelector)
AudioSelector.props = Object.assign({}, FileSelector.props)
AudioSelector.props.doSelectCustom = Object.assign({}, FileSelector.props.doSelectCustom)
AudioSelector.props.selectText = Object.assign({}, FileSelector.props.selectText)

const buildSelectorDialog = (type) => {
    return (cb) => {
        if (!('__selectorDialogServer' in window)) {
            alert('请先配置 window.__selectorDialogServer')
        }
        window.__selectorDialog = new window.api.selectorDialog({
            server: window.__selectorDialogServer + '/' + type,
            callback: (items) => {
                if (items.length > 0) cb(items[0].path)
            }
        }).show()
    };
}

const buildFilesSelectorDialog = (type) => {
    return (cb) => {
        window.__selectorDialog = new window.api.selectorDialog({
            server: window.__selectorDialogServer + '/' + type,
            callback: (items) => {
                cb(items)
            }
        }).show()
    };
}

ImageSelector.props.doSelectCustom.default = buildSelectorDialog('image')
FileSelector.props.doSelectCustom.default = buildSelectorDialog('file')
VideoSelector.props.doSelectCustom.default = buildSelectorDialog('video')
AudioSelector.props.doSelectCustom.default = buildSelectorDialog('audio')
AudioSelector.props.selectText.default = '选择音频'
ImagesSelector.props.doSelectCustom.default = buildFilesSelectorDialog('image')
FilesSelector.props.doSelectCustom.default = buildFilesSelectorDialog('file')

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
    Vue.component('rich-editor', () => import('./RichEditor'))
    Vue.component('name-value-list-editor', () => import('./NameValueListEditor'))
    Vue.component("smart-link", () => import('@ModStartAsset/svue/components/SmartLink'))
    Vue.component("smart-captcha", () => import('@ModStartAsset/svue/components/SmartCaptcha'))
    Vue.component("smart-verify", () => import('@ModStartAsset/svue/components/SmartVerify'))
}


// VueManager.Vue.component('icon-selector', require('./../components/IconSelector').default)
// VueManager.Vue.component('image-link-editor', require('./../components/ImageLinkEditor').default)
// VueManager.Vue.component('text-link-editor', require('./../components/TextLinkEditor').default)
// VueManager.Vue.component('text-link-list-editor', require('./../components/TextLinkListEditor').default)
// VueManager.Vue.component('group-text-link-list-editor', require('./../components/GroupTextLinkListEditor').default)
// VueManager.Vue.component('ratio-editor', require('./../components/RatioEditor').default)
// VueManager.Vue.component('link-selector', require('./../components/LinkSelector').default)
