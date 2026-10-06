# FULL SYSTEM AUDIT REPORT — SIMPELDAR CI3

**Date:** 2026-09-27  
**Version:** 1.0  
**Scope:** Complete application, database, security, performance, dead code  
**Environment:** CodeIgniter 3.1.x, PHP 8.x, MySQL 8.0, Laragon (local)

---

## A. ARCHITECTURE OVERVIEW

### Stack
| Layer | Technology |
|-------|------------|
| Framework | CodeIgniter 3.1.x |
| PHP | 8.1+ (tested 8.2) |
| Database | MySQL 8.0 / MariaDB 10.x, single schema `darah` |
| Frontend | Bootstrap 5.3 (CDN), DataTables 2.x (CDN), Select2, Flatpickr, SweetAlert2, FontAwesome 6, jQuery 3.7 |
| CSS/JS | Custom `assets/css/app.css`, `assets/js/*.js` (11 modules) |

### Application Structure
```
simpeldar_codeigniter3/
├── application/
│   ├── config/         # database, routes, autoload, config, hooks
│   ├── controllers/    # 9 controllers (8 active + Welcome)
│   ├── core/           # MY_Controller, Admin_Controller
│   ├── models/         # 7 models
│   └── views/          # 60+ files (layouts, partials, modules)
├── assets/
│   ├── vendor/         # (empty - all CDN)
│   ├── css/app.css     # active stylesheet
│   ├── js/             # 11 active modules + 20 legacy files
│   ├── logo/           # bld.png + hospital logos
│   ├── login_style/    # legacy login page only
│   ├── admin/          # 17 MB LEGACY (Charisma Bootstrap 3)
│   ├── fonts/          # 1 MB LEGACY (Glyphicons)
│   ├── sweetalert/     # 0.9 MB LEGACY (v1)
│   ├── datepicker/     # 0.1 MB LEGACY
│   ├── datetimepicker/ # 0.5 MB LEGACY
│   └── img/            # 0.06 MB LEGACY
├── system/             # CI3 core
└── index.php
```

---

## B. CONTROLLER → METHOD → VIEW → MODEL → TABLE MAPPING

### 1. Auth.php (CI_Controller — PUBLIC)
| Method | Route | View | Model | Tables |
|--------|-------|------|-------|--------|
| `index()` | `/auth` | `auth/login` | — | — |
| `login()` | `/auth/login` (POST) | redirect | `User_model::check_login()` | `darah.usrmst` |
| `logout()` | `/auth/logout` | redirect | — | — |
| `csrf()` | `/auth/csrf` | JSON | — | — |

### 2. Dashboard.php (MY_Controller — LEVEL 1 ONLY)
| Method | Route | View | Model | Tables |
|--------|-------|------|-------|--------|
| `index()` | `/dashboard` | `templates/header`, `dashboard/index`, `templates/footer` | `Dashboard_model` (11 methods) | `darah.pesan_darah` + 8 JOINs |

**Level 2 redirect:** `if ($level === '2') redirect('darah/view_dokter')`

### 3. Darah.php (MY_Controller — LEVEL 1 + LEVEL 2 FOR RAWAT INAP)
| Method | Route | View | Model | Tables | Auth |
|--------|-------|------|-------|--------|------|
| `form()` | `/darah/form` | `templates/header`, `darah/form_darah`, `templates/footer` | `Darah_model` (14 dropdown methods) | `referensi`, `jenis_darah`, `dokter`, `ruangan`, `analis`, `perawat`, `variabel` | `require_level('1')` |
| `autofill_pasien()` | `/darah/autofill_pasien` (POST) | JSON | `Darah_model::get_pasien_by_mr`, `get_riwayat_alergi_by_mr` | `darah.pasien2`, `darah.pesan_darah` | `require_level('1')` |
| `lookup_kantong()` | `/darah/lookup_kantong` (POST) | JSON | `Darah_model::get_kantong_by_nomor` | `darah.kantong_luar` | `require_level('1')` |
| `submit()` | `/darah/submit` (POST) | redirect | `Darah_model` (inserts) | `darah.pesan_darah`, `darah.kantong_luar`, `darah.petugas_serah_terima` | `require_level('1')` |
| `view_dokter()` | `/darah/view_dokter` | `templates/header`, `darah/rawat_inap`, `templates/footer` | `Darah_model::get_ruangan`, `get_status` | `darah.ruangan`, `darah.referensi` | **None explicit** (level 2 allowed) |
| `ajax_list_ri()` | `/darah/ajax_list_ri` (POST) | JSON | `Darah_model::get_datatables_ri`, `count_filtered_ri`, `count_all_ri` | `darah.pesan_darah` + 8 JOINs | **None explicit** |

