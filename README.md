# 🏆 SPK SMART - Sistem Pendukung Keputusan Metode SMART

Aplikasi web untuk pemilihan Bank Sampah sebagai HUB Program 3R SMART menggunakan metode **SMART (Simple Multi-Attribute Rating Technique)**.

## 📋 Deskripsi

Sistem Pendukung Keputusan ini membantu dalam menentukan prioritas Bank Sampah yang paling layak dijadikan sebagai HUB (titik pengumpulan) Program 3R SMART berdasarkan multiple kriteria penilaian.

### Kriteria Penilaian:
- **C1**: Volume Timbulan Sampah (Bobot: 0.4)
- **C2**: Aksesibilitas Transportasi (Bobot: 0.3)
- **C3**: Kepadatan Penduduk (Bobot: 0.2)
- **C4**: Jarak ke TPA (Bobot: 0.1)

## ✨ Fitur

- 📊 **Manajemen Data Alternatif** - Kelola data Bank Sampah
- 📈 **Manajemen Kriteria** - Pengaturan kriteria dan bobot penilaian
- 📝 **Parameter Kriteria** - Definisi parameter untuk setiap kriteria
- ⚖️ **Penilaian Alternatif** - Input nilai untuk setiap alternatif
- 🧮 **Perhitungan SMART** - Proses perhitungan metode SMART otomatis
- 🏅 **Perankingan** - Hasil akhir dengan ranking prioritas
- 📄 **Export CSV** - Export hasil untuk pelaporan

## 🛠️ Teknologi

- **Framework**: Laravel 11.x
- **PHP**: 8.2+
- **Database**: MySQL
- **Frontend**: Bootstrap 5, Bootstrap Icons
- **Template Engine**: Blade

## 📦 Instalasi

### Prasyarat

Pastikan sistem Anda sudah terinstall:
- PHP >= 8.2
- Composer
- MySQL/MariaDB
- Web Server (Apache/Nginx) atau gunakan built-in server Laravel

### Langkah Instalasi

1. **Clone Repository**
```bash
git clone <repository-url>
cd spk_smart
```

2. **Install Dependencies**
```bash
composer install
```

3. **Konfigurasi Environment**
```bash
# Windows
copy .env.example .env

# Linux/Mac
cp .env.example .env
```

4. **Generate Application Key**
```bash
php artisan key:generate
```

5. **Konfigurasi Database**

Edit file `.env` sesuaikan dengan konfigurasi database Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=spk_smart
DB_USERNAME=root
DB_PASSWORD=
```

6. **Buat Database**

Buat database baru dengan nama `spk_smart` melalui phpMyAdmin atau MySQL CLI:
```sql
CREATE DATABASE spk_smart;
```

7. **Jalankan Migration & Seeder**
```bash
php artisan migrate:fresh --seed
```

Perintah ini akan:
- Membuat semua tabel yang diperlukan
- Mengisi data awal (kriteria dan parameter)

8. **Jalankan Aplikasi**
```bash
php artisan serve
```

9. **Akses Aplikasi**

Buka browser dan akses: `http://localhost:8000`

### 🔑 Login Credentials

- **Email**: `admin@spksmart.com`
- **Password**: `password`

## 📖 Cara Penggunaan

1. **Login** ke sistem menggunakan kredensial admin
2. **Input Data Alternatif** - Tambahkan data Bank Sampah yang akan dinilai
3. **Cek Kriteria** - Pastikan kriteria dan bobot sudah sesuai
4. **Atur Parameter** - Sesuaikan parameter untuk setiap kriteria jika perlu
5. **Input Penilaian** - Masukkan nilai untuk setiap alternatif pada setiap kriteria
6. **Lihat Perhitungan** - Menu Perhitungan untuk melihat proses kalkulasi
7. **Lihat Hasil** - Menu Hasil untuk melihat ranking final
8. **Export CSV** - Download hasil untuk pelaporan

## 📁 Struktur Project

```
spk_smart/
├── app/
│   ├── Http/Controllers/    # Controller aplikasi
│   └── Models/              # Model Eloquent
├── database/
│   ├── migrations/          # Database migrations
│   └── seeders/             # Database seeders
├── resources/
│   └── views/               # Blade templates
├── routes/
│   └── web.php             # Route definitions
└── public/                  # Public assets
```

## 🔧 Troubleshooting

Jika mengalami error, coba jalankan:

```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

**Error Permission (Linux/Mac)**:
```bash
chmod -R 775 storage bootstrap/cache
```

## 📚 Dokumentasi Tambahan

- [INSTALASI.md](INSTALASI.md) - Panduan instalasi cepat
- [DOKUMENTASI_SMART.md](DOKUMENTASI_SMART.md) - Penjelasan metode SMART
- [README_SPK.md](README_SPK.md) - Dokumentasi SPK lengkap

## 🤝 Kontribusi

Kontribusi sangat diterima! Silakan buat issue atau pull request.

## 📝 License

Aplikasi ini menggunakan framework Laravel yang berlisensi [MIT license](https://opensource.org/licenses/MIT).

## 👨‍💻 Developer

Dikembangkan untuk Tugas Akhir Semester - Sistem Pendukung Keputusan

---

**Built with ❤️ using Laravel**
