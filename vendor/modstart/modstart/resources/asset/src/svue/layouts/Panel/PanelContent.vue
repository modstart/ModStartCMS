<template>
    <div class="pb-panel-content" ref="panelContent">
        <slot></slot>
    </div>
</template>

<script>
    import $ from 'jquery'

    export default {
        created() {
            setTimeout(() => this.$parent.$emit('updatePageMenu', this.$slots.panelPageMenu), 100)
        },
        beforeUpdate() {
            this.$parent.$emit('updatePageMenu', this.$slots.panelPageMenu)
        }
    }
</script>

<style lang="less">
    .pb-panel-content {
        position: relative;
        overflow: auto;
        padding: 0 10px 10px 10px;
    }
</style>
