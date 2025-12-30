# Dokumentasi Metode SMART

## Simple Multi Attribute Rating Technique (SMART)

### Penjelasan Metode

SMART adalah metode pengambilan keputusan multi kriteria yang menggunakan pembobotan dan normalisasi untuk menentukan alternatif terbaik dari beberapa pilihan berdasarkan kriteria yang telah ditentukan.

### Langkah-langkah Metode SMART

#### 1. Penentuan Kriteria dan Bobot

Tentukan kriteria penilaian dan bobot kepentingan masing-masing kriteria. Total bobot harus = 1.

**Contoh Kriteria:**
- C1 = Volume Timbulan Sampah (Bobot: 0.4)
- C2 = Aksesibilitas Transportasi (Bobot: 0.3)
- C3 = Kepadatan Penduduk (Bobot: 0.2)
- C4 = Jarak ke TPA (Bobot: 0.1)

#### 2. Penentuan Nilai Alternatif

Berikan nilai untuk setiap alternatif berdasarkan kriteria. Nilai dapat berupa:
- Nilai aktual (riil)
- Nilai parameter (skala 1-5)

**Contoh:**
| Alternatif | C1 | C2 | C3 | C4 |
|------------|----|----|----|----|
| A1 | 4 | 4 | 4 | 1 |
| A2 | 5 | 3 | 4 | 4 |

#### 3. Normalisasi (Nilai Utilitas)

Normalisasi mengubah nilai parameter ke skala 0-1 menggunakan rumus:

```
U(a) = (Nilai - Min) / (Max - Min)
```

**Keterangan:**
- **U(a)** = Nilai utilitas alternatif a
- **Nilai** = Nilai parameter alternatif untuk kriteria tertentu
- **Min** = Nilai minimum dari semua alternatif untuk kriteria tersebut
- **Max** = Nilai maksimum dari semua alternatif untuk kriteria tersebut

**Contoh Perhitungan:**
Untuk kriteria C1:
- Min = 4, Max = 5
- U(A1) = (4 - 4) / (5 - 4) = 0 / 1 = 0
- U(A2) = (5 - 4) / (5 - 4) = 1 / 1 = 1

#### 4. Perhitungan Nilai Preferensi

Nilai preferensi (V) dihitung dengan menjumlahkan hasil perkalian bobot kriteria dengan nilai utilitas:

```
V(a) = Σ (Bobot × Utilitas)
V(a) = (w1 × u1) + (w2 × u2) + (w3 × u3) + (w4 × u4)
```

**Keterangan:**
- **V(a)** = Nilai preferensi alternatif a
- **w** = Bobot kriteria
- **u** = Nilai utilitas

**Contoh Perhitungan:**
```
V(A1) = (0.4 × 0) + (0.3 × 0.5) + (0.2 × 0) + (0.1 × 0)
      = 0 + 0.15 + 0 + 0
      = 0.15

V(A2) = (0.4 × 1) + (0.3 × 0) + (0.2 × 0) + (0.1 × 1)
      = 0.4 + 0 + 0 + 0.1
      = 0.5
```

#### 5. Perankingan

Urutkan alternatif berdasarkan nilai preferensi (V) dari yang terbesar ke terkecil.

**Hasil:**
1. A2 (V = 0.5) ← **Prioritas Terbaik**
2. A1 (V = 0.15)

### Keunggulan Metode SMART

1. **Sederhana** - Mudah dipahami dan diimplementasikan
2. **Fleksibel** - Dapat disesuaikan dengan berbagai kasus
3. **Transparan** - Proses perhitungan jelas dan terstruktur
4. **Objektif** - Menggunakan perhitungan matematis

### Penerapan dalam SPK Bank Sampah

Metode SMART diterapkan untuk menentukan Bank Sampah prioritas sebagai HUB dengan:

1. **4 Kriteria Penilaian**
   - Volume Timbulan Sampah (40%)
   - Aksesibilitas Transportasi (30%)
   - Kepadatan Penduduk (20%)
   - Jarak ke TPA (10%)

2. **Proses Penilaian**
   - Input nilai aktual untuk setiap Bank Sampah
   - Konversi ke nilai parameter (1-5)
   - Normalisasi menggunakan min-max
   - Perhitungan nilai preferensi
   - Perankingan hasil

3. **Output**
   - Ranking Bank Sampah dari terbaik
   - Bank Sampah ranking #1 = Prioritas HUB

### Referensi

- Edwards, W., & Barron, F. H. (1994). SMARTS and SMARTER: Improved simple methods for multiattribute utility measurement. Organizational behavior and human decision processes, 60(3), 306-325.
- Goodwin, P., & Wright, G. (2004). Decision analysis for management judgment. John Wiley & Sons.

---

**Catatan:** Dokumentasi ini dibuat untuk keperluan akademik dan pemahaman metode SMART dalam konteks Sistem Pendukung Keputusan.
