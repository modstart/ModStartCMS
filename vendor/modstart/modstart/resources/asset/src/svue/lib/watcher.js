import {UUID} from "./util";

export const Watcher = function () {
  let me = this
  this._worker = []
  this._timer = null

  this._schedule = function () {
    let now = new Date().getTime()
    me._worker = me._worker.filter(worker => !worker._toRemove)
    let workers = me._worker.filter(worker => worker._reserve < now)
    // console.log('>> Watcher.schedule', workers)
    workers.forEach(worker => {
      worker._callback(worker)
      worker._reserve = now + worker._interval
    })
  }
  this.push = function (worker) {
    me._worker.push(worker)
    return me
  }
  this.remove = function (workerId) {
    me._worker = me._worker.filter(workerId => workerId._id !== workerId)
    return me
  }
  this.removeAll = function () {
    me._worker = []
    return me
  }
  this.start = function () {
    me._timer = setInterval(() => me._schedule(), 500)
    return me
  }
  this.stop = function () {
    if (me._timer) {
      clearInterval(me._timer)
    }
  }
}

export const WatcherWorker = function () {
  let me = this
  this._id = 'WatcherWorker_' + UUID.get()
  this._interval = 200
  this._callback = null
  this._reserve = 0
  this._toRemove = false
  this._param = {}
  this.id = function (id) {
    me._id = id
    return me
  }
  this.param = function (key, value) {
    me._param[key] = value
    return me
  }
  this.interval = function (interval) {
    me._interval = interval
    return me
  }
  this.callback = function (callback) {
    me._callback = callback
    return me
  }
  this.addToWatcher = function (watcher) {
    watcher.push(me)
    return me
  }
  this.get = function () {
    return me
  }
  this.remove = function () {
    me._toRemove = true
    return me
  }

  this.getParam = function (key) {
    if (key in me._param) {
      return me._param[key]
    }
    return null
  }

  return me
}
