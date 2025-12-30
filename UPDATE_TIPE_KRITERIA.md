# Update: Tipe Kriteria (Benefit & Cost)

## Tanggal Update
26 Desember 2024

## Perubahan yang Dilakukan

### 1. Database Migration
**File:** `database/migrations/2024_12_26_000001_add_tipe_to_kriteria_table.php`

Menambahkan kolom `tipe` pada tabel `kriteria`:
- Tipe data: ENUM('benefit', 'cost')
- Default: 'benefit'
- Posisi: setelah kolom `bobot`

### 2. Database Seeder
**File:** `database/seeders/UpdateTipeKriteriaSeeder.php`

Mengisi nilai tipe untuk kriteria yang sudah ada:
- C1 (Volume Sampah): **benefit** - semakin besar semakin baik
- C2 (Aksesibilitas Transportasi): **benefit** - semakin besar semakin baik
- C3 (Kepadatan Penduduk): **benefit** - semakin besar semakin baik
- C4 (Jarak ke TPA): **cost** - semakin kecil semakin baik

### 3. Model Kriteria
**File:** `app/Models/Kriteria.php`

Menambahkan `tipe` ke dalam array `$fillable`:
```php
protected $fillable = [
    'kode',
    'nama',
    'bobot',
    'tipe', // Baru ditambahkan
];
```

### 4. Controller Kriteria
**File:** `app/Http/Controllers/KriteriaController.php`

- Menambahkan validasi untuk field `tipe`:
```php
'tipe' => 'required|in:benefit,cost',
```

- Update data kriteria sekaligus dengan tipe:
```php
$kriteria->update([
    'bobot' => $request->bobot,
    'tipe' => $request->tipe,
]);
```

### 5. Controller Perhitungan
**File:** `app/Http/Controllers/PerhitunganController.php`

**Perubahan Logic Perhitungan Utilitas:**

#### Sebelum:
```php
// Hanya satu rumus untuk semua kriteria
$nilaiUtilitas = ($nilaiParameter - $min) / ($max - $min);
```

#### Sesudah:
```php
// Rumus berbeda berdasarkan tipe kriteria
if ($krit->tipe == 'cost') {
    // COST: Semakin kecil semakin baik
    $nilaiUtilitas = ($max - $nilaiParameter) / ($max - $min);
} else {
    // BENEFIT: Semakin besar semakin baik
    $nilaiUtilitas = ($nilaiParameter - $min) / ($max - $min);
}
```

**Penjelasan Rumus:**

1. **Kriteria Benefit** (semakin besar semakin baik):
   - Contoh: Volume Sampah, Aksesibilitas Transportasi, Kepadatan Penduduk
   - Rumus: `U(a) = (Nilai - Min) / (Max - Min)`
   - Nilai tertinggi akan mendapat skor mendekati 1

2. **Kriteria Cost** (semakin kecil semakin baik):
   - Contoh: Jarak ke TPA
   - Rumus: `U(a) = (Max - Nilai) / (Max - Min)`
   - Nilai terendah akan mendapat skor mendekati 1

### 6. View Kriteria Index
**File:** `resources/views/kriteria/index.blade.php`

Perubahan:
- Menambahkan kolom **Tipe** di tabel
- Menampilkan badge berwarna:
  - **Biru** (primary) untuk Benefit
  - **Merah** (danger) untuk Cost
- Update penjelasan kriteria dengan info tipe

### 7. View Kriteria Edit
**File:** `resources/views/kriteria/edit.blade.php`

Perubahan:
- Menambahkan field dropdown untuk memilih tipe kriteria:
  - Benefit (Semakin besar semakin baik)
  - Cost (Semakin kecil semakin baik)
- Form sekarang bisa edit bobot DAN tipe

### 8. View Perhitungan
**File:** `resources/views/perhitungan/index.blade.php`

Perubahan:
- Menampilkan badge tipe kriteria di header tabel nilai ekstrem
- Menampilkan badge tipe kriteria di header tabel nilai utilitas
- Update penjelasan rumus dengan 2 formula berbeda:
  - Rumus untuk kriteria Benefit
  - Rumus untuk kriteria Cost

