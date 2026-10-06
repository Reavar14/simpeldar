# PHASE UI-18 — REQUEST PROCESS DISPLAY FIX REPORT

**Tanggal:** 2026-09-28  
**Halaman:** `/index.php/requestcontroller/list`  
**Masalah:** Halaman terbuka, header DataTables tampil, tetapi **tabel kosong** (0 baris)  
**Status:** ✅ FIX SELESAI — DataTables sekarang render data

---

## 1. ROOT CAUSE

### Script Load Order Bug — `request-list.js` dieksekusi SEBELUM jQuery dimuat

**File:** `application/views/request/list.php` (line 27)  
**Mekanisme:**

```
HTML output (urutan render):
  ① <script> window.REQUEST_LIST_CONFIG = {...} </script>   ← inline, OK
  ② <script src=".../assets/js/request-list.js"></script>   ← DIEKSEKUSI SEKARANG
  ③ <script src="jquery-3.7.1.min.js"></script>             ← jQuery baru load di sini
  ④ <script src="dataTables.min.js"></script>
  ⑤ <script src=".../assets/js/app.js"></script>
```

`request-list.js` dibungkus IIFE yang langsung reference `jQuery`:

```javascript
(function ($) {
    'use strict';
    ...
    $table.DataTable({ ... });   // butuh jQuery + DataTables
    ...
})(jQuery);                       // ← CRASH: jQuery is not defined
```

Saat browser parse tag ②, `jQuery` **belum ada** (baru ada di tag ③).  
→ `ReferenceError: jQuery is not defined`  
→ Script berhenti total  
→ `initDataTable()` tidak pernah jalan  
→ DataTables tidak terbentuk  
→ **Tabel kosong, no AJAX call, no error visual**

### Bukti Teknis

DOM parse halaman (sebelum fix):
```
1. (inline)                     ← REQUEST_LIST_CONFIG
2. request-list.js              ← CRASH di sini (jQuery undefined)
3. jquery-3.7.1.min.js          ← terlambat
4. dataTables.min.js
...
```

Controller `RequestController::list()` (lines 19-21):
```php
$this->load->view('templates/header');   // ← tidak load JS
$this->load->view('request/list');       // ← request-list.js DI SINI
$this->load->view('templates/footer');   // ← jQuery & DataTables di SINI
```

---

## 2. CEK BACKEND — TIDAK BERMASALAH

Sebelum fix, backend sudah dikonfirmasi benar:

| Cek | Hasil |
|-----|-------|
| HTTP status POST `/requestcontroller/ajax_list` | **200** ✅ |
| JSON valid | ✅ |
| `recordsTotal` | **50** ✅ |
| `recordsFiltered` | **50** ✅ |
| `data` array | **10 rows** ✅ |
| Response time | ~1.0s ✅ |
| Format sesuai DataTables | ✅ `{draw, recordsTotal, recordsFiltered, data}` |
| Query DB (0.3s, scalar subqueries) | ✅ |
| Table ID `#tabelPermintaan` match JS | ✅ |
| 13 columns JS = 13 columns controller | ✅ |

**Kesimpulan:** Backend, query, model, controller, AJAX endpoint — semua benar.  
**Masalah 100% client-side: script execution order.**

---

## 3. PERBANDINGAN DENGAN NATIVE

| Aspek | Native (`simpeldar_new`) | CI3 (sebelum fix) |
|-------|--------------------------|-------------------|
| **JS framework** | jQuery 1.x + DataTables 1.10 (Charisma) | jQuery 3.7 + DataTables 2.0.8 (CDN) |
| **Script load** | Semua JS di `header.php` / `footer.php` (monolith, jQuery duluan) | Page JS di view body, jQuery di footer (jQuery terlambat) ❌ |
| **Query** | SSP class, LEFT JOIN 8 tabel, `status != 0` | Scalar subqueries (UI-16B), include status 0 |
| **Database** | `192.168.7.241` (production) | `localhost` (local copy) |
| **Kolom** | 13 (dt 0-12) | 13 (data 0-12) ✅ |
| **DataTables init** | Server-side SSP | Server-side CI3 QB ✅ |

Native bekerja karena **semua script dimuat di footer/header setelah jQuery**. CI3 salah urutan: page JS di view body sebelum footer.

---

