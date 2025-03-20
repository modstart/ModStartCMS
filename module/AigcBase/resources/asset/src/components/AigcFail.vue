<script>
export default {
    name: "AigcFail",
    props: {
        text: {
            type: String,
            default: '失败了'
        }
    },
}
</script>

<template>
    <div>
        <div class="ub-empty">
            <div class="icon">
                <i class="iconfont icon-warning ub-text-danger"></i>
            </div>
            <div class="text">
                {{ text }}
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
