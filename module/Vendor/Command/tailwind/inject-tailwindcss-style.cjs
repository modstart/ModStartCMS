#!/usr/bin/env node
'use strict';

const fs = require('fs');
const path = require('path');
const { execSync, spawnSync } = require('child_process');
const os = require('os');

const CACHE_DIR = path.join(os.homedir(), '.cache', 'modstart-tailwindcss-inject');

// ────────────────────────── 参数解析 ──────────────────────────
function parseArgs(argv) {
    const minimist = require(path.join(CACHE_DIR, 'node_modules', 'minimist'));
    const opts = minimist(argv, { string: ['html'], boolean: ['watch'] });
    const htmlFiles = [].concat(opts.html || []).filter(Boolean).map(f => path.resolve(f));
    const watch = !!opts.watch;
    return { htmlFiles, watch };
}

function log(msg) {
    process.stderr.write(`\x1b[36m[tw-compile]\x1b[0m ${msg}\n`);
}

// ────────────────────────── 路径映射 ──────────────────────────
// 向上查找 Laravel 项目根目录（含 artisan 文件）
function findProjectRoot(startDir) {
    let dir = startDir;
    while (dir !== path.dirname(dir)) {
        if (fs.existsSync(path.join(dir, 'artisan'))) return dir;
        dir = path.dirname(dir);
    }
    return null;
}

// 根据 blade 文件路径自动推导输出 CSS 路径和 @asset href
function resolveOutputPath(bladeFile) {
    const projectRoot = findProjectRoot(path.dirname(bladeFile));
    if (!projectRoot) {
        throw new Error(
            `无法找到 Laravel 项目根目录（artisan 文件），请确认文件在 Laravel 项目内\n` +
            `文件路径: ${bladeFile}`
        );
    }

    const rel = path.relative(projectRoot, bladeFile).split(path.sep).join('/');

    // resources/views/theme/default/pc/<file>.blade.php
    let m = rel.match(/^resources\/views\/theme\/default\/pc\/([^/]+)\.blade\.php$/);
    if (m) {
        const file = m[1];
        return {
            cssFile: path.join(projectRoot, 'public', 'theme', 'default', 'page', `${file}.css`),
            assetHref: `theme/default/page/${file}.css`
        };
    }

    // resources/views/theme/default/pc/<group>/<file>.blade.php
    m = rel.match(/^resources\/views\/theme\/default\/pc\/([^/]+)\/([^/]+)\.blade\.php$/);
    if (m) {
        const [, group, file] = m;
        return {
            cssFile: path.join(projectRoot, 'public', 'theme', 'default', 'page', `${group}-${file}.css`),
            assetHref: `theme/default/page/${group}-${file}.css`
        };
    }

    // module/<Module>/View/pc/<file>.blade.php
    m = rel.match(/^module\/([^/]+)\/View\/pc\/([^/]+)\.blade\.php$/);
    if (m) {
        const [, mod, file] = m;
        return {
            cssFile: path.join(projectRoot, 'module', mod, 'Asset', 'css', 'page', `${file}.css`),
            assetHref: `vendor/${mod}/css/page/${file}.css`
        };
    }

    // module/<Module>/View/pc/<group>/<file>.blade.php
    m = rel.match(/^module\/([^/]+)\/View\/pc\/([^/]+)\/([^/]+)\.blade\.php$/);
    if (m) {
        const [, mod, group, file] = m;
        return {
            cssFile: path.join(projectRoot, 'module', mod, 'Asset', 'css', 'page', `${group}-${file}.css`),
            assetHref: `vendor/${mod}/css/page/${group}-${file}.css`
        };
    }

    throw new Error(
        `无法识别的 Blade 文件路径: ${rel}\n` +
        `支持的路径格式:\n` +
        `  resources/views/theme/default/pc/[<group>/]<file>.blade.php\n` +
        `  module/<Module>/View/pc/[<group>/]<file>.blade.php`
    );
}

// ────────────────────────── CSS 压缩 ──────────────────────────
function minifyCss(css) {
    const CleanCSS = require(path.join(CACHE_DIR, 'node_modules', 'clean-css'));
    return new CleanCSS({ level: 2 }).minify(css).styles;
}

