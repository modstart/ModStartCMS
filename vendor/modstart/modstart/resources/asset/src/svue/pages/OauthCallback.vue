<style lang="less">
    @import "Login/style.less";
</style>

<template>
    <div>
        <div v-if="status==='init'" class="box ub-text-muted" style="text-align:center;padding-top:40px;">
            <div class="title" style="font-size:20px;padding-bottom:20px;">
                <i class="el-icon-loading"></i>
            </div>
            <div class="summary">
                正在处理登录
            </div>
        </div>
        <div v-if="status==='bind'" class="pb-page-login">
            <div class="box">
                <Logo/>
                <div class="nav">
                    <smart-link to="login">绑定用户</smart-link>
                </div>
                <el-form>
                    <el-form-item>
                        <el-avatar :size="40" :src="user.avatar">{{user.username}}</el-avatar>
                    </el-form-item>
                    <el-form-item>
                        <el-input
                                placeholder="用户名"
                                size="large"
                                prefix-icon="iconfont icon-user"
                                v-model="user.username">
                        </el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" size="large" style="width:100%;" :loading="isSubmitting"
                                   @click="submit()">确定
                        </el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>
    </div>
</template>

<script>
    import {UrlUtil} from "../lib/util";
    import {Storage} from "../lib/storage";
    import {routeReplaceRaw} from "../../main/router";
    import Logo from "./Login/Logo";

    export default {
        components: {Logo},
        metaInfo: {
            title: '正在处理'
        },
        data() {
            return {
                status: 'init',
                type: '',
                user: {},
                isSubmitting: false,
            };
        },
        mounted() {
            let type = this.$route.params.type
            if (!type) {
                this.$dialog.tipError('错误的来源')
                return;
            }
            this.type = type
            let code = UrlUtil.getQuery('code');
            if (!code) {
                this.$dialog.tipError('错误的来源')
                return
            }
            this.status = 'init'
            this.$api.post('oauth/callback', {
                type: type,
                code: code,
            }, res => {
                this.status = 'bind'
                this.user = res.data.user
            })
        },
        methods: {
            submit() {
                this.isSubmitting = true
                this.$api.post('oauth/bind', {
                    username: this.user.username,
                    avatar: this.user.avatar
                }, res => {
                    this.isSubmitting = false
                    let redirect = Storage.set('redirect', '/')
                    routeReplaceRaw(redirect)
                }, res => {
                    this.isSubmitting = false
                })
            }
        }
    }
</script>
