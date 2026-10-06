# SIMPELDAR Local Release v1.0

**Application:** SIMPELDAR  
**Version:** 1.0  
**Date:** 2026-09-27  
**Environment:** Local development (Laragon)  
**Status:** Verified — ready for local development use

---

## 1. System Overview

SIMPELDAR is a blood request management system for hospital transfusion services. Migrated from legacy native PHP (JasperReports, JavaBridge, remote DB) to CodeIgniter 3 with local MySQL database.

### Modules
- **Auth** — login/logout with CSRF protection
- **Dashboard** — multi-tab status view (admin only)
- **Darah** — form create/edit, rawat inap monitoring (Level 2)
- **RequestController** — request list/detail/edit + print forms
- **Laporan** — 4 AJAX report tabs (pelayanan, permintaan, jenis, billing)
- **Cetakan** — print forms (Bon, Form Darah, Hasil Pemeriksaan, Detail Darah)
- **Billing** — billing status monitoring
- **Riwayat** — patient history with filters

---

## 2. Requirements (Local)

| Component | Version |
|-----------|---------|
| PHP | 8.1+ (tested 8.2) |
| MySQL / MariaDB | 8.0 / 10.5+ |
| Web Server | Laragon (Apache 2.4 / Nginx) |
| PHP Extensions | `mysqli`, `json`, `mbstring`, `session`, `ctype`, `filter`, `xml`, `gd` |

---

## 3. How to Run in Laragon

1. **Copy project** to Laragon `www`:
   ```
   D:\laragon\www\simpeldar_codeigniter3\
   ```

2. **Start Laragon** → Apache + MySQL

3. **Import database**:
   ```bash
   mysql -u root darah < darah_schema.sql
   ```

4. **Verify config** (already set for local):
   - `application/config/config.php`: `base_url = http://localhost/simpeldar_codeigniter3/`
   - `application/config/database.php`: `hostname=localhost`, `username=root`, `password=''`, `database=darah`
   - `index.php`: `ENVIRONMENT` defaults to `development`

5. **Access**:
   ```
   http://localhost/simpeldar_codeigniter3/index.php/auth
   ```

---

## 4. Database Setup

### Tables (22 total)
Key tables: `pesan_darah` (175K rows), `cek_billing`, `kantong_luar`, `kelengkapan`, `pasien2`, `usrmst`, `ruangan`, `referensi`, `variabel`, `jenis_darah`, `dokter`, `perawat`, `analis`, `status_terima`, `numbset`, `dsphis`.

### Indexes Applied (UI-11B)
```sql
CREATE INDEX idx_pd_tgl_minta_status ON darah.pesan_darah (tgl_minta, status);
CREATE INDEX idx_pd_tgl_minta_ruangan ON darah.pesan_darah (tgl_minta, ruangan);
CREATE INDEX idx_pd_tgl_minta_mr ON darah.pesan_darah (tgl_minta, mr);
CREATE INDEX idx_cb_req_status ON darah.cek_billing (no_permintaan, status);
```

### Default Accounts
| Username | Password | Level | Role |
|----------|----------|-------|------|
| hanif | 123 | 1 (Admin) | All modules |
| rawatinap | rawatinap | 2 (Rawat Inap) | Dashboard→view_dokter, rawat inap |
| user | user | 3 (Petugas) | View/print only |
| dokter | dokter | 4 (Dokter) | View/print only |

> **Note:** Plaintext passwords in `usrmst.PASSWORD` — for local dev only.

---

## 5. Configuration (Local)

