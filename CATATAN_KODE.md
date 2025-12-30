# Catatan Kode Penting - SPK SMART

## Struktur Project

```
spk_smart/
├── app/
│   ├── Http/Controllers/
│   │   ├── AuthController.php          # Login/Logout
│   │   ├── DashboardController.php     # Dashboard Utama
│   │   ├── AlternatifController.php    # CRUD Alternatif
│   │   ├── KriteriaController.php      # CRUD Kriteria
│   │   ├── ParameterKriteriaController.php  # CRUD Parameter
│   │   ├── PenilaianController.php     # Penilaian
│   │   └── PerhitunganController.php   # Perhitungan SMART & Hasil
│   └── Models/
│       ├── Alternatif.php              # Model Bank Sampah
│       ├── Kriteria.php                # Model Kriteria
│       ├── ParameterKriteria.php       # Model Parameter
│       └── Penilaian.php               # Model Penilaian
├── database/
│   ├── migrations/                     # File migrations
│   └── seeders/
│       ├── KriteriaSeeder.php          # Data kriteria default
│       └── ParameterKriteriaSeeder.php # Data parameter default
├── resources/views/
│   ├── layouts/
│   │   └── app.blade.php               # Layout utama
│   ├── auth/
│   │   └── login.blade.php             # Halaman login
│   ├── dashboard/
│   │   └── index.blade.php             # Dashboard utama
│   ├── alternatif/                     # Views Alternatif
│   ├── kriteria/                       # Views Kriteria
│   ├── parameter/                      # Views Parameter
│   ├── penilaian/                      # Views Penilaian
│   └── perhitungan/                    # Views Perhitungan & Hasil
└── routes/
    └── web.php                         # Routing aplikasi
```

## Kode Penting

### 1. Perhitungan Nilai Utilitas (Normalisasi)

File: `app/Http/Controllers/PerhitunganController.php`

```php
private function getNilaiUtilitas($alternatif, $kriteria, $penilaian, $nilaiEkstrem)
{
    $utilitas = [];
    
    foreach ($alternatif as $alt) {
        $row = ['alternatif' => $alt];
        
        foreach ($kriteria as $krit) {
            $nilai = $penilaian->where('alternatif_id', $alt->id)
                ->where('kriteria_id', $krit->id)
                ->first();
            
            $nilaiParameter = $nilai ? $nilai->nilai_parameter : 0;
            $min = $nilaiEkstrem[$krit->kode]['min'];
            $max = $nilaiEkstrem[$krit->kode]['max'];
            
            // Rumus SMART: U(a) = (Nilai - Min) / (Max - Min)
            if ($max - $min == 0) {
                $nilaiUtilitas = 0;
            } else {
                $nilaiUtilitas = ($nilaiParameter - $min) / ($max - $min);
            }
            
            $row[$krit->kode] = round($nilaiUtilitas, 4);
        }
        
        $utilitas[] = $row;
    }
    
    return $utilitas;
}
```

### 2. Perhitungan Nilai Preferensi

File: `app/Http/Controllers/PerhitunganController.php`

```php
private function getNilaiPreferensi($alternatif, $kriteria, $nilaiUtilitas)
{
    $preferensi = [];
    
    foreach ($nilaiUtilitas as $row) {
        $alt = $row['alternatif'];
        $nilaiV = 0;
        
        foreach ($kriteria as $krit) {
            // Rumus SMART: V = Σ (bobot × utilitas)
            $nilaiV += $krit->bobot * $row[$krit->kode];
        }
        
        $preferensi[] = [
            'alternatif' => $alt,
            'nilai_v' => round($nilaiV, 4),
            'utilitas' => $row,
        ];
    }
    
    return $preferensi;
}
```

### 3. Generate Kode Alternatif Otomatis

File: `app/Models/Alternatif.php`

```php
public static function generateKode()
{
    $lastAlternatif = self::orderBy('id', 'desc')->first();
    
    if (!$lastAlternatif) {
        return 'A1';
    }

    $lastNumber = (int) substr($lastAlternatif->kode, 1);
    return 'A' . ($lastNumber + 1);
}
```

### 4. Validasi Total Bobot = 1

