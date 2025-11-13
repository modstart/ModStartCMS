const EventManager = {
    listeners: {},
    on(name, handler) {
        if (window.addEventListener) {
            window.addEventListener(name, handler, false)
        } else {
            window.attachEvent(name, handler)
        }
        if (!this.listeners[name]) {
            this.listeners[name] = []
        }
        this.listeners[name].push(handler)
    },
    off(name, handler) {
        if (window.removeEventListener) {
            window.removeEventListener(name, handler, false)
        } else {
            window.detachEvent(name, handler)
        }
        if (this.listeners[name]) {
            const index = this.listeners[name].indexOf(handler)
            if (index > -1) {
                this.listeners[name].splice(index, 1)
            }
            if (this.listeners[name].length === 0) {
                delete this.listeners[name]
            }
        }
    },
    /**
     * @Util 事件触发
     * @method MS.eventManager.fire
     * @param name String 事件名称
     * @param detail Object 事件参数
     * @param option Object 事件选项
     */
    fire(name, detail, option) {
        detail = detail || {}
        option = Object.assign({
            waitListeners: false,
            waitListenersTimeout: 5000,
        }, option)
        const process = function () {
            const event = new CustomEvent(name, {
                detail: detail
            });
            if (window.dispatchEvent) {
                window.dispatchEvent(event)
            } else {
                window.fireEvent(event)
            }
        }
        if (!option.waitListeners) {
            process()
            return;
        }
        const timeout = Date.now() + option.waitListenersTimeout
        const checkAndProcess = function () {
            if (EventManager.listeners[name] && EventManager.listeners[name].length > 0) {
                process()
            } else {
                if (Date.now() < timeout) {
                    setTimeout(checkAndProcess, 50)
                } else {
                    conosle.error('EventManager.fire waitListeners timeout: ' + name)
                }
            }
        }
        checkAndProcess()
    },
    /**
     * @Util 元素事件触发
     * @method MS.eventManager.fireElementEvent
     * @param element Element 元素
     * @param name String 事件名称
     * @param detail Object 事件参数
     */
    fireElementEvent(element, name, detail) {
        detail = detail || {}
        const event = new CustomEvent(name, {
            detail: detail
        });
        if (element.dispatchEvent) {
            element.dispatchEvent(event)
        } else {
            element.fireEvent(event)
        }
    }
}

module.exports = EventManager
