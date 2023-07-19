<template>
    <div class="pb-page-404">
        <div class="box" v-if="!isMigrate">
            <div class="title">
                404
            </div>
            <div class="summary">
                您访问的页面不存在
            </div>
            <div class="margin-top">
                <smart-link to=""><i class="iconfont icon-home"></i> 点击访问首页</smart-link>
            </div>
        </div>
        <div class="box" v-if="isMigrate">
            <div class="title">
                <i class="iconfont icon-smile"></i>
            </div>
            <div class="summary">
                您正在访问一个过时的页面，即将跳转到新页面...
                <br><br>
                <span class="ub-text-small">{{migrateInfo}}</span>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        metaInfo() {
            return {
                title: this.isMigrate ? '正在跳转' : '找不到页面'
            }
        },
        data() {
            return {
                isMigrate: false,
                migrateInfo: ''
            }
        },
        mounted() {
            const fullPath = this.$route.fullPath
            if (this.$migrateUrls) {
                this.$migrateUrls.forEach(p => {
                    if (p[0].test(fullPath)) {
                        this.isMigrate = true
                        const redirect = fullPath.replace(p[0], p[1])
                        console.log('migrate -> ', p[0], p[1], redirect)
                        this.migrateInfo = fullPath + ' -> ' + redirect
                        setTimeout(() => {
                            this.$router.replace(redirect)
                        }, 3000)
                    }
                })
            }
        }
    }
</script>

<style lang="less">
    .pb-page-404 {
        text-align: center;
        flex-grow: 1;

        .box {
            color: #CCC;
            margin: 50px 0;

            .title {
                font-size: 100px;
                line-height: 150px;
            }
        }
    }
</style>
