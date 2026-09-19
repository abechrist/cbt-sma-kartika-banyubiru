import { test, expect } from '@playwright/test';

test.describe('Manajemen Data Akademik & Master (Academic & Question Bank)', () => {

  test.beforeEach(async ({ page }) => {
    // Login sebagai admin
    await page.goto('/login');
    await page.fill('#login', 'admin@kartika.sch.id');
    await page.fill('input[name="password"]', 'password');
    await page.locator('form[action*="login"] button[type="submit"]').click();
    await page.waitForURL('**/dashboard');
  });

  test('Admin dapat membuat rombel/kelas baru dan tampil di daftar kelas', async ({ page }) => {
    await page.goto('/classes');
    await expect(page.locator('h1', { hasText: 'Rombongan Belajar' })).toBeVisible();

    // Buka form tambah kelas
    await page.click('a[href*="/classes/create"]');
    await page.waitForURL('**/classes/create');

    const testClassName = `X-TEST-${Date.now().toString().slice(-4)}`;
    await page.fill('input[name="name"]', testClassName);
    await page.fill('input[name="grade"]', '10');
    await page.fill('input[name="academic_year"]', '2026/2027');
    await page.fill('textarea[name="description"]', 'Kelas pengujian automated Playwright');
    
    // Klik tombol submit form simpan kelas
    await page.locator('button', { hasText: 'Simpan Kelas' }).click();

    // Diarahkan kembali ke daftar kelas dengan notifikasi sukses
    await page.waitForURL('**/classes');
    await expect(page.locator(`text=${testClassName}`).first()).toBeVisible();
  });

  test('Admin dapat membuat mata pelajaran baru dan memilih guru pengampu', async ({ page }) => {
    await page.goto('/subjects');
    await expect(page.locator('h1', { hasText: 'Mata Pelajaran' })).toBeVisible();

    // Buka form tambah mata pelajaran
    await page.click('a[href*="/subjects/create"]');
    await page.waitForURL('**/subjects/create');

    const testSubjectName = `Biologi Uji-${Date.now().toString().slice(-4)}`;
    await page.fill('input[name="name"]', testSubjectName);
    await page.fill('input[name="code"]', `BIO-${Date.now().toString().slice(-3)}`);
    
    // Pilih guru pengampu pertama jika ada opsi
    const teacherSelect = page.locator('select[name="teacher_id"]');
    const teacherOptions = await teacherSelect.locator('option').all();
    if (teacherOptions.length > 1) {
      await teacherSelect.selectOption({ index: 1 });
    }

    await page.fill('textarea[name="description"]', 'Mata pelajaran pengujian otomatis');
    await page.locator('button', { hasText: 'Simpan Mata Pelajaran' }).click();

    // Berhasil kembali ke /subjects
    await page.waitForURL('**/subjects');
    await expect(page.locator(`text=${testSubjectName}`).first()).toBeVisible();
  });

  test('Guru/Admin dapat membuat butir soal baru dengan opsi jawaban', async ({ page }) => {
    await page.goto('/questions');
    await expect(page.locator('h1', { hasText: 'Bank Soal' })).toBeVisible();

    // Buka form tambah butir soal
    await page.click('a[href*="/questions/create"]');
    await page.waitForURL('**/questions/create');

    // Isi formulir dasar
    await page.locator('select[name="subject_id"]').selectOption({ index: 1 });
    await page.locator('select[name="type"]').selectOption('pg');
    await page.locator('select[name="difficulty"]').selectOption('medium');
    await page.fill('input[name="score"]', '10');

    const uniqueQuestion = `Berapakah nilai 2 pangkat 5? [ID: ${Date.now()}]`;
    await page.fill('textarea[name="question_text"]', uniqueQuestion);

    // Isi teks opsi jawaban
    await page.fill('input[name="options[0][option_text]"]', '16');
    await page.fill('input[name="options[1][option_text]"]', '32');
    await page.fill('input[name="options[2][option_text]"]', '64');
    await page.fill('input[name="options[3][option_text]"]', '8');

    // Pastikan opsi B (indeks 1) ditandai sebagai benar
    const checkB = page.locator('input[name="options[1][is_correct]"]');
    await checkB.check();

    await page.locator('button', { hasText: 'Simpan Butir Soal' }).click();

    // Verifikasi kembali ke bank soal dan butir soal tercantum
    await page.waitForURL('**/questions');
    await expect(page.locator('text=Soal berhasil ditambahkan').first()).toBeVisible();
  });
});
