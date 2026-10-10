#!/usr/bin/env node
/**
 * ModStart 自动化测试浏览器驱动依赖初始化脚本
 *
 * 将 playwright-core 安装到用户目录下的共享位置（默认 ~/.modstart/auto-test/node-modules），
 * 由 vendor/modstart/modstart/src/Test/browser/browser-server.js 按绝对路径加载，
 * 避免污染各项目自身的 node_modules。
 *
 * 用法：
 *   node init.js            # 未安装时安装，已安装则跳过
 *   node init.js --force    # 强制重新安装
 *
 * 环境变量：
 *   MODSTART_AUTO_TEST_HOME       自定义安装根目录（默认 ~/.modstart/auto-test/node-modules）
 *   MODSTART_PLAYWRIGHT_VERSION   指定 playwright-core 版本（默认 latest）
 */

const fs = require('fs');
const os = require('os');
const path = require('path');
const { execFileSync } = require('child_process');

const HOME_DIR = process.env.MODSTART_AUTO_TEST_HOME
    ? path.resolve(process.env.MODSTART_AUTO_TEST_HOME)
    : path.join(os.homedir(), '.modstart', 'auto-test', 'node-modules');
const TARGET_DIR = path.join(HOME_DIR, 'playwright-core');
const VERSION = process.env.MODSTART_PLAYWRIGHT_VERSION || 'latest';
const FORCE = process.argv.indexOf('--force') >= 0;

function installedVersion() {
    try {
        return require(path.join(TARGET_DIR, 'package.json')).version;
    } catch (e) {
        return null;
    }
}

function removeDir(dir) {
    if (fs.rmSync) {
        fs.rmSync(dir, { recursive: true, force: true });
    } else if (fs.rmdirSync) {
        try {
            fs.rmdirSync(dir, { recursive: true });
        } catch (e) {
            // ignore
        }
    }
}

function main() {
    const current = installedVersion();
    if (current && !FORCE) {
        console.log('[modstart] playwright-core 已安装：' + current + '（' + TARGET_DIR + '）');
        return;
    }
    fs.mkdirSync(HOME_DIR, { recursive: true });
    // 通过临时 staging 目录安装，再把依赖移动到目标目录，保持目标目录直接作为依赖根
    const staging = path.join(HOME_DIR, '.staging-' + process.pid);
    removeDir(staging);
    fs.mkdirSync(staging, { recursive: true });
    fs.writeFileSync(path.join(staging, 'package.json'), JSON.stringify({
        name: 'modstart-auto-test-staging',
        private: true,
    }, null, 2));
    const npm = process.platform === 'win32' ? 'npm.cmd' : 'npm';
    console.log('[modstart] 正在安装 playwright-core@' + VERSION + ' ...');
    execFileSync(npm, ['install', 'playwright-core@' + VERSION, '--no-audit', '--no-fund'], {
        cwd: staging,
        stdio: 'inherit',
    });
    const staged = path.join(staging, 'node_modules', 'playwright-core');
    if (!fs.existsSync(staged)) {
        throw new Error('安装失败：未找到 ' + staged);
    }
    removeDir(TARGET_DIR);
    fs.renameSync(staged, TARGET_DIR);
    removeDir(staging);
    console.log('[modstart] playwright-core 安装完成：' + installedVersion() + '（' + TARGET_DIR + '）');
}

try {
    main();
} catch (e) {
    console.error('[modstart] 初始化失败：' + (e && e.message ? e.message : e));
    process.exit(1);
}
