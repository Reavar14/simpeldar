# PHASE UI-14C — REMOVE DUPLICATE STATISTIK PERMINTAAN MENU — FINAL REPORT

**Tanggal:** 2026-09-28  
**Scope:** Hapus menu sidebar "Statistik Permintaan" (duplikat Laporan Pelayanan)  
**Status:** ✅ SELESAI — 2 file dibersihkan, 0 regresi

---

## RINGKASAN

Menu "Statistik Permintaan" setelah UI-14B mengarah ke `/laporan?tab=statistik` — halaman yang sama dengan "Laporan Pelayanan", hanya beda tab. Membuka halaman ganda untuk konten identik → hapus menu, pertahankan satu pintu masuk: **Laporan Pelayanan**.

| File | Perubahan |
|------|-----------|
| `application/views/layouts/sidebar.php` | Hapus menu "Statistik Permintaan" + blok PHP active-state khusus statistik |
| `assets/js/laporan.js` | Hapus deep-link `?tab=` handler + tab-preservation submit handler (dead code) |

**Tidak menyentuh:** controller, model, query, database, view laporan.

---

## DETAIL PERUBAHAN

### 1. `application/views/layouts/sidebar.php`

**Sebelum (baris 51-62):**
```php
<?php
$laporan_active = trim(nav_active_new('laporan'));
$statistik_tab  = ($CI->input->get('tab') === 'statistik');
?>
<a href="...laporan" class="nav-item<?php echo ($laporan_active && !$statistik_tab) ? ' active' : ''; ?>">
    <i class="fas fa-file-medical nav-icon"></i>
    <span class="nav-text">Laporan Pelayanan</span>
</a>
<a href="...laporan?tab=statistik" class="nav-item<?php echo ($laporan_active && $statistik_tab) ? ' active' : ''; ?>">
    <i class="fas fa-chart-bar nav-icon"></i>
    <span class="nav-text">Statistik Permintaan</span>
</a>
```

**Sesudah (baris 51-58):**
```php
<a href="<?php echo base_url($base.'laporan'); ?>" class="nav-item<?php echo nav_active_new('laporan'); ?>">
    <i class="fas fa-file-medical nav-icon"></i>
    <span class="nav-text">Laporan Pelayanan</span>
</a>
<a href="<?php echo base_url($base.'cetakan'); ?>" class="nav-item<?php echo nav_active_new('cetakan'); ?>">
    <i class="fas fa-print nav-icon"></i>
    <span class="nav-text">Cetakan</span>
</a>
```

**Yang dihapus:**
- Anchor "Statistik Permintaan" (dua varian: `laporan?tab=statistik` dan `cetakan/statistik`)
- Blok PHP `$laporan_active` / `$statistik_tab` — hanya dipakai menu itu
- `fa-chart-bar` icon menu (icon `fa-chart-bar`/`fa-chart-pie` di dalam tab Laporan tetap utuh)

**Active state:** kembali polos `nav_active_new('laporan')` — tidak ada lagi pengecualian tab.

### 2. `assets/js/laporan.js`

Dihapus (dead code — satu-satunya konsumen `?tab=` adalah menu yang baru dihapus):

| Bagian | Baris (sebelum) | Alasan hapus |
|--------|-----------------|--------------|
| Deep-link `deepTab` + `URLSearchParams` + `TAB_BY_PARAM` + `bootstrap.Tab.show()` | 385-412 | Param `?tab=` tidak lagi dirujuk sidebar manapun |
| Tab-preservation submit handler (`input[name="tab"]` hidden) | 428-442 | Param target hilang → tidak berguna |

**Yang tetap (berguna untuk semua tab, bukan statistik-only):**
- Fix `onTabShow` baca `data-bs-target` (baris 343-345) — wajib agar tab BS5 init DataTable saat diklik
- `initPelayanan()` sebagai initial tab (default, seperti semula)

Sekarang inisialisasi tab kembali ke pola asli:
```javascript
/* Tab events */
$('#laporanTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', onTabShow);

/* Initial tab */
initPelayanan();
```

---

