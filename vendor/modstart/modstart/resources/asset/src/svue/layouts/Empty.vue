<template>
    <div>
        <router-view ref="page"/>
    </div>
</template>

<script>
    export default {
        metaInfo() {
            return {
                title: '空页面',
                titleTemplate: '%s' + (this.$store.state.base.config.siteName ? ' - ' + this.$store.state.base.config.siteName : '')
            }
        },
        data() {
            return {}
        }
    }

</script>

<style lang="less">
    body, html {
        background: #F8F8F8;
    }
</style>
