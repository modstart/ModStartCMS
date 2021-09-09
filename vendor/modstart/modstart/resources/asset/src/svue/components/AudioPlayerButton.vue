<template>
  <span>
     <a v-if="!playing" class="play" href="javascript:;" @click="playing=true">
      <i class="iconfont">&#xe636;</i>
    </a>
    <a v-if="playing" class="pause" href="javascript:;" @click="playing=false">
      <i class="iconfont">&#xe6a3;</i>
    </a>
    <audio ref="audio" :src="source"></audio>
  </span>
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
      }
    },
    data() {
      return {
        audio: undefined,
        loaded: false,
        playing: false
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
      loadeddata() {
        if (this.audio.readyState >= 2) {
          this.loaded = true
          this.durationSeconds = parseInt(this.audio.duration)
          return
        }
        throw new Error('Failed to load sound file.');
      },
    }
  }
</script>
