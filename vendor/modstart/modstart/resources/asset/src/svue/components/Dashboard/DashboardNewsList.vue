<template>
    <panel-box>
        <div slot="title">
            <i class="iconfont icon-list"></i>
            最新动态
        </div>
        <div class="ub-empty" v-if="!loading && list.length===0">
            暂无动态
        </div>
        <ul class="list" v-for="item in list" v-loading="loading">
            <li><a :href="item.link" target="_blank">{{item.title}}</a></li>
        </ul>
    </panel-box>
</template>

<style lang="less" scoped>
    .list {
        padding: 0 0 0 15px;
        margin: 0;
        li {
            padding: 0;
            margin: 0;
            list-style: disc outside;
            a {
                display: block;
                color: #666;
                &:hover {
                    color: var(--color-primary, #419488);
                }
            }
        }
    }
</style>

<script>
    export default {
        props: {
            loading: {
                type: Boolean,
                default: false
            },
            list: {
                type: Array,
                default: () => []
            }
        }
    }
</script>