function ensureDeps() {
    const twBin = path.join(CACHE_DIR, 'node_modules', '.bin', 'tailwindcss');
    const markers = [
        twBin,
        path.join(CACHE_DIR, 'node_modules', 'minimist'),
        path.join(CACHE_DIR, 'node_modules', 'clean-css'),
        path.join(CACHE_DIR, 'node_modules', 'postcss-prefix-selector'),
    ];
    if (markers.some(p => !fs.existsSync(p))) {
        log('安装依赖（tailwindcss, postcss, minimist, clean-css, postcss-prefix-selector）...');
        fs.mkdirSync(CACHE_DIR, { recursive: true });
        fs.writeFileSync(path.join(CACHE_DIR, 'package.json'), JSON.stringify({
            name: 'modstart-tailwindcss-inject-cache',
            version: '1.0.0',
            dependencies: {
                'tailwindcss': '^3.4.0',
                'postcss': '^8.4.0',
                'autoprefixer': '^10.4.0',
                'minimist': '^1.2.8',
                'clean-css': '^5.3.3',
                'postcss-prefix-selector': '^1.16.0'
            }
        }, null, 2));
        execSync('npm install --no-fund --no-audit', {
            cwd: CACHE_DIR,
            stdio: 'inherit'
        });
        log('依赖安装完成');
    }
    return twBin;
}

// 调用 tailwindcss CLI 生成 CSS（传入整个文件源码，让 Tailwind 扫描所有 class）
function generateTailwindCss(source, twBin) {
    const tmpDir = path.join(os.tmpdir(), `modstart-tw-compile-${Date.now()}`);
    fs.mkdirSync(tmpDir, { recursive: true });

    // 用整个文件源码作为 content 扫描来源
    fs.writeFileSync(path.join(tmpDir, 'content.html'), source);

    // tailwind config：关闭 preflight，只扫描临时 content.html
    fs.writeFileSync(path.join(tmpDir, 'tailwind.config.js'), `
module.exports = {
    content: ['./content.html'],
    theme: { extend: {} },
    plugins: [],
    corePlugins: { preflight: false }
};
`);

    // 仅引入 utilities，不引入 base/preflight，杜绝任何 font-family 污染
    fs.writeFileSync(path.join(tmpDir, 'input.css'), '@tailwind utilities;\n');

    const result = spawnSync(twBin, [
        '-i', path.join(tmpDir, 'input.css'),
        '-o', path.join(tmpDir, 'output.css'),
        '--config', path.join(tmpDir, 'tailwind.config.js')
    ], { cwd: tmpDir });

    if (result.status !== 0) {
        const errMsg = result.stderr ? result.stderr.toString() : 'unknown error';
        throw new Error(`Tailwind CSS 生成失败:\n${errMsg}`);
    }

    const outputPath = path.join(tmpDir, 'output.css');
    const css = fs.existsSync(outputPath) ? fs.readFileSync(outputPath, 'utf8') : '';

    // 清理临时目录
    try { fs.rmSync(tmpDir, { recursive: true, force: true }); } catch (_) {}
    return css;
}

// 给 CSS 中每条规则的选择器加前缀，提升权重（使用 postcss-prefix-selector 插件）
function prefixCssSelectors(css, prefix) {
    const postcss = require(path.join(CACHE_DIR, 'node_modules', 'postcss'));
    const prefixSelector = require(path.join(CACHE_DIR, 'node_modules', 'postcss-prefix-selector'));
    return postcss([
        prefixSelector({ prefix, exclude: [/^:root$/] })
    ]).process(css, { from: undefined }).css;
}

// 组装最终注入的 CSS：重置 + 高权重 Tailwind
function buildFinalCss(rawTailwindCss, prefix) {
    const reset = `${prefix},${prefix} *{}`;
    const prefixed = prefixCssSelectors(rawTailwindCss, prefix);
    return reset + prefixed;
}

