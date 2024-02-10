import Vue from 'vue';

let EventBus = new Vue();

let emitTimer = null
EventBus.$emitDelay = (event, param) => {
    if (emitTimer) {
        clearTimeout(emitTimer)
    }
    emitTimer = setTimeout(() => {
        EventBus.$emit(event, param)
        emitTimer = null
    }, 100)
}

EventBus.$onOnceListeners = {}
EventBus.$onOnce = (event, name, cb) => {
    if (!(event in EventBus.$onOnceListeners)) {
        EventBus.$onOnceListeners[event] = []
    }
    if (EventBus.$onOnceListeners[event].includes(name)) {
        return
    }
    EventBus.$onOnceListeners[event].push(name)
    EventBus.$on(event, cb)
}

export {
    EventBus
};

