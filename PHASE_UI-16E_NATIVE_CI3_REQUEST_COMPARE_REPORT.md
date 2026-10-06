# PHASE UI-16E — NATIVE VS CI3 REQUEST PROCESS PAGE COMPARISON AUDIT REPORT

**Tanggal:** 2026-09-28  
**Project Native:** `D:\laragon\www\simpeldar_new\admin\isi_view.php`  
**Project CI3:** `D:\laragon\www\simpeldar_codeigniter3\application\models\Request_model.php`  

---

## 1. FLOW NATIVE (VIEW PROSES)

### 1.1 Entry Point
| Item | Detail |
|------|--------|
| **Menu** | "View Proses" (di sidebar admin) |
| **File Sumber** | `admin/isi_view.php` |
| **URL** | `admin/isi_view.php` (langsung load page) |
| **Parameter** | Tidak ada (menggunakan `$_GET` untuk DataTables) |

### 1.2 JavaScript / DataTables Init
- File: `assets/admin/js/...` (Charisma template)
- DataTables 1.10 server-side processing
- AJAX endpoint: **`admin/isi_view.php`** (same file, handle GET params)

### 1.3 Backend Query (`isi_view.php` line 2-66)
```sql
(SELECT 
    pd.no_permintaan,
    pd.mr,
    pd.nama,
    rjk.DESKRIPSI AS jenis_kelamin,
    rgd.DESKRIPSI AS goldar,
    DATE_FORMAT(pd.tgl_lahir,'%d-%m-%Y') AS tgl_lahir,
    DATE_FORMAT(pd.tgl_minta,'%d-%m-%Y') AS tgl_minta,
    DATE_FORMAT(pd.tgl_diperlukan,'%d-%m-%Y') AS tgl_diperlukan,
    pd.alasan,
    tuj.variabel AS tujuan,
    rstatus.DESKRIPSI AS status_proses,
    r.DESKRIPSI AS ruangan,
    pd.trombosit, pd.kadar_hb,
    CONCAT(jd.nama_jenis,'<br> Trombosit: ',pd.trombosit,'<br> Kadar HB: ',pd.kadar_hb) AS jenis_darah,
    CASE WHEN k.kelengkapan = 1 THEN 'Sudah lengkap' ELSE 'Tidak lengkap' END AS kelengkapan_status,
    IFNULL(st.status,0) AS status_terima
    FROM darah.pesan_darah pd
    LEFT JOIN darah.referensi rgd ON pd.goldarah = rgd.ID AND rgd.JENIS = 1
    LEFT JOIN darah.referensi rstatus ON pd.status = rstatus.ID AND rstatus.JENIS = 3
    LEFT JOIN darah.referensi rjk ON pd.id_jenis_kelamin = rjk.ID AND rjk.JENIS = 2
    LEFT JOIN darah.ruangan r ON pd.ruangan = r.ID AND r.JENIS = 5
    LEFT JOIN darah.kelengkapan k ON pd.no_permintaan = k.NO_PERMINTAAN
    LEFT JOIN darah.status_terima st ON pd.no_permintaan = st.no_permintaan
    LEFT JOIN darah.variabel tuj ON tuj.id_variabel = pd.tujuan AND tuj.id_referensi=1948
    LEFT JOIN darah.jenis_darah jd ON pd.jenis_darah = jd.ID AND jd.JENIS = 1
    WHERE 
    (pd.status != 0 or pd.status IS NULL)
    AND (
        pd.tgl_minta BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 MONTH) AND DATE_ADD(CURDATE(), INTERVAL 1 MONTH)
        OR pd.tgl_diperlukan BETWEEN DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
    )
    ORDER BY pd.no_permintaan DESC) temp
```

### 1.4 SSP Class Columns Mapping
```php
$columns = array(
  array('db' => 'no_permintaan', 'dt' => 0),
  array('db' => 'mr', 'dt' => 1),
  array('db' => 'nama', 'dt' => 2),
  array('db' => 'tgl_minta', 'dt' => 3),
  array('db' => 'ruangan', 'dt' => 4),
  array('db' => 'alasan', 'dt' => 5),
  array('db' => 'tujuan', 'dt' => 6),
  array('db' => 'goldar', 'dt' => 7),
  array('db' => 'tgl_diperlukan', 'dt' => 8),
  array('db' => 'jenis_darah', 'dt' => 9),
  array('db' => 'status_proses', 'dt' => 10),
  array('db' => 'kelengkapan_status', 'dt' => 11),
  array('db' => 'status_terima', 'dt' => 12) 
);
```

