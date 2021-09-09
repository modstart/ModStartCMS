<style lang="less">
    @import "Login/style.less";
</style>

<template>
    <div class="pb-page-login">
        <div class="box">
            <Logo/>
            <div class="nav">
                <smart-link to="login">登录</smart-link>
                <span>·</span>
                <smart-link class="active" to="register">注册</smart-link>
            </div>
            <el-form>
                <el-form-item>
                    <el-input
                            placeholder="输入用户名"
                            size="large"
                            prefix-icon="iconfont icon-user"
                            v-model="form.username">
                    </el-input>
                </el-form-item>
                <el-form-item style="height:40px;">
                    <el-row :gutter="10">
                        <el-col :span="12">
                            <el-input
                                    placeholder="图片验证"
                                    size="large"
                                    prefix-icon="iconfont icon-check-alt"
                                    @blur="blurCaptcha"
                                    @focus="focusCaptcha"
                                    v-model="form.captcha">
                            </el-input>
                            <div class="ub-text-muted ub-text-small" v-if="captchaVerify==''">
                                <i class="el-icon-warning"></i> 输入完成校验
                            </div>
                            <div class="ub-text-success ub-text-small" v-if="captchaVerify=='pass'">
                                <i class="el-icon-success"></i> 验证通过
                            </div>
                            <div class="ub-text-danger ub-text-small" v-if="captchaVerify=='error'">
                                <i class="el-icon-error"></i> 验证错误
                            </div>
                        </el-col>
                        <el-col :span="12">
                            <smart-captcha ref="captcha" class="captcha"
                                           src="register_captcha"></smart-captcha>
                        </el-col>
                    </el-row>
                </el-form-item>
                <el-form-item v-if="$store.state.base.config.registerPhoneEnable">
                    <el-input
                            placeholder="输入手机"
                            size="large"
                            prefix-icon="iconfont icon-phone"
                            v-model="form.phone"
                            @keyup.enter.native="submit()">
                    </el-input>
                </el-form-item>
                <el-form-item v-if="$store.state.base.config.registerPhoneEnable">
                    <el-row :gutter="10">
                        <el-col :span="12">
                            <el-input
                                    placeholder="手机验证码"
                                    size="large"
                                    prefix-icon="iconfont icon-check-alt"
                                    v-model="form.phoneVerify"
                                    @keyup.enter.native="submit()">
                            </el-input>
                        </el-col>
                        <el-col :span="12">
                            <smart-verify class="verify" src="register_phone_verify" :target.sync="form.phone"
                                          :captcha.sync="form.captcha" :error="verifySendError"></smart-verify>
                        </el-col>
                    </el-row>
                </el-form-item>
                <el-form-item v-if="$store.state.base.config.registerEmailEnable">
                    <el-input
                            placeholder="输入邮箱"
                            size="large"
                            prefix-icon="iconfont icon-email"
                            v-model="form.email"
                            @keyup.enter.native="submit()">
                    </el-input>
                </el-form-item>
                <el-form-item v-if="$store.state.base.config.registerEmailEnable">
                    <el-row :gutter="10">
                        <el-col :span="12">
                            <el-input
                                    placeholder="邮箱验证码"
                                    size="large"
                                    prefix-icon="iconfont icon-check-alt"
                                    v-model="form.emailVerify"
                                    @keyup.enter.native="submit()">
                            </el-input>
                        </el-col>
                        <el-col :span="12">
                            <smart-verify class="verify" src="register_email_verify" :target.sync="form.email"
                                          :captcha.sync="form.captcha" :error="verifySendError"></smart-verify>
                        </el-col>
                    </el-row>
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
                <el-form-item>
                    <el-input
                            placeholder="重复密码"
                            size="large"
                            type="password"
                            prefix-icon="iconfont icon-lock"
                            v-model="form.passwordRepeat"
                            @keyup.enter.native="submit()">
                    </el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" size="large" style="width:100%;" :loading="isSubmitting"
                               @click="submit()">注册
                    </el-button>
                </el-form-item>
            </el-form>
            <Retrieve/>
        </div>
    </div>
</template>

<script>
    import {routeReplace} from "../../main/router";
    import {Dialog} from "../lib/dialog";

    export default {
        metaInfo: {
            title: '注册'
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
                    captcha: '',
                    phone: '',
                    phoneVerify: '',
                    email: '',
                    emailVerify: '',
                    password: '',
                    passwordRepeat: '',
                },
                captchaVerify: '',
            };
        },
        methods: {
            focusCaptcha() {
                this.captchaVerify = ''
            },
            blurCaptcha() {
                this.$api.post('register_captcha_verify', {captcha: this.form.captcha}, res => {
                    this.captchaVerify = 'pass'
                }, (res) => {
                    this.captchaVerify = 'error'
                    this.$refs.captcha.refresh()
                    return true
                })
            },
            verifySendError() {
            },
            submit() {
                this.isSubmitting = true
                this.$api.post('register', this.form, res => {
                    // this.isSubmitting = false
                    Dialog.tipSuccess('注册成功，请登录', () => {
                        routeReplace('login')
                    });
                }, res => {
                    this.isSubmitting = false
                })
            }
        }
    }
</script>

