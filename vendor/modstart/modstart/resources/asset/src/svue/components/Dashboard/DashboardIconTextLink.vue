<template>
    <div class="item">
        <smart-link :to="link">
            <div class="icon"><i :class="icon" :style="'color:'+color"></i></div>
            <div class="text">{{text}}</div>
        </smart-link>
    </div>
</template>

<style lang="less" scoped>
    .item {
        background: #FFF;
        border-radius: 5px;
        padding: 10px 10px 10px 40px;
        border: 1px solid #EEE;
        &:hover {
            box-shadow: 0 0 5px #EEE;
        }
        a {
            display: block;
        }
        .icon {
            float: left;
            margin: 0 0 0 -30px;
            text-align: center;
            width: 20px;
            line-height: 20px;
            i {
                font-size: 20px;
                color: #999;
            }
        }
        .text {
            line-height: 20px;
            color: #999;
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