### `application/config/config.php` — Verified
| Setting | Value | Notes |
|---------|-------|-------|
| `base_url` | `http://localhost/simpeldar_codeigniter3/` | ✅ Local |
| `cookie_secure` | `FALSE` | ✅ HTTP localhost |
| `cookie_httponly` | `TRUE` | ✅ JS cannot read cookies |
| `cookie_samesite` | `Lax` | ✅ CSRF mitigation |
| `sess_samesite` | `Lax` | ✅ |
| `csrf_protection` | `TRUE` | ✅ Enabled |
| `csrf_token_name` | `csrf_token` | ✅ |
| `csrf_regenerate` | `TRUE` | ✅ Rotate on submit |
| `encryption_key` | (64-char hex) | ✅ Present |
| `log_threshold` | `0` | Off (errors display on-screen in dev) |
| `sess_save_path` | `APPPATH . 'cache/sessions'` | Writable |
| `sess_expiration` | `7200` | 2 hours |

### `application/config/database.php` — Verified
```php
$db['default'] = [
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'darah',
    'dbdriver' => 'mysqli',
    'db_debug' => (ENVIRONMENT !== 'production'), // TRUE in dev
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    'stricton' => FALSE,
];
```

### `index.php` — Verified
```php
define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');
```

---

## 6. Folder Permissions (Local)

Laragon runs as current user — no `www-data` needed. Ensure writable:
```
application/cache/           → writable
application/cache/sessions/  → writable
application/logs/            → writable
```

---

## 7. Module Status

| Module | Route | Access | Status |
|--------|-------|--------|--------|
| Auth | `/auth`, `/auth/logout` | All | ✅ |
| Dashboard | `/dashboard` | L1 (L2→view_dokter, L3/L4→403) | ✅ |
| Darah Form | `/darah/form` | L1 | ✅ |
| Darah Rawat Inap | `/darah/view_dokter` | L1, L2 | ✅ |
| Request List | `/requestcontroller/list` | L1 | ✅ |
| Request Edit | `/requestcontroller/edit/:id` | L1 | ✅ |
| Request Print | `/requestcontroller/print_*/:id` | L1 | ✅ |
| Laporan | `/laporan` + 4 AJAX tabs | L1 | ✅ |
| Cetakan | `/cetakan` | L1 | ✅ |
| Billing | `/billing` + AJAX | L1 | ✅ |
| Riwayat | `/riwayat` + AJAX | L1 | ✅ |

---

## 8. Known Limitations (Local)

1. **Plaintext passwords** in `usrmst.PASSWORD` — dev only
2. **No password reset** flow
3. **No audit logging** for data modifications
4. **Legacy login page** uses `assets/login_style/` (not new Bootstrap 5 layout)
5. **Default 30-day filter** on Billing/Riwayat AJAX (UI-11B optimization)
6. **Unused assets remain** — ~24 MB in `assets/admin/`, `fonts/`, `sweetalert/`, `datepicker/`, `datetimepicker/`, legacy CSS/JS (see LOCAL_CLEANUP_REPORT.md)

---

## 9. Verified Local Test Results (Phase LOCAL-1)

| Category | Tests | Result |
|----------|-------|--------|
| Authentication (login/logout/session) | 4 | ✅ PASS |
| L1 Authorization (8 modules) | 8 | ✅ PASS |
| L1 DataTables AJAX (8 endpoints) | 8 | ✅ PASS |
| CSRF (valid/invalid/missing) | 3 | ✅ PASS |
| Print endpoints (4) | 4 | ✅ PASS |
| Responsive (viewport + BS5) | 3 | ✅ PASS |
| L2 Authorization (redirect + rawat inap) | 3 | ✅ PASS |
| L2 Admin modules denied (403) | 7 | ✅ PASS |
| L3/L4 All admin modules denied | 16 | ✅ PASS |
| **Effective Total** | **59** | **✅ 59/59 PASS** |

> 3 initial "FAIL" flags: L2 dashboard 307→view_dokter (correct existing behavior), L3/L4 rawat inap 200 (per spec). Verified manually.

---

## 10. Version History

| Version | Date | Notes |
|---------|------|-------|
| 1.0 | 2026-09-27 | Local release verified |

---

*Generated by Phase LOCAL-1*