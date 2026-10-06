# SIMPELDAR Local Cleanup Report

**Date:** 2026-09-27  
**Environment:** Local development (Laragon)  
**Policy:** Report only — **no assets deleted** in this phase

---

## 1. Old / Unused Assets

These are legacy assets from the pre-migration native app. They are **not referenced** by any current view or layout. The active UI uses `assets/vendor/` + `assets/css/app.css` + `assets/js/*.js`.

| Path | Size | Files | Status |
|------|------|-------|--------|
| `assets/admin/` | 17.27 MB | 1,322 | Legacy Charisma Bootstrap 3 admin theme (jQuery 1.x, DataTables 1.x, elFinder, Uploadify, Bootswatch themes, Highcharts, Flot, FullCalendar) |
| `assets/fonts/` | 1.18 MB | 10 | Glyphicons Halflings (Bootstrap 3 font icons) |
| `assets/sweetalert/` | 0.93 MB | 55 | SweetAlert v1 (superseded by SweetAlert2 in `assets/vendor/`) |
| `assets/datepicker/` | 0.14 MB | 6 | Legacy Bootstrap 3 datepicker (superseded by Flatpickr) |
| `assets/datetimepicker/` | 0.47 MB | 6 | Legacy Bootstrap 3 datetimepicker (superseded by Flatpickr) |
| `assets/img/` | 0.06 MB | 22 | Legacy logos / ajax-loaders / iPhone toggle images (logo now from `assets/logo/`) |
| `assets/css/*.css` (29 files) | ~1.3 MB | 29 | Bootstrap 3 core + 7 Bootswatch themes + Noty/jQuery UI/elfinder/eventCalendar/uploadify styles |
| `assets/js/*.js` (20 files) | ~0.7 MB | 20 | jQuery 1.10.1, moment.js, highcharts, charisma.js, uploadify, noty, raty, history, iphone toggle, legacy `main.js` |

**Total unused: ~24 MB (1,489 files)**

---

## 2. Safe Delete Candidates

Verified safe — no view, layout, or active JS references these paths:

```
assets/admin/                                      # 17.27 MB
assets/fonts/                                      # 1.18 MB
assets/sweetalert/                                 # 0.93 MB
assets/datepicker/                                 # 0.14 MB
assets/datetimepicker/                             # 0.47 MB
assets/img/                                        # 0.06 MB
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
assets/js/main.js                                  # legacy (references dead proses.php)
assets/js/moment.js
```

**Note:** `assets/main.js` (root-level, if present) is also dead — login page loads `assets/login_style/js/main.js`, not `assets/js/main.js`.

---

## 3. Currently Used Assets (DO NOT DELETE)

| Path | Size | Purpose |
|------|------|---------|
| `assets/css/app.css` | 52 KB | Main application stylesheet |
| `assets/css/select2.min.css` | 15 KB | Select2 dropdown styles |
| `assets/js/app.js` | 27 KB | Global init: DataTables defaults, CSRF refresh, sidebar, SwalHelper |
| `assets/js/billing.js` | 6 KB | Billing DataTables |
| `assets/js/cetakan.js` | 2 KB | Cetakan filters |
| `assets/js/form-darah.js` | 17 KB | Darah form (autofill MR, kantong lookup) |
| `assets/js/laporan.js` | 16 KB | Laporan tabs + 4 AJAX endpoints |
| `assets/js/rawat-inap.js` | 9 KB | Rawat inap DataTables |
| `assets/js/request-detail.js` | 3 KB | Request detail view |
| `assets/js/request-edit.js` | 8 KB | Request edit form |
| `assets/js/request-list.js` | 10 KB | Request list DataTables |
| `assets/js/riwayat.js` | 7 KB | Riwayat DataTables |
| `assets/js/select2.min.js` | 65 KB | Select2 library |
| `assets/login_style/` | 1.22 MB | Login page (`auth/login.php`) — only stylesheet for login |
| `assets/logo/` | 0.85 MB | `bld.png` and hospital logos used in templates |
| `assets/vendor/` | 1.58 MB | Bootstrap 5.3, DataTables 2.x, Flatpickr, Select2, SweetAlert2, jQuery 3.7 |

---

## 4. Dev Leftovers Scan

Scanned project for: `*.txt`, `*.log`, `*.tmp`, `*.bak`, `*.swp`, `*.orig`, `*.sql`, `cookie*`, `test*`, `debug*`.

**Result: NONE found in application or root.**

All matches reside inside vendor directories (`assets/admin/bower_components/`, `assets/vendor/`) — these are third-party license/example files, **not** dev leftovers.

Previously removed in Phase UI-11: `csrf_test.php`, `test_db.php`, `level_test.php`, `level_debug.php`, `c1–c5.txt`, `cookie.txt`, `perf_cookie.txt`, `test_cookies.txt`, `cleanup_candidate_report.md`.

---

## 5. Recommendation

- **Keep all unused assets** for local development (per LOCAL-1 policy).
- If disk space is needed, delete candidates in §2 — verified zero references.
- After any deletion, re-run regression test (login page + all DataTables pages).

---

*Generated by Phase LOCAL-1*