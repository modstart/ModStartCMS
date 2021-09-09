<template>
    <div class="pb-panel-box">
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
        .content {
            padding: 10px;
        }
    }
</style>

<script>
    export default {}
</script>
