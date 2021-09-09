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
                <el-steps :active="0" align-center>
                    <el-step title="选择方式"></el-step>
                    <el-step title="验证身份"></el-step>
                    <el-step title="重置密码"></el-step>
                </el-steps>
            </div>

            <el-form>
                <el-form-item v-if="$store.state.base.config.retrievePhoneEnable">
                    <el-button size="large" style="width:100%;" @click="choose('retrieve_phone')">通过手机找回</el-button>
                </el-form-item>
                <el-form-item v-if="$store.state.base.config.retrieveEmailEnable">
                    <el-button size="large" style="width:100%;" @click="choose('retrieve_email')">通过邮箱找回</el-button>
                </el-form-item>
            </el-form>

        </div>
    </div>
</template>

<script>
    import {routePush} from "../../main/router";

    export default {
        metaInfo: {
            title: '找回密码'
        },
        components: {
            Logo: () => import('./Login/Logo')
        },
        data() {
            return {}
        },
        methods: {
            choose(path) {
                routePush(path)
            }
        }
    }
</script>