## 4. FIX

### File Yang Berubah (1 file saja)

**`application/views/request/list.php`** — line 27

**Before:**
```html
<script src="<?php echo base_url('assets/js/request-list.js'); ?>"></script>
```

**After:**
```html
<script src="<?php echo base_url('assets/js/request-list.js'); ?>" defer></script>
```

### Kenapa `defer` memperbaiki?

`defer` = browser download script tapi **tunda eksekusi sampai document parse selesai**.

Setelah fix, urutan eksekusi:
```
1. inline REQUEST_LIST_CONFIG     → jalankan (set config) ✓
2. request-list.js [DEFER]        → antri, jangan eksekusi
3. jquery-3.7.1.min.js            → jalankan ✓ (jQuery loaded!)
4. dataTables.min.js              → jalankan ✓
5. app.js (SwalHelper, CSRF prefilter) → jalankan ✓
--- document parse selesai ---
6. request-list.js                → jalankan SEKARANG → jQuery tersedia ✓
   → initDataTable() → DataTables terbentuk
   → AJAX POST ajax_list → 50 rows → RENDER ✓
```

Tidak ada perubahan: database, controller, model, UI, CSS, struktur tabel, action buttons.

---

## 5. HASIL TESTING

### Test 1: Script order (verifikasi HTML output)
```
1. (inline)
2. request-list.js [DEFER]     ← fix aktif
3. jquery-3.7.1.min.js         ← jQuery before execution
4-12. (bootstrap, DataTables, app.js)
```

### Test 2: Page load
```
List page: HTTP 200
  Has table ID #tabelPermintaan: True
  Has defer on request-list.js: True
  Has REQUEST_LIST_CONFIG: True
```

### Test 3: AJAX endpoint (POST `/requestcontroller/ajax_list`)
```
HTTP: 200
recordsTotal: 50
recordsFiltered: 50
data rows: 10
first row no_permintaan: 2026080434
first row nama: HADIRI, TN
```

### Test 4: Full browser flow (login → list → DataTables)
```
1. Login admin (level 1)           → 302 redirect dashboard ✓
2. GET /requestcontroller/list     → 200, page renders ✓
3. DataTables init (defer)         → jQuery available ✓
4. AJAX POST ajax_list             → 200, 50 records ✓
5. Table render 10 rows/page       → DATA TAMPIL ✓
```

---

## 6. CATATAN: MODUL LAIN MEMILIKI BUG YANG SAMA

Semua view CI3 memuat page-specific JS di body sebelum footer:

| View | JS File | Status |
|------|---------|--------|
| `billing/index.php` | `billing.js` | ⚠️ Same bug |
| `riwayat/index.php` | `riwayat.js` | ⚠️ Same bug |
| `laporan/index.php` | `laporan.js` | ⚠️ Same bug |
| `cetakan/index.php` | `cetakan.js` | ⚠️ Same bug |
| `darah/form.php` | `form-darah.js` | ⚠️ Same bug |
| `request/detail.php` | `request-detail.js` | ⚠️ Same bug |
| `request/edit.php` | `request-edit.js` | ⚠️ Same bug |

Semua file JS memakai pattern `(function ($) { ... })(jQuery)` — crash jika jQuery belum load.

**Fix untuk modul lain (jika diperlukan):** tambahkan `defer` pada `<script src>` di masing-masing view, sama seperti fix list.php.

---

## 7. KESIMPULAN

| Pertanyaan | Jawaban |
|------------|---------|
| **Root cause** | `request-list.js` dieksekusi sebelum jQuery dimuat → `ReferenceError: jQuery is not defined` → DataTables tidak pernah init |
| **Backend bermasalah?** | TIDAK — AJAX return 200, 50 rows, JSON valid |
| **Query bermasalah?** | TIDAK — sudah dioptimasi di UI-16B (0.3s) |
| **Database bermasalah?** | TIDAK — data ada di local DB |
| **File berubah** | 1 file: `application/views/request/list.php` (tambah `defer`) |
| **UI berubah?** | TIDAK |
| **Database/controller/model berubah?** | TIDAK |
| **Data sekarang tampil?** | YA — 50 records, 10 rows/page |

**PHASE UI-18 SELESAI.** DataTables "Proses Permintaan Darah" sekarang menampilkan data.