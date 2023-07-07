module.exports = {
    getQuery: function (name, defaultValue) {
        defaultValue = defaultValue || null
        let query = this.parse().query
        if (name) {
            return query[name] || defaultValue
        }
        return query
    },
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
    merge: function (url, param) {
        let parsed = this.parse(url)
        for (let k in param) {
            parsed.query[k] = param[k]
        }
        return this.build(parsed)
    }
}
