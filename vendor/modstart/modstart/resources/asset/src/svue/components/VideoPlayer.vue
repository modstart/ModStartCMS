<template>
    <div ref="videoContainer" :style="{ width: width, height: height }"></div>
</template>

<script>
import Player from 'xgplayer';
import 'xgplayer/dist/index.min.css';

export default {
    props: {
        url: {
            type: String,
            default: '',
        },
        width: {
            type: String,
            default: '100%',
        },
        height: {
            type: String,
            default: '100%',
        },
        autoplay: {
            type: Boolean,
            default: false,
        },
        autoplayMuted: {
            type: Boolean,
            default: false,
        },
        loop: {
            type: Boolean,
            default: false,
        },
        controls: {
            type: Boolean,
            default: true,
        },
    },
    data() {
        return {
            player: null,
        };
    },
    mounted() {
        this.player = new Player({
            el: this.$refs.videoContainer,
            url: this.url,
            width: this.width,
            height: this.height,
            autoplay: this.autoplay,
            muted: this.autoplayMuted,
            loop: this.loop,
            controls: this.controls,
        });
    },
    beforeDestroy() {
        if (this.player) {
            this.player.destroy();
        }
    },
};
</script>
