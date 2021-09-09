<template>
    <div class="pb-panel-bread-crumb">
        <slot></slot>
    </div>
</template>

<script>
    export default {
        name: "PanelBreadCrumb"
    }
</script>

<style scoped lang="less">
    .pb-panel-bread-crumb {
        padding: 10px 0;
        a {
            color: rgb(108, 122, 135);
            display: inline-block;
            line-height: 20px;
            &:after {
                content: '/';
                display: inline-block;
                padding: 0 5px;
                color: #CCC;
                line-height: 20px;
            }
            &:last-child {
                &:after {
                    display: none;
                }
            }
        }
    }
</style>