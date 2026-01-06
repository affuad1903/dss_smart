# Update CRUD Kriteria - Sistem Lengkap

## Tanggal Update
5 Januari 2026

## Masalah yang Diperbaiki

### 1. **Error 500 di Edit Kriteria**
- **Penyebab**: Penggunaan `Kriteria::` langsung di Blade view (baris 135)
- **Solusi**: Kirim data `$otherBobotSum` dari controller ke view

### 2. **Fitur CRUD Tidak Lengkap**
- Sebelumnya hanya bisa **Read** dan **Update**
- Tidak bisa **Create** kriteria baru
- Tidak bisa **Delete** kriteria

## Fitur Baru yang Ditambahkan

### ✅ 1. CREATE - Tambah Kriteria Baru
**Route**: `GET /kriteria/create`

**Fitur**:
- Form tambah kriteria dengan validasi lengkap
- Real-time calculator untuk total bobot
- Menampilkan sisa bobot yang tersedia
- Auto-fill bobot dengan sisa yang tersedia
- Validasi kode unik (tidak boleh duplikat)

**Field**:
- Kode kriteria (unik, required)
- Nama kriteria (required)
- Tipe (benefit/cost, required)
- Bobot (0-1, required)

### ✅ 2. DELETE - Hapus Kriteria
**Route**: `DELETE /kriteria/{id}`

**Fitur**:
- Konfirmasi sebelum hapus (JavaScript alert)
- Validasi keamanan:
  - ❌ Tidak bisa hapus jika sudah digunakan dalam penilaian
  - ❌ Tidak bisa hapus jika masih memiliki parameter kriteria
- Notifikasi warning jika total bobot ≠ 1 setelah hapus
- Notifikasi success jika berhasil dihapus

### ✅ 3. UPDATE - Perbaikan Error
**Perbaikan**:
- Fix error 500 dengan mengirim `$otherBobotSum` dari controller
- Real-time calculator tetap berfungsi
- Validasi soft (warning system) tetap aktif

## File yang Diubah/Ditambahkan

### 1. **KriteriaController.php** (Updated)
```php
// Method baru:
- create()   → Tampilkan form tambah
- store()    → Simpan kriteria baru
- destroy()  → Hapus kriteria

// Method diupdate:
- edit()     → Kirim $otherBobotSum ke view
```

### 2. **create.blade.php** (New)
- Form tambah kriteria lengkap
- Real-time calculator
- Panduan pengisian
- Validasi error display

### 3. **edit.blade.php** (Fixed)
- Perbaiki error dengan menggunakan `$otherBobotSum` dari controller
- Bukan `Kriteria::where()` langsung di view

### 4. **index.blade.php** (Updated)
- Tambah tombol "Tambah Kriteria" di header
- Tambah tombol delete untuk setiap kriteria
- JavaScript konfirmasi delete
- Notifikasi error untuk feedback lebih baik

### 5. **web.php** (Updated)
```php
// Route baru:
Route::get('kriteria/create', ...)   → Form tambah
Route::post('kriteria', ...)         → Simpan baru
Route::delete('kriteria/{id}', ...)  → Hapus
```

## Cara Penggunaan

### 📝 Menambah Kriteria Baru

1. Masuk ke menu **Data Kriteria**
2. Klik tombol **"Tambah Kriteria"** (hijau, pojok kanan atas)
3. Isi form:
   - **Kode**: C5, C6, dst (harus unik)
   - **Nama**: Nama deskriptif kriteria
   - **Tipe**: Benefit atau Cost
   - **Bobot**: Nilai 0-1 (otomatis terisi sisa bobot)
4. Perhatikan calculator real-time
5. Klik **"Simpan"**
6. Jika total ≠ 1, akan muncul warning (tapi tetap tersimpan)

### ✏️ Mengedit Kriteria

1. Klik tombol **Edit** (kuning, ikon pensil)
2. Ubah **Tipe** atau **Bobot**
3. Perhatikan calculator real-time
4. Klik **"Update"**

*Note: Kode dan Nama tidak bisa diubah setelah dibuat*

### 🗑️ Menghapus Kriteria

1. Klik tombol **Hapus** (merah, ikon trash)
2. Konfirmasi dengan klik **OK**
3. Sistem akan validasi:
   - ✅ Bisa dihapus jika belum digunakan
   - ❌ Tidak bisa dihapus jika:
     - Sudah digunakan dalam penilaian
     - Masih memiliki parameter kriteria

**Tips**: Hapus parameter kriteria terlebih dahulu sebelum hapus kriteria

## Validasi Keamanan

### Saat CREATE
- ✅ Kode harus unik
- ✅ Semua field required
- ✅ Bobot antara 0-1
- ⚠️ Warning jika total ≠ 1 (tetap simpan)

### Saat DELETE
- ❌ Gagal jika sudah digunakan dalam penilaian
- ❌ Gagal jika masih punya parameter kriteria
- ⚠️ Warning jika total ≠ 1 setelah hapus

