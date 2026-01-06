# Dokumentasi Fitur Checklist Penilaian dan Perhitungan

## Overview
Sistem SPK SMART sekarang menggunakan pendekatan checklist untuk memilih alternatif yang akan dinilai dan dihitung. Ini memberikan fleksibilitas untuk fokus pada alternatif tertentu tanpa harus memproses semua data.

## 1. Flow Penilaian dengan Checklist

### 1.1 Halaman Index Penilaian
**File:** `resources/views/penilaian/index.blade.php`

**Fitur:**
- Tabel daftar semua alternatif dengan checkbox
- Status kelengkapan (Lengkap/Belum Lengkap) per alternatif
- Tombol "Detail" untuk melihat penilaian dalam modal
- Tombol "Proses Input Penilaian" yang aktif saat ada alternatif terpilih

**Cara Kerja:**
1. User checklist alternatif yang ingin dinilai
2. Klik tombol "Proses Input Penilaian"
3. Redirect ke halaman create dengan parameter `alternatif_ids[]`

**JavaScript:**
```javascript
// Check/Uncheck all
document.getElementById('checkAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.checkbox-alternatif');
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateButtonState();
});

// Update button state
function updateButtonState() {
    const checked = document.querySelectorAll('.checkbox-alternatif:checked').length;
    const btn = document.getElementById('btnProsesInput');
    btn.disabled = checked === 0;
}
```

### 1.2 Halaman Input Batch
**File:** `resources/views/penilaian/create.blade.php`

**Fitur:**
- Menampilkan semua alternatif yang dipilih dalam card terpisah
- Input semua kriteria untuk setiap alternatif dalam satu halaman
- Keterangan detail range parameter di setiap input field
- Save batch untuk semua penilaian sekaligus

**Structure:**
```blade
@foreach($alternatif as $alt)
    <div class="card mb-3">
        <div class="card-header">{{ $alt->nama }}</div>
        <div class="card-body">
            @foreach($kriteria as $krit)
                <!-- Input field dengan range parameter info -->
                <input name="penilaian[{{ $alt->id }}_{{ $krit->id }}][nilai_aktual]">
                <input type="hidden" name="penilaian[{{ $alt->id }}_{{ $krit->id }}][alternatif_id]">
                <input type="hidden" name="penilaian[{{ $alt->id }}_{{ $krit->id }}][kriteria_id]">
                
                <!-- Range parameter info -->
                <div class="alert alert-secondary">
                    <strong>Range Parameter:</strong>
                    <ul>
                        @foreach($krit->parameters as $param)
                        <li>{{ $param->deskripsi }} = Nilai {{ $param->nilai }}</li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>
@endforeach
```

### 1.3 Controller Penilaian
**File:** `app/Http/Controllers/PenilaianController.php`

**Method `create()`:**
```php
public function create(Request $request)
{
    // Cek apakah ada alternatif_ids dari checklist
    if (!$request->has('alternatif_ids') || empty($request->alternatif_ids)) {
        return redirect()->route('penilaian.index')
            ->with('error', 'Silakan pilih alternatif terlebih dahulu');
    }

    $alternatifIds = $request->alternatif_ids;
    $alternatif = Alternatif::whereIn('id', $alternatifIds)->get();
    $kriteria = Kriteria::with('parameters')->orderBy('kode')->get();
    $existingPenilaian = Penilaian::whereIn('alternatif_id', $alternatifIds)->get();

    return view('penilaian.create', compact('alternatif', 'kriteria', 'existingPenilaian'));
}
```

**Method `store()` - Batch Save:**
```php
public function store(Request $request)
{
    $request->validate([
        'penilaian' => 'required|array',
        'penilaian.*.alternatif_id' => 'required|exists:alternatif,id',
        'penilaian.*.kriteria_id' => 'required|exists:kriteria,id',
        'penilaian.*.nilai_aktual' => 'required',
    ]);

    $kriteria = Kriteria::with('parameters')->get()->keyBy('id');
    $successCount = 0;

    foreach ($request->penilaian as $data) {
        $kriteriaItem = $kriteria->get($data['kriteria_id']);
        
        // Tentukan nilai parameter
        if ($kriteriaItem->kode == 'C2') {
            $nilaiParameter = $data['nilai_parameter'] ?? 1;
        } else {
            $nilaiParameter = $this->getNilaiParameter($kriteriaItem, $data['nilai_aktual']);
        }

        Penilaian::updateOrCreate(
            [
                'alternatif_id' => $data['alternatif_id'],
                'kriteria_id' => $data['kriteria_id'],
            ],
            [
                'nilai_aktual' => $data['nilai_aktual'],
                'nilai_parameter' => $nilaiParameter,
            ]
        );
        
        $successCount++;
    }

    return redirect()->route('penilaian.index')
        ->with('success', "Berhasil menyimpan $successCount penilaian");
}
```

