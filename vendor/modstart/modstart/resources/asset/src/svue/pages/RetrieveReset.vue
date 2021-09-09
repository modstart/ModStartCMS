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
                <el-steps :active="2" align-center>
                    <el-step title="选择方式"></el-step>
                    <el-step title="验证身份"></el-step>
                    <el-step title="重置密码"></el-step>
                </el-steps>
            </div>
            <el-form>
                <el-form-item>
                    <el-input
                            placeholder="用户"
                            size="large"
                            prefix-icon="iconfont icon-user"
                            :disabled="true"
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
                               @click="submit()">设置新密码
                    </el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>
</template>

<script>
    import {routeReplace} from "../../main/router";
    import {Dialog} from "../lib/dialog";

    export default {
        metaInfo: {
            title: '重置密码'
        },
        components: {
            Logo: () => import('./Login/Logo')
        },
        data() {
            return {
                isSubmitting: false,
                form: {
                    username: '',
                    password: '',
                    passwordRepeat: '',
                }
            };
        },
        mounted() {
            this.$api.post('retrieve_reset_info', {}, res => {
                this.form.username = res.data.memberUser.username
            })
        },
        methods: {
            submit() {
                this.isSubmitting = true
                this.$api.post('retrieve_reset', this.form, res => {
                    Dialog.tipSuccess('重置密码成功，请您登录', () => {
                        routeReplace('login')
                    })
                }, res => {
                    this.isSubmitting = false
                })
            }
        }
    }
</script>

