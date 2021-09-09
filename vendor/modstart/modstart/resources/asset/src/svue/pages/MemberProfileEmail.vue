<template>
    <panel-content>

        <MemberProfileNav slot="panelPageMenu"/>

        <panel-box-body v-if="!!$store.state.user.email && !change">
            <el-form label-width="100px" style="max-width:400px;">
                <el-form-item label="已绑定">
                    {{$store.state.user.email}}
                </el-form-item>
                <el-form-item>
                    <el-button @click="change=true">修改</el-button>
                </el-form-item>
            </el-form>
        </panel-box-body>

        <panel-box-body v-if="!$store.state.user.email || change">
            <el-form label-width="100px" style="max-width:400px;">
                <el-form-item label="邮箱">
                    <el-input v-model="data.email"></el-input>
                </el-form-item>
                <el-form-item label="图片验证">
                    <el-row>
                        <el-col :span="12">
                            <el-input placeholder="图片验证" v-model="data.captcha"></el-input>
                        </el-col>
                        <el-col :span="12">
                            <smart-captcha height="32px" ref="captcha" class="captcha"
                                           src="member_profile/captcha"></smart-captcha>
                        </el-col>
                    </el-row>
                </el-form-item>
                <el-form-item label="验证码">
                    <el-row>
                        <el-col :span="12">
                            <el-input
                                    placeholder="邮箱验证码"
                                    v-model="data.verify"
                                    @keyup.enter.native="doSubmit()">
                            </el-input>
                        </el-col>
                        <el-col :span="12">
                            <smart-verify class="verify" src="member_profile/email_verify"
                                          height="32px" :target.sync="data.email"
                                          :captcha.sync="data.captcha" :error="onVerifyError"></smart-verify>
                        </el-col>
                    </el-row>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" :loading="loading" @click="doSubmit">确认修改</el-button>
                </el-form-item>
            </el-form>

        </panel-box-body>

    </panel-content>
</template>

<script>

    import MemberProfileNav from "./MemberProfileNav";
    import {Dialog} from "../lib/dialog";
    import {JsonUtil} from "../lib/util";
    import {EventBus} from "../lib/event-bus";

    export default {
        metaInfo: {
            title: '绑定邮箱'
        },
        components: {MemberProfileNav},
        data() {
            return {
                loading: false,
                change: false,
                data: {
                    email: '',
                    verify: '',
                    captcha: '',
                }
            }
        },
        mounted() {
        },
        methods: {
            doSubmit() {
                this.loading = true
                this.$api.post('member_profile/email', this.data, res => {
                    this.loading = false
                    this.change = false
                    JsonUtil.clearObject(this.data)
                    EventBus.$emit('UpdateApp')
                    Dialog.tipSuccess('修改成功')
                }, res => {
                    this.loading = false
                })
            },
            onVerifyError() {
                this.$refs.captcha.refresh()
            }
        },
    }
</script>
