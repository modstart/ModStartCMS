<style lang="less">
    @import "Login/style.less";
</style>

<template>
    <div class="pb-page-login">
        <div class="box">
            <Logo/>
            <div class="nav">
                <smart-link class="active" to="login">登录</smart-link>
                <span v-if="!$store.state.base.config.registerDisable">·</span>
                <smart-link v-if="!$store.state.base.config.registerDisable" to="register">注册</smart-link>
            </div>
            <el-form v-if="$store.state.user.id>0">
                <div class="ub-text-center" style="padding:40px 0;">
                    <div>
                        <el-button type="primary" size="large" style="width:80%;" @click="doRedirect()">
                            <i class="iconfont icon-checked"></i>
                            已经登录，点击跳转
                        </el-button>
                    </div>
                </div>
            </el-form>
            <el-form v-if="!$store.state.user.id && $store.state.base.config.ssoClientEnable">
                <div class="ub-text-center" style="padding:40px 0;">
                    <div>
                        <el-button type="primary" size="large" style="width:80%;" @click="doSSOLogin()">
                            <i class="iconfont icon-shield-check"></i>
                            点击一键登录
                        </el-button>
                    </div>
                </div>
            </el-form>
            <el-form v-if="!$store.state.user.id && !$store.state.base.config.ssoClientEnable">
                <el-form-item>
                    <el-input
                            placeholder="输入用户/手机/邮箱"
                            size="large"
                            prefix-icon="iconfont icon-user"
                            v-model="form.username"
                            @keyup.enter.native="submit()">
                    </el-input>
                </el-form-item>
                <el-form-item>
                    <el-input
                            placeholder="输入密码"
                            size="large"
                            type="password"
                            prefix-icon="iconfont icon-lock"
                            v-model="form.password"
                            @keyup.enter.native="submit()">
                    </el-input>
                </el-form-item>
                <el-form-item v-if="$store.state.base.config.loginCaptchaEnable">
                    <el-row :gutter="10">
                        <el-col :span="12">
                            <el-input
                                    placeholder="输入验证码"
                                    size="large"
                                    prefix-icon="iconfont icon-check-alt"
                                    v-model="form.captcha"
                                    @keyup.enter.native="submit()">
                            </el-input>
                        </el-col>
                        <el-col :span="12">
                            <smart-captcha ref="captcha" class="captcha"
                                           src="login_captcha"></smart-captcha>
                        </el-col>
                    </el-row>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" size="large" style="width:100%;" :loading="isSubmitting"
                               @click="submit()">登录
                    </el-button>
                </el-form-item>
            </el-form>
            <Oauth/>
            <Retrieve/>
        </div>
    </div>
</template>

<script>
    import {routeReplaceRaw} from "../../main/router";
    import {EventBus} from "../lib/event-bus";
    import {Code} from "../../main/constant";
    import {UrlUtil} from "../lib/util";
    import {Dialog} from "../lib/dialog";
    import {Storage} from "../lib/storage";

    export default {
        metaInfo: {
            title: '登录'
        },
        components: {
            Oauth: () => import('./Login/Oauth'),
            Retrieve: () => import('./Login/Retrieve'),
            Logo: () => import('./Login/Logo')
        },
        data() {
            return {
                isSubmitting: false,
                form: {
                    username: '',
                    password: '',
                    captcha: '',
                }
            };
        },
        mounted() {
            setTimeout(() => {
                if (this.$store.state.user.id > 0) {
                    const redirect = UrlUtil.getQuery('redirect', '/')
                    Dialog.tipSuccess('已经登录，正在跳转', () => {
                        routeReplaceRaw(redirect)
                    })
                }
            }, 3000)
        },
        methods: {
            doRedirect() {
                const redirect = UrlUtil.getQuery('redirect', '/')
                routeReplaceRaw(redirect)
            },
            doSSOLogin() {
                const redirect = UrlUtil.getQuery('redirect', '/')
                Storage.set('ssoClientRedirect', redirect)
                this.$api.post('sso/client_prepare', {
                    client: UrlUtil.domainUrl('sso/client'),
                }, res => {
                    window.location.href = res.data.redirect
                })
            },
            submit() {
                const redirect = UrlUtil.getQuery('redirect', '/')
                this.isSubmitting = true
                this.$api.post('login', this.form, res => {
                    EventBus.$emit('UpdateApp', () => {
                        routeReplaceRaw(redirect)
                    })
                }, res => {
                    this.isSubmitting = false
                    if (res.code === Code.CAPTCHA_ERROR) {
                        this.$refs.captcha.refresh()
                    }
                })
            }
        }
    }
</script>

