# Panduan Instalasi Cepat - SPK SMART

## Langkah-langkah Instalasi

### 1. Pastikan sudah ada Composer dan PHP 8.2+

### 2. Install Dependencies
```bash
composer install
```

### 3. Copy .env
Jika belum ada file `.env`, copy dari `.env.example`:
```bash
copy .env.example .env
```

### 4. Generate Key
```bash
php artisan key:generate
```

### 5. Konfigurasi Database di .env
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=spk_smart
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Buat Database
Buat database bernama `spk_smart` di MySQL/PhpMyAdmin

### 7. Jalankan Migration dan Seeder
```bash
php artisan migrate:fresh --seed
```

### 8. Jalankan Server
```bash
php artisan serve
```

### 9. Akses Aplikasi
Buka browser: `http://localhost:8000`

### 10. Login
- Email: `admin@spksmart.com`
- Password: `password`

## Troubleshooting

Jika ada error:
```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

## Fitur Utama
1. Data Alternatif (Bank Sampah)
2. Data Kriteria (C1-C4)
3. Parameter Kriteria
4. Penilaian
5. Perhitungan SMART
6. Hasil Akhir & Ranking

Selamat menggunakan! 🎉
