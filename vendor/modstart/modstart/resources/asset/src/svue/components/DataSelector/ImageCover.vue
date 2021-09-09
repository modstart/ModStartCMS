<template>
    <div class="pb-image-cover" :style="{'background-image':'url('+src+')'}">
        <div class="b" :style="{'margin-top':100*height/width+'%'}"></div>
    </div>
</template>

<script>
    export default {
        props: {
            src: {
                type: String,
                default: '',
            },
            ratio: {
                type: Number,
                default: null,
            },
        },
        data() {
            return {
                height: 1,
                width: 1,
            }
        },
        mounted() {
            if (this.ratio) {
                let pcs = this.ratio.split(':')
                this.width = pcs[0]
                this.height = pcs[1]
            }
        },
    }
</script>

<style lang="less">
    .pb-image-cover {
        display: block;
        background-size: contain;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 3px;
        border: 1px solid #EEE;
        .b {
            overflow: hidden;
        }
    }
</style>
