#!/usr/bin/env node
/**
 * ModStart UI 测试浏览器服务（常驻进程）
 *
 * 通过 stdin/stdout 以行 JSON 通信，驱动真实 Chrome（headless）完成界面操作。
 * 由 PHP 侧 TestUi 启动/关闭，实现"真实浏览器打开界面自动测试"。
 *
 * 指令（stdin 每行一个 JSON）：
 *   {"action":"goto","url":"http://...","name":"说明"}          打开页面
 *   {"action":"login","url":"http://.../admin/login","username":"admin","password":"123456"}  登录后台（填表提交，等待跳转）
 *   {"action":"fill","selector":"input[name=username]","value":"admin"}  填表
 *   {"action":"click","selector":"button[type=submit]"}          点击元素
 *   {"action":"screenshot","path":"/tmp/xxx.png"}                截图
 *   {"action":"eval","script":"document.title"}                  执行 JS 返回结果
 *   {"action":"html"}                                            返回当前页面 HTML
 *   {"action":"close"}                                           关闭浏览器并退出
 *
 * 结果（stdout 每行一个 JSON）：
 *   {"ok":true,"status":200,"title":"...","url":"...","html":"...","data":...}
 *   {"ok":false,"error":"..."}
 */

const { chromium } = require('playwright-core');

const CHROME_CANDIDATES = [
    '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
    '/usr/bin/google-chrome',
    '/usr/bin/google-chrome-stable',
    '/usr/bin/chromium',
    '/usr/bin/chromium-browser',
    '/mnt/c/Program Files/Google/Chrome/Application/chrome.exe',
    'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
];

let browser = null;
let context = null;
let page = null;
let busy = false;

function findChrome() {
    for (const c of CHROME_CANDIDATES) {
        try {
            if (require('fs').existsSync(c)) {
                return c;
            }
        } catch (e) { /* ignore */ }
    }
    return null;
}

function send(result) {
    process.stdout.write(JSON.stringify(result) + '\n');
}

async function ensurePage() {
    if (page && !page.isClosed()) {
        return page;
    }
    context = await browser.newContext({
        viewport: { width: 1440, height: 900 },
        ignoreHTTPSErrors: true,
    });
    page = await context.newPage();
    return page;
}

async function handleGoto(params) {
    await ensurePage();
    const url = params.url;
    let resp = null;
    let status = 0;
    try {
        resp = await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 30000 });
        if (resp) {
            status = resp.status();
        }
    } catch (e) {
        // 部分页面（如 AJAX 跳转）可能导航失败，但页面已加载
    }
    const title = await page.title().catch(() => '');
    const finalUrl = page.url();
    let html = '';
    if (params.withHtml) {
        html = await page.content().catch(() => '');
    }
    return { ok: true, status: status, title: title, url: finalUrl, html: html };
}

async function handleLogin(params) {
    await ensurePage();
    await page.goto(params.url, { waitUntil: 'domcontentloaded', timeout: 30000 }).catch(() => {});
    // 等待登录表单
    await page.waitForSelector('input[name=username]', { timeout: 10000 }).catch(() => {});
    await page.fill('input[name=username]', params.username || 'admin').catch(() => {});
    await page.fill('input[name=password]', params.password || '123456').catch(() => {});
    await page.click('button[type=submit]').catch(() => {});
    // 等待跳转（登录成功后跳转到后台首页，或 AJAX 处理完成）
    await page.waitForLoadState('domcontentloaded', { timeout: 15000 }).catch(() => {});
    await new Promise(r => setTimeout(r, 1500));
    const url = page.url();
    const title = await page.title().catch(() => '');
    // 判断是否登录成功：URL 不再是 login
    const loggedIn = url.indexOf('/login') === -1;
    return { ok: true, loggedIn: loggedIn, url: url, title: title };
}

async function handleFill(params) {
    await ensurePage();
    await page.waitForSelector(params.selector, { timeout: 10000 }).catch(() => {});
    await page.fill(params.selector, String(params.value == null ? '' : params.value));
    return { ok: true };
}

async function handleClick(params) {
    await ensurePage();
    await page.waitForSelector(params.selector, { timeout: 10000 }).catch(() => {});
    await page.click(params.selector);
    await new Promise(r => setTimeout(r, 500));
    return { ok: true, url: page.url() };
}

async function handleEval(params) {
    await ensurePage();
    const data = await page.evaluate(params.script);
    return { ok: true, data: data };
}

async function handleHtml() {
    await ensurePage();
    const html = await page.content().catch(() => '');
    const url = page.url();
    return { ok: true, html: html, url: url };
}

async function handleScreenshot(params) {
    await ensurePage();
    await page.screenshot({ path: params.path, fullPage: !!params.fullPage });
    return { ok: true, path: params.path };
}

async function dispatch(action, params) {
    switch (action) {
        case 'goto':
            return await handleGoto(params);
        case 'login':
            return await handleLogin(params);
        case 'fill':
            return await handleFill(params);
        case 'click':
            return await handleClick(params);
        case 'eval':
            return await handleEval(params);
        case 'html':
            return await handleHtml();
        case 'screenshot':
            return await handleScreenshot(params);
        default:
            return { ok: false, error: 'unknown action: ' + action };
    }
}

async function main() {
    const chromePath = findChrome();
    if (!chromePath) {
        send({ ok: false, error: 'Chrome 未找到' });
        process.exit(1);
    }
    // SHOW_BROWSER=1 时显示浏览器界面（headed），默认 headless 不显示
    const showBrowser = process.env.SHOW_BROWSER === '1';
    browser = await chromium.launch({
        executablePath: chromePath,
        headless: !showBrowser,
        args: ['--no-sandbox', '--disable-gpu', '--disable-dev-shm-usage'],
    });

    const rl = require('readline').createInterface({
        input: process.stdin,
        crlfDelay: Infinity,
    });

    rl.on('line', async (line) => {
        line = line.trim();
        if (!line) {
            return;
        }
        if (busy) {
            send({ ok: false, error: 'busy' });
            return;
        }
        busy = true;
        let params = {};
        try {
            params = JSON.parse(line);
        } catch (e) {
            busy = false;
            send({ ok: false, error: 'invalid json: ' + line });
            return;
        }
        try {
            const result = await dispatch(params.action, params);
            send(result);
        } catch (e) {
            send({ ok: false, error: e.message || String(e) });
        }
        busy = false;
        if (params.action === 'close') {
            try { await browser.close(); } catch (e) { /* ignore */ }
            process.exit(0);
        }
    });

    rl.on('close', async () => {
        try { await browser.close(); } catch (e) { /* ignore */ }
        process.exit(0);
    });
}

main().catch((e) => {
    send({ ok: false, error: e.message || String(e) });
    process.exit(1);
});