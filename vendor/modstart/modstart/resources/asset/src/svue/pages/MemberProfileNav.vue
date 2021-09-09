<template>
    <div>
        <smart-link :class="{active:$route.path==='/member_profile'}" to="member_profile">我的信息</smart-link>
        <smart-link v-if="$store.state.base.config.avatarDisable!==true":class="{active:$route.path==='/member_profile/avatar'}" to="member_profile/avatar">修改头像</smart-link>
        <smart-link :class="{active:$route.path==='/member_profile/password'}" to="member_profile/password">修改密码</smart-link>
        <smart-link v-if="$store.state.base.config.registerPhoneEnable" :class="{active:$route.path==='/member_profile/phone'}" to="member_profile/phone">绑定手机</smart-link>
        <smart-link v-if="$store.state.base.config.registerEmailEnable" :class="{active:$route.path==='/member_profile/email'}" to="member_profile/email">绑定邮箱</smart-link>
    </div>
</template>

<script>
    export default {
        name: 'MemberProfileNav'
    }
</script>
