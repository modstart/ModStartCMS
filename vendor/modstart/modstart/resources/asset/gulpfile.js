const gulp = require('gulp');
const path = require('path');
const fs = require('fs');
const mergeStream = require('merge-stream');
const gulpPrint = require('gulp-print').default;
const gulpLess = require('gulp-less');
const gulpCleanCss = require('gulp-clean-css');
const gulpEach = require('gulp-each');
const UglifyJS = require("uglify-js");
const gulpIgnore = require('gulp-ignore');
const exclude = require('./src/mod/exclude');

let config = require('./config.js');

const Env = {
    exclude: ['common', 'lib', 'entry', 'sui', 'svue', 'mod', 'main'],
    root: process.env.INIT_CWD.replace(/\/$/, '') + '/',
    src: './src/',
    dist: path.resolve(config.dist),
    distAsset: path.resolve(config.distAsset),
    ext: {
        'static': ['otf', 'eot', 'ttf', 'woff', 'woff2', 'swf', 'svg', 'html', 'xml', 'mp4', 'mp3', 'json', 'md'],
        'image': ['png', 'jpg', 'jpeg', 'gif'],
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
                .pipe(gulpEach(function(content, file, callback) {
                    callback(null, UglifyJS.minify(content).code);
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

// gulp.task('watching', function () {
//     if (!Env.isWatching) {
//         return;
//     }
//
//     const srcs = modules.map(o => [Env.src + o + '/*.*', Env.src + o + '/**/*.*']).flat()
//     const watch = gulp.watch(srcs);
//     watch.on('all', function (type, path, stats) {
//         try {
//             if (!['add', 'change'].includes(type)) {
//                 return;
//             }
//             var extension = path.substring(path.lastIndexOf('.') + 1).toLowerCase();
//             var groupFound = null;
//             for (var group in Env.ext) {
//                 if (Env.ext[group].indexOf(extension) >= 0) {
//                     groupFound = group;
//                     break;
//                 }
//             }
//             if (!groupFound) {
//                 // console.log('watching: ignore path', path);
//                 return;
//             }
//             console.log('>>> watching build ' + path + ' starting');
//             Env.watch[groupFound] = path;
//             gulp.series('build:' + groupFound);
//             // gulp.start('build:' + groupFound);
//         } catch (e) {
//             console.error('watching: build error', e);
//         }
//     });
//     return watch
// });

let series = []
series.push('build:css')
series.push('build:less')
series.push('build:static')
series.push('build:image')
series.push('build:js')
// series.push('watching')

gulp.task('default', gulp.series(...series)
)
;
