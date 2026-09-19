import { test, expect } from '@playwright/test';

test.describe('Integrasi RPP dengan Modul LMS dan CBT (RPP Integration)', () => {

  test.beforeEach(async ({ page }) => {
    // Login sebagai Admin
    await page.goto('/login');
    await page.fill('#login', 'admin@kartika.sch.id');
    await page.fill('input[name="password"]', 'password');
    await page.locator('form[action*="login"] button[type="submit"]').click();
    await page.waitForURL('**/dashboard');
  });

  test('Admin dapat melihat daftar RPP yang telah diimpor', async ({ page }) => {
    await page.goto('/rpps');
    await expect(page.locator('h1', { hasText: 'Daftar RPP' })).toBeVisible();

    // Pastikan topik RPP seeded tampil
    await expect(page.locator('text=Teks Eksplanasi').first()).toBeVisible();
    await expect(page.locator('a[href*="/rpps/create"]').first()).toBeVisible();
  });

  test('Admin dapat melihat detail RPP serta tab integrasi LMS dan CBT', async ({ page }) => {
    await page.goto('/rpps/1');
    await expect(page.locator('h1', { hasText: 'Teks Eksplanasi' })).toBeVisible();

    // 1. Verifikasi Tab Navigasi Integrasi
    const overviewTab = page.locator('button', { hasText: 'Overview' });
    const lmsTab = page.locator('button', { hasText: 'LMS Integration' });
    const cbtTab = page.locator('button', { hasText: 'CBT Integration' });

    await expect(overviewTab).toBeVisible();
    await expect(lmsTab).toBeVisible();
    await expect(cbtTab).toBeVisible();

    // 2. Akses Tab LMS Integration
    await lmsTab.click();
    await expect(page.locator('text=Integrasi ke LMS').first()).toBeVisible();

    // 3. Akses Tab CBT Integration
    await cbtTab.click();
    await expect(page.locator('text=Integrasi ke CBT').first()).toBeVisible();
  });

  test('Endpoint API integration-status mengembalikan data integrasi RPP yang valid', async ({ page, request }) => {
    // Pastikan session autentikasi terbawa ke request context
    const response = await page.goto('/rpps/1/integration-status');
    expect(response?.status()).toBe(200);

    const json = await response?.json();
    expect(json.success).toBe(true);
    expect(json.data.topic).toBe('Teks Eksplanasi');
    expect(json.data).toHaveProperty('lms_integration');
    expect(json.data).toHaveProperty('cbt_integration');
  });
});
