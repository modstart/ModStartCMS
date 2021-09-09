<template>
  <div>
    <div class="retrieve" v-if="!$store.state.base.config.retrieveDisable">
      忘记密码?
      <smart-link to="retrieve">找回密码</smart-link>
    </div>
  </div>
</template>

<style lang="less" scoped>
  .retrieve {
    text-align: center;
    color: #999;
  }
</style>
