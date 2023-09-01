module.exports = {
    /**
     * @Util 获取请求参数
     * @method MS.url.getQuery
     * @param name String 参数名
     * @param defaultValue String 默认值
     * @return String 参数值
     */
    getQuery: function (name, defaultValue) {
        defaultValue = defaultValue || null
        let query = this.parse().query
        if (name) {
            return query[name] || defaultValue
        }
        return query
    },
    /**
     * @Util 解析URL
     * @method MS.url.parse
     * @param url String URL
     * @return Object 解析后的URL对象
     */
    parse: function (url) {
        url = url || window.location.href;
        const u = new URL(url);
        let query = {}
        u.searchParams.forEach((v, k) => {
            query[k] = v
        })
        let hash = '';
        if (u.hash) {
            hash = u.hash.substr(1)
        }
        return {
            origin: u.origin,
            base: u.origin + u.pathname,
            query: query,
            hash: hash,
        }
    },
    /**
     * @Util 构建URL
     * @method MS.url.build
     * @param parsed Object 解析后的URL
     * @return String URL
     */
    build: function (parsed) {
        let url = parsed.base
        if (parsed.query) {
            let query = []
            for (let k in parsed.query) {
                query.push(k + '=' + parsed.query[k])
            }
            if (query.length > 0) {
                url += '?' + query.join('&')
            }
        }
        if (parsed.hash) {
            url += '#' + parsed.hash
        }
        return url
    },
    /**
     * @Util 合并URL参数
     * @method MS.url.merge
     * @param url String URL
     * @param param Object 参数
     * @return String URL
     */
    merge: function (url, param) {
        let parsed = this.parse(url)
        for (let k in param) {
            parsed.query[k] = param[k]
        }
        return this.build(parsed)
    }
}
