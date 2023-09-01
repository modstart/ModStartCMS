const Storage = {
    /**
     * @Util 存储数据
     * @method MS.storage.set
     * @param key String 键
     * @param value String|Object|Array 值
     */
    set: function (key, value) {
        window.localStorage.setItem(key, JSON.stringify(value))
    },
    /**
     * @Util 获取数据
     * @method MS.storage.get
     * @param key String 键
     * @param defaultValue String|Object|Array 默认值
     * @return String|Object|Array 返回值
     */
    get: function (key, defaultValue) {
        let value = window.localStorage.getItem(key)
        try {
            return JSON.parse(value);
        } catch (e) {
        }
        return defaultValue
    },
    /**
     * @Util 获取数组数据
     * @method MS.storage.getArray
     * @param key String 键
     * @param defaultValue Array 默认值
     * @return Array 返回值
     */
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
    /**
     * @Util 获取对象数据
     * @method MS.storage.getObject
     * @param key String 键
     * @param defaultValue Object 默认值
     * @return Array 返回值
     */
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
    prepare(k, cb, failCb) {
        let v = Storage.get(k, null)
        if (null !== v) {
            cb(v)
        } else {
            failCb()
        }
    },
}


export {
    Storage
}
