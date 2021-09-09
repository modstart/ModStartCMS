<template>
    <div>
        <div class="logo">
            <div class="image">
                <img :src="$store.state.base.config.siteLogo"/>
            </div>
        </div>
    </div>
</template>

<style lang="less" scoped>
    .logo {
        text-align: center;
        height: 40px;
        .image {
            display: inline-block;
            margin-right: 5px;
        }
        img {
            height: 40px;
            vertical-align: middle;
        }
        font-size: 20px;
        line-height: 40px;
        vertical-align: middle;
        color: #333;
    }
</style>

<script>
    export default {
        name: 'Logo'
    }
</script>