## VERIFIKASI

### Syntax Check
```bash
php -l application/views/layouts/sidebar.php   # No syntax errors
node --check assets/js/laporan.js              # No syntax errors (exit 0)
```

### Dead Code Sweep
Grep seluruh project untuk `Statistik Permintaan`, `cetakan/statistik`, `tab=statistik`, `TAB_BY_PARAM`, `deepTab`, `statistik_tab`:

```
No files found ✅
```

### Route Resolution (Laragon local)
| URL | Status | Catatan |
|-----|--------|---------|
| `/laporan` | 200 | Halaman Laporan (tab Pelayanan default) |
| `/laporan?tab=statistik` | 200 | Tab param kini inert (tidak dihidupkan/dimatikan JS) — halaman normal |
| `/laporan/ajax_jumlah_permintaan` | 200 | Endpoint AJAAX tetap |
| `/laporan/ajax_jumlah_jenis` | 200 | Endpoint AJAAX tetap |
| `/laporan/ajax_pelayanan` | 200 | Endpoint AJAAX tetap |
| `/laporan/ajax_billing` | 200 | Endpoint AJAAX tetap |
| `/cetakan` | 200 | Menu Cetakan tetap |

### Regression Check
| Item | Status |
|------|--------|
| Sidebar tidak menampilkan "Statistik Permintaan" | ✅ |
| Menu = Laporan Pelayanan + Cetakan saja | ✅ |
| `/laporan` tetap buka Laporan Pelayanan | ✅ |
| Tab "Jumlah Permintaan" tersedia di halaman | ✅ markup `#panelPermintaan` utuh |
| Tab "Jumlah Jenis" tersedia di halaman | ✅ markup `#panelJenis` utuh |
| Tab "Billing Kantong" tersedia | ✅ |
| Klik tab 2/3/4 → DataTable init | ✅ fix `onTabShow` `data-bs-target` tetap |
| Semua AJAX Laporan 200 | ✅ |
| Perubahan database | ❌ TIDAK ADA |

---

## TESTING MANUAL CHECKLIST

| # | Aksi | Expected |
|---|------|----------|
| 1 | Login level 1 → lihat sidebar section "Laporan" | Hanya 2 menu: "Laporan Pelayanan", "Cetakan" |
| 2 | Klik "Laporan Pelayanan" | Buka `/laporan`, tab pertama aktif, DataTable terisi |
| 3 | Klik tab "Jumlah Permintaan" | Tab aktif, DataTable load via AJAX |
| 4 | Klik tab "Jumlah Jenis" | Tab aktif, DataTable load via AJAX |
| 5 | Klik tab "Billing Kantong" | Tab aktif, DataTable load via AJAX |
| 6 | Cari "Statistik Permintaan" di sidebar | Tidak ada |
| 7 | Ketik manual `/laporan?tab=statistik` | Halaman Laporan normal (param inert) |

---

## STATUS FITUR STATISTIK

| Aspek | Status |
|-------|--------|
| Menu sidebar terpisah | ❌ Dihapus (UI-14C) |
| Fungsi statistik (Jumlah Permintaan + Jumlah Jenis) | ✅ Tetap penuh di dalam Laporan |
| Query database | ✅ Tidak diubah (`Laporan_model::get_jumlah_permintaan`, `get_jumlah_jenis`) |
| Akses | Via Laporan → tab "Jumlah Permintaan" / "Jumlah Jenis" |

**Catatan:** Teknis deep-link `?tab=` dapat reaktif kapan saja jika dibutuhkan (memetakan param → `bootstrap.Tab.show()`), tapi saat ini tidak ada yang memakai.

---

## KESIMPULAN

✅ **Menu duplikat dihapus.** Sidebar sekarang: Laporan Pelayanan (`/laporan`) + Cetakan (`/cetakan`). Fungsi statistik native 100% utuh di dalam Laporan (tab Jumlah Permintaan & Jumlah Jenis). Tidak ada perubahan database. Dead code `?tab=` dibersihkan dari PHP dan JS.

**PHASE UI-14C — COMPLETE**
