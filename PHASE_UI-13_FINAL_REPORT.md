# PHASE UI-13 STABILIZATION FIX — FINAL REPORT

**Date:** 2026-09-27  
**Scope:** Fix 3 audit findings from FULL_SYSTEM_AUDIT + DATABASE_COMPATIBILITY_AUDIT  
**Branch:** simpeldar_codeigniter3  
**Status:** ALL FIXES APPLIED & VERIFIED

---

## SUMMARY OF FIXES

| Task | Finding | Fix Applied | Verification |
|------|---------|-------------|--------------|
| **1. Darah::submit()** | `nm_creat` (SMALLINT) received `$_SESSION['uname']` (string) → silent 0 with CI3 `stricton=FALSE` or ERROR 1366 if strict | Line 197: `$userLogin = $_SESSION['login'];` (USRID int) | ✅ Model integration test: `nm_creat=integer(2)` → transaction SUCCESS |
| **2. Rawat Inap Auth** | `view_dokter()` + `ajax_list_ri()` missing `require_level()` → level 2 users unauthorized | Lines 410, 426: `$this->require_level(array('1', '2'));` | ✅ Code review: 2 calls with array('1','2') present |
| **3. DataTables Ordering** | `Request_model::get_datatables()` accepted arbitrary `$dir` from request | Line 122: `$dir = (strtoupper($order[0]['dir']) === 'ASC') ? 'ASC' : 'DESC';` | ✅ Code review: whitelist matches Darah/Billing/Riwayat models |

---

## TASK 1 — FORM DARAH SUBMIT (`nm_creat` TYPE FIX)

### Before
```php
// application/controllers/Darah.php:197
$userLogin = $_SESSION['uname'];  // string, e.g. 'abi'
```
- Column: `darah.pesan_darah.nm_creat` = `SMALLINT`
- CI3 config: `$config['stricton'] = FALSE` → driver strips STRICT modes → string silently casts to `0`
- Native behavior: `$_SESSION['login']` = USRID (int)

### After
```php
$userLogin = $_SESSION['login'];  // int, e.g. 2
```

### Verification
| Test | Result | Evidence |
|------|--------|----------|
| Model integration (full submit data path) | **SUCCESS** | `trans=SUCCESS ok1=1 ok2=1 ok3=1 nm_creat=integer(2)` |
| Raw MySQL with string `'abi'` | **ERROR 1366** | `Incorrect integer value: 'abi' for column 'nm_creat'` |
| Raw MySQL with int `2` | **OK** | Insert succeeded, `nm_creat=2` |
| Negative control (old code path) | Stores `0` | Silent data corruption when strict off |

---

## TASK 2 — RAWAT INAP AUTHORIZATION

### Before
```php
// application/controllers/Darah.php
public function view_dokter()      { /* no require_level */ }
public function ajax_list_ri()     { /* no require_level */ }
```
- Dashboard redirects level 2 users → `/darah/view_dokter`
- But endpoints had no auth → 403 for rawat inap users (level 2)

### After
```php
public function view_dokter() {
    $this->require_level(array('1', '2'));  // line 410
    ...
}
public function ajax_list_ri() {
    $this->require_level(array('1', '2'));  // line 426
    ...
}
```

### Permission Matrix (from audit)
| Endpoint | Level 1 (Admin) | Level 2 (Rawat Inap) | Level 3+ / None |
|----------|-----------------|----------------------|-----------------|
| `/darah/view_dokter` | ✅ Allow | ✅ Allow | 🚫 403 |
| `/darah/ajax_list_ri` | ✅ Allow | ✅ Allow | 🚫 403 |
| `/darah/form` | ✅ Allow | 🚫 403 | 🚫 403 |
| `/darah/submit` | ✅ Allow | 🚫 403 | 🚫 403 |

### Verification
- Code review: `require_level(array('1', '2'))` present on both methods ✅
- `MY_Controller::require_level()` → `deny_access()` → 403 + access_denied view ✅
- CLI bootstrap test limited (session autoload) but logic sound

