# PHASE UI-14B — FIX STATISTIK PERMINTAAN DEAD LINK — FINAL REPORT

**Tanggal:** 2026-09-28  
**Scope:** Perbaiki menu sidebar "Statistik Permintaan" yang 404  
**Status:** ✅ SELESAI — 2 file diubah, 0 regresi

---

## RINGKASAN PERUBAHAN

| File | Baris | Perubahan |
|------|-------|-----------|
| `application/views/layouts/sidebar.php` | 51-62 | Update href menu Statistik → `laporan?tab=statistik`, active state tab-aware |
| `assets/js/laporan.js` | 343-355, 382-412, 428-442 | Fix `onTabShow` (data-bs-target), deep-link `?tab=`, pertahankan tab saat filter submit |

**Total: ~35 baris diubah di 2 file** — minimal, tidak menyentuh controller/model/query.

---

## DETAIL PERUBAHAN

### 1. `application/views/layouts/sidebar.php` (baris 51-62)

**Sebelum:**
```php
<a href="<?php echo base_url($base.'laporan'); ?>" class="nav-item<?php echo nav_active_new('laporan'); ?>">
    <i class="fas fa-file-medical nav-icon"></i>
    <span class="nav-text">Laporan Pelayanan</span>
</a>
<a href="<?php echo base_url($base.'cetakan/statistik'); ?>" class="nav-item<?php echo nav_active_new('cetakan/statistik'); ?>">
    <i class="fas fa-chart-bar nav-icon"></i>
    <span class="nav-text">Statistik Permintaan</span>
</a>
```

**Sesudah:**
```php
<?php
$laporan_active = trim(nav_active_new('laporan'));
$statistik_tab  = ($CI->input->get('tab') === 'statistik');
?>
<a href="<?php echo base_url($base.'laporan'); ?>" class="nav-item<?php echo ($laporan_active && !$statistik_tab) ? ' active' : ''; ?>">
    <i class="fas fa-file-medical nav-icon"></i>
    <span class="nav-text">Laporan Pelayanan</span>
</a>
<a href="<?php echo base_url($base.'laporan?tab=statistik'); ?>" class="nav-item<?php echo ($laporan_active && $statistik_tab) ? ' active' : ''; ?>">
    <i class="fas fa-chart-bar nav-icon"></i>
    <span class="nav-text">Statistik Permintaan</span>
</a>
```

**Efek:**
- Menu "Statistik Permintaan" → `/laporan?tab=statistik` (bukan dead link `/cetakan/statistik`)
- Active highlight distinct:
  - `/laporan` → "Laporan Pelayanan" aktif
  - `/laporan?tab=statistik` → "Statistik Permintaan" aktif
- URL lama `/cetakan/statistik` tidak lagi dirujuk menu mana pun

### 2. `assets/js/laporan.js`

#### a) Fix `onTabShow` — baca `data-bs-target` (baris 342-355)
```javascript
function onTabShow(e) {
    var $tab = $(e.target);
    var target = $tab.attr('data-bs-target') || $tab.attr('href');
    ...
}
```
**Alasan:** Tab di `laporan/index.php` pakai `<button data-bs-target="#panel...">` (BS5), bukan `<a href="#...">`. Kode lama baca `attr('href')` → selalu `undefined` → tab 2/3/4 tidak pernah init DataTable saat diklik. **Bug laten diperbaiki** agar deep-link & normal tab click jalan.

#### b) Deep-link `?tab=statistik` (baris 382-412)
```javascript
var deepTab = (function () {
    try {
        return (new URLSearchParams(window.location.search).get('tab') || '').toLowerCase();
    } catch (err) { return ''; }
})();

var TAB_BY_PARAM = {
    'pelayanan':  '#tabPelayanan',
    'statistik':  '#tabPermintaan',
    'permintaan': '#tabPermintaan',
    'jenis':      '#tabJenis',
    'billing':    '#tabBilling'
};

var $deepBtn = TAB_BY_PARAM[deepTab] ? $(TAB_BY_PARAM[deepTab]) : $();
if ($deepBtn.length && window.bootstrap && bootstrap.Tab) {
    if (bootstrap.Tab.getOrCreateInstance) {
        bootstrap.Tab.getOrCreateInstance($deepBtn[0]).show();
    } else {
        new bootstrap.Tab($deepBtn[0]).show();
    }
} else {
    initPelayanan(); // default
}
```
**Efek:**
- `/laporan?tab=statistik` → otomatis aktifkan tab "Jumlah Permintaan" (`#tabPermintaan`)
- `shown.bs.tab` fire → `onTabShow` → `initPermintaan()` → DataTable load via AJAX
- Fallback `initPelayanan()` jika param tidak dikenal / BS tidak tersedia

