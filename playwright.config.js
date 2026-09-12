import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
    testDir: './tests/e2e',
    fullyParallel: false,
    forbidOnly: true,
    timeout: 120_000,
    expect: { timeout: 10_000 },
    retries: 0,
    workers: 1,
    reporter: [['list'], ['html', { outputFolder: 'storage/app/private/playwright-report', open: 'never' }]],
    outputDir: 'storage/app/private/playwright-results',
    use: {
        baseURL: process.env.E2E_BASE_URL || 'http://127.0.0.1:8000',
        channel: 'chrome',
        headless: true,
        screenshot: 'only-on-failure',
        trace: 'retain-on-failure',
        video: 'retain-on-failure',
    },
    projects: [
        { name: 'desktop-chrome', use: { ...devices['Desktop Chrome'] } },
    ],
});
