<template>
    <div class="item">
        <div class="number">{{number!==null?number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","):'-'}}</div>
        <div class="text">{{text}}</div>
    </div>
</template>

<style lang="less" scoped>
    .item {
        background: #FFF;
        border-radius: 5px;
        text-align: center;
        padding:10px 0;
        .number {
            line-height: 35px;
            font-size: 22px;
            color: #666;
        }
        .text {
            line-height: 20px;
            color: #999;
        }
    }
</style>

<script>
    export default {
        props: ['number', 'text']
    }
</script>