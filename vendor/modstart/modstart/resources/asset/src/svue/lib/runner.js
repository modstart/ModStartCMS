const Runner = function () {
  var me = this
  this.push = function (call) {
    setTimeout(() => call(), 0)
    return me
  }
}
export {
  Runner
}
