const { chromium } = require('./node_modules/playwright/index.js');

const BASE  = 'http://localhost:8000';
const EMAIL = 'tom@platz.com';
const PASS  = 'test1234';

const log = (msg) => console.log(`[${new Date().toISOString().slice(11,19)}] ${msg}`);

(async () => {
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext();
    const page    = await context.newPage();

    // ── 1. Login ─────────────────────────────────────────────────────────────
    log('Navigating to login…');
    await page.goto(`${BASE}/login`);
    await page.fill('input[name="email"]', EMAIL);
    await page.fill('input[name="password"]', PASS);
    await page.click('button[type="submit"]');
    await page.waitForURL(/\/client/, { timeout: 10000 });
    log(`Logged in — now at: ${page.url()}`);

    // ── 2. Navigate to check-in ──────────────────────────────────────────────
    await page.goto(`${BASE}/client/check-in`);
    await page.waitForLoadState('networkidle');
    log(`Check-in page loaded: ${page.url()}`);

    // List all form inputs
    const inputs = await page.$$eval(
        'form input, form textarea, form select',
        els => els.map(el => ({ tag: el.tagName, name: el.name, type: el.type, value: el.value }))
    );
    log('Form inputs: ' + JSON.stringify(inputs, null, 2));

    // ── 3. Fill inputs ───────────────────────────────────────────────────────
    const numberInputs = await page.$$('form input[type="number"]');
    if (numberInputs.length > 0) {
        await numberInputs[0].fill('88.8');
        log(`Filled first number input (${await numberInputs[0].getAttribute('name')}) = 88.8`);
    }
    if (numberInputs.length > 1) {
        await numberInputs[1].fill('7777');
        log(`Filled second number input (${await numberInputs[1].getAttribute('name')}) = 7777`);
    }

    // Click "Yes" on first boolean
    const yesBtn = page.locator('button[type="button"]').filter({ hasText: /yes/i }).first();
    if (await yesBtn.count()) {
        await yesBtn.click();
        log('Clicked Yes on first boolean metric');
    }

    // Fill text area
    const textarea = page.locator('form textarea').first();
    if (await textarea.count()) {
        await textarea.fill('Playwright test note');
        log('Filled textarea');
    }

    // ── 4. Screenshot before submit ──────────────────────────────────────────
    await page.screenshot({ path: '/tmp/checkin-before.png', fullPage: true });
    log('Screenshot: /tmp/checkin-before.png');

    // ── 5. Submit and capture network ────────────────────────────────────────
    log('Clicking submit…');

    let postStatus = null;
    let postResponseUrl = null;
    let redirectLocation = null;

    page.on('response', async (resp) => {
        if (resp.url().includes('/client/check-in') && resp.request().method() === 'POST') {
            postStatus = resp.status();
            postResponseUrl = resp.url();
            redirectLocation = resp.headers()['location'] ?? null;
            log(`POST /client/check-in → ${postStatus}  location: ${redirectLocation}`);

            // Try to read body for 422 validation errors
            if (postStatus === 422 || postStatus === 302 || postStatus === 200) {
                try {
                    const body = await resp.text();
                    log(`POST response body (first 500): ${body.slice(0, 500)}`);
                } catch (e) {
                    log(`Could not read body: ${e.message}`);
                }
            }
        }
    });

    await Promise.all([
        page.waitForLoadState('networkidle', { timeout: 10000 }),
        page.getByRole('button', { name: /save check.in/i }).click(),
    ]);

    log(`After-submit URL: ${page.url()}`);
    log(`POST status captured: ${postStatus}`);

    // ── 6. Screenshot after submit ───────────────────────────────────────────
    await page.screenshot({ path: '/tmp/checkin-after.png', fullPage: true });
    log('Screenshot: /tmp/checkin-after.png');

    // ── 7. Check success / error banners ────────────────────────────────────
    const pageText = await page.innerText('body').catch(() => '');
    if (pageText.includes('saved') || pageText.includes('Check-in saved')) {
        log('✅ SUCCESS banner detected in page');
    } else {
        log('❌ No success banner found');
    }

    // ── 8. Reload and verify values persisted ───────────────────────────────
    const currentUrl = page.url();
    await page.goto(currentUrl);
    await page.waitForLoadState('networkidle');

    const weightAfter = await page.$eval(
        'form input[type="number"]:first-of-type',
        el => el.value
    ).catch(() => '(not found)');
    log(`Weight input after reload: "${weightAfter}"`);

    await page.screenshot({ path: '/tmp/checkin-reload.png', fullPage: true });
    log('Screenshot: /tmp/checkin-reload.png');

    // Final verdict
    const saved = weightAfter === '88.8';
    log(saved ? '✅ DATA WAS SAVED' : '❌ DATA WAS NOT SAVED');

    await browser.close();
})();