## 2. Flow Perhitungan dengan Checklist

### 2.1 Halaman Checklist Perhitungan
**File:** `resources/views/perhitungan/checklist.blade.php`

**Fitur:**
- Checkbox untuk setiap alternatif
- Status kelengkapan penilaian (Lengkap/Belum Lengkap)
- Progress bar kelengkapan per alternatif
- Disable checkbox untuk alternatif yang belum lengkap
- Counter alternatif yang dipilih

**Logic Kelengkapan:**
```blade
@php
    $jumlahPenilaian = \App\Models\Penilaian::where('alternatif_id', $alt->id)->count();
    $jumlahKriteria = $kriteria->count();
    $isLengkap = $jumlahPenilaian >= $jumlahKriteria;
    $persentase = $jumlahKriteria > 0 ? round(($jumlahPenilaian / $jumlahKriteria) * 100) : 0;
@endphp

<input type="checkbox" 
       name="alternatif_ids[]" 
       value="{{ $alt->id }}" 
       class="form-check-input checkbox-alternatif"
       {{ !$isLengkap ? 'disabled' : '' }}>
```

### 2.2 Controller Perhitungan
**File:** `app/Http/Controllers/PerhitunganController.php`

**Method `index()` - Checklist atau Perhitungan:**
```php
public function index(Request $request)
{
    $semuaAlternatif = Alternatif::get();
    $kriteria = Kriteria::get();
    
    // Ambil alternatif yang dipilih
    $alternatifIds = $request->input('alternatif_ids', []);
    
    // Jika tidak ada yang dipilih, tampilkan halaman checklist
    if (empty($alternatifIds)) {
        return view('perhitungan.checklist', compact('semuaAlternatif', 'kriteria'));
    }
    
    // Filter alternatif berdasarkan yang dipilih
    $alternatif = Alternatif::whereIn('id', $alternatifIds)->get();
    $penilaian = Penilaian::whereIn('alternatif_id', $alternatifIds)->get();

    // Hitung nilai ekstrem hanya dari alternatif terpilih
    $nilaiEkstrem = $this->getNilaiEkstrem($kriteria, $penilaian);
    
    // Hitung utilitas
    $nilaiUtilitas = $this->getNilaiUtilitas($alternatif, $kriteria, $penilaian, $nilaiEkstrem);
    
    // ... dst
    
    return view('perhitungan.index', compact(
        'alternatif', 'kriteria', 'tabelPenilaian', 
        'nilaiEkstrem', 'nilaiUtilitas', 'alternatifIds'
    ));
}
```

**Method `hasil()` - Hasil dengan Session:**
```php
public function hasil(Request $request)
{
    // Ambil dari request atau session
    $alternatifIds = $request->input('alternatif_ids', session('alternatif_ids', []));
    
    if (empty($alternatifIds)) {
        return redirect()->route('perhitungan.index')
            ->with('error', 'Silakan pilih alternatif terlebih dahulu');
    }
    
    // Simpan ke session untuk export dan history
    session(['alternatif_ids' => $alternatifIds]);
    
    // Filter data
    $alternatif = Alternatif::whereIn('id', $alternatifIds)->get();
    $penilaian = Penilaian::whereIn('alternatif_id', $alternatifIds)->get();
    
    // Hitung
    $nilaiEkstrem = $this->getNilaiEkstrem($kriteria, $penilaian);
    $nilaiUtilitas = $this->getNilaiUtilitas($alternatif, $kriteria, $penilaian, $nilaiEkstrem);
    $nilaiPreferensi = $this->getNilaiPreferensi($alternatif, $kriteria, $nilaiUtilitas);
    $ranking = $this->getRanking($nilaiPreferensi);
    
    return view('perhitungan.hasil', compact(
        'alternatif', 'kriteria', 'nilaiUtilitas',
        'nilaiPreferensi', 'ranking', 'alternatifIds'
    ));
}
```

**Method `exportCsv()` - Menggunakan Session:**
```php
public function exportCsv()
{
    // Ambil dari session
    $alternatifIds = session('alternatif_ids', []);
    
    if (empty($alternatifIds)) {
        return redirect()->route('perhitungan.index')
            ->with('error', 'Silakan pilih alternatif terlebih dahulu');
    }
    
    $alternatif = Alternatif::whereIn('id', $alternatifIds)->get();
    $penilaian = Penilaian::whereIn('alternatif_id', $alternatifIds)->get();
    
    // ... proses export
}
```

**Method `simpanHistory()` - Menggunakan Session:**
```php
public function simpanHistory(Request $request)
{
    // Ambil dari session
    $alternatifIds = session('alternatif_ids', []);
    
    if (empty($alternatifIds)) {
        return back()->with('error', 'Silakan pilih alternatif terlebih dahulu');
    }
    
    $alternatif = Alternatif::whereIn('id', $alternatifIds)->get();
    $penilaian = Penilaian::whereIn('alternatif_id', $alternatifIds)->get();
    
    // ... simpan history
}
```

