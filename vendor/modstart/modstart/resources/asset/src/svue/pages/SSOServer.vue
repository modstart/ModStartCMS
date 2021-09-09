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
    import {UrlUtil} from "../lib/util";
    import {Storage} from "../lib/storage";
    import {routeReplace} from "../../main/router";

    export default {
        metaInfo: {
            title: '正在处理'
        },
        data() {
            return {};
        },
        mounted() {
            const param = UrlUtil.parseQuery()
            Storage.set('ssoServerClient', UrlUtil.getQuery('client'))
            this.$api.post('sso/server', param, res => {
                if (res.data.isLogin) {
                    routeReplace('sso/server_success')
                } else {
                    routeReplace('login?redirect=%2Fsso%2Fserver_success')
                }
            })
        }
    }
</script>
