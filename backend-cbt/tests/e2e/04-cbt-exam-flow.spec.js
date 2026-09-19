import { test, expect } from '@playwright/test';
import { execSync } from 'child_process';

test.describe('Siklus Lengkap Pengerjaan Ujian Siswa (CBT Full Lifecycle)', () => {

  test.beforeAll(async () => {
    const setupCommand = `php artisan tinker --execute '
      $exam = App\\Models\\Exam::whereHas("questions")->first();
      $exam->update(["randomize_questions" => false]);
      $session = App\\Models\\ExamSession::updateOrCreate(
        ["name" => "Sesi E2E Playwright CBT Live"],
        [
          "exam_id" => $exam->id,
          "start_at" => now()->subMinutes(15),
          "end_at" => now()->addHours(3),
          "room" => "Lab Komputer CBT A",
          "max_participants" => 50,
          "status" => "open",
          "token_prefix" => "CBT",
          "instructions" => "Petunjuk pengerjaan otomatis Playwright",
          "allow_resume" => true,
          "auto_submit_on_timeout" => true,
        ]
      );
      App\\Models\\ExamToken::updateOrCreate(
        ["token" => "CBT-PLAYTEST1"],
        [
          "exam_session_id" => $session->id,
          "expires_at" => now()->addHours(3),
          "is_active" => true,
          "is_single_use" => false,
        ]
      );
      $user = App\\Models\\User::where("nisn", "00000001")->first();
      if ($user) {
        App\\Models\\ExamAttempt::where("user_id", $user->id)->where("exam_session_id", $session->id)->delete();
      }
    '`;
    execSync(setupCommand.replace(/\n/g, ' '));
  });

  test('Siswa dapat memasukkan token, mengerjakan ujian, menyimpan jawaban, dan melihat hasil capaian', async ({ page }) => {
    // 1. Siswa login menggunakan NISN
    await page.goto('/login');
    await page.fill('#login', '00000001');
    await page.fill('input[name="password"]', 'password');
    await page.locator('form[action*="login"] button[type="submit"]').click();
    await page.waitForURL('**/dashboard');

    // 2. Masuk ke halaman aktivasi token ujian
    await page.goto('/exam/token');
    await expect(page.locator('h1', { hasText: 'Masukkan Token Ujian' })).toBeVisible();

    // 3. Masukkan kode token yang telah disiapkan
    await page.fill('#tokenInput', 'CBT-PLAYTEST1');
    await page.locator('button[type="submit"]', { hasText: 'Mulai Ujian' }).click();

    // 4. Diarahkan ke lembar pengerjaan ujian
    await page.waitForURL('**/exam/take/**');
    await expect(page.locator('.question-card.active')).toBeVisible();

    // 5. Pilih salah satu jawaban pada soal yang aktif
    const activeRadio = page.locator('.question-card.active input[type="radio"]').first();
    await activeRadio.check();

    // Verifikasi tombol navigasi soal berubah status menjadi terjawab
    await expect(page.locator('.question-nav-btn.answered').first()).toBeVisible();

    // 6. Tangani dialog konfirmasi pengumpulan ujian
    page.on('dialog', async dialog => {
      await dialog.accept();
    });

    // 7. Kumpulkan ujian
    const submitBtn = page.locator('#submitForm button[type="submit"]');
    await submitBtn.click();

    // 8. Diarahkan ke halaman hasil penilaian ujian
    await page.waitForURL('**/exam/result/**');
    await expect(page.locator('h1', { hasText: 'Hasil Penilaian Ujian' })).toBeVisible();
    await expect(page.locator('text=Nilai Akhir Capaian')).toBeVisible();
    await expect(page.locator('text=Informasi Pelaksanaan Ujian')).toBeVisible();
  });
});
