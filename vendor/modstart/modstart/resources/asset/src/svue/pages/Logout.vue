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
                正在退出登录
            </div>
        </div>
    </div>
</template>

<script>
    import {routeReplaceRaw} from "@/main/router";
    import {EventBus} from "../lib/event-bus";
    import {Storage} from "../lib/storage";
    import {UrlUtil} from "../lib/util";

    export default {
        metaInfo: {
            title: '退出登录'
        },
        data() {
            return {};
        },
        mounted() {
            const redirect = UrlUtil.getQuery('redirect', '/')
            if (this.$store.state.base.config.ssoClientEnable) {
                Storage.set('ssoLogoutRedirect', redirect)
                this.$api.post('sso/client_logout_prepare', {
                    domainUrl: UrlUtil.domainUrl(),
                }, res => {
                    window.location.href = res.data.redirect
                })
            } else {
                this.$api.post('logout', {}, res => {
                    EventBus.$emit('UpdateApp', () => {
                        routeReplaceRaw(redirect)
                    })
                })
            }
        }
    }
</script>
