# PHASE UI-19 — DASHBOARD STATUS CARD SYNC

## Tujuan
Pastikan card status pada Dashboard mengambil data real dari database dan
sesuai dengan status yang digunakan pada modul Proses Permintaan Darah,
tanpa menambah status baru dan tanpa mengubah desain/layout/ikon/warna CSS.

---

## 1. File yang Berubah

| File | Jenis Perubahan |
|---|---|
| `application/models/Dashboard_model.php` | Tambahan method `get_status_summary()` — single query summary 10 status |
| `application/controllers/Dashboard.php` | Tambahan 1 baris: load `$data['summary']` |
| `application/views/dashboard/index.php` | Stat card & tab badge memakai angka dari `$summary` (bukan `count()` hasil query per tab) |

### Tidak diubah (sesuai constraint)
- `application/controllers/RequestController.php` — **tidak disentuh**
- `application/models/Request_model.php` (UI-16B) — **tidak disentuh**
- Struktur database — **tidak ada migration, tidak ada tabel baru**
- Layout dashboard, warna card, icon, CSS existing — **dipertahankan 100%**

---

## 2. Audit Sumber Data Dashboard

### Sebelum
- `Dashboard::index()` memanggil **10 method terpisah** di `Dashboard_model`
  (`get_permintaan()`, `get_sedang_proses()`, …, `get_tidaklengkap()`).
- Setiap method menjalankan query `SELECT ... FROM darah.pesan_darah` sendiri
  hanya untuk dihitung barisnya dengan `count()` di view.
- 10 query ke DB hanya untuk mendapat 10 angka.

### Field database yang dipakai untuk menghitung status
- `darah.pesan_darah.status` — FK ke `darah.referensi` (JENIS = 3)
- `darah.pesan_darah.tgl_minta` — filter rentang waktu
- `darah.status_terima.status` — pembeda "belum diterima" untuk Permintaan
- `darah.kelengkapan.kelengkapan` — pembeda "Tidak Lengkap"

### Referensi status (darah.referensi WHERE JENIS = 3)

| ID | DESKRIPSI |
|---|---|
| 1 | Sedang Proses |
| 2 | Darah Siap |
| 3 | Perlu Donor |
| 4 | Sampel Baru |
| 5 | Belum Terima Sampel Darah |
| 6 | Incompatible |
| 7 | masa simpan habis |
| 8 | Sudah Habis |
| 9 | Permintaan Sebelumnya Belum Diambil |
| 10 | Sudah Terima Sampel Darah |

Tidak ada status baru ditambah — hanya 10 status yang sudah dipakai dashboard.

---

## 3. Query Summary Status

Satu query, satu scan tabel, menggantikan 10 query terpisah.
Memakai `SUM(CASE WHEN ...)` (bentuk agregasi GROUP BY pada kondisi)
agar semua status dihitung dalam satu pass.

```sql
SELECT
  SUM(CASE WHEN pd.status = 5 AND (st.status != 1 OR st.status IS NULL)
           AND pd.tgl_minta >= DATE_SUB(NOW(), INTERVAL 5 DAY)
      THEN 1 ELSE 0 END) AS permintaan,
  SUM(CASE WHEN pd.status = 1
           AND pd.tgl_minta >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
      THEN 1 ELSE 0 END) AS sedang_proses,
  SUM(CASE WHEN pd.status = 2
           AND pd.tgl_minta >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
      THEN 1 ELSE 0 END) AS darah_siap,
  SUM(CASE WHEN pd.status = 3
           AND pd.tgl_minta >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
      THEN 1 ELSE 0 END) AS perlu_donor,
  SUM(CASE WHEN pd.status = 4
           AND pd.tgl_minta >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
      THEN 1 ELSE 0 END) AS sampel_baru,
  SUM(CASE WHEN pd.status = 6
           AND pd.tgl_minta >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
      THEN 1 ELSE 0 END) AS incompatible,
  SUM(CASE WHEN pd.status = 7
           AND pd.tgl_minta >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
      THEN 1 ELSE 0 END) AS masa_simpan_habis,
  SUM(CASE WHEN pd.status = 9
           AND pd.tgl_minta >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
      THEN 1 ELSE 0 END) AS belum_diambil,
  SUM(CASE WHEN pd.status = 8
           AND pd.tgl_minta >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
      THEN 1 ELSE 0 END) AS sudah_habis,
  SUM(CASE WHEN pd.status <> 0 AND k.kelengkapan = 0
           AND pd.tgl_minta >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
      THEN 1 ELSE 0 END) AS tidak_lengkap
FROM darah.pesan_darah pd
LEFT JOIN darah.status_terima st ON pd.no_permintaan = st.no_permintaan
LEFT JOIN darah.kelengkapan    k  ON pd.no_permintaan = k.NO_PERMINTAAN;
```

### Contoh output (database saat pengujian)

```php
array(
  'permintaan'        => 0,
  'sedang_proses'     => 15,
  'darah_siap'        => 144,
  'perlu_donor'       => 17,
  'sampel_baru'       => 0,
  'incompatible'      => 0,
  'masa_simpan_habis' => 141,
  'belum_diambil'     => 0,
  'sudah_habis'       => 616,
  'tidak_lengkap'     => 128,
)
```

Tabel yang dipakai **sama** dengan `/requestcontroller/list`:
`darah.pesan_darah` (plus `darah.status_terima` & `darah.kelengkapan`
yang sudah dipakai dashboard sebelumnya). Tidak ada tabel baru.

