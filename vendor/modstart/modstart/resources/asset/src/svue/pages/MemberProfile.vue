<template>

    <panel-content>

        <MemberProfileNav slot="panelPageMenu"/>

        <panel-box-body>

            <el-form label-width="100px">
                <el-form-item label="ID">
                    {{$store.state.user.id}}
                </el-form-item>
                <el-form-item label="昵称">
                    {{$store.state.user.nickname}}
                </el-form-item>
                <el-form-item label="手机">
                    {{$store.state.user.phone}}
                </el-form-item>
            </el-form>

        </panel-box-body>

    </panel-content>
</template>

<script>

    import MemberProfileNav from "./MemberProfileNav";

    export default {
        metaInfo: {
            title: '我的信息'
        },
        components: {MemberProfileNav},
        data() {
            return {}
        },
        mounted() {
            this.doInit()
        },
        methods: {
            doInit() {

            },
        },
    }
</script>
