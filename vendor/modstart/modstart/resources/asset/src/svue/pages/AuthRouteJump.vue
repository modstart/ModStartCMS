<template>
    <div></div>
</template>

<script>
    import {authRouteJump} from './../store/auth'

    export default {
        components: {},
        metaInfo: {
            title: '...'
        },
        data() {
            return {}
        },
        mounted() {
            authRouteJump(this.$route.meta.authRouteJump)
        },
        methods: {}
    }
</script>