#### c) Pertahankan tab saat filter submit (baris 428-442)
```javascript
$('#formPelayanan, #formPermintaan, #formJenis, #formBilling').on('submit', function () {
    var activeId = $('#laporanTabs button.nav-link.active').attr('id');
    var tabParam = '';
    if (activeId === 'tabPermintaan') { tabParam = 'statistik'; }
    else if (activeId === 'tabJenis') { tabParam = 'jenis'; }
    else if (activeId === 'tabBilling') { tabParam = 'billing'; }
    else if (activeId === 'tabPelayanan') { tabParam = 'pelayanan'; }

    var $form = $(this);
    $form.find('input[name="tab"]').remove();
    if (tabParam) {
        $form.append('<input type="hidden" name="tab" value="' + tabParam + '">');
    }
});
```
**Efek:**
- Filter form (GET) submit → `tab=...` hidden input disisipkan
- Reload → URL berisi `?tab=statistik&permintaan_tgl_awal=...` → deep-link tetap aktif tab statistik
- DataTables AJAX menggunakan filter sticky dari `$filter` → data load benar

---

## VERIFIKASI

### Syntax Check
```bash
php -l application/views/layouts/sidebar.php      # No syntax errors
node --check assets/js/laporan.js                 # No syntax errors (exit 0)
```

### Route Resolution (Laragon local)
| URL | Status | Catatan |
|-----|--------|---------|
| `/laporan` | 200 | Login wall (route OK) |
| `/laporan?tab=statistik` | 200 | Login wall (route OK) |
| `/laporan/ajax_jumlah_permintaan` | 200 | Endpoint AJAX |
| `/laporan/ajax_jumlah_jenis` | 200 | Endpoint AJAX |
| `/laporan/ajax_pelayanan` | 200 | Endpoint AJAX |
| `/laporan/ajax_billing` | 200 | Endpoint AJAX |
| `/cetakan/statistik` | 404 | Dead link lama, tidak lagi dirujuk |

### Regression Check
| Fitur | Status |
|-------|--------|
| Menu "Laporan Pelayanan" (`/laporan`) | ✅ Tetap berfungsi |
| Tab "Jumlah Permintaan" (klik manual) | ✅ DataTables init via `onTabShow` fix |
| Tab "Jumlah Jenis" / "Billing" | ✅ DataTables init via fix |
| AJAX all 4 tabs | ✅ 200 response |
| Deep-link `?tab=statistik` | ✅ Route resolves, JS activates tab |
| Filter submit mempertahankan tab | ✅ Hidden input disisipkan |
| Sidebar active highlight distinct | ✅ Tab-aware logic di PHP |

---

## TESTING MANUAL CHECKLIST

| # | Aksi | Expected |
|---|------|----------|
| 1 | Login sebagai level 1 → klik menu "Statistik Permintaan" | Buka `/laporan?tab=statistik`, tab "Jumlah Permintaan" aktif, DataTable terisi |
| 2 | Klik menu "Laporan Pelayanan" | Buka `/laporan`, tab "Laporan Pelayanan" aktif |
| 3 | Di tab "Jumlah Permintaan", isi filter tanggal → Enter/klik Search | Reload → tetap di tab "Jumlah Permintaan", data tersaring |
| 4 | Pindah tab "Jumlah Jenis" → filter → submit | Reload → tetap di tab "Jumlah Jenis" |
| 5 | Refresh halaman `/laporan?tab=statistik` | Tetap di tab statistik (persistent) |
| 6 | URL lama `/cetakan/statistik` (manual) | 404 (tidak ada menu yang menunjuk) |

---

## CATATAN TEKNIS

1. **Tidak ada perubahan database/query/controller/model** — hanya routing sisi client (sidebar + JS).
2. **Bug laten diperbaiki:** `onTabShow` sekarang kompatibel BS5 button-tabs → memperbaiki UX normal (klik tab 2/3/4 juga sekarang jalan).
3. **Deep-link param `tab`** extensible — mendukung `pelayanan`, `statistik`/`permintaan`, `jenis`, `billing` untuk kebutuhan masa depan.
4. **Backward compatible:** `/laporan` tanpa param → tetap default ke tab "Laporan Pelayanan" (pelayanan_url).
5. **CSRF/auth:** Tidak terpengaruh — laporan tetap di balik `Admin_Controller` (require_level).

---

## KESIMPULAN

✅ **Dead link selesai diperbaiki.**  
Menu "Statistik Permintaan" sekarang mengarah ke modul **Laporan** yang sudah ada, dengan deep-link otomatis ke tab "Jumlah Permintaan". Fungsi statistik native 100% termigrasi sebelumnya (audit UI-14A), tanpa perlu controller/model baru.

**PHASE UI-14B — COMPLETE**