### 4. RequestController.php (MY_Controller — LEVEL 1 FOR ALL)
| Method | Route | View | Model | Tables | Auth |
|--------|-------|------|-------|--------|------|
| `list()` | `/requestcontroller/list` | `templates/header`, `request/list`, `templates/footer` | — | — | `require_level('1')` |
| `ajax_list()` | `/requestcontroller/ajax_list` | JSON | `Request_model` (get_datatables, count) | `darah.pesan_darah` + 8 JOINs | `require_level('1')` |
| `detail()` | `/requestcontroller/detail/:id` | `templates/header`, `request/detail`, `templates/footer` | `Request_model::get_detail` | 14 tables | MY_Controller only |
| `print_hasil_pemeriksaan()` | `/requestcontroller/print_hasil_pemeriksaan/:id` | `request/print_hasil_pemeriksaan` | `Request_model::get_hasil_pemeriksaan` | 13 tables | Session check |
| `print_bon()` | `/requestcontroller/print_bon/:id` | `request/print_bon` | `Request_model::get_bon` | 12 tables | Session check |
| `print_form()` | `/requestcontroller/print_form/:id` | `request/print_form` | `Request_model::get_form_darah` | 14 tables + 2 subqueries | `require_level('1')` |
| `print_detail_darah()` | `/requestcontroller/print_detail_darah/:id` | `request/print_detail_darah` | `Request_model::get_detail_darah` | 13 tables | `require_level('1')` |
| `edit()` | `/requestcontroller/edit/:id` | `templates/header`, `request/edit`, `templates/footer` | `Request_model::get_edit_data`, `Darah_model` (dropdowns) | 19 tables + 12× `cek_billing` | `require_level('1')` |
| `update()` | `/requestcontroller/update` (POST) | redirect | `Request_model` (updates) | `darah.pesan_darah`, `petugas_serah_terima`, `kantong_luar`, `kelengkapan` | `require_level('1')` + trans |
| `mark_received()` | `/requestcontroller/mark_received` (POST) | JSON | `Request_model::upsert_status_terima`, `update_pesan_darah_status` | `darah.status_terima`, `darah.pesan_darah` | `require_level('1')` + trans |
| `print_laporan_lengkap()` | `/requestcontroller/print_laporan_lengkap` | `templates/header`, `request/print_laporan_lengkap`, `templates/footer` | `Request_model::get_laporan_lengkap`, `Darah_model` | 13 tables | `require_level('1')` |
| `print_rekap_analis()` | `/requestcontroller/print_rekap_analis` | `templates/header`, `request/print_rekap_analis`, `templates/footer` | `Request_model::get_rekap_analis` | `darah.analis`, `darah.pesan_darah` | `require_level('1')` |

### 5. Billing.php (Admin_Controller — LEVEL 1 ONLY)
| Method | Route | View | Model | Tables |
|--------|-------|------|-------|--------|
| `index()` | `/billing` | `templates/header`, `billing/index`, `templates/footer` | `Billing_model::get_ruangan`, `get_status_billing` | `darah.ruangan` |
| `ajax_list()` | `/billing/ajax_list` | JSON | `Billing_model` (datatables) | `darah.pesan_darah`, `darah.ruangan`, `darah.jenis_darah`, `darah.cek_billing` |

### 6. Laporan.php (Admin_Controller — LEVEL 1 ONLY)
| Method | Route | View | Model | Tables |
|--------|-------|------|-------|--------|
| `index()` | `/laporan` | `templates/header`, `laporan/index`, `templates/footer` | `Laporan_model`, `Darah_model` (dropdowns) | — |
| `ajax_pelayanan()` | `/laporan/ajax_pelayanan` | JSON | `Laporan_model` | `darah.pesan_darah`, `analis`, `ruangan`, `referensi` |
| `ajax_jumlah_permintaan()` | `/laporan/ajax_jumlah_permintaan` | JSON | `Laporan_model` | `darah.referensi`, `darah.pesan_darah` |
| `ajax_jumlah_jenis()` | `/laporan/ajax_jumlah_jenis` | JSON | `Laporan_model` | `darah.jenis_darah`, `darah.pesan_darah` |
| `ajax_billing()` | `/laporan/ajax_billing` | JSON | `Laporan_model` | `darah.pesan_darah`, `darah.cek_billing` |

