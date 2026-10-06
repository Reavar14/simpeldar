# PHASE UI-19 — JAVASCRIPT LOAD ORDER AUDIT ALL MODULES

**Tanggal:** 2026-09-28  
**Cakupan:** 8 modul, 9 file JS kustom, 1 file shared (app.js)  
**Referensi:** Fix UI-18 (`request-list.js` → `defer`)  

---

## 1. RINGKASAN EKSEKUTIF

| Status | Jumlah Modul |
|--------|--------------|
| ✅ **SUDAH DIPERBAIKI** (UI-18) | 1 (`request/list`) |
| ❌ **BERMASALAH — CRITICAL** | 7 (`billing`, `riwayat`, `laporan`, `rawat_inap`, `request/detail`, `request/edit`, `darah/form`) |
| ⚠️ **RISIKO SEDANG** | 1 (`cetakan`) |
| ✅ **AMAN** (sudah di footer) | 1 (`app.js`) |

**Root Cause Universal:**  
Semua view CI3 memuat **custom JS di dalam body view** (sebelum `templates/footer`), sedangkan **jQuery + DataTables + plugin dimuat di `footer.php`**.  
→ Custom JS dieksekusi saat `jQuery` **belum didefinisikan** → `ReferenceError: jQuery is not defined` → script crash diam-diam.

---

## 2. DETAIL PER MODUL

### 2.1 Modul Bermasalah — CRITICAL (7)

| Modul | View | JS File | IIFE | DataTables | Swal | DocReady | Urutan Load | Status |
|-------|------|---------|------|------------|------|----------|-------------|--------|
| **billing** | `billing/index.php:19` | `billing.js` | ✅ | ✅ | ✅ | ✅ | View body → footer | ❌ CRASH |
| **riwayat** | `riwayat/index.php:21` | `riwayat.js` | ✅ | ✅ | ✅ | ✅ | View body → footer | ❌ CRASH |
| **laporan** | `laporan/index.php:63` | `laporan.js` | ✅ | ✅ | ✅ | ✅ | View body → footer | ❌ CRASH |
| **rawat_inap** | `darah/rawat_inap.php:117` | `rawat-inap.js` | ✅ | ✅ | ✅ | ✅ | View body → footer | ❌ CRASH |
| **request/detail** | `request/detail.php:93` | `request-detail.js` | ✅ | ❌ | ✅ | ✅ | View body → footer | ❌ CRASH |
| **request/edit** | `request/edit.php:43` | `request-edit.js` | ✅ | ❌ | ✅ | ✅ | View body → footer | ❌ CRASH |
| **darah/form** | `darah/form.php:44` | `form-darah.js` | ❌ | ❌ | ✅ | ❌ | View body → footer | ❌ CRASH + TOP-LEVEL $ |

### 2.2 Modul Risiko Sedang (1)

| Modul | View | JS File | IIFE | DataTables | Swal | DocReady | Urutan Load | Status |
|-------|------|---------|------|------------|------|----------|-------------|--------|
| **cetakan** | `cetakan/index.php:15` | `cetakan.js` | ❌ | ❌ | ✅ | ✅ | View body → footer | ⚠️ RISK |

**Catatan cetakan.js:** Tidak pakai IIFE, tapi pakai `$(function() { ... })` (document ready). Jika `jQuery` belum load saat file dieksekusi, `$` undefined → error. Tapi karena dibungkus doc-ready, *mungkin* tidak crash sampai event ready fire — tapi `$` undefined saat parsing.

### 2.3 Modul Aman (1)

| Modul | View | JS File | Lokasi | Status |
|-------|------|---------|--------|--------|
| **app.js (shared)** | `templates/footer.php:36` | `app.js` | **Footer (setelah jQuery)** | ✅ AMAN |

---

## 3. ANALISIS DEPENDENCY PER FILE JS

