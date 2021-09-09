import Vue from 'vue';

let EventBus = new Vue();

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

