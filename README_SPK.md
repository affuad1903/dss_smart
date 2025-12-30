# Sistem Pendukung Keputusan Penentuan Prioritas Bank Sampah Sebagai HUB Program 3R SMART

Aplikasi berbasis web untuk menentukan prioritas Bank Sampah terbaik sebagai HUB (Titik Pengumpulan) menggunakan metode SMART (Simple Multi Attribute Rating Technique).

## Informasi Proyek

- **Judul**: SPK Penentuan Prioritas Bank Sampah Sebagai HUB Program 3R SMART
- **Metode**: SMART (Simple Multi Attribute Rating Technique)
- **Pembuat**: Affandi Putra Pradana
- **Institusi**: Universitas Mercu Buana Yogyakarta
- **Program Studi**: Sistem Informasi

## Fitur Aplikasi

1. **Dashboard Utama** - Statistik dan informasi sistem
2. **Data Alternatif** - Manajemen Bank Sampah (CRUD)
3. **Data Kriteria** - Manajemen kriteria penilaian dengan bobot
4. **Parameter Kriteria** - Pengaturan range nilai untuk setiap kriteria
5. **Penilaian** - Input nilai untuk setiap Bank Sampah
6. **Perhitungan SMART** - Proses normalisasi dan perhitungan utilitas
7. **Hasil Akhir** - Perankingan Bank Sampah prioritas

## Kriteria Penilaian

| Kode | Nama Kriteria | Bobot |
|------|--------------|-------|
| C1 | Volume Timbulan Sampah Wilayah | 0.4 (40%) |
| C2 | Aksesibilitas Transportasi DLH | 0.3 (30%) |
| C3 | Kepadatan Penduduk | 0.2 (20%) |
| C4 | Jarak Bank Sampah ke TPA | 0.1 (10%) |

## Teknologi yang Digunakan

- **Framework**: Laravel 11
- **Database**: MySQL/MariaDB
- **Frontend**: Bootstrap 5, Bootstrap Icons
- **PHP**: 8.2+

## Instalasi

### 1. Clone/Download Project

```bash
cd c:\Kuliah\Semester 7\DSS\UAS\spk_smart
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Konfigurasi Environment

Copy file `.env.example` menjadi `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=spk_smart
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Jalankan Migration dan Seeder

```bash
php artisan migrate:fresh --seed
```

Ini akan membuat:
- Tabel-tabel database
- User default (admin@spksmart.com / password)
- Data kriteria dan parameter kriteria

### 6. Jalankan Aplikasi

```bash
php artisan serve
```

Akses aplikasi di: `http://localhost:8000`

## Login Default

- **Email**: admin@spksmart.com
- **Password**: password

## Cara Penggunaan

### 1. Login
- Buka aplikasi dan login menggunakan kredensial default

### 2. Tambah Data Alternatif
- Masuk ke menu **Data Alternatif**
- Klik tombol **Tambah Alternatif**
- Masukkan nama Bank Sampah
- Kode akan dibuat otomatis (A1, A2, A3, dst)

### 3. Periksa Data Kriteria
- Masuk ke menu **Data Kriteria**
- Pastikan total bobot = 1
- Edit bobot jika diperlukan

### 4. Periksa Parameter Kriteria
- Masuk ke menu **Parameter Kriteria**
- Lihat range nilai untuk setiap kriteria
- Tambah/edit parameter jika diperlukan

### 5. Lakukan Penilaian
- Masuk ke menu **Penilaian**
- Klik tombol pada setiap sel untuk memberikan nilai
- Masukkan nilai aktual untuk setiap kriteria:
  - **C1**: Volume sampah dalam kg
  - **C2**: Pilih jenis transportasi
  - **C3**: Kepadatan penduduk dalam Jiwa/Km²
  - **C4**: Jarak ke TPA dalam Km
- Sistem akan menentukan nilai parameter otomatis

### 6. Lihat Perhitungan
- Masuk ke menu **Perhitungan**
- Lihat proses perhitungan SMART:
  - Tabel Penilaian (nilai parameter)
  - Nilai Ekstrem (min-max)
  - Nilai Utilitas (normalisasi)

### 7. Lihat Hasil Akhir
- Masuk ke menu **Hasil Akhir**
- Lihat tabel perhitungan lengkap
- Lihat perankingan Bank Sampah
- Bank Sampah dengan ranking #1 adalah prioritas utama untuk dijadikan HUB

## Metode SMART

### Langkah Perhitungan

1. **Input Nilai Aktual**
   - Masukkan data riil untuk setiap alternatif per kriteria

2. **Konversi ke Nilai Parameter**
   - Nilai aktual dikonversi ke skala 1-5 berdasarkan range parameter

3. **Normalisasi (Nilai Utilitas)**
   ```
   U(a) = (Nilai - Min) / (Max - Min)
   ```
   - Mengubah nilai parameter ke skala 0-1

4. **Perhitungan Nilai Preferensi**
   ```
   V(a) = Σ (Bobot × Utilitas)
   ```
   - Mengalikan bobot kriteria dengan nilai utilitas
   - Menjumlahkan semua hasil perkalian

5. **Perankingan**
   - Urutkan alternatif dari nilai V tertinggi ke terendah
   - Nilai V tertinggi = Prioritas terbaik

## Struktur Database

### Tabel `alternatif`
- id (PK)
- kode (A1, A2, A3, ...)
- nama (Nama Bank Sampah)

### Tabel `kriteria`
- id (PK)
- kode (C1, C2, C3, C4)
- nama (Nama Kriteria)
- bobot (0-1, total = 1)

### Tabel `parameter_kriteria`
- id (PK)
- kriteria_id (FK)
- deskripsi (Deskripsi range)
- nilai (1-5)
- batas_bawah (nullable)
- batas_atas (nullable)

### Tabel `penilaian`
- id (PK)
- alternatif_id (FK)
- kriteria_id (FK)
- nilai_aktual (Nilai riil)
- nilai_parameter (1-5)

## Catatan Penting

1. **Total Bobot Kriteria** harus selalu = 1
2. **Nilai Parameter** berkisar 1-5 (semakin tinggi = semakin baik)
3. **Nilai Utilitas** hasil normalisasi berkisar 0-1
4. **Nilai Preferensi (V)** menentukan ranking akhir
5. Untuk kriteria **C2 (Transportasi)**, pilih kategori (Motor, Pick Up, dll)
6. Untuk kriteria **C1, C3, C4**, masukkan nilai numerikal

## Troubleshooting

### Error "Class not found"
```bash
composer dump-autoload
```

### Error Migration
```bash
php artisan migrate:fresh --seed
```

### Cache Clear
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## Pengembangan Lebih Lanjut

Beberapa fitur yang dapat ditambahkan:
- Export hasil ke PDF/Excel
- Grafik visualisasi data
- History penilaian
- Multiple user dengan role
- Backup database otomatis

## Lisensi

Proyek ini dibuat untuk keperluan akademik (Skripsi/Tugas Kuliah).

## Kontak

Affandi Putra Pradana  
Program Studi Sistem Informasi  
Universitas Mercu Buana Yogyakarta

---

**Catatan**: Pastikan semua dependensi terinstal dengan baik sebelum menjalankan aplikasi.
