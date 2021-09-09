<template>

</template>

<script>
    export default {
        name: "RedirectWindow",
        mounted() {
            setTimeout(() => {
                window.location.reload()
            }, 1000)
        }
    }
</script>

<style scoped>

</style>