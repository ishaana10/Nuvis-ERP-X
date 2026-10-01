import { test, expect } from '@playwright/test';

test('Reporting tab is visible under Invoices', async ({ page }) => {
    // Navigate to admin
    await page.goto('http://localhost:8000/admin/login');
    await page.fill('input[type="email"]', 'admin@example.com');
    await page.fill('input[type="password"]', 'admin');
    await page.click('button[type="submit"]');

    await page.waitForURL('**/admin**');

    // Go to Invoices customers list
    await page.goto('http://localhost:8000/admin/invoices/customers/invoices');
    await page.waitForTimeout(2000);

    // Take screenshot of top nav
    await page.screenshot({ path: '/home/jules/verification/screenshots/reporting_tab_visible.png', fullPage: true });
});
