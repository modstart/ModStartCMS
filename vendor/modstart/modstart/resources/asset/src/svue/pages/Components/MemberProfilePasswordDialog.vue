<template>
    <el-drawer
            class="pb-member-profile-password-dialog"
            :visible.sync="visible"
            append-to-body
            :size="width"
            :before-close="onBeforeClose"
            ref="drawer"
            direction="rtl"
    >
        <div slot="title">
            修改密码
        </div>
        <div class="body">
            <el-form label-width="100px">
                <el-form-item label="原密码 : ">
                    <el-input type="password" style="max-width:20em;" v-model="data.passwordOld"></el-input>
                </el-form-item>
                <el-form-item label="新密码 : ">
                    <el-input type="password" style="max-width:20em;" v-model="data.passwordNew"></el-input>
                </el-form-item>
                <el-form-item label="确认新密码 : ">
                    <el-input type="password" style="max-width:20em;" v-model="data.passwordRepeat"></el-input>
                </el-form-item>
            </el-form>
        </div>
        <div class="foot">
            <el-button style="width:6em;" @click="$refs.drawer.closeDrawer()">取消</el-button>
            <el-button style="width:6em;" type="primary" :loading="loading" @click="doSubmit">确定</el-button>
        </div>
    </el-drawer>

</template>

<script>
    import {Dialog} from "../../lib/dialog";
    import $ from 'jquery'

    export default {
        name: 'MemberProfilePasswordDialog',
        data() {
            return {
                visible: false,
                loading: false,
                direction: 'rtl',
                width:null,
                data: {
                    passwordOld: '',
                    passwordNew: '',
                    passwordRepeat: '',
                }
            }
        },
        mounted() {
            this.width = Math.min($(window).width(),400)+'px'
        },
        methods: {
            show() {
                this.visible = true
                this.data.passwordOld = ''
                this.data.passwordNew = ''
                this.data.passwordRepeat = ''
            },
            doSubmit() {
                this.loading = true
                this.$api.post('member_profile/password', this.data, res => {
                    this.loading = false
                    this.visible = false
                    Dialog.tipSuccess('修改成功，请重新登录', () => {
                        routeReplace('logout')
                    })
                }, res => {
                    this.loading = false
                })
            },
            isDirty() {
                return this.data.passwordOld || this.data.passwordNew || this.data.passwordRepeat
            },
            onBeforeClose(done) {
                if (!this.isDirty()) {
                    done()
                    return
                }
                this.$dialog.confirm('您的数据已经修改，关闭将丢失数据，确认继续吗？', () => done())
            }
        },
    }
</script>