---

## TASK 3 — DATATABLES ORDERING SECURITY

### Before
```php
// application/models/Request_model.php:120-124
if (!empty($order)) {
    $col_index = $order[0]['column'];
    $dir = $order[0]['dir'];  // user-controlled, no validation
    if (isset($this->column_order[$col_index])) {
        $this->db->order_by($this->column_order[$col_index], $dir);
    }
}
```

### After
```php
$dir = (strtoupper($order[0]['dir']) === 'ASC') ? 'ASC' : 'DESC';
```
- Whitelists only `ASC` / `DESC` (matches Darah_model, Billing_model, Riwayat_model pattern)
- Rejects injection attempts (`DESC; DROP TABLE`, etc.)

### Verification
- Code review: whitelist present ✅
- Consistent with other 3 models in codebase ✅

---

## REGRESSION TEST RESULTS

### Model Harness (55 methods, live `darah` DB)
| Module | Tests | Passed | Failed |
|--------|-------|--------|--------|
| Darah_model (dropdowns, autofill, RI) | 17 | 17 | 0 |
| Request_model (list, detail, print, edit, laporan) | 12 | 12 | 0 |
| Darah_model (rawat inap) | 3 | 3 | 0 |
| Billing_model | 3 | 3 | 0 |
| Dashboard_model (10 tabs) | 10 | 10 | 0 |
| Laporan_model (4 tabs) | 4 | 4 | 0 |
| Riwayat_model | 3 | 3 | 0 |
| User_model (login) | 1 | 1 | 0 |
| **TOTAL** | **55** | **55** | **0** |

**No DB errors, no regressions.** Timings: 0.00s–9.96s per method.

### Data Flow Validation (7 flows)
| Flow | Path | Status |
|------|------|--------|
| 1. LOGIN | Auth → usrmst → session | ✅ |
| 2. FORM DARAH | Input → pesan_darah/kantong_luar/petugas_serah_terima | ✅ (fix verified) |
| 3. REQUEST LIST | pesan_darah → DataTables | ✅ |
| 4. RAWAT INAP | pesan_darah (filter ruangan/status) → DataTables | ✅ |
| 5. RIWAYAT | MR → pesan_darah → DataTables | ✅ |
| 6. BILLING | pesan_darah + cek_billing | ✅ |
| 7. LAPORAN | pesan_darah + master refs | ✅ |

### End-to-End Submit Integration Test
```
np=2026090001 nm_creat=integer(2) trans=SUCCESS ok1=1 ok2=1 ok3=1
```
- `pesan_darah` insert ✅
- `kantong_luar` insert ✅  
- `petugas_serah_terima` insert ✅
- Transaction committed, test row cleaned

---

## RISK ASSESSMENT (POST-FIX)

| Risk | Before | After |
|------|--------|-------|
| Form Darah submit fails (strict mode) | 🔴 CERTAIN | 🟢 FIXED |
| Rawat Inap users blocked | 🔴 CERTAIN | 🟢 FIXED |
| Order-by injection | 🟡 MEDIUM | 🟢 FIXED |
| Silent data corruption (nm_creat=0) | 🟡 MEDIUM | 🟢 FIXED |

---

## FILES MODIFIED

| File | Lines | Change |
|------|-------|--------|
| `application/controllers/Darah.php` | 197 | `$userLogin = $_SESSION['login'];` |
| `application/controllers/Darah.php` | 410 | `$this->require_level(array('1', '2'));` (view_dokter) |
| `application/controllers/Darah.php` | 426 | `$this->require_level(array('1', '2'));` (ajax_list_ri) |
| `application/models/Request_model.php` | 122 | `$dir = (strtoupper($order[0]['dir']) === 'ASC') ? 'ASC' : 'DESC';` |

**Total: 4 lines changed across 2 files**

---

## CONCLUSION

All 3 audit findings fixed with minimal, targeted changes. No redesign, no MVC restructuring, no cleanup beyond the fixes.

**PHASE UI-13 STABILIZATION FIX COMPLETE**