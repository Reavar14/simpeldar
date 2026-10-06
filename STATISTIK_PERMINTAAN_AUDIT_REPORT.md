# PHASE UI-14A — AUDIT MENU "STATISTIK PERMINTAAN"

**Tanggal:** 2026-09-28  
**Masalah:** Menu sidebar "Statistik Permintaan" → HTTP 404  
**Status audit:** SELESAI — belum ada kode diubah

---

## 1. SUMSUM MENU (Trace Sumber)

### 1.1 sidebar.php
`application/views/layouts/sidebar.php:55-58` (hanya muncul untuk level 1):

```php
<a href="<?php echo base_url($base.'cetakan/statistik'); ?>" class="nav-item<?php echo nav_active_new('cetakan/statistik'); ?>">
    <i class="fas fa-chart-bar nav-icon"></i>
    <span class="nav-text">Statistik Permintaan</span>
</a>
```

**URL target:** `index.php/cetakan/statistik`

### 1.2 routes.php
`application/config/routes.php:52-54`:

```php
$route['default_controller'] = 'auth';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
```

Tidak ada route eksplisit. CI3 default routing: `cetakan/statistik` → `Cetakan::statistik()`.

### 1.3 Controller tujuan (Cetakan.php)
`application/controllers/Cetakan.php` — hanya punya **1 method**:

| Method | Ada? |
|--------|------|
| `index()` | ✅ Ada (baris 23) |
| **`statistik()`** | ❌ **TIDAK ADA** |

`Cetakan::statistik()` tidak ada → CI3 memanggil method yang tidak exist → **404**. 
Penyebab 404 dikonfirmasi: **method target tidak pernah dibuat**, bukan auth, bukan DB.

### 1.4 View lama
Pencarian `**/views/**/*statistik*` → **tidak ada file**. 
Tidak pernah dibuat view untuk route ini.

---

## 2. PENCARIAN IMPLEMENTASI NATIVE

Hasil grep seluruh project untuk keyword terkait:

| Keyword | Hasil |
|---------|-------|
| `"Statistik Permintaan"` | 1 match — **hanya sidebar.php:57** |
| `statistik` (case-insensitive, *.php) | 1 match — **hanya sidebar.php:55** (URL) |
| `grafik` / `chart` | Hanya FontAwesome icon `fa-chart-bar`/`fa-chart-pie`, mime type di `config/mimes.php`. **Tidak ada library grafik (Chart.js/canvas) di seluruh project.** |
| `jumlah permintaan` | Ditemukan — **di dalam modul Laporan** |
| `jenis darah` | Ditemukan — form input, dropdown, print templates, Laporan |
| `permintaan darah` | Ditemukan — controller/model/view umum |

### Detail temuan "Jumlah Permintaan" & "Jenis Darah"

Fungsi statistik **sudah ada**, tapi sebagai **tab di dalam modul Laporan**, bukan modul sendiri:

`application/views/laporan/index.php:17-26`:

```php
<li class="nav-item" ...><button id="tabPermintaan" ...>
    <i class="fas fa-chart-bar me-1"></i> Jumlah Permintaan
</button></li>
<li class="nav-item" ...><button id="tabJenis" ...>
    <i class="fas fa-chart-pie me-1"></i> Jumlah Jenis
</button></li>
```

Data di-load via AJAX dari `Laporan` controller (bukan Cetakan):

| Tab | Controller Method | Model Method | Output |
|-----|-------------------|--------------|--------|
| Jumlah Permintaan | `Laporan::ajax_jumlah_permintaan()` | `Laporan_model::get_jumlah_permintaan()` | DataTables group-by status |
| Jumlah Jenis | `Laporan::ajax_jumlah_jenis()` | `Laporan_model::get_jumlah_jenis()` | DataTables group-by jenis darah |

View: `application/views/laporan/components/permintaan_table.php` & `jenis_table.php`  
JS: `assets/js/laporan.js` (konfigurasi AJAX di `views/laporan/index.php:56-62`)

### Asal native
`Laporan_model` doc-comment: *"Migrasi query dari admin/form_laporan.php (section 2/3)"*. 
Query native sudah dipindahkan ke CI3, tetapi **tidak pernah dibuat halaman terpisah bernama "statistik"** — semuanya diserap ke dalam tab Laporan.

Dipertegas di `FULL_SYSTEM_AUDIT_REPORT.md:97-104` dan `DATABASE_MIGRATION_FINAL_REPORT.md:35` — modul statistik native "Jumlah Permintaan" & "Jumlah Jenis" ada di modul **Laporan**, status ✅ PASS.

---

## 3. KESIMPULAN

## → **B. MENU HANYA SISA / DEAD LINK** (dengan catatan)

Bukan modul statistik tersendiri yang perlu dimigrasi — **fungsinya sudah termigrasi penuh ke modul Laporan** (Tab 2 "Jumlah Permintaan" + Tab 3 "Jumlah Jenis"). Yang tertinggal hanyalah **entri menu sidebar dengan URL ke controller/method yang tidak pernah dibuat** (`cetakan/statistik`).