---

## 4. Mapping Status Database → Label Dashboard

| Status DB (`pesan_darah.status`) | Label Dashboard | Key Summary | Filter Tambahan |
|---|---|---|---|
| 5 — Belum Terima Sampel Darah | Permintaan | `permintaan` | `status_terima.status != 1 OR NULL`, tgl_minta ≤ 5 hari |
| 1 — Sedang Proses | Sedang Proses | `sedang_proses` | — |
| 2 — Darah Siap | Darah Siap | `darah_siap` | — |
| 3 — Perlu Donor | Perlu Donor | `perlu_donor` | — |
| 4 — Sampel Baru | Sampel Baru | `sampel_baru` | — |
| 6 — Incompatible | Incompatible | `incompatible` | — |
| 7 — masa simpan habis | Masa Simpan Habis | `masa_simpan_habis` | — |
| 9 — Permintaan Sebelumnya Belum Diambil | Belum Diambil | `belum_diambil` | — |
| 8 — Sudah Habis | Sudah Habis | `sudah_habis` | — |
| status <> 0 + `kelengkapan.kelengkapan = 0` | Tidak Lengkap | `tidak_lengkap` | kelengkapan = 0 |

Status 10 (Sudah Terima Sampel Darah) dan status 0 **tidak** dimasukkan
karena dashboard sebelumnya tidak memakainya sebagai card — konversi dari
status 5 ke 10 ditangani modul Proses Permintaan, dan angka Permintaan
sudah eksplisit memfilter `status_terima.status != 1`.

---

## 5. Integrasi ke Dashboard

### Controller (`Dashboard.php`)
```php
$data['summary'] = $this->Dashboard_model->get_status_summary();
```
Method per-tab yang lama **dipertahankan** karena tabel per tab masih
membutuhkan baris data (bukan hanya jumlah).

### View (`dashboard/index.php`)
Default-safe array digabung dengan hasil query:
```php
$sum = array_merge(array(
    'permintaan' => 0, 'sedang_proses' => 0, 'darah_siap' => 0, /* ... */
), is_array($summary) ? $summary : array());

$tabSummaryKey = array(
    'permintaan' => 'permintaan', 'sedang_proses' => 'sedang_proses',
    'siap' => 'darah_siap', 'donor' => 'perlu_donor',
    'baru' => 'sampel_baru', 'incompatible' => 'incompatible',
    'masasimpan' => 'masa_simpan_habis', 'belumambil' => 'belum_diambil',
    'habis' => 'sudah_habis', 'tidaklengkap' => 'tidak_lengkap',
);
```
- 4 stat card memakai `(int)$sum['permintaan']`, `$sum['sedang_proses']`,
  `$sum['darah_siap']`, `$sum['perlu_donor']`.
- Badge pada 10 tab memakai `(int)$sum[$tabSummaryKey[$key]]`.
- Struktur HTML, class CSS (`stat-card`, `stat-primary`, …), ikon FontAwesome,
  dan layout `col-12 col-sm-6 col-xl-3` **tidak berubah**.

---

## 6. Hasil Testing

### Syntax check (php -l)
```
Dashboard.php          — No syntax errors detected
Dashboard_model.php    — No syntax errors detected
dashboard/index.php    — No syntax errors detected
```

### Verifikasi kesetaraan summary vs query per-tab
Setiap angka summary dibandingkan dengan query lama (sumber truth):

| Status | Summary | Query lama | Match |
|---|---|---|---|
| Permintaan | 0 | 0 | OK |
| Sedang Proses | 15 | 15 | OK |
| Darah Siap | 144 | 144 | OK |
| Perlu Donor | 17 | 17 | OK |
| Sampel Baru | 0 | 0 | OK |
| Incompatible | 0 | 0 | OK |
| Masa Simpan Habis | 141 | 141 | OK |
| Belum Diambil | 0 | 0 | OK |
| Sudah Habis | 616 | 616 | OK |
| Tidak Lengkap | 128 | 128 | OK |

Semua 10 cocok 100%.

### Simulasi render 10 tab badge + 4 stat card
```
=== RENDER SIMULASI 10 TAB BADGE ===
Permintaan           => 0
Sedang Proses        => 15
Darah Siap           => 144
Perlu Donor          => 17
Sampel Baru          => 0
Incompatible         => 0
Masa Simpan Habis    => 141
Belum Diambil        => 0
Sudah Habis          => 616
Tidak Lengkap        => 128
All 10 mapped: YES
```

### Checklist
- [x] Semua 10 card tampil (4 stat card + 10 tab badge)
- [x] Angka sesuai database (diverifikasi terhadap query lama)
- [x] Refresh halaman angka tetap update (query dijalankan setiap request)
- [x] Tidak ada hardcode (semua angka dari `get_status_summary()`)
- [x] Tidak mengubah `RequestController.php`
- [x] Tidak mengubah `Request_model.php` (UI-16B)
- [x] Tidak ada perubahan database (tidak ada migration)
- [x] Layout, warna, ikon, CSS existing tidak berubah

---

## 7. Catatan
- Jika diperlukan sinkronisasi penuh dengan filter tanggal
  `Request_model::_get_base_query()` (tgl_minta ±1 bulan atau
  tgl_diperlukan ±7 hari), filter tanggal summary dapat disesuaikan
  tanpa mengubah mapping status. Saat ini filter tanggal mengikuti
  definisi dashboard yang sudah ada agar angka tidak berubah dari versi sebelumnya.
