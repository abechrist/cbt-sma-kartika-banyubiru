#!/usr/bin/env bash
# ============================================================
# Buat ZIP siap-upload ke Hostinger (File Manager)
#
# Cara pakai:
#   ./deploy/make-upload-zip.sh               # tanpa vendor (rekomendasi, file kecil)
#   WITH_VENDOR=1 ./deploy/make-upload-zip.sh # SERTAKAN vendor (untuk tanpa SSH)
#
# Hasil: ../cbt-hostinger-upload.zip (di parent folder)
# ============================================================
set -euo pipefail

cd "$(dirname "$0")/.."
OUT="../cbt-hostinger-upload-$(date +%Y%m%d-%H%M).zip"

EXCLUDES=(
  "-x" "node_modules/*"
  "-x" ".git/*"
  "-x" ".env"
  "-x" ".env.backup"
  "-x" ".env.production"
  "-x" "tests/*"
  "-x" "docs/*"
  "-x" "playwright-report/*"
  "-x" "test-results/*"
  "-x" "*.sqlite"
  "-x" "*.sqlite-journal"
  "-x" "storage/logs/*"
  "-x" "storage/framework/cache/*"
  "-x" "storage/framework/sessions/*"
  "-x" "storage/framework/views/*"
  "-x" "storage/app/backups/*"
  "-x" ".agents/*"
  "-x" ".claude/*"
  "-x" ".factory/*"
  "-x" ".grok/*"
  "-x" ".impeccable/*"
  "-x" "skills/*"
  "-x" "public/*.png"
  "-x" ".phpunit.cache/*"
)

if [[ "${WITH_VENDOR:-0}" != "1" ]]; then
  EXCLUDES+=("-x" "vendor/*")
fi

echo ">>> Membuat $OUT ..."
# -r rekursif; file yang di-exclude tidak ikut
zip -rq "$OUT" . "${EXCLUDES[@]}"

echo ">>> Berhasil. Ukuran:"
du -h "$OUT"
echo
echo ">>> Langkah berikutnya di server:"
echo "  1. Upload & extract zip ini di folder public_html"
echo "  2. cp .env.production.example .env && php artisan key:generate"
echo "  3. php artisan migrate --force && php artisan storage:link"
if [[ "${WITH_VENDOR:-0}" != "1" ]]; then
  echo "  4. composer install --no-dev --optimize-autoloader"
fi
echo "  Lihat docs/DEPLOYMENT_HOSTINGER.md untuk detail lengkap."