| File | Lines | IIFE | jQueryCall | DataTables | Swal/SwalHelper | Top-Level $ | Risk Level |
|------|-------|------|------------|------------|-----------------|-------------|------------|
| `billing.js` | 159 | ✅ | ✅ | ✅ | ✅ | ❌ | CRITICAL |
| `riwayat.js` | 173 | ✅ | ✅ | ✅ | ✅ | ❌ | CRITICAL |
| `laporan.js` | 410 | ✅ | ✅ | ✅ | ✅ | ❌ | CRITICAL |
| `rawat-inap.js` | 219 | ✅ | ✅ | ✅ | ✅ | ❌ | CRITICAL |
| `request-detail.js` | 68 | ✅ | ✅ | ❌ | ✅ | ❌ | CRITICAL |
| `request-edit.js` | 219 | ✅ | ✅ | ❌ | ✅ | ❌ | CRITICAL |
| `riwayat.js` | 173 | ✅ | ✅ | ✅ | ✅ | ❌ | CRITICAL |
| `form-darah.js` | 508 | ❌ | ❌ | ❌ | ✅ | ✅ | CRITICAL+ |
| `cetakan.js` | 54 | ❌ | ❌ | ❌ | ✅ | ❌ | MEDIUM |

**Legenda:**
- **IIFE = ` (function($){ ... })(jQuery) `** → butuh `jQuery` global saat parse
- **jQueryCall = ` })(jQuery) `** → butuh `jQuery` saat eksekusi IIFE
- **Top-Level $ = penggunaan `$` di luar `$(function() {...})`** → crash instan
- **DocReady = `$(function() {...})` atau `$(document).ready()`** → aman *jika* jQuery sudah load saat ready fire

---

## 4. POLA KEGAGALAN

### 4.1 Pola A — IIFE Immediate Execution (7 file)
```javascript
(function ($) {
    'use strict';
    var dt = $('#table').DataTable({ ... });  // butuh jQuery + DataTables
    ...
})(jQuery);  // ← CRASH: jQuery is not defined
```
**File:** `billing.js`, `riwayat.js`, `laporan.js`, `rawat-inap.js`, `request-detail.js`, `request-edit.js`, `request-list.js` (FIXED)

### 4.2 Pola B — Top-Level $ Tanpa IIFE (1 file)
```javascript
// form-darah.js
var $form = $('#formEditPermintaan');  // ← CRASH: $ is not defined
$(function() { ... });  // terlambat, sudah crash di atas
```
**File:** `form-darah.js` — **PALING BERBAHAYA**

### 4.3 Pola C — Document Ready Only (1 file)
```javascript
// cetakan.js
$(function() {
    // code here
});
```
**File:** `cetakan.js` — Mungkin tidak crash sampai ready event, tapi `$` undefined saat file dieksekusi → risky.

---

## 5. URUTAN LOAD HTML (SEBELUM FIX)

```
HTML Output (rendered):
├── <head> ... </head>
├── <body>
│   ├── header (navbar, sidebar)
│   ├── page content
│   │   ├── VIEW BODY
│   │   │   ├── inline CONFIG (window.XXX_CONFIG)    ← OK
│   │   │   ├── <script src="custom-module.js"></>  ← EXECUTE NOW — jQuery UNDEFINED
│   │   │   └── ...
│   │   └── ...
│   └── footer.php (templates/footer.php)
│       ├── <script src="jquery.min.js"></>          ← jQuery LOAD DI SINI
│       ├── <script src="bootstrap.bundle.min.js"></>
│       ├── <script src="flatpickr"></>
│       ├── <script src="dataTables.min.js"></>      ← DataTables LOAD DI SINI
│       ├── <script src="dataTables.bootstrap5.min.js"></>
│       ├── <script src="dataTables.responsive.min.js"></>
│       ├── <script src="select2.min.js"></>
│       ├── <script src="sweetalert2.all.min.js"></>
│       └── <script src="app.js"></>                ← app.js OK (setelah jQuery)
```

---

## 6. REKOMENDASI FIX

### Fix Minimal — Tambahkan `defer` pada Semua Custom JS di View (PRIORITAS 1)

**Pola:**
```html
<!-- SEBELUM -->
<script src="<?php echo base_url('assets/js/billing.js'); ?>"></script>

<!-- SESUDAH -->
<script src="<?php echo base_url('assets/js/billing.js'); ?>" defer></script>
```

