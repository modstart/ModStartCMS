<template>
    <div class="item">
        <smart-link v-if="!!to" :to="to">
            <div class="icon" v-if="icon"><i :class="icon" :style="'color:'+color"></i></div>
            <div class="image" v-if="image" :style="{backgroundImage:'url('+image+')'}"></div>
            <div class="number" v-if="number">{{number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}}</div>
            <div class="number" v-if="!number">-</div>
            <div class="text">{{text}}</div>
        </smart-link>
        <template v-if="!to">
            <div class="icon" v-if="icon"><i :class="icon" :style="'color:'+color"></i></div>
            <div class="image" v-if="image" :style="{backgroundImage:'url('+image+')'}"></div>
            <div class="number" v-if="number">{{number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}}</div>
            <div class="number" v-if="!number">-</div>
            <div class="text">{{text}}</div>
        </template>
    </div>
</template>

<style lang="less" scoped>
    .item {
        background: #FFF;
        border-radius: 5px;
        padding: 20px 20px 20px 90px;
        a {
            display: block;
        }
        .image{
            float: left;
            margin: 0 0 0 -70px;
            width: 55px;
            height:55px;
            background:center no-repeat;
            background-size: contain;
        }
        .icon {
            float: left;
            margin: 0 0 0 -70px;
            text-align: center;
            width: 55px;
            height:55px;
            line-height:55px;
            border-radius:50%;
            border:2px solid #EEE;
            i {
                font-size: 40px;
                color: #999;
            }
        }
        .number {
            line-height: 35px;
            font-size: 22px;
            color: #666;
            font-weight: bold;
        }
        .text {
            line-height: 20px;
            color: #999;
        }
    }
</style>

<script>
    export default {
        props: ['icon', 'image', 'color', 'number', 'text', 'to']
    }
</script>