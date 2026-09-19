import { test, expect } from '@playwright/test';

test.describe('Autentikasi & Kontrol Akses (Auth & RBAC)', () => {

  test('Halaman Portal / Welcome dapat dimuat dengan baik', async ({ page }) => {
    await page.goto('/');
    await expect(page).toHaveTitle(/SMA Kartika III-1 Banyubiru/i);
    
    // Periksa keberadaan tombol Masuk / Portal
    const loginLink = page.locator('a[href*="login"]').first();
    await expect(loginLink).toBeVisible();
  });

  test('Admin dapat login dengan email dan password', async ({ page }) => {
    await page.goto('/login');
    await expect(page.locator('h1').first()).toContainText('SMA KARTIKA III-1 BANYUBIRU');

    await page.fill('#login', 'admin@kartika.sch.id');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');

    // Berhasil diarahkan ke /dashboard
    await page.waitForURL('/dashboard');
    await expect(page.locator('h1', { hasText: 'Selamat Datang' })).toBeVisible();
    await expect(page.locator('h1', { hasText: 'Administrator' })).toBeVisible();
  });

  test('Siswa dapat login dengan NISN dan password', async ({ page }) => {
    await page.goto('/login');

    // Siswa pertama memiliki NISN 00000001
    await page.fill('#login', '00000001');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');

    await page.waitForURL('/dashboard');
    await expect(page.locator('h1', { hasText: 'Selamat Datang' })).toBeVisible();
    await expect(page.locator('h1', { hasText: 'Ahmad Fauzan' })).toBeVisible();
  });

  test('Login dengan password salah menampilkan pesan kesalahan', async ({ page }) => {
    await page.goto('/login');

    await page.fill('#login', 'admin@kartika.sch.id');
    await page.fill('input[name="password"]', 'wrong-password-123');
    await page.click('button[type="submit"]');

    await expect(page.locator('text=Kredensial tidak valid.').first()).toBeVisible();
    expect(page.url()).toContain('/login');
  });

  test('Pengguna dapat melakukan logout dan sesi dibersihkan', async ({ page }) => {
    await page.goto('/login');
    await page.fill('#login', 'admin@kartika.sch.id');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    await page.waitForURL('/dashboard');

    // Klik tombol logout
    const logoutBtn = page.locator('button[title="Keluar dari sistem"]').first();
    await logoutBtn.click();

    // Verifikasi kembali ke portal atau login
    await page.waitForURL(url => url.pathname === '/' || url.pathname === '/login');
    
    // Akses dashboard kembali harus diarahkan ke login
    await page.goto('/dashboard');
    await page.waitForURL('/login');
  });

  test('Siswa dialihkan kembali ke dashboard saat mengakses halaman restricted (/users)', async ({ page }) => {
    // Login sebagai Siswa
    await page.goto('/login');
    await page.fill('#login', '00000001');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    await page.waitForURL('/dashboard');

    // Coba akses rute admin (/users) -> dicegat middleware CheckRole dan di-redirect ke /dashboard
    await page.goto('/users');
    await page.waitForURL('/dashboard');
    expect(page.url()).toContain('/dashboard');
  });
});