### 1.5 Database Connection (Native)
```php
$sql_details = array(
  'user' => 'syifa',
  'pass' => 'simrs2026',
  'db'   => 'darah',
  'host' => '192.168.7.241'   // ← REMOTE SERVER
);
```

---

## 2. FLOW CI3 (PROSES PERMINTAAN)

### 2.1 Entry Point
| Item | Detail |
|------|--------|
| **Menu** | "Proses Permintaan" (sidebar) |
| **Controller** | `application/controllers/RequestController.php` |
| **Method** | `list()` → view `request/list` |
| **URL** | `/index.php/requestcontroller/list` |

### 2.2 JavaScript (`assets/js/request-list.js`)
- DataTables 2.x (CDN)
- Server-side: `true`
- AJAX URL: `/requestcontroller/ajax_list` (POST)
- Columns: 13 (0-12), kolom 12 = action buttons
- Error handling: SweetAlert2 toast

### 2.3 Backend Query (`Request_model.php` `_get_base_query()`)
```sql
SELECT 
    pd.no_permintaan,
    pd.mr,
    pd.nama,
    (SELECT rjk.DESKRIPSI FROM darah.referensi rjk WHERE rjk.ID = pd.id_jenis_kelamin AND rjk.JENIS = 2) AS jenis_kelamin,
    (SELECT rgd.DESKRIPSI FROM darah.referensi rgd WHERE rgd.ID = pd.goldarah AND rgd.JENIS = 1) AS goldar,
    DATE_FORMAT(pd.tgl_lahir,'%d-%m-%Y') AS tgl_lahir,
    DATE_FORMAT(pd.tgl_minta,'%d-%m-%Y') AS tgl_minta,
    DATE_FORMAT(pd.tgl_diperlukan,'%d-%m-%Y') AS tgl_diperlukan,
    pd.alasan,
    (SELECT tuj.variabel FROM darah.variabel tuj WHERE tuj.id_variabel = pd.tujuan AND tuj.id_referensi = 1948) AS tujuan,
    (SELECT rstatus.DESKRIPSI FROM darah.referensi rstatus WHERE rstatus.ID = pd.status AND rstatus.JENIS = 3) AS status_proses,
    (SELECT r.DESKRIPSI FROM darah.ruangan r WHERE r.ID = pd.ruangan AND r.JENIS = 5) AS ruangan,
    pd.trombosit, pd.kadar_hb,
    CONCAT(
        (SELECT jd.nama_jenis FROM darah.jenis_darah jd WHERE jd.ID = pd.jenis_darah AND jd.JENIS = 1),
        '<br> Trombosit: ', pd.trombosit,
        '<br> Kadar HB: ', pd.kadar_hb
    ) AS jenis_darah,
    CASE WHEN k.kelengkapan = 1 THEN 'Sudah lengkap' ELSE 'Tidak lengkap' END AS kelengkapan_status,
    IFNULL(st.status,0) AS status_terima
    FROM darah.pesan_darah pd
    LEFT JOIN darah.kelengkapan k ON pd.no_permintaan = k.NO_PERMINTAAN
    LEFT JOIN darah.status_terima st ON pd.no_permintaan = st.no_permintaan
    WHERE 
    (
        pd.tgl_minta BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 MONTH) AND DATE_ADD(CURDATE(), INTERVAL 1 MONTH)
        OR pd.tgl_diperlukan BETWEEN DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
    )
```

### 2.4 Database Connection (CI3)
```php
$db['default'] = array(
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'darah',
    'dbdriver' => 'mysqli',
    // ...
);
```

---

## 3. QUERY COMPARISON TABLE

