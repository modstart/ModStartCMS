<template>
  <div>
    <div class="oauth" v-if="false">
      <div class="title">
        您还可以使用以下方式登录
      </div>
      <div class="body">
        <a href="javascript:;" @click="authDialog('wechat')" class="wechat"><i class="iconfont icon-wechat"></i></a>
        <a href="javascript:;" @click="authDialog('qq')" class="qq"><i class="iconfont icon-qq"></i></a>
        <a href="javascript:;" @click="authDialog('weibo')" class="weibo"><i class="iconfont icon-weibo"></i></a>
      </div>
    </div>

    <el-dialog
      title="授权登录"
      :visible.sync="dialogShow">
      <div>
        <iframe style="width:100%;height:400px;border:1px solid #EEE;" src="https://open.weixin.qq.com/connect/qrconnect?appid=" frameborder="0"></iframe>
      </div>
    </el-dialog>

  </div>
</template>

<script>
  export default {
    data() {
      return {
        type: null,
        dialogShow: false,
      }
    },
    methods: {
      authDialog(type) {
        this.type = type
        this.dialogShow = true
      }
    }
  }
</script>

<style lang="less" scoped>
  .oauth {
    padding-top: 20px;
    text-align: center;
    color: #999;
    .title {

    }
    .body {
      padding: 20px 0;
    }
    a {
      display: inline-block;
      width: 50px;
      height: 50px;
      background: #999;
      color: #FFF;
      border-radius: 50%;
      text-align: center;
      margin: 0 10px;
      i {
        font-size: 30px;
        line-height: 50px;
      }
      &.qq {
        background: #498ad5;
      }
      &.weibo {
        background: #e05244;
      }
      &.wechat {
        background: #00bb29;
      }
    }
  }
</style>
