const path = require('path');
const webpack = require('webpack');
const WebpackOnBuildPlugin = require('on-build-webpack');
const fs = require('fs');
const UglifyJS = require("uglify-js");
const jquery = require('jquery');
const file = require('./src/lib/file.js');
let config = require('./config.js');
const VueLoaderPlugin = require('vue-loader/lib/plugin');
const WebpackBuildNotifierPlugin = require('webpack-build-notifier');

const ts = function () {
    return (new Date()).getTime();
};
const d = function () {
    return (new Date()).toJSON().replace(/T/, ' ').replace('Z', '');
};
const listEntries = function () {
    let entries = {};
    for (let j = 0; j < config.apps.length; j++) {
        let files = file.listFiles('./src/' + config.apps[j] + '/');
        for (let i = 0; i < files.length; i++) {
            if (/\.js$/.test(files[i])) {
                let flag = files[i].replace(/\.js$/, '').replace('src/', '');
                entries[flag] = files[i];
            }
        }
    }
    return entries;
}

const webpackConfig = {
    mode: 'production',
    entry: listEntries(),
    output: {
        path: path.resolve(config.dist),
        filename: '[name].js',
        publicPath: config.cdn
    },
    performance: {
        hints: false
    },
    plugins: [
        new WebpackBuildNotifierPlugin({
            title: 'ModStart Assets',
            showDuration: true,
        }),
        new VueLoaderPlugin(),
        new webpack.ProvidePlugin({
            $: 'jquery',
            jQuery: 'jquery',
            'window.jQuery': 'jquery',
            'window.$': 'jquery',
        }),
        new WebpackOnBuildPlugin(function (stats) {
            if (stats.compilation.options.mode === 'development') {
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
                        const codeCompress = UglifyJS.minify(data, {
                            output: {
                                comments: /^SOME_NONE_COMMENTS/
                            }
                        }).code
                        if (codeCompress) {
                            console.log(`saved ${fileFullPath} (${data.length} -> ${codeCompress.length})`);
                            fs.writeFile(fileFullPath, codeCompress, () => {
                            });
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
                    {loader: 'less-loader'},
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
                    {loader: 'url-loader?limit=10000&name=sprites/[hash].[ext]'}
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
            }
        ]
    },
    resolve: {
        extensions: ['.js', '.css', '.vue'],
        alias: {
            'jquery': 'jquery',
            'vue$': 'vue/dist/vue.esm.js',
            '@ModStartAsset': path.resolve(__dirname, './src/'),
        }
    },
    externals: {
        'vue': 'Vue',
        'element-ui': 'ELEMENT',
        'jquery': 'window.$',
        //'echarts': 'echarts',
    }
}

module.exports = (env) => {
    console.log('webpack env  -> ', env)
    let mode = 'production'
    if (env.dev) {
        mode = 'development'
    }
    console.log('webpack mode -> ', mode)
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
                publicPath: config.cdn + '/'
            },
        }))
    }
    return results
}
