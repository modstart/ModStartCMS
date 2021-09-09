<template>
    <panel-content>

        <MemberProfileNav slot="panelPageMenu"/>

        <panel-box-body>
            <el-form label-width="100px">
                <el-form-item label="旧密码">
                    <el-input type="password" style="max-width:20em;" v-model="data.passwordOld"></el-input>
                </el-form-item>
                <el-form-item label="新密码">
                    <el-input type="password" style="max-width:20em;" v-model="data.passwordNew"></el-input>
                </el-form-item>
                <el-form-item label="重复密码">
                    <el-input type="password" style="max-width:20em;" v-model="data.passwordRepeat"></el-input>
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
    import {routeReplace} from "../../main/router";

    export default {
        metaInfo: {
            title: '修改密码'
        },
        components: {MemberProfileNav},
        data() {
            return {
                loading: false,
                data: {
                    passwordOld: '',
                    passwordNew: '',
                    passwordRepeat: '',
                }
            }
        },
        mounted() {
        },
        methods: {
            doSubmit() {
                this.loading = true
                this.$api.post('member_profile/password', this.data, res => {
                    this.loading = false
                    Dialog.tipSuccess('修改成功，请重新登录', () => {
                        routeReplace('logout')
                    })
                }, res => {
                    this.loading = false
                })
            },
        },
    }
</script>