File: `app/Http/Controllers/KriteriaController.php`

```php
public function update(Request $request, Kriteria $kriteria)
{
    $request->validate([
        'bobot' => 'required|numeric|min:0|max:1',
    ]);

    // Validasi total bobot harus = 1
    $totalBobot = Kriteria::where('id', '!=', $kriteria->id)->sum('bobot');
    $totalBobot += $request->bobot;

    if (round($totalBobot, 2) != 1.00) {
        return back()->withErrors([
            'bobot' => 'Total bobot semua kriteria harus sama dengan 1'
        ]);
    }

    $kriteria->update(['bobot' => $request->bobot]);
    return redirect()->route('kriteria.index')->with('success', 'Bobot kriteria berhasil diperbarui');
}
```

### 5. Cek Nilai dalam Range Parameter

File: `app/Models/ParameterKriteria.php`

```php
public function isInRange($nilaiAktual)
{
    // Jika tidak ada batas (kategorikal), return false
    if ($this->batas_bawah === null && $this->batas_atas === null) {
        return false;
    }

    // Jika hanya ada batas atas (< x)
    if ($this->batas_bawah === null) {
        return $nilaiAktual < $this->batas_atas;
    }

    // Jika hanya ada batas bawah (> x)
    if ($this->batas_atas === null) {
        return $nilaiAktual >= $this->batas_bawah;
    }

    // Jika ada kedua batas (x <= y < z)
    return $nilaiAktual >= $this->batas_bawah && $nilaiAktual < $this->batas_atas;
}
```

## Routing Penting

File: `routes/web.php`

```php
// Login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes dengan Auth
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('alternatif', AlternatifController::class);
    Route::resource('kriteria', KriteriaController::class)->only(['index', 'edit', 'update']);
    Route::resource('parameter', ParameterKriteriaController::class);
    Route::get('penilaian', [PenilaianController::class, 'index'])->name('penilaian.index');
    Route::get('perhitungan', [PerhitunganController::class, 'index'])->name('perhitungan.index');
    Route::get('hasil', [PerhitunganController::class, 'hasil'])->name('hasil.index');
});
```

## Database Schema

### Tabel Alternatif
```sql
CREATE TABLE alternatif (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    kode VARCHAR(255) UNIQUE,
    nama VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Tabel Kriteria
```sql
CREATE TABLE kriteria (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    kode VARCHAR(255) UNIQUE,
    nama VARCHAR(255),
    bobot DECIMAL(3,2),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Tabel Parameter Kriteria
```sql
CREATE TABLE parameter_kriteria (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    kriteria_id BIGINT,
    deskripsi VARCHAR(255),
    nilai INT,
    batas_bawah DECIMAL(10,2) NULL,
    batas_atas DECIMAL(10,2) NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (kriteria_id) REFERENCES kriteria(id) ON DELETE CASCADE
);
```

### Tabel Penilaian
```sql
CREATE TABLE penilaian (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    alternatif_id BIGINT,
    kriteria_id BIGINT,
    nilai_aktual DECIMAL(10,2),
    nilai_parameter INT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (alternatif_id) REFERENCES alternatif(id) ON DELETE CASCADE,
    FOREIGN KEY (kriteria_id) REFERENCES kriteria(id) ON DELETE CASCADE,
    UNIQUE KEY unique_penilaian (alternatif_id, kriteria_id)
);
```

## Command Artisan Penting

```bash
# Migration
php artisan migrate:fresh --seed

# Clear Cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Generate Key
php artisan key:generate

# Autoload
composer dump-autoload

# Server
php artisan serve
```

## Login Default

- Email: admin@spksmart.com
- Password: password

## Tips Pengembangan

1. **Testing**: Gunakan data dummy untuk test perhitungan
2. **Validasi**: Pastikan semua input tervalidasi dengan benar
3. **Error Handling**: Tambahkan try-catch pada perhitungan kompleks
4. **Documentation**: Tambahkan komentar pada logika perhitungan
5. **UI/UX**: Pastikan tampilan responsive dan user-friendly

---

**Catatan**: File ini berisi kode-kode penting untuk referensi dan pemahaman logika aplikasi.
