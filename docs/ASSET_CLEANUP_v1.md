# SIMPELDAR Asset Cleanup Report v1.0

**Date:** 2026-09-27  
**Phase:** UI-12 Production Preparation  
**Total Reclaimable:** ~24 MB (1,489 files)

---

## Summary by Directory

| Directory | Size | Files | Status | Safe Delete |
|-----------|------|-------|--------|-------------|
| `assets/admin/` | 17.27 MB | 1,322 | **UNUSED** (Charisma legacy) | **YES** |
| `assets/fonts/` | 1.18 MB | 10 | **UNUSED** (Glyphicons) | **YES** |
| `assets/sweetalert/` | 0.93 MB | 55 | **UNUSED** (SweetAlert v1) | **YES** |
| `assets/datepicker/` | 0.14 MB | 6 | **UNUSED** (legacy) | **YES** |
| `assets/datetimepicker/` | 0.47 MB | 6 | **UNUSED** (legacy) | **YES** |
| `assets/img/` | 0.06 MB | 22 | **UNUSED** (legacy) | **YES** |
| `assets/css/*` (29 unused) | ~1.3 MB | 29 | **UNUSED** (Bootstrap 3 themes, legacy) | **YES** |
| `assets/js/*` (20 unused) | ~0.7 MB | 20 | **UNUSED** (legacy jQuery plugins) | **YES** |
| `assets/login_style/` | 1.22 MB | 25 | **USED** (auth/login.php) | **NO** |
| `assets/logo/` | 0.85 MB | 8 | **USED** (bld.png, etc.) | **NO** |
| `assets/vendor/` | 1.58 MB | 17 | **USED** (BS5, DT2, Flatpickr, etc.) | **NO** |

---

## Detailed Analysis

### 1. `assets/admin/` — Charisma Admin Template (17.27 MB / 1,322 files)

**Contents:** Complete Bootstrap 3 admin theme with jQuery 1.x, DataTables 1.x, elFinder, Uploadify, multiple Bootswatch themes, FullCalendar, Flot charts, Highcharts, jQuery UI, etc.

**Why Unused:**
- App uses Bootstrap 5.3 via `assets/vendor/bootstrap/`
- Modern JS: DataTables 2.x, Flatpickr, Select2, SweetAlert2 via `assets/vendor/`
- No view references `assets/admin/` — templates use `assets/vendor/` + `assets/css/app.css` + `assets/js/*.js`

**Risk:** Zero — not loaded anywhere

**Safe Delete: YES**

---

### 2. `assets/fonts/` — Glyphicons (1.18 MB / 10 files)

**Contents:** `glyphicons-halflings-regular.{eot,svg,ttf,woff,woff2}`

**Why Unused:**
- Bootstrap 5 uses SVG icons
- FontAwesome 6 loaded via CDN in `templates/header.php`
- No CSS references Glyphicons

**Safe Delete: YES**

---

### 3. `assets/sweetalert/` — SweetAlert v1 (0.93 MB / 55 files)

**Why Unused:**
- `templates/header.php` loads SweetAlert2 11.10.5 from `assets/vendor/sweetalert/`
- No view includes `assets/sweetalert/`

**Safe Delete: YES**

---

### 4. `assets/datepicker/` & `assets/datetimepicker/` (0.61 MB / 12 files)

**Why Unused:**
- `templates/header.php` loads Flatpickr from `assets/vendor/flatpickr/`
- Legacy Bootstrap 3 datetimepicker (jQuery plugin)
- Flatpickr: lighter, no jQuery dependency

**Safe Delete: YES**

---

### 5. `assets/img/` (0.06 MB / 22 files)

**Why Unused:**
- Favicon, old logos, ajax loaders, iPhone toggle images
- Logo now loaded from `assets/logo/bld.png` in `templates/header.php`

**Safe Delete: YES**

---

### 6. Unused CSS in `assets/css/` (~1.3 MB / 29 files)

| File | Size | Reason |
|------|------|--------|
| animate.min.css | 54 KB | Legacy animation lib |
| bootstrap.css | 144 KB | Bootstrap 3 core |
| bootstrap.min.css | 107 KB | Bootstrap 3 minified |
| bootstrap-cerulean.min.css | 112 KB | Bootswatch theme |
| bootstrap-cyborg.min.css | 109 KB | Bootswatch theme |
| bootstrap-darkly.min.css | 111 KB | Bootswatch theme |
| bootstrap-lumen.min.css | 114 KB | Bootswatch theme |
| bootstrap-simplex.min.css | 110 KB | Bootswatch theme |
| bootstrap-slate.min.css | 121 KB | Bootswatch theme |
| bootstrap-spacelab.min.css | 114 KB | Bootswatch theme |
| bootstrap-united.min.css | 107 KB | Bootswatch theme |
| bootstrap-datetimepicker.css | 9 KB | Legacy datetimepicker |
| charisma-app.css | 17 KB | Charisma theme |
| elfinder.min.css + theme | 31 KB | File manager (unused) |
| eventCalendar*.css | ~17 KB | Legacy calendar |
| font-awesome.css/.min.css | 67 KB | FA4 (using FA6 via CDN) |
| ilmudetil.css | 1 KB | Legacy |
| jquery-ui-1.8.21.custom.css | 32 KB | jQuery UI (unused) |
| jquery.iphone.toggle.css | 4 KB | Legacy toggle |
| jquery.noty.css + theme | 11 KB | Noty v2 (using SweetAlert2) |
| style.css / style_.css / styles_cetak.css | <1 KB | Legacy |
| uploadify.css | 2 KB | Uploadify (unused) |

