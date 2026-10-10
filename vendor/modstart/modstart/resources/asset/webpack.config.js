const path = require('path');
const fs = require('fs');
const webpack = require('webpack');
const jquery = require('jquery');
const file = require('./src/lib/file.js');
let config = require('./config.js');
const VueLoaderPlugin = require('vue-loader/lib/plugin');
const WebpackBuildNotifierPlugin = require('webpack-build-notifier');
const TerserPlugin = require('terser-webpack-plugin');

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

// Copies every emitted asset to the second output directory, so the bundle is
// only compiled/minified once instead of running two identical webpack configs.
const CopyAssetsPlugin = function (from, to) {
    this.from = from;
    this.to = to;
};
CopyAssetsPlugin.prototype.apply = function (compiler) {
    const from = path.resolve(this.from);
    const to = path.resolve(this.to);
    compiler.hooks.afterEmit.tapAsync('ModStartCopyAssetsPlugin', function (compilation, callback) {
        try {
            Object.keys(compilation.assets).forEach(function (name) {
                const src = path.join(from, name);
                if (!fs.existsSync(src)) {
                    return;
                }
                const dest = path.join(to, name);
                fs.mkdirSync(path.dirname(dest), { recursive: true });
                fs.copyFileSync(src, dest);
            });
        } catch (e) {
            return callback(e);
        }
        callback();
    });
};

// Fresh minimizer per config: TerserPlugin (terser) runs in parallel across CPU cores.
// A new instance is created for every webpack config to keep the plugin state isolated.
const createOptimization = function (minimize) {
    return {
        minimize: minimize,
        minimizer: [
            new TerserPlugin({
                parallel: true,
                cache: true,
                terserOptions: {
                    compress: true,
                    mangle: false,
                    output: {
                        comments: false,
                        beautify: false
                    }
                }
            })
        ]
    };
}

const webpackConfig = {
    mode: 'production',
    entry: listEntries(),
    output: {
        path: path.resolve(config.dist),
        filename: '[name].js',
        publicPath: config.cdn
    },
    optimization: createOptimization(true),
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
        })
    ],
    module: {
        noParse: [
            /webuploader\.js$/,
        ],
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
    const isDev = !!(env && env.dev)
    const mode = isDev ? 'development' : 'production'
    console.log('webpack env  -> ', env)
    console.log('webpack mode -> ', mode)
    const plugins = isDev
        ? webpackConfig.plugins
        : webpackConfig.plugins.concat([
            new CopyAssetsPlugin(path.resolve(config.dist), path.resolve(config.distAsset))
        ])
    return [Object.assign({}, webpackConfig, {
        mode,
        optimization: createOptimization(!isDev),
        plugins
    })]
}
