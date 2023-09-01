const EventManager = {
    /**
     * @Util 事件触发
     * @method MS.eventManager.fire
     * @param name String 事件名称
     * @param detail Object 事件参数
     */
    fire(name, detail) {
        detail = detail || {}
        const event = new CustomEvent(name, {
            detail: detail
        });
        if (window.dispatchEvent) {
            window.dispatchEvent(event)
        } else {
            window.fireEvent(event)
        }
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
