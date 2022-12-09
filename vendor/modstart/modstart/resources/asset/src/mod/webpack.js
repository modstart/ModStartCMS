const path = require('path');
const fs = require('fs');
const process = require('process')
let moduleConfig = require('./config.js');

module.exports = function (dirname, buildOption) {
    let mod = dirname.replace(/^.+[\/\\]([A-Za-z0-9]+)[\/\\]resources[\/\\]asset/, '$1')
    if (!/^[A-Za-z0-9]+$/.test(mod)) {
        mod = 'App'
    }
    buildOption = Object.assign({
        compress: true,
    }, buildOption)
    let config = moduleConfig(mod)
    config.dist = path.join(dirname, config.dist)
    config.distAsset = path.join(dirname, config.distAsset)
    // console.log(config)
    const webpack = require('webpack');
    const WebpackOnBuildPlugin = require('on-build-webpack');
    // const UglifyJS = require("uglify-js");
    const jquery = require('jquery');
    const file = require('./../lib/file.js');
    const VueLoaderPlugin = require('vue-loader/lib/plugin');
    const WebpackBuildNotifierPlugin = require('webpack-build-notifier');
    const CleanObsoleteChunks = require('webpack-clean-obsolete-chunks');
    const WebpackShellPlugin = require('webpack-shell-plugin');
    const WebpackDynamicPublicPathPlugin = require("webpack-dynamic-public-path");

    const glob = require("glob");
    const UglifyJS = require("uglify-js");
    // const files = glob.readDirSync('*.js', {});
    let files = glob.sync(path.resolve(config.distAsset) + '/entry-chunk-*.js')
    files = files.concat(glob.sync(path.resolve(config.dist) + '/entry-chunk-*.js'))

    switch (config.platform) {
        case "windows":
            files = files.map(f => f.replace(/\//g, '\\'))
            break
    }

    const ts = function () {
        return (new Date()).getTime();
    };
    const d = function () {
        return (new Date()).toJSON().replace(/T/, ' ').replace('Z', '');
    };
    const listEntries = function () {
        const src = path.join(dirname, './src/')
        let entries = {};
        for (let j = 0; j < config.apps.length; j++) {
            let files = file.listFiles(src + config.apps[j] + '/');
            for (let i = 0; i < files.length; i++) {
                if (/\.js$/.test(files[i])) {
                    let flag = files[i].replace(/\.js$/, '').replace(src, '');
                    switch (config.platform) {
                        case 'windows':
                            entries[flag] = './' + files[i].replace(dirname + '\\', '');
                            break
                        default:
                            entries[flag] = './' + files[i].replace(dirname + '/', '');
                            break
                    }
                }
            }
        }
        return entries;
    }

    let cleanFilesCommands = []
    console.log("remove old files")
    switch (config.platform) {
        case 'windows':
            cleanFilesCommands.push(files.length > 0 ? 'del /F ' + files.join(' ') : 'cd')
            break
        default:
            cleanFilesCommands.push(files.length > 0 ? 'rm -rv ' + files.join(' ') : 'pwd')
            break
    }

    const webpackConfig = {
        mode: 'production',
        entry: listEntries(),
        output: {
            path: path.resolve(config.dist),
            filename: '[name].js',
            publicPath: 'publicPathPlaceholder',
            chunkFilename: 'entry-chunk-[id]-[hash:8].js',
        },
        performance: {
            hints: false
        },
        plugins: [
            new WebpackDynamicPublicPathPlugin({
                externalPublicPath: "window.__msCDN+'" + config.cdn.substring(1) + "/'"
            }),
            new WebpackShellPlugin({
                onBuildStart: cleanFilesCommands
            }),
            new WebpackBuildNotifierPlugin({
                title: `Module ${mod}`,
                showDuration: true,
            }),
            new VueLoaderPlugin(),
            new webpack.ProvidePlugin({
                $: 'jquery',
                jQuery: 'jquery',
                'window.jQuery': 'jquery',
                'window.$': 'jquery',
            }),
            new CleanObsoleteChunks({
                verbose: true,
                deep: true
            }),
            new webpack.optimize.MinChunkSizePlugin({
                minChunkSize: 20 * 1000
            }),
            new WebpackOnBuildPlugin(function (stats) {
                if (stats.compilation.options.mode === 'development' || !buildOption.compress) {
                    return
                }
                const assets = stats.compilation.getAssets()
                assets
                    .filter(f => /.js$/.test(f.name))
                    .forEach(f => {
                        const fileFullPath = f.source.existsAt
                        if (!fileFullPath) {
                            console.log('process ignore', f.name)
                            return;
                        }
                        console.log("process", fileFullPath)
                        fs.readFile(fileFullPath, {
                            flag: 'r+',
                            encoding: 'utf8'
                        }, function (err, data) {
                            if (err) {
                                console.error(err);
                                return;
                            }
                            console.log(`begin ${fileFullPath} (${data.length})`)
                            if (data.length > 2 * 1024 * 1024) {
                                console.log(`skip ${fileFullPath} (too large)`)
                                return;
                            }
                            const compressResult = UglifyJS.minify(data, {
                                output: {
                                    comments: /^SOME_NONE_COMMENTS/
                                }
                            })
                            console.log(`end ${fileFullPath}`)
                            const codeCompress = compressResult.code
                            if (codeCompress) {
                                console.log(`saved ${fileFullPath} (${data.length} -> ${codeCompress.length})`);
                                fs.writeFile(fileFullPath, codeCompress, () => {
                                });
                            } else {
                                console.log(`failed ${fileFullPath}`)
                            }
                        });
                    })
            })
        ],
        module: {
            rules: [
                {
                    test: /\.css$/,
                    use: [
                        {loader: 'style-loader'},
                        {loader: 'css-loader'},
                    ]
                },
                {
                    test: /\.less$/,
                    use: [
                        {loader: 'style-loader'},
                        {loader: 'css-loader'},
                        {loader: 'less-loader', options: {javascriptEnabled: true}},
                    ]
                },
                {
                    test: /\.html$/i,
                    use: [
                        {loader: 'html-loader'},
                    ],
                },
                {
                    test: require.resolve("jquery"),
                    use: [
                        {loader: 'expose-loader', options: {exposes: ['$', 'jQuery']}}
                    ]
                },
                {
                    test: /\.(png|jpg|gif|jpeg|)$/,
                    use: [
                        {loader: 'url-loader?limit=10000&name=sprites/[hash].[ext]', options: {esModule: false}}
                    ]
                },
                {
                    test: /\.vue$/,
                    use: [
                        {loader: 'vue-loader'},
                    ]
                },
                {
                    test: /\.woff(\?v=\d+\.\d+\.\d+)?$/,
                    use: [
                        {loader: "url-loader?limit=10000&mimetype=application/font-woff&name=./assets/fonts/[hash].[ext]"}
                    ]
                },
                {
                    test: /\.woff2(\?v=\d+\.\d+\.\d+)?$/,
                    use: [
                        {loader: "url-loader?limit=10000&mimetype=application/font-woff&name=./assets/fonts/[hash].[ext]"}
                    ]
                },
                {
                    test: /\.ttf(\?v=\d+\.\d+\.\d+)?$/,
                    use: [
                        {loader: "url-loader?limit=10000&mimetype=application/octet-stream&name=./assets/fonts/[hash].[ext]"}
                    ]
                },
                {
                    test: /\.eot(\?v=\d+\.\d+\.\d+)?$/,
                    use: [
                        {loader: "file-loader"}
                    ]
                },
                {
                    test: /\.svg$/,
                    use: [
                        {loader: "file-loader"}
                    ]
                },
                {
                    test: /package\.json$/,
                    loader: 'package-json-cleanup-loader',
                    options: {
                        only: ['version', 'name', 'otherParam']
                    }
                },
            ]
        },
        resolve: {
            extensions: ['.js', '.css', '.vue'],
            alias: {
                'jquery': 'jquery',
                'vue$': 'vue/dist/vue.esm.js',
                '@ModStartAsset': path.resolve(__dirname, '../'),
            }
        },
        externals: {
            'vue': 'Vue',
            'element-ui': 'ELEMENT',
            'jquery': 'window.$',
            //'echarts': 'echarts',
        }
    }
    return (env) => {
        console.log('webpack env  -> ', env)
        let mode = 'production'
        if (env.dev) {
            mode = 'development'
        }
        console.log('webpack mode -> ', mode)
        console.log('webpack build option -> ', buildOption)
        let results = []
        results.push(Object.assign({}, webpackConfig, {
            mode
        }))
        if (mode === 'production') {
            results.push(Object.assign({}, webpackConfig, {
                mode,
                output: {
                    path: path.resolve(config.distAsset),
                    filename: '[name].js',
                    publicPath: 'publicPathPlaceholder',
                    chunkFilename: 'entry-chunk-[id]-[hash:8].js',
                },
            }))
        }
        return results
    };
}
