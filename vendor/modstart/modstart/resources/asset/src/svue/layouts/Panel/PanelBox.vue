<template>
    <div class="pb-panel-box">
        <div class="title">
            <div class="more">
                <slot name="more"></slot>
            </div>
            <slot name="title"></slot>
        </div>
        <div class="content">
            <slot></slot>
        </div>
    </div>
</template>

<style lang="less">
    .pb-panel-box {
        background: #FFF;
        border-radius: 3px;
        margin-top: 10px;
        & > .title {
            border-bottom: 1px solid #EEE;
            padding: 10px;
            color: #333;
            overflow: hidden;
            line-height:28px;
            .more {
                float: right;
            }
            &:empty {
                display: none;
            }
        }
        & > .content {
            padding: 10px;
        }
    }
</style>

<script>
    export default {}
</script>
