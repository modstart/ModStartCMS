<template>
    <span>
        {{hour}}:{{minute}}:{{second}}
    </span>
</template>

<script>
    import {StrUtil} from './../lib/util'

    export default {
        name: "DatetimeCountDown",
        props: {
            timeLeft: {
                type: Number,
                default: 0,
            }
        },
        data() {
            return {
                timer: null,
                seconds: null,
            }
        },
        watch: {
            timeLeft: {
                handler(n, o) {
                    this.seconds = Math.max(n, 0)
                },
                immediate: true
            }
        },
        computed: {
            hour() {
                if (this.seconds <= 0) {
                    return '00'
                }
                return StrUtil.sprintf('%02d', Math.floor(this.seconds / 3600))
            },
            minute() {
                if (this.seconds <= 0) {
                    return '00'
                }
                return StrUtil.sprintf('%02d', Math.floor((this.seconds - this.hour * 3600) / 60))
            },
            second() {
                if (this.seconds <= 0) {
                    return '00'
                }
                return StrUtil.sprintf('%02d', (this.seconds % 60))
            }
        },
        mounted: function () {
            this.timer = setInterval(() => {
                this.doUpdate()
            }, 1000)
        },
        beforeDestroy() {
            clearInterval(this.timer)
        },
        methods: {
            doUpdate() {
                this.seconds--
            }
        }
    }
</script>

<style scoped>

</style>
