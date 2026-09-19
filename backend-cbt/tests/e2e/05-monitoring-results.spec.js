import { test, expect } from '@playwright/test';

test.describe('Monitoring Ujian & Hasil Penilaian (Monitoring & Analytics)', () => {

  test.beforeEach(async ({ page }) => {
    // Login sebagai Admin/Proktor
    await page.goto('/login');
    await page.fill('#login', 'admin@kartika.sch.id');
    await page.fill('input[name="password"]', 'password');
    await page.locator('form[action*="login"] button[type="submit"]').click();
    await page.waitForURL('**/dashboard');
  });

  test('Pengawas/Admin dapat membuka pusat monitoring live ujian', async ({ page }) => {
    await page.goto('/monitoring');
    await expect(page.locator('h1', { hasText: 'Monitoring Ujian Berjalan' })).toBeVisible();

    // Pastikan session yang aktif tadi muncul di monitoring list
    await expect(page.locator('text=Sesi E2E Playwright CBT Live').first()).toBeVisible();
    await expect(page.locator('text=Lab Komputer CBT A').first()).toBeVisible();
  });

  test('Pendidik/Admin dapat membuka rekapitulasi nilai capaian siswa', async ({ page }) => {
    await page.goto('/results');
    await expect(page.locator('h1', { hasText: 'Rekapitulasi Capaian Nilai' })).toBeVisible();

    // Verifikasi tombol export CSV tersedia
    const exportBtn = page.locator('a[href*="/export/rekap"]').first();
    await expect(exportBtn).toBeVisible();

    // Verifikasi hasil ujian siswa (Ahmad Fauzan) tercantum di rekap
    await expect(page.locator('table').getByText('Ahmad Fauzan').first()).toBeVisible();
  });

  test('Guru/Admin dapat mengakses modul psikometri analisis butir soal', async ({ page }) => {
    await page.goto('/item-analysis');
    await expect(page.locator('h1', { hasText: 'Analisis Butir Soal' })).toBeVisible();

    // Verifikasi paket ujian matematika tercantum di tabel
    await expect(page.locator('table').getByText('Matematika').first()).toBeVisible();
  });
});
