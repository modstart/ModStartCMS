<template>
    <div class="pb-text-editor">
        <div class="tw-bg-white tw-rounded tw-border tw-border-solid tw-border-gray-100">
            <div class="tw-flex tw-border-0 tw-border-b tw-border-solid tw-border-gray-100 tw-m-1">
                <div class="tw-flex tw-items-center tw-justify-center">
                    <el-popover
                            placement="bottom-start"
                            v-model="emotionVisible"
                            trigger="click">
                        <a href="javascript:;" slot="reference"
                           class="tw-text-gray-400 tw-px-2">
                            <i class="iconfont icon-smile" style="font-size:0.8rem;"></i>
                        </a>
                        <div style="max-width:400px;">
                            <div v-for="(emotionItem,emotionIndex) in emotions">
                                <div v-for="(eItem,eIndex) in emotionItem.list"
                                     class="tw-rounded tw-cursor-pointer tw-inline-block tw-border-solid tw-border tw-border-white hover:tw-border-gray-200"
                                     @click="doEmotionSelect(eItem)">
                                    <img :src="eItem.url" class="tw-h-6 tw-w-6"/>
                                </div>
                            </div>
                        </div>
                    </el-popover>
                </div>
                <div class="tw-flex tw-items-center tw-justify-center">
                    <el-upload :limit="1" :show-file-list="false"
                               ref="imageUpload"
                               :data="{action:'uploadDirectRaw'}"
                               :on-success="onImageFinish"
                               :action="imageUploadUrl">
                        <a href="javascript:;"
                           class="tw-text-gray-400 tw-px-2">
                            <i class="iconfont icon-image" style="font-size:0.8rem;"></i>
                        </a>
                    </el-upload>
                </div>
            </div>
            <div class="tw-p-1">
                <textarea
                        v-model="text"
                        class="tw-border tw-border-transparent tw-w-full tw-flex tw-items-center hover:tw-bg-transparent hover:tw-border-transparent"
                        rows="3"
                        style="resize:none;"
                        @keypress="onTextareaKeyPress"
                        placeholder="输入消息发送">
                </textarea>
            </div>
            <div class="tw-py-1 tw-border-0 tw-border-t tw-border-solid tw-border-gray-100">
                <div class="ub-text-right tw-p-1">
                    <el-dropdown split-button @command="onSendKeyChange" @click="doTextSend">
                        <template v-if="sendKeyTypeValue==='button'">发送</template>
                        <span v-for="(sendKeyItem,sendKeyIndex) in SendKeyType"
                              v-if="sendKeyTypeValue!=='button' && sendKeyItem.value===sendKeyTypeValue">
                                {{sendKeyItem.name}}发送
                            </span>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item v-for="(sendKeyItem,sendKeyIndex) in SendKeyType"
                                              :key="sendKeyIndex"
                                              :command="sendKeyItem.value">
                                {{sendKeyItem.name}}发送
                            </el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
                </div>
            </div>
        </div>
        <el-dialog :visible.sync="imageVisible" append-to-body width="50%">
            <div slot="title">
                发送图片
            </div>
            <div slot="footer">
                <el-button @click="imageVisible=false">取消</el-button>
                <el-button type="primary" @click="doImageSend">发送</el-button>
            </div>
            <div>
                <img class="tw-rounded tw-w-full" :src="imageUrl"/>
            </div>
        </el-dialog>
    </div>
</template>

<script>
    import {ChatMsgUtil} from './../../lib/chat-msg';
    import UploadButton from "./UploadButton";
    import ImageSelector from "./ImageSelector";

    export default {
        name: "TextEditor",
        components: {UploadButton, ImageSelector},
        props: {
            imageUploadUrl: {
                type: String,
                default: '/api/member_data/file_manager/image'
            },
            sendKeyType: {
                type: String,
                default: 'button'
            },
        },
        data() {
            return {
                SendKeyType: [
                    {
                        value: 'button',
                        name: "按钮"
                    },
                    {
                        value: 'enter',
                        name: "Enter"
                    },
                    {
                        value: 'ctrl_enter',
                        name: "Ctrl+Enter"
                    },
                ],
                sendKeyTypeValue: '',
                emotions: ChatMsgUtil.emotions(),
                emotionVisible: false,
                imageVisible: false,
                imageUrl: '',
                text: '',
            }
        },
        watch: {
            sendKeyType: {
                handler(n, o) {
                    this.sendKeyTypeValue = n
                },
                immediate: true,
            }
        },
        methods: {
            onImageFinish(res, file, fileList) {
                this.$refs.imageUpload.clearFiles()
                if (res.code) {
                    this.$dialog.tipError(res.msg)
                    return
                }
                this.imageUrl = res.data.fullPath
                this.imageVisible = true
            },
            doImageSend() {
                this.imageVisible = false
                this.$emit('on-send', 'image', this.imageUrl)
            },
            doTextSend() {
                if (!this.text) {
                    this.$dialog.tipError('请输入内容')
                    return
                }
                this.$emit('on-send', 'text', this.text)
            },
            doClear() {
                this.text = ''
            },
            doEmotionSelect(eItem) {
                this.text += eItem.key
                this.emotionVisible = false
            },
            onSendKeyChange(command) {
                this.sendKeyTypeValue = command
                this.$emit('on-send-key-type-change', command)
            },
            onTextareaKeyPress(e) {
                switch (this.sendKeyTypeValue) {
                    case 'button':
                        break
                    case 'enter':
                        if (e.keyCode === 13) {
                            this.doTextSend()
                            e.preventDefault()
                        }
                        break
                    case 'ctrl_enter':
                        if (e.keyCode === 13 && e.ctrlKey) {
                            this.doTextSend()
                            e.preventDefault()
                        }
                        break
                }
            }
        }
    }
</script>

