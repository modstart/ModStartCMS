<style lang="less">
    @import "Login/style.less";
</style>

<template>
    <div class="pb-page-login">
        <div class="box">
            <logo/>
            <div class="nav">
                <smart-link to="login">登录</smart-link>
                <span>·</span>
                <smart-link class="active" to="retrieve">找回密码</smart-link>
            </div>
            <div class="step">
                <el-steps :active="1" align-center>
                    <el-step title="选择方式"></el-step>
                    <el-step title="验证身份"></el-step>
                    <el-step title="重置密码"></el-step>
                </el-steps>
            </div>

            <el-form>
                <el-form-item>
                    <el-input
                            placeholder="输入手机"
                            size="large"
                            prefix-icon="iconfont icon-phone"
                            v-model="form.phone"
                            @keyup.enter.native="submit()">
                    </el-input>
                </el-form-item>
                <el-form-item>
                    <el-row :gutter="10">
                        <el-col :span="12">
                            <el-input
                                    placeholder="图片验证"
                                    size="large"
                                    prefix-icon="iconfont icon-check-alt"
                                    v-model="form.captcha"
                                    @keyup.enter.native="submit()">
                            </el-input>
                        </el-col>
                        <el-col :span="12">
                            <smart-captcha ref="captcha" class="captcha"
                                           height="40px"
                                           src="retrieve_captcha"></smart-captcha>
                        </el-col>
                    </el-row>
                </el-form-item>
                <el-form-item>
                    <el-row :gutter="10">
                        <el-col :span="12">
                            <el-input
                                    placeholder="手机验证码"
                                    size="large"
                                    prefix-icon="iconfont icon-check-alt"
                                    v-model="form.verify"
                                    @keyup.enter.native="submit()">
                            </el-input>
                        </el-col>
                        <el-col :span="12">
                            <smart-verify class="verify" src="retrieve_phone_verify" :target.sync="form.phone"
                                          :captcha.sync="form.captcha" :error="verifySendError"></smart-verify>
                        </el-col>
                    </el-row>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" size="large" style="width:100%;" :loading="isSubmitting"
                               @click="submit()">下一步
                    </el-button>
                </el-form-item>
            </el-form>

        </div>
    </div>
</template>

<script>
    import {routeReplace} from "../../main/router";

    export default {
        metaInfo: {
            title: '通过手机找回密码'
        },
        components: {
            Retrieve: () => import('./Login/Retrieve'),
            Logo: () => import('./Login/Logo')
        },
        data() {
            return {
                isSubmitting: false,
                form: {
                    phone: '',
                    captcha: '',
                    verify: '',
                }
            };
        },
        methods: {
            verifySendError() {
                this.$refs.captcha.refresh()
            },
            submit() {
                this.isSubmitting = true
                this.$api.post('retrieve_phone', this.form, res => {
                    routeReplace('retrieve_reset')
                }, res => {
                    this.isSubmitting = false
                })
            }
        }
    }
</script>