### Saat UPDATE
- ✅ Hanya bisa ubah tipe dan bobot
- ✅ Kode dan nama locked
- ⚠️ Warning jika total ≠ 1 (tetap simpan)

## Real-Time Calculator

Semua form (Create & Edit) dilengkapi calculator yang menampilkan:

### Warna Badge:
- 🟢 **Hijau**: Total = 1.00 (Perfect!)
- 🟡 **Kuning**: Total ≠ 1.00 (Warning)

### Informasi:
- Total bobot setelah aksi
- Selisih yang harus disesuaikan
- Persentase yang tersisa/berlebih

## Notifikasi

### Success (Hijau)
- Kriteria berhasil ditambahkan/diupdate dengan total = 1
- Kriteria berhasil dihapus

### Warning (Kuning)
- Kriteria berhasil ditambahkan/diupdate tapi total ≠ 1
- Kriteria berhasil dihapus tapi total ≠ 1

### Error (Merah)
- Kriteria tidak bisa dihapus (sudah digunakan)
- Validasi gagal (kode duplikat, dll)

## Contoh Skenario

### Skenario 1: Tambah Kriteria Baru
```
Kondisi awal:
- C1: 0.4
- C2: 0.3
- C3: 0.2
- C4: 0.1
Total: 1.0

Aksi: Tambah C5 dengan bobot 0.15
Hasil: Total jadi 1.15
Notifikasi: Warning (total melebihi 1 sebesar 0.15)

Solusi: Edit C1 dari 0.4 → 0.25 (kurangi 0.15)
Hasil: Total kembali 1.0 ✓
```

### Skenario 2: Hapus Kriteria
```
Kondisi awal:
- C1: 0.4
- C2: 0.3
- C3: 0.2
- C4: 0.1
Total: 1.0

Aksi: Hapus C4 (0.1)
Hasil: Total jadi 0.9
Notifikasi: Warning (kurang 0.1 dari total yang dibutuhkan)

Solusi: Tambah 0.1 ke kriteria lain
Contoh: C1 dari 0.4 → 0.5
Hasil: Total kembali 1.0 ✓
```

### Skenario 3: Hapus Kriteria dengan Parameter
```
Aksi: Hapus C2 yang masih punya parameter
Hasil: Error
Notifikasi: "Kriteria tidak dapat dihapus karena masih memiliki parameter kriteria"

Solusi: 
1. Hapus semua parameter kriteria C2 terlebih dahulu
2. Baru hapus kriteria C2
```

## Testing Checklist

- [x] ✅ Tambah kriteria baru dengan total = 1
- [x] ✅ Tambah kriteria baru dengan total ≠ 1 (warning muncul)
- [x] ✅ Validasi kode duplikat
- [x] ✅ Real-time calculator di form create
- [x] ✅ Real-time calculator di form edit
- [x] ✅ Edit kriteria berhasil tanpa error 500
- [x] ✅ Hapus kriteria yang belum digunakan
- [x] ✅ Hapus kriteria dengan parameter (harus gagal)
- [x] ✅ Hapus kriteria yang sudah dinilai (harus gagal)
- [x] ✅ Konfirmasi delete muncul
- [x] ✅ Notifikasi success/warning/error tampil

## Catatan Penting

### ⚠️ Sebelum Menghapus Kriteria:
1. Hapus data penilaian terkait (jika ada)
2. Hapus parameter kriteria terkait (jika ada)
3. Baru hapus kriteria

### ⚠️ Setelah Tambah/Hapus Kriteria:
1. Pastikan total bobot = 1.00
2. Sesuaikan bobot kriteria lain jika perlu
3. Tambahkan parameter kriteria untuk kriteria baru

### 💡 Best Practice:
- Rencanakan kriteria sebelum input data penilaian
- Jaga total bobot = 1 untuk akurasi perhitungan
- Dokumentasikan perubahan kriteria
- Backup data sebelum hapus kriteria

## Route Lengkap

```
GET    /kriteria              → Daftar kriteria
GET    /kriteria/create       → Form tambah kriteria
POST   /kriteria              → Simpan kriteria baru
GET    /kriteria/{id}/edit    → Form edit kriteria
PUT    /kriteria/{id}         → Update kriteria
DELETE /kriteria/{id}         → Hapus kriteria
```

## Error yang Diperbaiki

### Error 500 (FIXED ✓)
```
Error: Kriteria::where('id', '!=', $kriteria->id)->sum('bobot')
       ^^^^^^^ Cannot use Kriteria model directly in Blade

Fix: Kirim $otherBobotSum dari controller
     const otherBobotSum = {{ $otherBobotSum }};
```

---

**Status**: ✅ Implemented & Tested  
**Dibuat oleh**: GitHub Copilot  
**Versi**: 2.0  
**Fitur**: CRUD Lengkap (Create, Read, Update, Delete)
