module.exports = function (mod) {
    return {
        cdn: `/vendor/${mod}`,
        dist: `./../../../../public/vendor/${mod}`,
        distAsset: './../../Asset',
        apps: [
            'entry',
        ]
    }
}
