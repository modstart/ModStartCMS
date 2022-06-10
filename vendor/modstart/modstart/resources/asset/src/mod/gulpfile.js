const path = require('path');
const fs = require('fs');
let moduleConfig = require('./config.js');

module.exports = function (gulp, dirname, excludes) {
    excludes = excludes || []
    const mergeStream = require('merge-stream');
    const gulpPrint = require('gulp-print').default;
    const gulpLess = require('gulp-less');
    const gulpCleanCss = require('gulp-clean-css');
    const gulpEach = require('gulp-each');
    const UglifyJS = require("uglify-js");
    const gulpIgnore = require('gulp-ignore');
    const exclude = require('./exclude');
    const gulpStyleAliases = require('gulp-style-aliases');
    const mod = dirname.replace(/^.+[\/\\]([A-Za-z0-9]+)[\/\\]resources[\/\\]asset/, '$1');
    let config = moduleConfig(mod)
    const Env = {
        exclude: ['common', 'lib', 'entry', 'sui', 'svue', 'components'],
        root: process.env.INIT_CWD.replace(/\/$/, '') + '/',
        src: './src/',
        dist: path.resolve(config.dist),
        distAsset: path.resolve(config.distAsset),
        ext: {
            'static': ['otf', 'eot', 'ttf', 'woff', 'woff2', 'swf', 'svg', 'html', 'xml', 'mp4', 'mp3', 'json', 'md', 'txt', 'properties'],
            'image': ['png', 'jpg', 'jpeg', 'gif', 'svg'],
            'less': ['less'],
            'css': ['css'],
            'js': ['js']
        },
        watch: {},
        isWatching: true,
    };
    const modules = fs.readdirSync(Env.src).filter(f => !Env.exclude.includes(f))
    const getBuildParam = function (buildGroup, defaultFileBuildPath) {
        var param = [];

        var fileBuildPath = null;
        if (buildGroup in Env.watch) {
            fileBuildPath = Env.watch[buildGroup];
        }
        Env.watch[buildGroup] = null;

        if (!fileBuildPath) {
            var src = null;
            if (typeof defaultFileBuildPath == 'string') {
                src = Env.src + defaultFileBuildPath;
            } else {
                src = [];
                for (var i = 0; i < defaultFileBuildPath.length; i++) {
                    src.push(Env.src + defaultFileBuildPath[i]);
                }
            }
            param.push({
                src: src,
                base: Env.src
            });
        } else {
            if (fs.existsSync(Env.SRC + fileBuildPath)) {
                param.push({
                    src: Env.src + fileBuildPath,
                    base: Env.src
                });
            }
        }

        return param;
    };

    const buildWith = function (buildGroup, builder) {
        const buildPath = modules.map(o => o + '/**/*.@(' + Env.ext[buildGroup].join('|') + ')')
        var buildParam = getBuildParam(buildGroup, buildPath);
        var merged = mergeStream();
        for (var i = 0; i < buildParam.length; i++) {
            var build = builder(buildParam[i].src, buildParam[i].base);
            merged.add(build);
        }
        return merged;
    };


    gulp.task('build:static', function () {
        return buildWith('static', function (src, base) {
                return gulp.src(src, {base})
                    .pipe(gulpIgnore.exclude(exclude.condition))
                    .pipe(gulpPrint(function (filepath) {
                        return "build: " + filepath;
                    }))
                    .pipe(gulp.dest(Env.dist))
                    .pipe(gulp.dest(Env.distAsset));
            }
        );
    });

    gulp.task('build:js', function () {
        return buildWith('js', function (src, base) {
                return gulp.src(src, {base})
                    .pipe(gulpIgnore.exclude(exclude.condition))
                    .pipe(gulpEach(function (content, file, callback) {
                        const code = UglifyJS.minify(content).code
                        if (!code) {
                            console.log('build error:' + file.path)
                            callback(null, content);
                            return
                        }
                        callback(null, code);
                    }))
                    .pipe(gulpPrint(function (filepath) {
                        return "build: " + filepath;
                    }))
                    .pipe(gulp.dest(Env.dist))
                    .pipe(gulp.dest(Env.distAsset));
            }
        );
    });

    gulp.task('build:less', function () {
        return buildWith('less', function (src, base) {
                return gulp.src(src, {base})
                    .pipe(gulpIgnore.exclude(exclude.condition))
                    .pipe(gulpPrint(function (filepath) {
                        return "build: " + filepath;
                    }))
                    .pipe(gulpStyleAliases({
                        "@ModStartAsset": path.resolve(__dirname, '../'),
                    }))
                    .pipe(gulpLess())
                    .pipe(gulpCleanCss({
                        advanced: true,
                        keepSpecialComments: '*'
                    }))
                    .pipe(gulp.dest(Env.dist))
                    .pipe(gulp.dest(Env.distAsset));
            }
        );
    });

    gulp.task('build:css', function () {
        return buildWith('css', function (src, base) {
                return gulp.src(src, {base})
                    .pipe(gulpIgnore.exclude(exclude.condition))
                    .pipe(gulpPrint(function (filepath) {
                        return "build: " + filepath;
                    }))
                    .pipe(gulpCleanCss({
                        advanced: true,
                        keepSpecialComments: '*'
                    }))
                    .pipe(gulp.dest(Env.dist))
                    .pipe(gulp.dest(Env.distAsset));
            }
        );
    });

    gulp.task('build:image', function () {
        return buildWith('image', function (src, base) {
                return gulp.src(src, {base})
                    .pipe(gulpIgnore.exclude(exclude.condition))
                    .pipe(gulpPrint(function (filepath) {
                        return "build: " + filepath;
                    }))
                    .pipe(gulp.dest(Env.dist))
                    .pipe(gulp.dest(Env.distAsset));
            }
        );
    });

    let series = []
    series.push('build:css')
    series.push('build:less')
    series.push('build:static')
    series.push('build:image')
    series.push('build:js')

    gulp.task('default', gulp.series(...series));
}
