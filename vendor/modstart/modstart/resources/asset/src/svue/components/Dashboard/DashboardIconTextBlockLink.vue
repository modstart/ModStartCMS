<template>
    <div class="item">
        <smart-link :to="link">
            <div class="icon" :style="{color:color}"><i :class="icon"></i></div>
            <div class="text">{{text}}</div>
        </smart-link>
    </div>
</template>

<style lang="less" scoped>
    .item {
        background: #FFF;
        border-radius: 5px;
        padding: 40px 20px;
        text-align: center;
        margin-bottom:5px;
        a {
            display: block;
        }
        .icon {
            border-radius: 5px;
            width: 50px;
            height: 50px;
            line-height: 50px;
            display: block;
            margin: 0 auto;
            color:#EEE;
            i {
                font-size: 30px;
                display: block;
                line-height: 50px;
            }
        }
        .text {
            line-height: 20px;
            color: #666;
            height: 20px;
            overflow: hidden;
            white-space: normal;
            text-overflow: ellipsis;
        }
    }
</style>

<script>
    export default {
        props: ['icon', 'color', 'link', 'text']
    }
</script>