<template>
    <div>

        <div class="pb-layout-panel-menu" :style="{top:headMenuTop}"
             v-bind:class="{'collapse':menuCollapse}">
            <smart-link to="" v-if="$store.state.panel.style.showLogo">
                <div class="logo">
                    <div class="cover"
                         :style="{backgroundImage:`url(${$store.state.base?$store.state.base.config.siteLogo:''})`}"></div>
                </div>
            </smart-link>
            <PanelMenu @onPanelMenuHide="onPanelMenuHide" :collapse.sync="menuCollapse"></PanelMenu>
            <div class="mask" :style="{top:headMenuTop}"></div>
        </div>

        <div class="pb-layout-panel-head" :style="{top:headTop}"
             v-bind:class="{'menu-collapse':menuCollapse}">
            <div class="right-toggle" v-if="$store.state.panel.style.showHeadRightTool">
                <a href="javascript:;" @click="showRight=!showRight">
                    <i class="iconfont icon-user"></i>
                </a>
            </div>
            <div class="right" v-if="$store.state.panel.style.showHeadRightTool && (isPC || showRight)">
                <smart-link class="item" to="member_message" @click="showRight=false">
                    <i class="iconfont icon-bell"></i>
                    消息
                    <span class="badge" v-if="$store.getters.app && $store.getters.app.message.newMessageCount>0">
                        {{$store.getters.app.message.newMessageCount}}
                    </span>
                </smart-link>
                <el-dropdown class="item">
                    <span class="el-dropdown-link">
                        <img v-if="$store.state.base && $store.state.base.config.avatarDisable!==true"
                             class="avatar" :src="$store.state.user.avatar"/>
                        <span v-if="$store.getters.app && $store.state.user.nickname">{{$store.state.user.nickname}}</span>
                        <span v-if="$store.getters.app && !$store.state.user.nickname">{{$store.state.user.username}}</span>
                        <i class="el-icon-arrow-down el-icon--right"></i>
                    </span>
                    <el-dropdown-menu slot="dropdown" class="pb-layout-panel-dropdown">
                        <el-dropdown-item>
                            <smart-link to="member_profile" @click="showRight=false">我的信息</smart-link>
                        </el-dropdown-item>
                        <el-dropdown-item>
                            <smart-link to="member_profile/password" @click="showRight=false">修改密码</smart-link>
                        </el-dropdown-item>
                        <el-dropdown-item>
                            <smart-link to="logout" confirm="即将退出登录，请确认？" @click="showRight=false">退出登录</smart-link>
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
            </div>
            <div class="menu-toggle">
                <a href="javascript:;" @click="menuToggle" v-bind:class="{'menu-collapse':menuCollapse}">
                    <i class="iconfont icon-list"></i>
                </a>
            </div>
            <div class="nav">
                <component :is="pageMenuLayout">
                    <router-view :layout.sync="pageMenuLayout"></router-view>
                </component>
            </div>
        </div>

        <div class="pb-layout-panel-body-wrap" v-bind:class="{'menu-collapse':menuCollapse}">
            <div class="pb-layout-panel-body-content">
                <router-view ref="mainView" @updatePageMenu="handleUpdatePageMenu"></router-view>
            </div>
        </div>
    </div>
</template>

<script src="./PanelScript.js"></script>
<style lang="less" src="./Panel.less"></style>