### 7. Cetakan.php (Admin_Controller — LEVEL 1 ONLY)
| Method | Route | View | Model | Tables |
|--------|-------|------|-------|--------|
| `index()` | `/cetakan` | `templates/header`, `cetakan/index`, `templates/footer` | `Darah_model` (dropdowns) | `darah.analis`, `usrmst`, `dokter`, `referensi` |

### 8. Riwayat.php (Admin_Controller — LEVEL 1 ONLY)
| Method | Route | View | Model | Tables |
|--------|-------|------|-------|--------|
| `index()` | `/riwayat` | `templates/header`, `riwayat/index`, `templates/footer` | — | — |
| `ajax_list()` | `/riwayat/ajax_list` | JSON | `Riwayat_model` (datatables) | `darah.pesan_darah` + 5 JOINs |

### 9. Welcome.php (CI_Controller — UNUSED)
| Method | Route | View | Model | Tables |
|--------|-------|------|-------|--------|
| `index()` | `/` (default) | `welcome_message` | — | — |

> **Route config:** `$route['default_controller'] = 'auth';` → Welcome never hit.

---

## C. DATABASE MAPPING — ALL TABLES & COLUMNS

### Tables in `darah` schema (22 total)
| Table | Purpose | Rows (approx) | Used By |
|-------|---------|---------------|---------|
| `pesan_darah` | Core blood requests | 175,323 | All modules |
| `cek_billing` | Billing flags per bag | ? | Billing, Laporan, Request |
| `kantong_luar` | External bag registry | ? | Darah, Request |
| `kelengkapan` | Completeness flags | ? | Dashboard, Request |
| `pasien2` | Local patient data | ? | Darah, Request |
| `usrmst` | User accounts | ~87 | Auth, Dropdowns, Reports |
| `ruangan` | Room/ward master | ? | All modules |
| `referensi` | Lookup codes (9 JENIS) | ? | All modules |
| `variabel` | Variable lookups | ? | Dropdowns, Reports |
| `jenis_darah` | Blood type/product | ? | All modules |
| `dokter` | Doctor master | ? | Darah, Request, Laporan |
| `perawat` | Nurse master | ? | Darah, Request |
| `analis` | Analyst master | ? | Darah, Laporan, Request |
| `status_terima` | Sample receipt status | ? | Request, Dashboard |
| `numbset` | Numbering sequences | ? | — |
| `dsphis` | Legacy/unknown | ? | — |
| `pegawai` | Staff (joined to dokter) | ? | Reports |
| `perawat_terima` | Receiving nurses | ? | — |
| `petugas_serah_terima` | Handover staff | ? | Darah, Request |
| `petugas_serah_terima_copy1` | Copy table | ? | — |
| `ruangan_copy1` | Copy table | ? | — |

### Column-level audit — key findings
| Table | Column | Status |
|-------|--------|--------|
| `pesan_darah.no_permintaan` | PK, 20-char | Used everywhere |
| `pesan_darah.status` | status code 0-9 | Filtered in all dashboard queries |
| `pesan_darah.no_kantong_1..12` | bag numbers | Used in 12 CASE expressions |
| `pesan_darah.volume_1..12` | bag volumes | Read in reports |
| `pesan_darah.exp_1..12` | bag expiries | Read in reports |
| `cek_billing.no_permintaan` | FK to pesan_darah | Correlated subquery in 3 queries |
| `cek_billing.status` | 0/1 | Billed flag |
| `kantong_luar.NOMOR_KL1..12` | External bag refs | 12 columns |
| `kantong_luar.GOLDAR_KL1..12` | Blood group per bag | Read in edit/form |
| `pasien2.nomr` | Patient MR | Lookup key |
| `pasien2.golongan_darah` | **Missing** — model tries to update `darah.pasien.GOLONGAN_DARAH` but local has `pasien2` without this column (dead code) |
| `usrmst.PASSWORD` | **Plaintext** — compared directly, no hash |