**KEPT (in use):**
- `app.css` (52 KB) — referenced in `templates/header.php` + `layouts/main.php`
- `select2.min.css` (15 KB) — Select2 dependency

**Safe Delete: YES** (29 files)

---

### 7. Unused JS in `assets/js/` (~0.7 MB / 20 files)

| File | Size | Reason |
|------|------|--------|
| bootstrap-datetimepicker.js | 99 KB | Legacy datetimepicker |
| charisma.js | 15 KB | Charisma admin app |
| exporting.js | 7 KB | Highcharts export |
| highcharts.js | 119 KB | Highcharts (unused) |
| init-chart.js | 7 KB | Chart init |
| jquery.autogrow-textarea.js | 2 KB | jQuery plugin |
| jquery.cookie.js | 2 KB | Use native JS cookies |
| jquery.eventCalendar.js | 15 KB | Legacy calendar |
| jquery.eventCalendar.min.js | 11 KB | Legacy calendar |
| jquery.history.js | 21 KB | jQuery History plugin |
| jquery.iphone.toggle.js | 10 KB | Legacy toggle |
| jquery.noty.js | 8 KB | Noty v2 |
| jquery.raty.min.js | 7 KB | Star rating (unused) |
| jquery.uploadify-3.1.min.js | 45 KB | Uploadify (unused) |
| jquery-1.10.1.min.js | 91 KB | jQuery 1.x (using 3.7 via vendor) |
| main.js | 1 KB | Legacy (`proses.php` ref) |
| moment.js | 120 KB | Moment.js (using native/Flatpickr) |

**KEPT (in use):**
- `app.js` (27 KB) — global init, CSRF, DataTables defaults
- `billing.js` (6 KB) — billing DataTables
- `cetakan.js` (2 KB) — cetakan filters
- `form-darah.js` (17 KB) — darah form logic
- `laporan.js` (16 KB) — laporan tabs + 4 AJAX
- `rawat-inap.js` (9 KB) — rawat inap DataTables
- `request-detail.js` (3 KB) — detail view
- `request-edit.js` (8 KB) — edit form
- `request-list.js` (10 KB) — request list DataTables
- `riwayat.js` (7 KB) — riwayat DataTables
- `select2.min.js` (65 KB) — Select2 library

**Safe Delete: YES** (20 files)

---

## Safe To Delete (Immediate)

```
assets/admin/
assets/fonts/
assets/sweetalert/
assets/datepicker/
assets/datetimepicker/
assets/img/
assets/css/animate.min.css
assets/css/bootstrap.css
assets/css/bootstrap.min.css
assets/css/bootstrap-cerulean.min.css
assets/css/bootstrap-cyborg.min.css
assets/css/bootstrap-darkly.min.css
assets/css/bootstrap-lumen.min.css
assets/css/bootstrap-simplex.min.css
assets/css/bootstrap-slate.min.css
assets/css/bootstrap-spacelab.min.css
assets/css/bootstrap-united.min.css
assets/css/bootstrap-datetimepicker.css
assets/css/charisma-app.css
assets/css/elfinder.min.css
assets/css/elfinder.theme.css
assets/css/eventCalendar.css
assets/css/eventCalendar_theme.css
assets/css/eventCalendar_theme_responsive.css
assets/css/font-awesome.css
assets/css/font-awesome.min.css
assets/css/ilmudetil.css
assets/css/jquery-ui-1.8.21.custom.css
assets/css/jquery.iphone.toggle.css
assets/css/jquery.noty.css
assets/css/noty_theme_default.css
assets/css/style.css
assets/css/style_.css
assets/css/styles_cetak.css
assets/css/uploadify.css
assets/js/bootstrap-datetimepicker.js
assets/js/charisma.js
assets/js/exporting.js
assets/js/highcharts.js
assets/js/init-chart.js
assets/js/jquery.autogrow-textarea.js
assets/js/jquery.cookie.js
assets/js/jquery.eventCalendar.js
assets/js/jquery.eventCalendar.min.js
assets/js/jquery.history.js
assets/js/jquery.iphone.toggle.js
assets/js/jquery.noty.js
assets/js/jquery.raty.min.js
assets/js/jquery.uploadify-3.1.min.js
assets/js/jquery-1.10.1.min.js
assets/js/main.js
assets/js/moment.js
```

---

## Keep (In Use)

```
assets/css/app.css
assets/css/select2.min.css
assets/js/app.js
assets/js/billing.js
assets/js/cetakan.js
assets/js/form-darah.js
assets/js/laporan.js
assets/js/rawat-inap.js
assets/js/request-detail.js
assets/js/request-edit.js
assets/js/request-list.js
assets/js/riwayat.js
assets/js/select2.min.js
assets/login_style/    # auth/login.php only
assets/logo/           # bld.png, etc.
assets/vendor/         # BS5, DT2, Flatpickr, Select2, SA2
```

---

## Notes

1. **Do NOT delete** `assets/login_style/` — it's the only stylesheet for legacy `auth/login.php`. Consider migrating login to new layout in future phase.

2. **Vendor directory is fully used** — do not touch.

3. **No database changes** required — purely asset cleanup.

4. **Test after deletion** — verify login page still renders, all DataTables/Select2/Flatpickr work.

---

*Generated by Phase UI-12 validation*