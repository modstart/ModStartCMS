<template>
    <div class="pb-smart-qrcode" @click="showQrcode">
        <slot></slot>
        <el-dialog title="扫描二维码" :visible.sync="visible" append-to-body>
            <div style="text-align:center;">
                <canvas :id="id"></canvas>
            </div>
        </el-dialog>
    </div>
</template>

<script>
    import QRCode from "qrcode"
    import {StrUtil} from "../lib/util";

    export default {
        name: "SmartQrcode",
        props: {
            content: {
                type: String,
                default: ''
            },
            width: {
                type: Number,
                default: 300
            },
            height: {
                type: Number,
                default: 300
            },
        },
        data() {
            return {
                id: '',
                visible: false,
            }
        },
        mounted() {
            this.id = 'SmartQrcode_' + StrUtil.randomString(10)
        },
        methods: {
            showQrcode() {
                if (this.visible) {
                    return
                }
                this.visible = true
                this.$nextTick(() => {
                    QRCode.toCanvas(document.getElementById(this.id), this.content, {
                        width: this.width,
                        height: this.height,
                    });
                }, 0)

            }
        }
    }
</script>

<style lang="less" scoped>
    .pb-smart-qrcode {
        display: inline-block;
        .qrcode-box {
            text-align: center;
        }
    }
</style>