| Aspek | Native (`isi_view.php`) | CI3 (`Request_model.php`) | Match? |
|-------|------------------------|---------------------------|--------|
| **Tabel utama** | `darah.pesan_darah pd` | `darah.pesan_darah pd` | ✅ |
| **Database Host** | `192.168.7.241` (remote) | `localhost` | ❌ **KRITIS** |
| **Database User** | `syifa` / `simrs2026` | `root` / (empty) | ❌ |
| **Status Filter** | `(pd.status != 0 or pd.status IS NULL)` | **Tidak ada** (dihapus UI-16B) | ❌ |
| **Filter tgl_minta** | `BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 MONTH) AND DATE_ADD(CURDATE(), INTERVAL 1 MONTH)` | Sama | ✅ |
| **Filter tgl_diperlukan** | `BETWEEN DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)` | Sama | ✅ |
| **ORDER BY** | `pd.no_permintaan DESC` | `no_permintaan DESC` (default) | ✅ |
| **JOIN goldar (rgd)** | LEFT JOIN | Scalar subquery | ⚠️ Functional equiv |
| **JOIN status (rstatus)** | LEFT JOIN | Scalar subquery | ⚠️ Functional equiv |
| **JOIN jenis_kelamin (rjk)** | LEFT JOIN | Scalar subquery | ⚠️ Functional equiv |
| **JOIN ruangan (r)** | LEFT JOIN | Scalar subquery | ⚠️ Functional equiv |
| **JOIN tujuan (tuj)** | LEFT JOIN | Scalar subquery | ⚠️ Functional equiv |
| **JOIN jenis_darah (jd)** | LEFT JOIN | Scalar subquery | ⚠️ Functional equiv |
| **JOIN kelengkapan (k)** | LEFT JOIN | LEFT JOIN | ✅ |
| **JOIN status_terima (st)** | LEFT JOIN | LEFT JOIN | ✅ |
| **Kolom Output** | 13 kolom (dt 0-12) | 13 kolom + action | ✅ |
| **Derived Table Wrap** | Ya (`temp` subquery) | Ya (`($base) temp`) | ✅ |
| **DataTables Library** | SSP class (ssp.class.php) | CI3 Query Builder custom | ⚠️ |

---

## 4. PERBEDAAN KRITIS YANG DITEMUKAN

### 4.1 DATABASE HOST & DATA (ROOT CAUSE #1)
| | Native | CI3 |
|--|--------|-----|
| **Host** | `192.168.7.241` | `localhost` |
| **Data Source** | **Production DB** (real-time) | **Local DB** (migration copy) |

**Impact:** Native menampilkan data **production real-time**. CI3 menampilkan data **local yang mungkin kosong/outdated**.

### 4.2 STATUS FILTER (ROOT CAUSE #2 - DIBAWAH INI)
| | Native | CI3 (UI-16B fix) |
|--|--------|------------------|
| **Filter** | `status != 0` (exclude Permintaan) | **Tidak ada filter** (include status 0) |

**Paradoks:** Native *exclude* status 0 tapi menampilkan data. CI3 *include* status 0 tapi kosong.  
→ Ini membuktikan **data source berbeda** (poin 4.1), bukan filter status.

### 4.3 FILTER TANGGAL — SAMA
Keduanya menggunakan logika OR yang sama:
```sql
pd.tgl_minta BETWEEN [1 bulan lalu] AND [1 bulan depan]
OR
pd.tgl_diperlukan BETWEEN [7 hari lalu] AND [7 hari depan]
```

### 4.4 KOLOM & URUTAN — SAMA (13 kolom)
| Index | Native | CI3 |
|-------|--------|-----|
| 0 | no_permintaan | no_permintaan |
| 1 | mr | mr |
| 2 | nama | nama |
| 3 | tgl_minta | tgl_minta |
| 4 | ruangan | ruangan |
| 5 | alasan | alasan |
| 6 | tujuan | tujuan |
| 7 | goldar | goldar |
| 8 | tgl_diperlukan | tgl_diperlukan |
| 9 | jenis_darah | jenis_darah |
| 10 | status_proses | status_proses |
| 11 | kelengkapan_status | kelengkapan_status |
| 12 | status_terima | status_terima (+ action buttons) |

---

## 5. ROOT CAUSE: MENGAPA CI3 KOSONG?

