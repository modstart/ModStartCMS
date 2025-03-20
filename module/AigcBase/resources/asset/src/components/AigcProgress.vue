<script>
export default {
    name: "AigcProgress",
    props: {
        auto: {
            type: Number,
            default: 0
        },
        percent: {
            type: Number,
            default: 0
        },
        text: {
            type: String,
            default: '正在加载中...'
        }
    },
    computed: {
        showPercent() {
            return parseInt(this.auto ? this.currentPercent : this.percent);
        }
    },
    data() {
        return {
            autoTimer: null,
            currentPercent: this.percent
        }
    },
    mounted() {
        if (this.auto > 0) {
            this.autoProgress();
        }
    },
    beforeDestroy() {
        clearInterval(this.autoTimer);
    },
    methods: {
        autoProgress() {
            const totalSecond = this.auto
            const updateInterval = 100
            this.currentPercent = 0;
            const step = 100 / (totalSecond * 1000 / updateInterval);
            this.autoTimer = setInterval(() => {
                this.currentPercent += step;
                if (this.currentPercent >= 99) {
                    this.currentPercent = 99;
                    clearInterval(this.autoTimer);
                }
            }, updateInterval);
        }
    }
}
</script>

<template>
    <div>
        <div>
            <el-progress type="circle" :percentage="showPercent"></el-progress>
        </div>
        <div class="tw-pt-2">
            {{ text }}
        </div>
    </div>
</template>
