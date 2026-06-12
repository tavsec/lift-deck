import { chromium } from '@playwright/test';

const BASE = 'http://localhost:8000';
const EMAIL = 'tom@platz.com';
const PASS  = 'test1234';

const browser = await chromium.launch({ headless: true });
const context = await browser.newContext();
const page    = await context.newPage();

// ── Helpers ─────────────────────────────────────────────────────────────────
const log = (msg) => console.log(`[${new Date().toISOString().slice(11,19)}] ${msg}`);

// ── 1. Login ─────────────────────────────────────────────────────────────────
log('Navigating to login page…');
await page.goto(`${BASE}/login`);
await page.fill('input[name="email"]', EMAIL);
await page.fill('input[name="password"]', PASS);
await page.click('button[type="submit"]');
await page.waitForURL(/\/client/, { timeout: 8000 });
log(`Logged in — now at: ${page.url()}`);

// ── 2. Navigate to check-in ──────────────────────────────────────────────────
await page.goto(`${BASE}/client/check-in`);
await page.waitForLoadState('networkidle');
log(`Check-in page loaded: ${page.url()}`);

// Snapshot form before submission
const metricsBefore = await page.$$eval('input[name^="metrics["], textarea[name^="metrics["]', els =>
  els.map(el => ({ name: el.name, value: el.value }))
);
log('Metric inputs found: ' + JSON.stringify(metricsBefore));

// ── 3. Fill number / text inputs ────────────────────────────────────────────
const weightInput = page.locator('input[name^="metrics["][type="number"]').first();
if (await weightInput.count()) {
  await weightInput.fill('88.8');
  log('Filled weight input with 88.8');
}

const stepsInput = page.locator('input[name^="metrics["][type="number"]').nth(1);
if (await stepsInput.count()) {
  await stepsInput.fill('7777');
  log('Filled steps input with 7777');
}

// Boolean — click "Yes" for first boolean metric
const yesBtn = page.locator('button[type="button"]').filter({ hasText: /yes/i }).first();
if (await yesBtn.count()) {
  await yesBtn.click();
  log('Clicked Yes on first boolean metric');
}

// Text area
const textarea = page.locator('textarea[name^="metrics["]').first();
if (await textarea.count()) {
  await textarea.fill('Playwright test note');
  log('Filled text area');
}

// Screenshot before submit
await page.screenshot({ path: '/tmp/before-submit.png', fullPage: true });
log('Screenshot saved: /tmp/before-submit.png');

// ── 4. Submit ────────────────────────────────────────────────────────────────
log('Submitting form…');

const [response] = await Promise.all([
  page.waitForResponse(r => r.url().includes('/client/check-in') && r.request().method() === 'POST', { timeout: 8000 }),
  page.locator('button[type="submit"]').click(),
]);

log(`POST response status: ${response.status()}`);
log(`POST response URL:    ${response.url()}`);

// Wait for navigation to settle
await page.waitForLoadState('networkidle');
log(`After submit URL: ${page.url()}`);

// Screenshot after submit
await page.screenshot({ path: '/tmp/after-submit.png', fullPage: true });
log('Screenshot saved: /tmp/after-submit.png');

// ── 5. Check for success banner ──────────────────────────────────────────────
const successText = await page.locator('[class*="green"]').textContent().catch(() => null);
log(`Success banner text: ${successText ?? '(none found)'}`);

const pageContent = await page.content();
const hasSuccess = pageContent.includes('saved') || pageContent.includes('Check-in');
log(`Page contains "saved"/"Check-in": ${hasSuccess}`);

// ── 6. Verify DB via API / form values ───────────────────────────────────────
// Re-load the page and check if values are pre-populated
await page.goto(page.url());
await page.waitForLoadState('networkidle');

const weightAfter = await page.locator('input[name^="metrics["][type="number"]').first().inputValue().catch(() => null);
log(`Weight input value after reload: ${weightAfter}`);

await page.screenshot({ path: '/tmp/after-reload.png', fullPage: true });
log('Screenshot saved: /tmp/after-reload.png');

// ── 7. Validation error check ────────────────────────────────────────────────
const errors = await page.locator('[class*="red"], [class*="error"], .alert').allTextContents();
log(`Visible errors: ${JSON.stringify(errors)}`);

await browser.close();
log('Done.');
