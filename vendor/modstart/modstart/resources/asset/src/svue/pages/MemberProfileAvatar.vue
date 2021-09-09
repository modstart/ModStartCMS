<template>
    <panel-content>

        <MemberProfileNav slot="panelPageMenu"/>

        <panel-box-body>

            <el-avatar shape="square" :size="200" fit="fill" :src="$store.state.user.avatarBig"></el-avatar>
            <el-avatar style="margin-left:10px;" shape="square" :size="40" fit="fill"
                       :src="$store.state.user.avatarBig"></el-avatar>
            <el-avatar style="margin-left:10px;" :size="40" fit="fill"
                       :src="$store.state.user.avatarBig"></el-avatar>

            <div class="text-center">
                <el-button :loading="loading" id="avatarChange" style="outline:none;">修改头像</el-button>
                <avatar-cropper
                        trigger="#avatarChange"
                        :upload-handler="doSubmit"/>
            </div>
        </panel-box-body>

    </panel-content>
</template>

<style lang="less">


    .avatar-cropper .avatar-cropper-container .avatar-cropper-footer .avatar-cropper-btn {
        &:hover {
            background: var(--color-primary, #419488);
        }
    }
</style>

<script>

    import MemberProfileNav from "./MemberProfileNav";
    import {Dialog} from "../lib/dialog";
    import {EventBus} from "../lib/event-bus";
    import AvatarCropper from "vue-avatar-cropper"


    export default {
        metaInfo: {
            title: '修改头像'
        },
        components: {MemberProfileNav, AvatarCropper},
        data() {
            return {
                loading: false,
                data: {}
            }
        },
        mounted() {
        },
        methods: {
            doSubmit(cropper) {
                const image = cropper.getCroppedCanvas({maxWidth:400,maxHeight:400}).toDataURL('image/png')
                this.loading = true
                this.$api.post('member_profile/avatar', {type: 'cropper', avatar: image}, res => {
                    this.loading = false
                    EventBus.$emit('UpdateApp')
                    Dialog.tipSuccess('修改成功')
                }, res => {
                    this.loading = false
                })
            }
        },
    }
</script>
