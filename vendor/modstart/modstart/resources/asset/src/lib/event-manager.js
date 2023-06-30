const EventManager = {
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
