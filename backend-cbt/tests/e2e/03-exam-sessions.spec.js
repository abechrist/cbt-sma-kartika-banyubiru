import { test, expect } from '@playwright/test';

test.describe('Manajemen Sesi Ujian & Distribusi Token (Sessions & Tokens)', () => {

  test.beforeEach(async ({ page }) => {
    await page.goto('/login');
    await page.fill('#login', 'admin@kartika.sch.id');
    await page.fill('input[name="password"]', 'password');
    await page.locator('form[action*="login"] button[type="submit"]').click();
    await page.waitForURL('**/dashboard');
  });

  test('Admin dapat melihat daftar paket ujian yang telah dipublikasikan', async ({ page }) => {
    await page.goto('/exams');
    await expect(page.locator('h1', { hasText: 'Paket Ujian' })).toBeVisible();

    // Verifikasi ujian matematika seeded tampil di tabel
    await expect(page.locator('table').getByText('Matematika').first()).toBeVisible();
  });

  test('Admin dapat menjadwalkan sesi ujian baru, membuka sesi, dan menghasilkan token ujian', async ({ page }) => {
    await page.goto('/sessions');
    await expect(page.locator('h1', { hasText: 'Sesi Ujian' })).toBeVisible();

    // Buka form penjadwalan sesi
    await page.click('a[href*="/sessions/create"]');
    await page.waitForURL('**/sessions/create');

    // Pilih paket ujian pertama
    await page.locator('select[name="exam_id"]').selectOption({ index: 1 });

    const sessionName = `Sesi CBT Test-${Date.now().toString().slice(-4)}`;
    await page.fill('input[name="name"]', sessionName);

    // Format tanggal ISO local YYYY-MM-DDTHH:mm
    const now = new Date();
    const startStr = new Date(now.getTime() - 10 * 60000).toISOString().slice(0, 16); // 10 menit lalu
    const endStr = new Date(now.getTime() + 4 * 3600000).toISOString().slice(0, 16);   // 4 jam ke depan

    await page.fill('input[name="start_at"]', startStr);
    await page.fill('input[name="end_at"]', endStr);
    await page.fill('input[name="room"]', 'Laboratorium Komputer 1');
    await page.fill('input[name="max_participants"]', '30');
    await page.fill('input[name="token_prefix"]', 'TEST');

    // Submit form sesi
    await page.locator('button', { hasText: 'Simpan Sesi Ujian' }).click();

    // Berhasil disimpan dan kembali ke index atau show
    await page.waitForURL('**/sessions**');
    await expect(page.locator(`text=${sessionName}`).first()).toBeVisible();

    // Masuk ke detail sesi
    await page.locator(`text=${sessionName}`).first().click();
    await page.waitForURL(/\/sessions\/\d+/);

    // 1. Generate Token Baru
    await page.fill('input[name="count"]', '5');
    await page.locator('button', { hasText: 'Generate Token Baru' }).click();
    await expect(page.locator('text=token berhasil dibuat').first()).toBeVisible();

    // 2. Buka Sesi Ujian
    const openBtn = page.locator('button', { hasText: 'Buka Sesi' });
    if (await openBtn.isVisible()) {
      await openBtn.click();
      await expect(page.locator('text=Sesi ujian dibuka').first()).toBeVisible();
    }

    // 3. Verifikasi tombol Cetak Token berfungsi
    const printTokensLink = page.locator('a[href*="/print-tokens"]').first();
    await expect(printTokensLink).toBeVisible();
    const printUrl = await printTokensLink.getAttribute('href');
    expect(printUrl).toContain('/print-tokens');

    // Kunjungi halaman cetak token langsung
    await page.goto(printUrl);
    await expect(page.locator('h2', { hasText: 'LEMBAR TOKEN RUANG UJIAN' })).toBeVisible();
    await expect(page.locator('.token').first()).toBeVisible();
  });
});
