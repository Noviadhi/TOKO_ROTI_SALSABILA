# Deploy ke Vercel dan Supabase

Panduan ini menyiapkan Laravel 12 agar berjalan di Vercel memakai runtime PHP komunitas dan database PostgreSQL dari Supabase.

## 1. Buat database Supabase

1. Buka Supabase, buat project baru, lalu simpan password database.
2. Di dashboard Supabase, klik **Connect** dan salin **Session Pooler connection string**.
3. Jika ingin mengikuti rekomendasi Supabase untuk memisahkan schema Laravel dari `public`, buat schema baru misalnya `laravel`, lalu set `DB_SCHEMA=laravel`. Jika tidak, biarkan `DB_SCHEMA=public`.

## 2. Siapkan environment Vercel

Tambahkan variable berikut di **Vercel Project Settings > Environment Variables**.

```env
APP_NAME="Ratisabilla Snack"
APP_ENV=production
APP_KEY=base64:ISI_DARI_PERINTAH_KEY_GENERATE
APP_DEBUG=false
APP_URL=https://domain-vercel-kamu.vercel.app

LOG_CHANNEL=stderr
LOG_LEVEL=warning

DB_CONNECTION=pgsql
DB_URL=postgres://postgres.your-project-ref:your-password@your-pooler-host.pooler.supabase.com:5432/postgres
DB_SSLMODE=require
DB_SCHEMA=public

SESSION_DRIVER=cookie
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
CACHE_STORE=array
QUEUE_CONNECTION=sync

LARAVEL_STORAGE_PATH=/tmp/laravel-storage
VIEW_COMPILED_PATH=/tmp/laravel-storage/framework/views
```

Generate `APP_KEY` tanpa mengubah file lokal:

```bash
php artisan key:generate --show
```

## 3. Import project ke Vercel

1. Push repository ke GitHub.
2. Di Vercel, pilih **Add New Project** lalu import repository ini.
3. Framework preset boleh **Other**.
4. `vercel.json` sudah mengatur:
   - build asset Vite dengan `npm install && npm run build`
   - semua route Laravel ke `api/index.php`
   - static asset dari `public/build`, `public/images`, `favicon.ico`, dan `robots.txt`

## 4. Jalankan migrasi ke Supabase

Migrasi jangan dijalankan otomatis di build Vercel supaya deploy tidak berulang-ulang mengubah database. Jalankan sekali dari lokal setelah `.env` lokal diarahkan ke Supabase:

```bash
php artisan migrate --seed --force
```

Jika data seed tidak dibutuhkan di production, pakai:

```bash
php artisan migrate --force
```

## 5. Catatan serverless

- Vercel Function punya filesystem read-only, jadi `api/index.php` mengarahkan storage Laravel ke `/tmp/laravel-storage`.
- Gunakan `SESSION_DRIVER=cookie` dan `CACHE_STORE=array` untuk menghindari penulisan file/session di disk permanen.
- Untuk upload file production, gunakan storage eksternal seperti Supabase Storage atau S3-compatible storage, bukan filesystem lokal Vercel.
