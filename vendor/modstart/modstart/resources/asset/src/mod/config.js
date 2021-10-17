module.exports = function (mod) {
    let config = {
        cdn: `/vendor/${mod}`,
        dist: `./../../../../public/vendor/${mod}`,
        distAsset: './../../Asset',
        apps: [
            'entry',
        ],
    }
    switch(process.platform){
        case 'win32':
            config.platform = 'windows'
            break
        default:
            config.platform = 'linux'
            break
    }
    return config
}