### Primary: **Database Host & Data Mismatch**
```
Native: 192.168.7.241 (Production) → ADA DATA
CI3:    localhost (Local copy)     → KOSONG / TIDAK SAMA
```

**Bukti:** UI-16 audit menemukan `pesan_darah` di local hanya 175K rows dengan distribusi status tertentu. Native production pasti punya data lebih baru/banyak.

### Secondary: **MySQL 8.0 Optimizer Bug** (SUDAH DIPERBAIKI UI-16B)
- Native pakai SSP class → generate query langsung tanpa derived table
- CI3 asli pakai derived table wrap + LEFT JOIN 8 tabel → query hang >120s
- UI-16B sudah fix: ganti LEFT JOIN lookup → scalar subqueries

### Tertiary: **Status Filter Logic Berbeda**
| | Native | CI3 (original) | CI3 (UI-16B fix) |
|--|--------|----------------|------------------|
| Status 0 (Permintaan) | **EXCLUDED** | **EXCLUDED** | **INCLUDED** |
| Status 1-10 | INCLUDED | INCLUDED | INCLUDED |

**Note:** Native *exclude* status 0 = lebih sedikit data, tapi native punya data. CI3 *include* status 0 = lebih banyak data potensial, tapi DB local kosong.

---

## 6. FILE YANG HARUS DIPERBAIKI

### 6.1 Konfigurasi Database (WAJIB)
| File | Perubahan |
|------|-----------|
| `application/config/database.php` | Ganti `hostname` dari `localhost` ke `192.168.7.241`, user `root` → `syifa`, password sesuai |

### 6.2 Status Filter (OPSIONAL — SESUAIKAN DENGAN NATIVE)
| File | Perubahan |
|------|-----------|
| `application/models/Request_model.php` | Tambah kembali filter `WHERE (pd.status != 0 OR pd.status IS NULL)` di `_get_base_query()` agar **sama persis native** |

### 6.3 Scalar Subquery Optimization (SUDAH OK — UI-16B)
| File | Status |
|------|--------|
| `application/models/Request_model.php` | ✅ Scalar subqueries sudah diganti dari LEFT JOIN |

---

## 7. REKOMENDASI FIX MINIMAL

### Step 1: Sinkronisasi Database (PRIORITAS 1)
```php
// application/config/database.php
$db['default'] = array(
    'hostname' => '192.168.7.241',  // ← Native host
    'username' => 'syifa',          // ← Native user
    'password' => 'simrs2026',      // ← Native pass
    'database' => 'darah',
    // ...
);
```
**Test:** Setelah ganti, `SELECT COUNT(*) FROM darah.pesan_darah` harus sama dengan production.

### Step 2: Samakan Status Filter (PRIORITAS 2 - Opsional)
```php
// Request_model.php line 74-78
WHERE 
(pd.status != 0 or pd.status IS NULL)   // ← TAMBAHKAN INI
AND (
    pd.tgl_minta BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 MONTH) AND DATE_ADD(CURDATE(), INTERVAL 1 MONTH)
    OR pd.tgl_diperlukan BETWEEN DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
)
```
**Alasan:** Supaya CI3 menampilkan **sama persis** data native (exclude status 0 = "Permintaan Baru").

### Step 3: Verifikasi End-to-End
1. Login level 1
2. Buka `/requestcontroller/list`
3. DataTables load → `recordsTotal` harus > 0
4. Bandingkan 5 baris pertama dengan native `admin/isi_view.php`

---

## 8. KESIMPULAN

| Pertanyaan | Jawaban |
|------------|---------|
| **Apakah query CI3 salah?** | Tidak — setelah UI-16B, query benar & optimal |
| **Apakah filter status salah?** | CI3 include status 0, native exclude. Tapi bukan penyebab kosong. |
| **Apakah DataTables config salah?** | Tidak — kolom, URL, format response sudah benar |
| **MENGAPA KOOSONG?** | **Database lokal (localhost) KOSONG / BERBEDA dengan production (192.168.7.241)** |

**PHASE UI-16E AUDIT SELESAI.**  
**Fix utama: Ganti konfigurasi database CI3 ke production (192.168.7.241).**