// 更新（或新建）blade 文件中的 <link id="pageStyle"> 标签
function updateLinkTag(source, assetHref) {
    const existingRe = /<link\s[^>]*\bid=["']pageStyle["'][^>]*\/?>/i;
    const newTag = `<link id="pageStyle" rel="stylesheet" href="@asset('${assetHref}')" />`;

    if (existingRe.test(source)) {
        return source.replace(existingRe, newTag);
    }

    // 没有找到：插入到 @parent 之后（headAppend section 内）
    const parentRe = /@parent/;
    const parentM = parentRe.exec(source);
    if (parentM) {
        const pos = parentM.index + parentM[0].length;
        return source.slice(0, pos) + '\n    ' + newTag + source.slice(pos);
    }

    // 兜底：插入到 @section('headAppend') 之后
    const headRe = /@section\(\s*['"]headAppend['"]\s*\)/;
    const headM = headRe.exec(source);
    if (headM) {
        const pos = headM.index + headM[0].length;
        return source.slice(0, pos) + '\n    ' + newTag + source.slice(pos);
    }

    // 最后兜底：追加到文件开头
    return newTag + '\n' + source;
}

// ─────────────────────────────── 核心编译 ───────────────────────────────
function compileFiles(htmlFiles, twBin) {
    for (const filePath of htmlFiles) {
        if (!fs.existsSync(filePath)) {
            log(`\x1b[33m跳过不存在的文件: ${filePath}\x1b[0m`);
            continue;
        }
        log(`处理: ${path.basename(filePath)}`);
        const { cssFile, assetHref } = resolveOutputPath(filePath);
        const source = fs.readFileSync(filePath, 'utf8');
        const rawCss = generateTailwindCss(source, twBin);
        if (!rawCss.trim()) { log('  \x1b[33m警告: Tailwind 未生成任何 CSS，跳过\x1b[0m'); continue; }
        const finalCss = minifyCss(buildFinalCss(rawCss, '#pageContainer'));
        fs.mkdirSync(path.dirname(cssFile), { recursive: true });
        fs.writeFileSync(cssFile, finalCss, 'utf8');
        log(`  CSS → ${path.relative(process.cwd(), cssFile)} (${finalCss.length} bytes)`);
        const updatedSource = updateLinkTag(source, assetHref);
        fs.writeFileSync(filePath, updatedSource, 'utf8');
        log(`  \x1b[32m✓ <link id="pageStyle"> 已更新\x1b[0m`);
    }
}

// ─────────────────────────────── main ───────────────────────────────
function main() {
    const args = process.argv.slice(2);
    if (args.length === 0 || args[0] === '-h' || args[0] === '--help') {
        console.log('用法: inject-tailwindcss-style.cjs --html <文件> [--html <文件2> ...] [--watch]');
        console.log('');
        console.log('  --html <路径>   blade 文件路径（可多次指定）');
        console.log('  --watch        监听文件变动，自动重新编译');
        console.log('');
        console.log('CSS 输出路径根据 blade 文件位置自动推导：');
        console.log('  resources/views/theme/default/pc/<file>.blade.php');
        console.log('    → public/theme/default/page/<file>.css');
        console.log('  resources/views/theme/default/pc/<group>/<file>.blade.php');
        console.log('    → public/theme/default/page/<group>-<file>.css');
        console.log('  module/<Module>/View/pc/<file>.blade.php');
        console.log('    → module/<Module>/Asset/css/page/<file>.css');
        console.log('  module/<Module>/View/pc/<group>/<file>.blade.php');
        console.log('    → module/<Module>/Asset/css/page/<group>-<file>.css');
        process.exit(0);
    }

    const twBin = ensureDeps();

    const { htmlFiles, watch } = parseArgs(args);
    if (htmlFiles.length === 0) {
        console.error('错误: 至少需要指定一个 --html 文件');
        process.exit(1);
    }

    compileFiles(htmlFiles, twBin);

    if (!watch) return;

    // ── watch 模式 ──
    log('\x1b[35m[watch]\x1b[0m 监听文件变动中... (Ctrl+C 退出)');
    const debounceMap = new Map();

    for (const filePath of htmlFiles) {
        if (!fs.existsSync(filePath)) {
            log(`\x1b[33m[watch] 文件不存在，跳过监听: ${filePath}\x1b[0m`);
            continue;
        }
        fs.watch(filePath, (eventType) => {
            if (eventType !== 'change') return;
            if (debounceMap.has(filePath)) clearTimeout(debounceMap.get(filePath));
            debounceMap.set(filePath, setTimeout(() => {
                debounceMap.delete(filePath);
                log(`\x1b[35m[watch]\x1b[0m 检测到变动: ${path.basename(filePath)}`);
                try {
                    compileFiles([filePath], twBin);
                } catch (e) {
                    log(`\x1b[31m编译出错: ${e.message}\x1b[0m`);
                }
            }, 300));
        });
        log(`\x1b[35m[watch]\x1b[0m 监听: ${path.relative(process.cwd(), filePath)}`);
    }
}

main();
