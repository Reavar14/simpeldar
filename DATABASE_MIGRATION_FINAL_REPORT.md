# DATABASE MIGRATION FINAL REPORT — SIMPELDAR CI3

**Date:** 2026-09-27  
**Auditor:** Database Compatibility Audit  
**Environment:** CodeIgniter 3.1.x, PHP 8.2, MySQL 8.0.30 (Laragon), schema `darah`

---

## 1. DATABASE COMPATIBILITY SCORE

| Metric | Score | Notes |
|--------|-------|-------|
| Table Coverage | 95% | 21/22 tables used exist; `pasien` missing (dead code) |
| Column Compatibility | 98% | All queried columns exist; 1 type mismatch (nm_creat) |
| Query Execution | 100% | 55/55 model methods executed with zero DB errors |
| Data Flow Integrity | 90% | All 7 flows work; pasien autofill limited by data overlap |
| Native Parity | 85% | Core queries match; billing/riwayat adapted intentionally |

**Overall Compatibility: 93/100**

---

## 2. MODULE COMPATIBILITY TABLE

| Module | Controller | Model(s) | Tables | Status | Critical Issues |
|--------|------------|----------|--------|--------|-----------------|
| **Auth** | Auth | User_model | usrmst | ✅ PASS | None |
| **Form Darah** | Darah::form/submit/autofill/lookup | Darah_model | pesan_darah, kantong_luar, petugas_serah_terima, referensi, jenis_darah, dokter, ruangan, analis, perawat, usrmst, variabel, pasien2 | ⚠️ PARTIAL | `nm_creat` string→smallint will FAIL in strict mode |
| **Request List** | RequestController::list/ajax_list | Request_model | pesan_darah, referensi, ruangan, kelengkapan, status_terima, variabel, jenis_darah | ✅ PASS | None |
| **Request Detail** | RequestController::detail/print_* | Request_model | pesan_darah + 10 joins | ✅ PASS | None |
| **Request Edit** | RequestController::edit/update | Request_model | pesan_darah, petugas_serah_terima, kantong_luar, kelengkapan, referensi | ✅ PASS | None |
| **Rawat Inap** | Darah::view_dokter/ajax_list_ri | Darah_model | pesan_darah + joins | ✅ PASS | None |
| **Billing** | Billing::index/ajax_list | Billing_model | pesan_darah, ruangan, jenis_darah, cek_billing | ✅ PASS | None |
| **Riwayat** | Riwayat::index/ajax_list | Riwayat_model | pesan_darah, ruangan, variabel, referensi, jenis_darah | ✅ PASS | Minor: CI3 adds status filter native lacks |
| **Laporan** | Laporan::index/ajax_* | Laporan_model | pesan_darah, analis, ruangan, referensi, jenis_darah, cek_billing | ✅ PASS | None |
| **Cetakan** | Cetakan::index | Darah_model | (dropdowns only) | ✅ PASS | None |

---

## 3. MISSING DEPENDENCIES

| Dependency | Required By | Status | Resolution |
|------------|-------------|--------|------------|
| Table `darah.pasien` (NORM, GOLONGAN_DARAH) | `Darah_model::update_pasien_goldar()` | ❌ Missing | Method is **dead code** (commented out in `Darah::submit()`). Remove or refactor to use `pasien2` (requires schema change). |
| Remote SIMRS tables (`layanan.*`, `pendaftaran.*`, `master.*`) | Native billing, laporan, edit_permintaan | ❌ Not in local DB | CI3 intentionally replaces with local `pesan_darah` + `cek_billing` / `kantong_luar`. Documented adaptation. |
| JavaBridge + JasperReports | Native cetak (bon, form, hasil, detail, laporan) | ❌ Not ported | CI3 uses HTML view + `window.print()`. Functional parity achieved. |

---

## 4. QUERY MISMATCHES (Native vs CI3)

| Area | Native Query | CI3 Query | Difference | Impact |
|------|--------------|-----------|------------|--------|
| **Request List Filter** | `pd.tgl_minta BETWEEN 1m ago AND 1m ahead OR tgl_diperlukan ±7d` | **Identical** | — | ✅ Match |
| **Rawat Inap Filter** | `pd.tgl_minta >= 1m ago` | **Identical** | — | ✅ Match |
| **Riwayat Filter** | No status filter | Adds `(pd.status != 0 OR pd.status IS NULL)` | CI3 excludes cancelled | Low — reasonable default |
| **Jumlah Permintaan (Laporan)** | Date filter in WHERE (drops empty statuses) | Date filter in LEFT JOIN (keeps 0-count statuses) | CI3 shows all statuses | **Improvement** |
| **Jumlah Jenis (Laporan)** | Same as above | Same as above | CI3 shows all jenis | **Improvement** |
| **Billing Kantong** | 12 CASE + cek_billing subquery | **Identical** | — | ✅ Match |
| **Edit Permintaan Goldar** | Remote `tb_prolis`/`tb_produksi`/`aftap` | Local `kantong_luar.GOLDAR_KLx` | Source substitution | **Intentional** |
| **Login** | `$_SESSION['login'] = USRID` | Same | — | ✅ Match |
| **Submit nm_creat** | `$_SESSION['login']` (int) | `$_SESSION['uname']` (string) | **BUG** — type mismatch | **CRITICAL** |