Bukti:
1. `statistik()` tidak ada di `Cetakan.php` (hanya `index()`)
2. Tidak ada view `*statistik*`
3. Tidak ada model method berlabel statistik
4. Tidak ada route khusus
5. Tidak ada aset JS/chart library untuk grafik statistik
6. Query native `form_laporan.php section 2/3` sudah ada di `Laporan_model` dan teruji (55/55 PASS)

---

## 4. MAPPING (jika modul lama ada — untuk referensi parity)

Meskipun bukan modul terpisah, ini mapping fungsi statistik yang sudah aktif:

### Statistik Jumlah Permintaan (group by status)
- **Controller:** `Laporan::ajax_jumlah_permintaan()` — `application/controllers/Laporan.php:90`
- **View:** `application/views/laporan/components/permintaan_table.php`
- **Model:** `Laporan_model::get_jumlah_permintaan()` — `application/models/Laporan_model.php:70`
- **Query:**
```sql
SELECT ref.ID AS status_id, ref.DESKRIPSI AS status, COUNT(pd.no_permintaan) AS jumlah
FROM darah.referensi ref
LEFT JOIN darah.pesan_darah pd
    ON pd.status = ref.ID AND pd.tgl_minta BETWEEN ? AND ?
WHERE ref.JENIS = 3
[AND ref.ID = ?]
GROUP BY ref.ID, ref.DESKRIPSI ORDER BY ref.ID ASC
```
- **Table database:** `darah.referensi` (JENIS=3 = status), `darah.pesan_darah`

### Statistik Jumlah Jenis (group by jenis darah)
- **Controller:** `Laporan::ajax_jumlah_jenis()` — `application/controllers/Laporan.php:120`
- **View:** `application/views/laporan/components/jenis_table.php`
- **Model:** `Laporan_model::get_jumlah_jenis()` — `application/models/Laporan_model.php:120`
- **Query:**
```sql
SELECT jd.id AS jenis_id, jd.nama_jenis, COUNT(pd.no_permintaan) AS jumlah
FROM darah.jenis_darah jd
LEFT JOIN darah.pesan_darah pd
    ON pd.jenis_darah = jd.id AND pd.tgl_minta BETWEEN ? AND ?
WHERE jd.jenis = 1
GROUP BY jd.id, jd.nama_jenis ORDER BY jd.id ASC
```
- **Table database:** `darah.jenis_darah` (jenis=1), `darah.pesan_darah`

---

## 5. REKOMENDASI

Karena fungsi sudah 100% ada di `/laporan`, **tidak perlu buat modul baru**.

### Opsi 1 — REKOMENDASI: Perbaiki href menu sidebar (1 baris)
`application/views/layouts/sidebar.php:55` ganti target dari `cetakan/statistik` → `laporan`:

```php
<?php // SEBELUM ?>
<a href="<?php echo base_url($base.'cetakan/statistik'); ?>" class="nav-item<?php echo nav_active_new('cetakan/statistik'); ?>">

<?php // SESUDAH ?>
<a href="<?php echo base_url($base.'laporan'); ?>" class="nav-item<?php echo nav_active_new('laporan'); ?>">
```

**Alasan:**
- Nihil duplikasi — statistik adalah subset Laporan (Tab 2 & 3)
- `nav_active_new('laporan')` sudah benar; saat di `/laporan` menu akan tersorot
- Konsisten dengan menu "Laporan Pelayanan" di atasnya yang juga ke `/laporan`
- Query sudah teruji 55/55 PASS di `PHASE_UI-13_FINAL_REPORT.md`

**Catatan:** menu "Laporan Pelayanan" (`sidebar.php:51`) dan "Statistik Permintaan" sama-sama akan mengarah ke `/laporan`. Bisa diterima (kedua menu = pintasan ke tab berbeda di halaman yang sama), atau opsi 2 jika ingin tetap distinct.

### Opsi 2 — Hapus menu "Statistik Permintaan" dari sidebar
Hapus `sidebar.php:55-58`. Menghilangkan kebingungan ganda. Statistik tetap reachable via Laporan → tab "Jumlah Permintaan" / "Jumlah Jenis".

### Opsi 3 — JANGAN: Buat modul statistik baru
Tidak ada dasar. Native `form_laporan.php` pun menaruh statistik dalam satu halaman laporan, bukan modul sendiri. Membuat `Cetakan::statistik()` akan duplikasi `Laporan_model` yang sudah jalan.

---

## 6. RINGKASAN EKSEKUSI

| Item | Status |
|------|--------|
| Sidebar menu source | Ditemukan `sidebar.php:55-58` → target `cetakan/statistik` |
| Route | Default CI3 routing, tidak ada route eksplisit |
| Controller lama | `Cetakan::statistik()` **tidak pernah ada** |
| View lama | **tidak ada** |
| Modul statistik native? | **B — dead link**, fungsi sudah termigrasi ke Laporan (Tab 2/3) |
| A/B verdict | **B** (dengan catatan: fungsi exists, hanya URL menu yang salah) |
| Rekomendasi | **Opsi 1** — perbaiki href sidebar → `laporan` (1 baris) |
| Kode diubah? | **BELUM** — sesuai instruksi, laporan dibuat dulu |

---

**PHASE UI-14A — AUDIT SELESAI. Menunggu konfirmasi opsi sebelum eksekusi perbaikan.**