## 3. Flow Lengkap Penggunaan

### Flow Penilaian:
```
1. User → /penilaian (index)
2. Checklist alternatif yang ingin dinilai
3. Klik "Proses Input Penilaian"
4. → /penilaian/create?alternatif_ids[]=1&alternatif_ids[]=2
5. Input semua nilai dalam satu halaman
6. Submit form
7. → POST /penilaian (batch save)
8. → Redirect ke /penilaian dengan success message
```

### Flow Perhitungan:
```
1. User → /perhitungan
2. Tampilkan checklist alternatif
3. Pilih alternatif (hanya yang penilaian lengkap)
4. Klik "Lanjut ke Perhitungan"
5. → GET /perhitungan?alternatif_ids[]=1&alternatif_ids[]=2
6. Tampilkan proses perhitungan (nilai ekstrem, utilitas)
7. Klik "Lihat Hasil Akhir"
8. → GET /hasil?alternatif_ids[]=1&alternatif_ids[]=2
9. Session menyimpan alternatif_ids
10. User dapat:
    - Export CSV → menggunakan session
    - Simpan History → menggunakan session
    - Lihat Proses Perhitungan → kirim alternatif_ids via form
```

## 4. Session Management

**Variabel Session:**
- `alternatif_ids` (array): Menyimpan ID alternatif yang dipilih untuk perhitungan

**Set Session:**
```php
// Di method hasil()
session(['alternatif_ids' => $alternatifIds]);
```

**Get Session:**
```php
// Di method exportCsv() dan simpanHistory()
$alternatifIds = session('alternatif_ids', []);
```

**Clear Session (Optional):**
```php
// Bisa ditambahkan di method index() saat kembali ke checklist
session()->forget('alternatif_ids');
```

## 5. Validasi dan Error Handling

### Penilaian:
- ✅ Validasi alternatif_ids tidak kosong
- ✅ Validasi array penilaian
- ✅ Validasi alternatif_id dan kriteria_id exists
- ✅ Validasi nilai_aktual required

### Perhitungan:
- ✅ Cek alternatif_ids tidak kosong
- ✅ Hanya alternatif dengan penilaian lengkap yang dapat dipilih
- ✅ Redirect ke checklist jika tidak ada alternatif dipilih
- ✅ Error message jika session kosong saat export/simpan history

## 6. Keuntungan Sistem Checklist

1. **Fleksibilitas**: User dapat memilih alternatif spesifik yang ingin diproses
2. **Efisiensi**: Tidak perlu memproses semua data jika hanya butuh sebagian
3. **Batch Processing**: Input dan save banyak data sekaligus
4. **User Experience**: Interface lebih intuitif dengan checklist
5. **Performance**: Perhitungan lebih cepat dengan data yang lebih sedikit
6. **Perbandingan**: User dapat membandingkan subset alternatif tertentu

## 7. Tips Pengembangan

### Jika ingin menambah fitur "Save Selection":
```php
// Simpan selection ke database
Route::post('penilaian/save-selection', [PenilaianController::class, 'saveSelection']);

public function saveSelection(Request $request)
{
    $user = Auth::user();
    $user->saved_selections()->create([
        'name' => $request->name,
        'alternatif_ids' => $request->alternatif_ids
    ]);
}
```

### Jika ingin menambah fitur "Compare Multiple Selections":
```php
// Load multiple saved selections
public function compare(Request $request)
{
    $selections = $request->selection_ids;
    $results = [];
    
    foreach ($selections as $selectionId) {
        $selection = SavedSelection::find($selectionId);
        $results[] = $this->calculateRanking($selection->alternatif_ids);
    }
    
    return view('perhitungan.compare', compact('results'));
}
```

## 8. Testing Checklist

- [ ] Test checklist penilaian dengan 1 alternatif
- [ ] Test checklist penilaian dengan multiple alternatif
- [ ] Test checklist penilaian dengan select all
- [ ] Test input batch dengan data valid
- [ ] Test input batch dengan data tidak lengkap
- [ ] Test checklist perhitungan dengan alternatif lengkap
- [ ] Test checklist perhitungan dengan alternatif belum lengkap (disabled)
- [ ] Test perhitungan dengan alternatif terpilih
- [ ] Test export CSV dengan session
- [ ] Test simpan history dengan session
- [ ] Test redirect jika tidak ada alternatif dipilih
- [ ] Test modal detail penilaian
- [ ] Test progress bar kelengkapan

---

**Catatan:** Dokumentasi ini menjelaskan implementasi fitur checklist untuk penilaian dan perhitungan. Sistem ini memberikan fleksibilitas kepada user untuk memilih data yang akan diproses, meningkatkan efisiensi dan user experience.
