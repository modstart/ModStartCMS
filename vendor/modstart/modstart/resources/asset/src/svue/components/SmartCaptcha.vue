<template>
  <el-tooltip class="item" effect="dark" content="点击刷新" placement="top">
    <img class="captcha" @click="refresh" :style="{height:height}" :src="image"/>
  </el-tooltip>
</template>

<script>
  const empty = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAwAAAAOCAYAAAAbvf3sAAAAH0lEQVQoU2P88ePHfwYSAOOoBiJCazSUiAgkBpJDCQD/sTaxd/eHJAAAAABJRU5ErkJggg=='
  export default {
    props: {
      src: {
        type: String,
        default: '',
      },
      height: {
        type: String,
        default: '40px'
      }
    },
    data() {
      return {
        image: empty,
      }
    },
    mounted() {
      this.refresh()
    },
    methods: {
      refresh() {
        this.image = empty
        this.$api.post(this.src, {}, res => {
          this.image = res.data.image
        })
      }
    }
  }
</script>

<style lang="less" scoped>
  .captcha {
    width: 100%;
    cursor: pointer;
  }
</style>