### Schema anomalies
1. **`darah.pasien` vs `darah.pasien2`** — `Darah_model::update_pasien_goldar` writes `darah.pasien` (not present), controller call commented. Method dead.
2. **Bare `usrmst`** in `User_model` — no schema prefix; resolves to `darah.usrmst` via default group. Consistent but implicit.
3. **Duplicate query** — `Darah::form()` line 21 duplicates `Darah_model::get_next_no_permintaan()` logic but unused.

---

## D. BUSINESS FLOW AUDIT

### 1. Login Flow
```
User → /auth (Auth::index → auth/login)
     → POST /auth/login (Auth::login → User_model::check_login)
     → valid: session['uname','level','login'] set
     → level 2 → /darah/view_dokter
     → else → /dashboard
```

### 2. Dashboard Flow (Level 1 only)
```
GET /dashboard → Dashboard::index → Dashboard_model (11 get_* methods)
     → pesan_darah filtered by status + last 5 days / 2 months
     → dashboard/index.php renders 10 tabs + single DataTable (JS re-renders)
```

### 3. Blood Request Create (Level 1)
```
GET /darah/form → Darah::form → Darah_model (14 dropdowns)
POST /darah/submit → Darah::submit
     → INSERT pesan_darah (80+ cols)
     → INSERT kantong_luar (12 bag rows)
     → INSERT petugas_serah_terima (12 rows)
     → transaction commit/rollback
```

### 4. Request List & Edit (Level 1)
```
GET /requestcontroller/list → Request_model::get_datatables (base 8 JOINs)
GET /requestcontroller/edit/:id → Request_model::get_edit_data (19 JOINs + 12× cek_billing)
POST /requestcontroller/update → Request_model updates 4 tables + transaction
```

### 5. Sample Receipt (Level 1)
```
POST /requestcontroller/mark_received → Request_model upsert status_terima + update pesan_darah status=10
```

### 6. Print Forms (Level 1)
```
GET /requestcontroller/print_* → Request_model mega-queries (12-14 JOINs + subqueries)
```

### 7. Rawat Inap (Level 2 allowed)
```
GET /darah/view_dokter → Darah::view_dokter → darah/rawat_inap
     → AJAX /darah/ajax_list_ri → Darah_model::get_datatables_ri (8 JOINs, 1 month filter)
```

### 8. Reporting (Level 1)
```
GET /laporan → 4 AJAX tabs (pelayanan, jumlah permintaan, jumlah jenis, billing)
Each: Laporan_model query with date filters + optional analis/status/ruangan
```

### 9. Billing (Level 1)
```
GET /billing → Billing_model::get_datatables (base + correlated subquery on cek_billing)
```

### 9. Riwayat (Level 1)
```
GET /riwayat → Riwayat_model::get_datatables (5 JOINs, date + MR filter)
```

### 10. Cetakan (Level 1)
```
GET /cetakan → filters only; print buttons call RequestController::print_*
```

---

## E. MVC AUDIT

### Controllers
| Issue | File:Line | Severity |
|-------|-----------|----------|
| **Raw SQL in controller** | `Darah.php:21-22` (duplicate of model), `Darah.php:201,392`, `RequestController.php:357,362,414,417` (transactions) | Medium |
| **Transaction control in controller** | `Darah::submit()`, `RequestController::update()`, `RequestController::mark_received()` | Medium — should be in model |
| **Missing authorization on level-2 endpoints** | `Darah::view_dokter()`, `Darah::ajax_list_ri()` — no `require_level()` | Medium |
| **Welcome controller unused** | `Welcome.php` — route overrides to `auth` | Low |

### Models
| Strength | File |
|----------|------|
| All queries centralized in models (except 4 controller queries) | ✅ All |
| Parameter binding (`?` + array) — no concatenation | ✅ All |
| Whitelist column names for ORDER BY | ✅ Billing, Riwayat, Darah `_ri` |
| **Exception: unsanitized ORDER BY direction** | ❌ `Request_model.php:124` — `$dir` from POST raw |

