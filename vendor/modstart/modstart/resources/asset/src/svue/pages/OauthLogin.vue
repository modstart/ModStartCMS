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

    export default {
        metaInfo: {
            title: '正在处理'
        },
        data() {
            return {};
        },
        mounted() {
            Storage.set('redirect', UrlUtil.getQuery('redirect', '/'))
            let type = this.$route.params.type
            if (!type) {
                this.$dialog.tipError('错误的来源')
                return;
            }
            this.$api.post(`oauth/login`, {
                type: type,
                callback: UrlUtil.domainUrl(`oauth/callback/${type}`)
            }, res => {
                setTimeout(() => {
                    window.location.href = res.data.redirect
                }, 10)
            })
        }
    }
</script>
