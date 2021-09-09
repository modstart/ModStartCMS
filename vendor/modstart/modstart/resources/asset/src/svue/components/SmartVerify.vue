<template>
    <div class="pb-smart-verify">
        <el-button class="btn" :size="size" :style="{height:height}" :disabled="loading" @click="send"
                   v-if="status==='init'">点击获取
        </el-button>
        <el-button class="btn" :size="size" :style="{height:height}" :disabled="true" v-if="status==='sent'">
            {{count}}秒
        </el-button>
        <el-button class="btn" :size="size" :style="{height:height}" :disabled="loading" v-if="status==='retry'"
                   @click="send">重新获取
        </el-button>
    </div>
</template>

<script>
    export default {
        props: {
            src: {
                type: String,
                default: ''
            },
            target: {
                type: String,
                default: ''
            },
            captcha: {
                type: String,
                default: ''
            },
            error: {
                type: Function,
                default: null
            },
            size: {
                type: String,
                default: 'large'
            },
            height: {
                type: String,
                default: '40px'
            },
        },
        data() {
            return {
                loading: false,
                status: 'init', // init sent retry
                count: 0,
            }
        },
        mounted() {
        },
        methods: {
            countDown() {
                this.count--
                if (this.count > 0) {
                    setTimeout(() => {
                        this.countDown()
                    }, 1000)
                } else {
                    this.status = 'retry'
                }
            },
            send() {
                this.loading = true
                this.$api.post(this.src, {target: this.target, captcha: this.captcha}, res => {
                    this.loading = false
                    this.count = 60
                    this.status = 'sent'
                    this.countDown()
                }, res => {
                    this.loading = false
                    if (this.error) {
                        return this.error()
                    }
                })
            }
        }
    }
</script>

<style lang="less">
    .pb-smart-verify {
        .btn {
            width: 100%;
        }
    }
</style>
