const Storage = {
    set: function (key, value) {
        window.localStorage.setItem(key, JSON.stringify(value))
    },
    get: function (key, defaultValue) {
        let value = window.localStorage.getItem(key)
        try {
            return JSON.parse(value);
        } catch (e) {
        }
        return defaultValue
    },
    getArray: function (key, defaultValue) {
        defaultValue = defaultValue || []
        let value = window.localStorage.getItem(key)
        try {
            value = JSON.parse(value)
            if (!Array.isArray(value)) {
                return defaultValue
            }
            return value
        } catch (e) {
        }
        return defaultValue
    },
    getObject: function (key, defaultValue) {
        defaultValue = defaultValue || []
        let value = window.localStorage.getItem(key)
        try {
            value = JSON.parse(value)
            if (!Array.isArray(value) && (typeof value === 'object')) {
                return value
            }
            return defaultValue
        } catch (e) {
        }
        return defaultValue
    },
}


export {
    Storage
}