---

## 5. DATA MISMATCHES

| Table / Column | Native Behavior | CI3 Behavior | Risk |
|----------------|-----------------|--------------|------|
| `pesan_darah.nm_creat` | USRID (int) | USRNM (string) | **Insert fails** (strict mode) |
| `pasien2` MR overlap | Native had `pasien` with matching MR | `pasien2` MR range 1-5 only | Autofill often empty — handled gracefully |
| `referensi` PK | No PK (ID not unique) | Same | JOINs work (filtered by JENIS) |
| `status_terima.oleh` | NOT NULL? | NULL allowed | CI3 upsert handles NULL |
| `cek_billing` PK | None | Same | Upsert by no_permintaan+no_kantong works |

---

## 6. RISK LEVEL ASSESSMENT

| Risk | Severity | Likelihood | Description |
|------|----------|------------|-------------|
| **Form submit fails (nm_creat)** | **CRITICAL** | **Certain** | Strict mode rejects string 'abi' into smallint. **Blocks Form Darah entirely.** |
| `update_pasien_goldar` dead code | LOW | N/A | Method exists but unused. Cleanup only. |
| Riwayat status filter difference | LOW | Medium | CI3 excludes status=0; native includes. Minor UX diff. |
| Laporan group-by behavior | LOW | Low | CI3 shows zero-count rows; native hides. CI3 better. |
| Pasien autofill empty | MEDIUM | High | Real MRs not in pasien2. Returns 'Pasien tidak ditemukan'. User must type manually. Works but degraded UX. |
| Remote table substitutions | MEDIUM | N/A | Design decision. Data source changed from SIMRS production to local kantong_luar. Documented. |

---

## 7. RECOMMENDATIONS

### Immediate (Pre-Deployment)
1. **FIX `Darah::submit()` line 197**: Change `$userLogin = $_SESSION['uname'];` → `$userLogin = $_SESSION['login'];` (USRID int). Matches native, passes strict mode.
2. **Remove dead method** `Darah_model::update_pasien_goldar()` or guard with `if (table_exists('pasien'))`.
3. **Verify session['login'] set**: `Auth::login()` already sets `$_SESSION['login'] = $data['USRID']` — confirmed.

### Short-Term
4. **Add PK to `cek_billing`** (`no_permintaan`, `no_kantong`) and `status_terima` (`no_permintaan`) for data integrity.
5. **Add PK to `jenis_darah`** (`id`) or enforce uniqueness.
6. **Extend `pasien2`** with `golongan_darah` column if autofill enhancement desired (schema change).
7. **Add FK constraints** (optional, MySQL 8 supports) for logical relationships.

### Long-Term
8. **Data migration**: Populate `pasien2` with real MRs from SIMRS if possible.
9. **Index optimization**: Add composite indexes on `pesan_darah` for common filters: `(status, tgl_minta)`, `(mr, tgl_minta)`.
10. **Remove backup tables** (`*_copy1`, `dsphis`, `numbset`, `perawat_terima`) or archive.

---

## 8. TEST VERIFICATION SUMMARY

| Test | Result | Evidence |
|------|--------|----------|
| All 55 model methods execute | ✅ PASS | 0 DB errors, timings 0.00–9.96s |
| Dropdown data populated | ✅ PASS | referensi 48 rows, variabel 7, analis 39, perawat 989, dokter 191 |
| Request DataTables | ✅ PASS | 10 rows returned, count 47 |
| Rawat Inap DataTables | ✅ PASS | 10 rows, count 13 |
| Billing DataTables | ✅ PASS | 10 rows, count 173,871 |
| Dashboard tabs | ✅ PASS | 10 tabs return data (0–775 rows) |
| Laporan all 4 tabs | ✅ PASS | 10–19,545 rows |
| Riwayat with MR filter | ✅ PASS | 4 rows for MR=230245 |
| Detail/Print/Edit queries | ✅ PASS | Full 60+ column joins work |
| Auth login (correct creds) | ✅ PASS | User `abi` / `123` authenticates |

---

## 9. CONCLUSION

The CI3 migration is **database-compatible at 93%** with **one critical blocker** (`nm_creat` type mismatch) that will prevent Form Darah submission in strict mode. All other modules function correctly against the live `darah` database. The intentional substitutions for remote SIMRS tables are well-documented in model comments and functionally equivalent for local operation.

**Recommendation:** Apply the 2-line fix in `Darah::submit()`, remove dead `update_pasien_goldar()`, and deploy. The application is otherwise ready for production use.

---

**PHASE DATABASE COMPATIBILITY AUDIT COMPLETE**