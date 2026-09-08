# Sistem Absensi Laravel

Migrasi dari aplikasi PHP lama ada di folder `../sistem-absensi`. Aplikasi ini memakai Laravel, MySQL, unggahan foto pada disk `public`, dan autentikasi untuk area admin.

## Menjalankan secara lokal

1. Pasang PHP 8.3+, Composer, dan MySQL.
2. Salin `.env.example` menjadi `.env`, lalu sesuaikan `APP_URL`, `DB_*`, dan kredensial admin berikut:

   ```dotenv
   ADMIN_NAME="Administrator"
   ADMIN_EMAIL=admin@contoh.go.id
   ADMIN_PASSWORD="ganti-dengan-kata-sandi-kuat"
   ```

3. Jalankan `composer install`, `php artisan key:generate`, `php artisan migrate --seed`, dan `php artisan storage:link`.
4. Untuk pengujian dari perangkat lain pada Wi-Fi yang sama: `php artisan serve --host=0.0.0.0 --port=8000`. Buka `http://IP-KOMPUTER:8000` dari perangkat tersebut.

## Deploy publik

Deploy ke layanan hosting/VPS yang mendukung PHP 8.3+ dan arahkan document root ke folder `public/`, bukan akar proyek. Konfigurasikan domain HTTPS, isi `APP_URL` dengan domain tersebut, set `APP_ENV=production`, `APP_DEBUG=false`, jalankan migrasi, lalu pastikan `storage/` dan `bootstrap/cache/` dapat ditulis.

Halaman `/` terbuka untuk semua peserta magang. Halaman `/admin` memerlukan akun admin. Foto absensi tersimpan di `storage/app/public/attendance` dan hanya tautannya yang ditampilkan pada rekap admin.