**File View yang Harus Diubah (8 file):**
1. `application/views/billing/index.php` line 19
2. `application/views/riwayat/index.php` line 21
3. `application/views/laporan/index.php` line 63
4. `application/views/darah/rawat_inap.php` line 117
5. `application/views/request/detail.php` line 93
6. `application/views/request/edit.php` line 43
7. `application/views/darah/form.php` line 44
8. `application/views/cetakan/index.php` line 15

### Fix Alternatif — Pindah Custom JS ke Footer (PRIORITAS 2 — Lebih Bersih)

**Kelebihan:** Semua script urut, tidak perlu `defer` per file  
**Kekurangan:** Perlu mekanisme pass config dari view ke footer (misal `window.XXX_CONFIG` sudah global, OK)

**Implementasi:**
1. Di view: HAPUS `<script src="custom.js"></script>`
2. Di `templates/footer.php`: Tambah array config → render script tags setelah app.js

```php
<!-- templates/footer.php (setelah app.js) -->
<?php if (isset($page_scripts)): ?>
    <?php foreach ($page_scripts as $script): ?>
        <script src="<?php echo base_url($script); ?>" defer></script>
    <?php endforeach; ?>
<?php endif; ?>
```

**View controller:** `$data['page_scripts'] = ['assets/js/billing.js'];`

---

## 7. PRIORITAS DAN ESTIMASI

| Prioritas | Modul | File View | Fix | Estimasi |
|-----------|-------|-----------|-----|----------|
| **P0** | billing | `billing/index.php` | add `defer` | 1 menit |
| **P0** | riwayat | `riwayat/index.php` | add `defer` | 1 menit |
| **P0** | laporan | `laporan/index.php` | add `defer` | 1 menit |
| **P0** | rawat_inap | `darah/rawat_inap.php` | add `defer` | 1 menit |
| **P0** | request/detail | `request/detail.php` | add `defer` | 1 menit |
| **P0** | request/edit | `request/edit.php` | add `defer` | 1 menit |
| **P0** | darah/form | `darah/form.php` | add `defer` | 1 menit |
| **P1** | cetakan | `cetakan/index.php` | add `defer` | 1 menit |

**Total:** ~8 menit untuk fix minimal `defer` ke semua 8 view.

---

## 8. VERIFIKASI PASCA FIX

Setiap modul setelah fix `defer`:

| Cek | Cara |
|-----|------|
| Script punya `defer` | `grep -n 'defer' application/views/**/*.php` |
| jQuery load sebelum custom JS execute | DevTools → Network → urutan load |
| DataTables init berhasil | DevTools → Console → no error |
| AJAX request terkirim | Network → POST `/ajax_list` → 200 |
| Tabel render data | Visual check halaman |

---

## 9. KESIMPULAN

| Metrik | Nilai |
|--------|-------|
| **Modul crash karena load order** | 7 dari 8 (87.5%) |
| **Root cause** | Custom JS di view body dieksekusi sebelum jQuery (di footer) |
| **Fix yang direkomendasikan** | Tambah `defer` pada 8 tag `<script>` di view |
| **Waktu fix** | ~8 menit |
| **Risiko regressi** | Rendah — `defer` standar HTML5, tidak ubah logic |

**Next Action:** Apply `defer` ke 8 view file, lalu test semua modul.

---

## 10. CATATAN TAMBAHAN

**form-darah.js (darah/form.php)** — Khusus perilaku:
- Tidak pakai IIFE, tidak pakai document ready
- Menggunakan `$` di top-level (line 1+: `var $form = $('#formEditPermintaan')`)
- **Ini CRASH PASTI** tanpa `defer` — paling urgent di antara 8

**app.js** — Sudah benar di footer setelah jQuery. Tetap biarkan.

**layouts/main.php** — Layout alternatif (tidak dipakai controller utama). Load jQuery di header, custom JS di line 86. Jika dipakai, urutannya benar (jQuery → app.js → custom). Tidak perlu ubah.