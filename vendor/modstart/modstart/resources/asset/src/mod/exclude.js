const packIgnores = [
    'demo_index.html',
    'demo.html',
    'demo.json',
    'demo-response.json',
    'demo.css',
    'china.json',
]

const excludeCondition = function (file) {
    for (let t of packIgnores) {
        if ('string' === typeof t) {
            if (file.path.endsWith(t)) {
                return true
            }
        } else {
            if (t.test(file.path)) {
                return true
            }
        }
    }
    return false;
};

module.exports = {
    condition: excludeCondition,
}