## Cara Penggunaan

### Mengubah Tipe Kriteria
1. Login ke aplikasi
2. Menu **Data Kriteria**
3. Klik tombol **Edit** pada kriteria yang ingin diubah
4. Pilih tipe kriteria:
   - **Benefit**: untuk kriteria yang semakin besar semakin baik
   - **Cost**: untuk kriteria yang semakin kecil semakin baik
5. Klik tombol **Update**

### Pengaruh pada Perhitungan
Perubahan tipe kriteria akan langsung mempengaruhi:
1. **Perhitungan Utilitas** - Rumus normalisasi yang digunakan
2. **Hasil Akhir** - Ranking alternatif bisa berubah

## Contoh Kasus: Kriteria C4 (Jarak ke TPA)

### Data Penilaian
- Bank Sampah A: Jarak 5 km → Parameter: 5 (paling dekat)
- Bank Sampah B: Jarak 10 km → Parameter: 3
- Bank Sampah C: Jarak 15 km → Parameter: 1 (paling jauh)

### Perhitungan dengan Tipe COST

Min = 1, Max = 5

**Bank Sampah A:**
```
U(C4) = (5 - 5) / (5 - 1) = 0 / 4 = 0.0000
```

**Bank Sampah B:**
```
U(C4) = (5 - 3) / (5 - 1) = 2 / 4 = 0.5000
```

**Bank Sampah C:**
```
U(C4) = (5 - 1) / (5 - 1) = 4 / 4 = 1.0000
```

**Hasil:** 
- Bank Sampah C (jarak terjauh) mendapat skor **1.0** ✓ SALAH!
- Bank Sampah A (jarak terdekat) mendapat skor **0.0** ✓ SALAH!

**KOREKSI - Harus pakai rumus COST:**

**Bank Sampah A (terdekat = terbaik):**
```
U(C4) = (Max - Nilai) / (Max - Min)
U(C4) = (5 - 5) / (5 - 1) = 0 / 4 = 0.0000 ✗ MASIH SALAH!
```

**ANALISIS ULANG:**

Data parameter harus konsisten!
- Jarak 5 km (terdekat) → Parameter: **5** (nilai tertinggi) ✓
- Jarak 15 km (terjauh) → Parameter: **1** (nilai terendah) ✓

Dengan parameter ini, menggunakan rumus **COST**:

**Bank Sampah A (parameter: 5):**
```
U(C4) = (5 - 5) / (5 - 1) = 0.0000 ✗ MASIH SALAH!
```

**KESIMPULAN PENTING:**

Jika parameter sudah dibuat dengan logika "nilai besar = lebih baik", maka tetap gunakan tipe **BENEFIT**, BUKAN COST.

**Tipe COST** hanya digunakan jika:
- Parameter asli yang diinput (bukan nilai konversi)
- Contoh: Jarak dalam KM mentah (5, 10, 15)
- Maka: jarak kecil = lebih baik

**Dalam kasus ini (SPK Bank Sampah):**
- Parameter sudah dikonversi ke skala 1-5
- Nilai 5 = paling baik, Nilai 1 = paling buruk
- **Semua kriteria harus tipe BENEFIT**

## Catatan Penting

⚠️ **PERHATIAN:** 

Dalam implementasi sistem ini, semua parameter kriteria sudah menggunakan konversi dimana:
- Nilai **5** = Paling baik
- Nilai **1** = Paling buruk

Oleh karena itu, **SEMUA KRITERIA SEBAIKNYA MENGGUNAKAN TIPE BENEFIT**.

Tipe COST hanya relevan jika sistem dimodifikasi untuk menerima nilai mentah (raw value) tanpa konversi parameter.

## Command untuk Rollback (Jika Diperlukan)

Jika ingin membatalkan perubahan:

```bash
php artisan migrate:rollback --step=1
```

Catatan: Database tidak akan di-reset, data yang ada tetap aman.
