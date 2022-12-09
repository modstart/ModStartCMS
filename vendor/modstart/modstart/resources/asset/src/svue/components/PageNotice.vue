<template>
    <div>
        <audio ref="audio" :src="sound"></audio>
    </div>
</template>

<script>
export default {
    name: "PageNotice",
    props: {
        sound: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            tipTimer: null,
            tipTimerCount: 0,
            titleOld: null,
        }
    },
    methods: {
        tip(msg, type) {
            this.doPageTitle(msg, type)
            if (this.sound) {
                this.doPlaySound()
            }
        },
        doClearTimer() {
            clearInterval(this.tipTimer)
            this.tipTimer = null
            if (this.titleOld) {
                window.document.title = this.titleOld
            }
            this.tipTimerCount = 0
        },
        doPageTitle(msg, type) {
            type = type || '提醒'
            if (this.tipTimer) {
                this.doClearTimer()
            }
            this.titleOld = document.title
            this.tipTimerCount = 0
            this.tipTimer = setInterval(() => {
                this.tipTimerCount++
                if (this.tipTimerCount % 2 === 0) {
                    window.document.title = '【' + type + '】' + msg
                } else {
                    window.document.title = '' + msg
                }
                if (this.tipTimerCount > 6) {
                    this.doClearTimer()
                }
            }, 500)
        },
        doPlaySound() {
            let audio = this.$refs.audio
            if (!audio) {
                return
            }
            try {
                audio.pause()
                audio.play()
            } catch (e) {
            }
        }
    }
}
</script>
