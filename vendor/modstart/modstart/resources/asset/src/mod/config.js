module.exports = function (mod) {
    let config = {
        cdn: `/vendor/${mod}`,
        dist: `./../../../../public/vendor/${mod}`,
        distAsset: './../../Asset',
        apps: [
            'entry',
        ],
    }
    if ('App' === mod) {
        config.dist = './../../public/asset-app'
        config.distAsset = './../asset-build'
        config.cdn = '/asset-app'
    }
    switch (process.platform) {
        case 'win32':
            config.platform = 'windows'
            break
        default:
            config.platform = 'linux'
            break
    }
    return config
}