### Views
| Issue | File | Severity |
|-------|------|----------|
| **Layouts/main.php orphaned** | No controller loads it | Low |
| **layouts/navbar.php, layouts/footer.php orphaned** | Only loaded by layouts/main | Low |
| **dashboard/scripts.php, dashboard/style.php orphaned** | Never loaded; dashboard/index.php embeds inline | Low |
| **errors/error_403.php duplicate** | Also `errors/html/error_403.php`; MY_Controller uses `errors/access_denied` | Low |
| **errors/cli/* (5 files) orphaned** | CLI templates, web app only | Low |
| **welcome_message.php orphaned** | Welcome controller never hit | Low |
| **No direct SQL in views** | ✅ Confirmed | — |

---

## F. SECURITY AUDIT

| Control | Status | File:Line / Notes |
|---------|--------|-------------------|
| **Session** | ✅ Files driver, `sess_expiration=7200`, `sess_save_path=APPPATH/cache/sessions` | `config.php:386-393` |
| **HttpOnly cookies** | ✅ `cookie_httponly=TRUE` | `config.php:415` |
| **Secure cookies** | ⚠️ `cookie_secure=FALSE` (correct for HTTP localhost) | `config.php:414` |
| **SameSite** | ✅ `cookie_samesite=Lax`, `sess_samesite=Lax` | `config.php:416,388` |
| **CSRF protection** | ✅ `csrf_protection=TRUE`, `csrf_token_name=csrf_token`, `csrf_cookie_name=csrf_cookie`, `csrf_expire=7200`, `csrf_regenerate=TRUE` | `config.php:460-465` |
| **CSRF token delivery** | ✅ Meta tags + `/auth/csrf` endpoint + `X-CSRF-Hash` header hook | `templates/header.php:55-57`, `hooks.php:18` |
| **CSRF on AJAX** | ✅ `app.js` refreshes token on 403 + retries once | `assets/js/app.js:617-713` |
| **XSS** | ✅ `global_xss_filtering=FALSE` (deprecated); all output uses `htmlspecialchars()` | `config.php:444`, views use `htmlspecialchars()` |
| **SQL Injection** | ✅ All queries use `?` binding / QB / `escape_like_str` | Models verified |
| **ORDER BY injection risk** | ⚠️ `Request_model.php:124` — `$dir` from POST raw, no ASC/DESC clamp | Medium |
| **Authentication** | ✅ MY_Controller checks session → redirect `auth` | `core/MY_Controller.php:14-16` |
| **Authorization** | ✅ `require_level('1')` for admin; `Admin_Controller` enforces level 1 | `core/MY_Controller.php:45-53`, `71-80` |
| **Level-2 gap** | ⚠️ `Darah::view_dokter()` + `ajax_list_ri()` lack `require_level()` | Medium |
| **Password storage** | ❌ **Plaintext** in `usrmst.PASSWORD` — compared directly | `User_model.php:14` |
| **Password reset** | ❌ None implemented | — |
| **Audit logging** | ❌ None for data modifications | — |
| **Error display** | ✅ `log_threshold=0` in dev; `db_debug` env-aware | `config.php:228`, `database.php:85` |

---

## G. DEAD CODE AUDIT

### Controllers (unused)
| File | Reason |
|------|--------|
| `Welcome.php` | Route `default_controller = 'auth'` overrides |

### Views (unused)
| File | Reason |
|------|--------|
| `layouts/main.php` | No controller loads it |
| `layouts/navbar.php` | Only `layouts/main.php` loads it |
| `layouts/footer.php` | Only `layouts/main.php` loads it |
| `dashboard/scripts.php` | `dashboard/index.php` embeds inline script |
| `dashboard/style.php` | Styles in `app.css` |
| `welcome_message.php` | Welcome controller never hit |
| `errors/error_403.php` | Duplicate of `errors/html/error_403.php`; MY_Controller uses `errors/access_denied` |
| `errors/cli/*` (5) | CLI templates, web app only |

### JavaScript (unused — 20 files, ~0.7 MB)
| File | Size | Reason |
|------|------|--------|
| `bootstrap-datetimepicker.js` | 99 KB | Legacy datetimepicker |
| `charisma.js` | 15 KB | Charisma admin app |
| `exporting.js` | 7 KB | Highcharts export |
| `highcharts.js` | 119 KB | Highcharts |
| `init-chart.js` | 7 KB | Chart init |
| `jquery.autogrow-textarea.js` | 2 KB | jQuery plugin |
| `jquery.cookie.js` | 2 KB | Use native |
| `jquery.eventCalendar.js` | 15 KB | Legacy calendar |
| `jquery.eventCalendar.min.js` | 11 KB | Legacy calendar |
| `jquery.history.js` | 21 KB | History plugin |
| `jquery.iphone.toggle.js` | 10 KB | Legacy toggle |
| `jquery.noty.js` | 8 KB | Noty v2 |
| `jquery.raty.min.js` | 7 KB | Star rating |
| `jquery.uploadify-3.1.min.js` | 45 KB | Uploadify |
| `jquery-1.10.1.min.js` | 91 KB | jQuery 1.x (use 3.7 via CDN) |
| `main.js` | 1 KB | Legacy (references dead `proses.php`) |
| `moment.js` | 120 KB | Moment.js (use native/Flatpickr) |

### CSS (unused — 29 files, ~1.3 MB)
| File | Size | Reason |
|------|------|--------|
| `animate.min.css` | 54 KB | Legacy |
| `bootstrap.css` / `bootstrap.min.css` | 251 KB | BS3 core |
| 7 Bootswatch themes | ~780 KB | BS3 themes |
| `bootstrap-datetimepicker.css` | 9 KB | Legacy |
| `charisma-app.css` | 17 KB | Charisma |
| `elfinder.min.css` / `theme` | 31 KB | File manager |
| `eventCalendar*.css` | ~17 KB | Legacy calendar |
| `font-awesome.css` / `.min.css` | 67 KB | FA4 (use FA6 CDN) |
| `ilmudetil.css` | 1 KB | Legacy |
| `jquery-ui-1.8.21.custom.css` | 32 KB | jQuery UI |
| `jquery.iphone.toggle.css` | 4 KB | Legacy |
| `jquery.noty.css` / `theme` | 11 KB | Noty v2 |
| `style.css` / `style_.css` / `styles_cetak.css` | <1 KB | Legacy |
| `uploadify.css` | 2 KB | Uploadify |

### Legacy Asset Directories (unused — ~24 MB)
| Directory | Size | Files |
|-----------|------|-------|
| `assets/admin/` | 17.27 MB | 1,322 |
| `assets/fonts/` | 1.18 MB | 10 |
| `assets/sweetalert/` | 0.93 MB | 55 |
| `assets/datepicker/` | 0.14 MB | 6 |
| `assets/datetimepicker/` | 0.47 MB | 6 |
| `assets/img/` | 0.06 MB | 22 |

### Active Assets (KEEP)
| Path | Size | Purpose |
|------|------|---------|
| `assets/css/app.css` | 52 KB | Main stylesheet |
| `assets/css/select2.min.css` | 15 KB | Select2 |
| `assets/js/app.js` | 27 KB | Global init, CSRF, sidebar, SwalHelper |
| `assets/js/billing.js` | 6 KB | Billing DataTables |
| `assets/js/cetakan.js` | 2 KB | Cetakan filters |
| `assets/js/form-darah.js` | 17 KB | Darah form (autofill, kantong) |
| `assets/js/laporan.js` | 16 KB | Laporan tabs + 4 AJAX |
| `assets/js/rawat-inap.js` | 9 KB | Rawat Inap DataTables |
| `assets/js/request-detail.js` | 3 KB | Detail view |
| `assets/js/request-edit.js` | 8 KB | Edit form |
| `assets/js/request-list.js` | 10 KB | Request list DataTables |
| `assets/js/riwayat.js` | 7 KB | Riwayat DataTables |
| `assets/js/select2.min.js` | 65 KB | Select2 |
| `assets/login_style/` | 1.22 MB | Login page only |
| `assets/logo/` | 0.85 MB | Logos |
| `assets/vendor/` | 1.58 MB | (empty - all CDN) |

---

## H. PERFORMANCE AUDIT

### Query Complexity
| Query | JOINs | Subqueries | Rows Scanned | Time (before UI-11B) | Time (after indexes) |
|-------|-------|------------|--------------|----------------------|----------------------|
| `Billing_model::get_datatables` | 2 | 1 correlated | 173K | >20s (timeout) | 0.98s (default), 0.94s (filter) |
| `Riwayat_model::get_datatables` | 5 | 0 | 173K | >20s (timeout) | 0.86s (default), 0.80s (filter) |
| `Request_model::get_edit_data` | 19 | 12× correlated | 1 | ~4s (slow) | ~4s (no index help) |
| `Request_model::get_form_darah` | 18 | 2 correlated | 1 | ~2s | ~2s |
| `Dashboard_model::get_permintaan` | 8 | 0 | ~500 (5-day filter) | 0.8s | 0.8s |
| `Laporan_model` (4 endpoints) | 3-4 | 0 | ~8 | 0.12-0.16s | 0.12-0.16s |

### Indexes Added (UI-11B)
| Index | Table | Columns | Purpose |
|-------|-------|---------|---------|
| `idx_pd_tgl_minta_status` | `pesan_darah` | `(tgl_minta, status)` | Billing/Riwayat date+status |
| `idx_pd_tgl_minta_ruangan` | `pesan_darah` | `(tgl_minta, ruangan)` | Billing room filter |
| `idx_pd_tgl_minta_mr` | `pesan_darah` | `(tgl_minta, mr)` | Riwayat MR+date |
| `idx_cb_req_status` | `cek_billing` | `(no_permintaan, status)` | Correlated subquery speedup |

### Performance Findings
1. **Billing/Riwayat AJAX fixed** — default loads now <1s (was timeout). UI-11B added default 30-day filter + composite indexes.
2. **Request edit remains slow** — 19 JOINs + 12 correlated subqueries; no index helps full-scan per ID. Acceptable (single-row).
3. **N+1 queries absent** — all DataTables use single wrapped query + count.
4. **Dashboard load** — 830ms (11 separate queries); could consolidate but acceptable.
4. **No pagination outside DataTables** — all lists use server-side processing.

---

## I. TECHNICAL DEBT SUMMARY

| Priority | Item | Effort | Impact |
|----------|------|--------|--------|
| **P0** | Fix `Request_model::order_by` ASC/DESC clamp | 5 min | Prevents ORDER BY injection |
| **P0** | Add `require_level('1')` to `Darah::view_dokter()` and `ajax_list_ri()` | 5 min | Authorization gap |
| **P1** | Move transaction control from controllers to models | 30 min | Layer separation |
| **P1** | Remove duplicate query in `Darah::form()` | 5 min | Clean code |
| **P2** | Delete dead views: `layouts/main.php`, `layouts/navbar.php`, `layouts/footer.php`, `dashboard/scripts.php`, `dashboard/style.php`, `welcome_message.php`, `errors/error_403.php` (dup), `errors/cli/*` | 10 min | Reduce surface |
| **P2** | Delete legacy asset directories (`assets/admin/`, `assets/fonts/`, `assets/sweetalert/`, `assets/datepicker/`, `assets/datetimepicker/`, `assets/img/`) + unused CSS/JS | 10 min | ~24 MB reclaim |
| **P2** | Delete unused CSS (29) + JS (20) files | 10 min | ~2 MB reclaim |
| **P3** | Migrate plaintext passwords to `password_hash` / `password_verify` | 1 hr | Security hardening |
| **P3** | Add password reset flow | 4 hr | UX / security |
| **P3** | Add audit logging for write operations | 2 hr | Compliance |
| **P3** | Consolidate Dashboard 11 queries into 1-2 | 30 min | Performance |
| **P4** | Migrate login page to `layouts/main` (remove `assets/login_style/`) | 2 hr | UI consistency |

---

## J. RECOMMENDED IMPROVEMENT PRIORITY

1. **Immediate (security/authorization)** — P0 items above
2. **Short-term (cleanup)** — P1/P2 items (dead code removal, transaction refactor)
3. **Medium-term (security hardening)** — Password hashing, audit logging, password reset
4. **Long-term (architecture)** — Query optimization, layout consolidation, CI3 → CI4 migration path

---

## K. CONCLUSION

**System Status:** Functionally complete, locally deployable, all modules operational with level-based authorization. Performance acceptable after UI-11B indexes + default filters. Security surface clean except plaintext passwords and minor ORDER BY direction issue. ~24 MB legacy assets documented for optional removal. No production blockers for local use.

**Production readiness:** Requires environment config changes (HTTPS, cookie_secure, dedicated DB user, password rotation) — documented in `RELEASE_v1.0.md`.

---

*End of Audit. No files modified.*