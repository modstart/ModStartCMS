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
    }
}

module.exports = EventManager
