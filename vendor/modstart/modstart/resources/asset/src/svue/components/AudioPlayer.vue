<template>
    <div class="ppb-audio-player">
        <a v-if="!!downloadUrl" class="download" :href="downloadUrl" target="_blank" data-uk-tooltip title="下载">
            <i class="iconfont">&#xe62c;</i>
        </a>
        <span data-time-total class="time-total">{{durationTime}}</span>
        <a v-if="!playing" class="play" href="javascript:;" @click="playing=true">
            <i class="iconfont">&#xe636;</i>
        </a>
        <a v-if="playing" class="pause" href="javascript:;" @click="playing=false">
            <i class="iconfont">&#xe6a3;</i>
        </a>
        <span data-time-play class="time-play">{{currentTime}}</span>
        <div class="bar-box">
            <div class="bar" @click="seek">
                <div class="bar-played" :style="{width:percentComplete+'%'}"></div>
            </div>
        </div>
        <audio ref="audio" :src="source"></audio>
    </div>
</template>

<script>
    const convertTimeHHMMSS = (val) => {
        let hhmmss = new Date(val * 1000).toISOString().substr(11, 8);
        return hhmmss.indexOf("00:") === 0 ? hhmmss.substr(3) : hhmmss;
    };

    export default {
        props: {
            source: {
                type: String,
                default: ''
            },
            downloadUrl: {
                type: String,
                default: ''
            }
        },
        data() {
            return {
                audio: undefined,
                loaded: false,
                playing: false,
                durationSeconds: 0,
                currentSeconds: 0,
            }
        },
        computed: {
            currentTime() {
                return convertTimeHHMMSS(this.currentSeconds);
            },
            durationTime() {
                return convertTimeHHMMSS(this.durationSeconds);
            },
            percentComplete() {
                return parseInt(this.currentSeconds / this.durationSeconds * 100);
            },
            muted() {
                return this.volume / 100 === 0;
            }
        },
        watch: {
            source(newValue, oldValue) {
                if (newValue && newValue != oldValue) {
                    this.init()
                }
            },
            playing(value) {
                if (value) {
                    return this.audio.play();
                }
                this.audio.pause();
            }
        },
        mounted() {
            this.audio = this.$refs.audio
            this.audio.addEventListener('timeupdate', this.timeupdate);
            this.audio.addEventListener('loadeddata', this.loadeddata);
            this.audio.addEventListener('pause', () => {
                this.playing = false
            });
            this.audio.addEventListener('play', () => {
                this.playing = true
            });
        },
        methods: {
            init() {
            },
            seek(e) {
                if (!this.playing || e.target.tagName === 'div') {
                    return;
                }
                const el = e.target.getBoundingClientRect();
                const seekPos = (e.clientX - el.left) / el.width;
                this.audio.currentTime = parseInt(this.audio.duration * seekPos);
            },
            timeupdate(e) {
                this.currentSeconds = parseInt(this.audio.currentTime);
            },
            loadeddata() {
                if (this.audio.readyState >= 2) {
                    this.loaded = true
                    this.durationSeconds = parseInt(this.audio.duration)
                    return
                }
                throw new Error('Failed to load sound file.');
            },
            play() {
                this.audio.play()
            }
        }
    }
</script>

<style lang="less">


    .ppb-audio-player {
        background: #FFF;
        border: 1px solid #EEE;
        height: 40px;
        line-height: 40px;
        border-radius: 3px;

        .download, .play, .pause, .time-play, .time-total {
            display: block;
            text-align: center;
            float: left;
            width: 40px;
            color: #333;
            line-height: 40px;
            font-size: 12px;
        }

        .download, .play, .pause {
            font-size: 20px;

            i {
                font-size: 20px;
            }

            &:hover {
                color: var(--color-primary, #419488);
            }
        }

        .download, .time-total {
            float: right;
        }

        .bar-box {
            padding-left: 80px;
            padding-right: 80px;

            .bar {
                margin-top: 10px;
                height: 20px;
                background: #EEE;
                border-radius: 5px;

                .bar-played {
                    border-radius: 5px;
                    height: 20px;
                    background: #999;
                    width: 1%;
                }
            }
        }
    }
</style>
