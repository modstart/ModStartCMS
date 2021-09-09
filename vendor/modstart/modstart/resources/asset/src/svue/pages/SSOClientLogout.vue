<style lang="less">
    @import "Login/style.less";
</style>

<template>
    <div>
        <div class="box ub-text-muted" style="text-align:center;padding-top:40px;">
            <div class="title" style="font-size:20px;padding-bottom:20px;">
                <i class="el-icon-loading"></i>
            </div>
            <div class="summary">
                正在处理登录
            </div>
        </div>
    </div>
</template>

<script>
    import {Storage} from "../lib/storage";
    import {routeReplaceRaw} from "../../main/router";
    import {EventBus} from "../lib/event-bus";

    export default {
        metaInfo: {
            title: '正在处理'
        },
        data() {
            return {};
        },
        mounted() {
            const redirect = Storage.get('ssoLogoutRedirect')
            if (!redirect) {
                this.$dialog.tipError('错误的来源')
                return
            }
            this.$api.post('sso/client_logout', {}, res => {
                EventBus.$emit('UpdateApp', () => {
                    routeReplaceRaw(redirect)
                })
            })
        }
    }
</script>
