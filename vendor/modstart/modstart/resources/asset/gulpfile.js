const gulp = require('gulp');
const path = require('path');
const fs = require('fs');
const crypto = require('crypto');
const mergeStream = require('merge-stream');
const gulpPrint = require('gulp-print').default;
const gulpLess = require('gulp-less');
const gulpCleanCss = require('gulp-clean-css');
const os = require('os');
const workerFarm = require('worker-farm');
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

// build:js cache: skip re-minifying files whose content and outputs are unchanged.
const BUILD_JS_CACHE_VERSION = 1;
const BUILD_JS_CACHE_FILE = path.resolve('./node_modules/.cache/modstart-asset/build-js.json');

const readBuildJsCache = function () {
    try {
        const data = JSON.parse(fs.readFileSync(BUILD_JS_CACHE_FILE, 'utf8'));
        if (data && data.version === BUILD_JS_CACHE_VERSION && data.files) {
            return data.files;
        }
    } catch (e) {
        // no cache / broken cache -> rebuild everything
    }
    return {};
};

const writeBuildJsCache = function (filesMap) {
    try {
        fs.mkdirSync(path.dirname(BUILD_JS_CACHE_FILE), { recursive: true });
        fs.writeFileSync(BUILD_JS_CACHE_FILE, JSON.stringify({ version: BUILD_JS_CACHE_VERSION, files: filesMap }));
    } catch (e) {
        // cache write is best-effort
    }
};

gulp.task('build:js', function () {
    const buildPath = modules.map(o => o + '/**/*.@(' + Env.ext.js.join('|') + ')')
    const buildParam = getBuildParam('js', buildPath);
    const files = [];

    // Minify pending files across all CPU cores through a worker-farm pool.
    const runMinify = function (jobs) {
        if (!jobs.length) {
            return Promise.resolve();
        }
        const farm = workerFarm(
            { maxConcurrentWorkers: Math.max(1, os.cpus().length - 1), maxRetries: 0, autoStart: true },
            require.resolve('./build/minify-worker.js')
        );
        const calls = jobs.map(function (job) {
            return new Promise(function (resolve, reject) {
                farm(job, function (err, result) {
                    if (err) {
                        reject(err);
                    } else {
                        resolve(result);
                    }
                });
            });
        });
        return Promise.all(calls).then(function (results) {
            results.forEach(function (result) {
                console.log("build: " + result.relative);
            });
            workerFarm.end(farm);
        }).catch(function (err) {
            workerFarm.end(farm);
            throw err;
        });
    };

    return new Promise(function (resolve, reject) {
        if (!buildParam.length) {
            resolve();
            return;
        }
        let pending = buildParam.length;
        buildParam.forEach(function (param) {
            const stream = gulp.src(param.src, { base: param.base })
                .pipe(gulpIgnore.exclude(exclude.condition));
            stream.on('data', function (file) {
                if (file.isNull() || file.isStream()) {
                    return;
                }
                files.push(file);
            });
            stream.on('error', reject);
            stream.on('end', function () {
                pending--;
                if (pending !== 0) {
                    return;
                }
                const previous = readBuildJsCache();
                const nextCache = {};
                const jobs = [];
                files.forEach(function (file) {
                    const hash = crypto.createHash('sha1').update(file.contents).digest('hex');
                    nextCache[file.relative] = { hash: hash };
                    const out = [
                        path.join(Env.dist, file.relative),
                        path.join(Env.distAsset, file.relative)
                    ];
                    const cached = previous[file.relative];
                    const outputsExist = out.every(function (filePath) {
                        return fs.existsSync(filePath);
                    });
                    if (cached && cached.hash === hash && outputsExist) {
                        console.log("cached: " + file.relative);
                        return;
                    }
                    jobs.push({ src: file.path, relative: file.relative, out: out });
                });
                runMinify(jobs).then(function () {
                    writeBuildJsCache(nextCache);
                    resolve();
                }, reject);
            });
        });
    });
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
