<template>
</template>

<script>
    import {LazyValue} from "../lib/lazyvalue";

    export default {
        name: "LazyValue",
        model: {
            prop: 'data',
            event: 'update'
        },
        props: {
            data: {
                type: Object,
                default: () => {
                    return {
                        loading: false,
                        value: {},
                    }
                }
            },
            url: {
                type: String,
                default: '',
            }
        },
        mounted() {
            this.data.loading = true
            this.sync()
            new LazyValue()
                .url(this.url)
                .fetch((url, cb) => {
                    this.$api.post(url, {}, res => {
                        cb(res)
                    }, res => {
                        cb(res)
                    })
                })
                .update(value => {
                    this.data.value = value
                    this.sync()
                })
                .finish(() => {
                    this.data.loading = false
                    this.sync()
                })
                .start()
        },
        methods: {
            sync() {
                this.$emit('update', JSON.parse(JSON.stringify(this.data)))
            }
        }
    }
</script>

<style scoped>

</style>
