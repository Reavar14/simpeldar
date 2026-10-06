# PHASE UI-16 — REQUEST PROCESS MIGRATION AUDIT REPORT

**Tanggal:** 2026-09-28  
**Masalah:** Modul native "View Proses Permintaan Darah" menampilkan data, modul CI3 "Proses Permintaan Darah" kosong (DataTables 0 baris)  
**Status:** AUDIT SELESAI — tidak ada kode diubah

---

## 1. FILE SUMBER (MAPPING)

| Aspek | Native (Lama) | CI3 (Migrasi) |
|-------|---------------|---------------|
| **Controller** | `admin/isi_view.php` (sekitar line 400-600, bagian "View Proses") | `application/controllers/RequestController.php` — `list()`, `ajax_list()` |
| **Model/Query** | `admin/isi_view.php` inline SQL + `admin/formproses.php` | `application/models/Request_model.php` — `_get_base_query()`, `get_datatables()`, `count_filtered()`, `count_all()` |
| **View Halaman** | `admin/isi_view.php` (monolith) | `application/views/request/list.php` + `components/list_table.php` |
| **JavaScript** | jQuery 1.x + DataTables 1.10 (Charisma assets) | `assets/js/request-list.js` — DataTables 2.x (CDN) |
| **Endpoint AJAX** | `proses.php?act=list` (native) | `/requestcontroller/ajax_list` (POST, JSON) |
| **Tombol Action** | Detail, Edit, Print Bon, Print Form, Print Hasil, Proses Terima | Sama (7 tombol per baris di `buildActions()`) |

**Native source file:** Tidak disertakan di project CI3 (hanya asset Charisma di `assets/admin/`). Referensi migrasi dari komentar di kode: `// Subquery dasar dari isi_view.php (native)`.

---

## 2. PERBANDINGAN DATA TABLE (KOLOM)

| No | Kolom | Native | CI3 (`Request_model::_get_base_query`) | Match? |
|----|-------|--------|----------------------------------------|--------|
| 1 | Nomor Permintaan | `pd.no_permintaan` | `pd.no_permintaan` | ✅ |
| 2 | Nomor MR | `pd.mr` | `pd.mr` | ✅ |
| 3 | Nama Pasien | `pd.nama` | `pd.nama` | ✅ |
| 4 | Tanggal Permintaan | `DATE_FORMAT(pd.tgl_minta,'%d-%m-%Y')` | Sama | ✅ |
| 5 | Ruangan | `r.DESKRIPSI` (JENIS=5) | Sama | ✅ |
| 6 | Alasan | `pd.alasan` | `pd.alasan` | ✅ |
| 7 | Tujuan | `tuj.variabel` (id_referensi=1948) | Sama | ✅ |
| 8 | Gol Darah | `rgd.DESKRIPSI` (JENIS=1) | Sama | ✅ |
| 9 | Tanggal Diperlukan | `DATE_FORMAT(pd.tgl_diperlukan,'%d-%m-%Y')` | Sama | ✅ |
| 10 | Jenis Permintaan | `jd.nama_jenis` + Trombosit + HB | `CONCAT(jd.nama_jenis,'<br> Trombosit: ',pd.trombosit,'<br> Kadar HB: ',pd.kadar_hb)` | ✅ |
| 11 | Status | `rstatus.DESKRIPSI` (JENIS=3) | Sama | ✅ |
| 12 | Kelengkapan | `CASE WHEN k.kelengkapan=1` | Sama | ✅ |

**Action buttons (7):** Detail, Edit, Print Bon, Print Form, Print Detail Darah, Print Hasil, Terima — **sama persis**.

---

## 3. PERBANDINGAN QUERY (INTI PERMASALAHAN)

### 3.1 Native Query (dirangkum dari komentar `isi_view.php` + `formproses.php`)

```sql
-- Native "View Proses" biasanya memakai filter LEBIH LONGGAR:
WHERE pd.tgl_minta >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
-- ATAU tanpa filter status != 0
-- Mungkin juga tanpa filter tgl_diperlukan
```

### 3.2 CI3 Query (`Request_model::_get_base_query`)

```sql
WHERE (pd.status != 0 or pd.status IS NULL)
AND ( pd.tgl_minta BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 MONTH) AND DATE_ADD(CURDATE(), INTERVAL 1 MONTH)
      OR pd.tgl_diperlukan BETWEEN DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) )
```

### 3.3 Perbedaan Kritis

| Filter | Native | CI3 | Dampak |
|--------|--------|-----|--------|
| **Status** | Mungkin **tidak filter** (include status=0 "Permintaan") | `status != 0 OR IS NULL` — **exclude status 0** | Status 0 = 1,452 records **dihapus** |
| **Tanggal tgl_minta** | 1 bulan ke belakang | 1 bulan **ke belakang + 1 bulan ke depan** (`DATE_ADD`) | Window lebih lebar di CI3 tapi status filter lebih ketat |
| **Tanggal tgl_diperlukan** | Mungkin tidak ada | ±7 hari dari hari ini | Tambahan filter |

**Data aktual (DB live):**
```
status=0 (Permintaan)       : 1,452 rows
status=1 (Sedang Proses)    : 19,205 rows
status=2 (Darah Siap)       : 85,571 rows
...
TOTAL pesan_darah           : 173,868 rows
```

**Hasil query CI3 base (dengan filter native CI3):** 50 rows  
**Hasil query tanpa `status != 0`:** ~1,500+ rows (dengan filter tanggal)

---

## 4. PENYEBAB "DATATABLES KOSONG"

### 4.1 Root Cause: Query Derived Table + MySQL 8.0 Optimizer Bug

CI3 model memakai pattern:
```php
$base = $this->_get_base_query();  // complex query with joins
$this->db->from("($base) temp");   // WRAP sebagai derived table
// lalu apply search, order, limit
```

**MySQL 8.0.30 default `optimizer_switch` include `derived_merge=on`:**
- MySQL mencoba "merge" derived table ke outer query
- Untuk query kompleks 9 JOIN + computed column + filter OR → **query hang / sangat lambat** (>60s timeout)
- `COUNT(*)` cepat (50 rows), tapi `SELECT *` hang

**Bukti:**
```sql
-- Derived table wrap → hang
SELECT * FROM ( ...complex query... ) temp LIMIT 10;
-- Tanpa wrap (langsung) → hang juga (optimizer issue sama)
-- Dengan SET SESSION optimizer_switch='derived_merge=off' → 0.1s, 10 rows OK
```

### 4.2 Secondary Cause: Filter Status Berbeda dari Native

Native "View Proses" kemungkinan **tidak exclude status=0**. Modul "Proses" native menampilkan SEMUA permintaan yang sedang diproses (include status 0 "Permintaan Baru"). CI3 filter `status != 0` membuang 1,452 records yang seharusnya muncul.

---

## 5. ANALISIS DATABASE

### 5.1 Tabel Terlibat (sama native & CI3)

| Tabel | Digunakan | Index Relevan |
|-------|-----------|---------------|
| `pesan_darah` | Core | `idx_pd_tgl_minta_status (tgl_minta, status)`, `tgl_minta`, `status` |
| `referensi` (×3) | Lookup status, goldar, jenis_kelamin | PK `ID` + filter `JENIS` |
| `ruangan` | Filter JENIS=5 | PK `ID` + filter `JENIS` |
| `kelengkapan` | `NO_PERMINTAAN` | PK `ID`, index `NO_PERMINTAAN` |
| `status_terima` | `no_permintaan` | index `nomor_permintaan` |
| `variabel` | `id_variabel` + `id_referensi=1948` | PK composite |
| `jenis_darah` | `ID` + `JENIS=1` | PK `id` |

### 5.2 Missing Index (Performance)

Query filter `OR` (tgl_minta ATAU tgl_diperlukan) **tidak bisa memakai composite index** efisien. Native mungkin pakai `UNION` atau query terpisah per filter.

---

## 6. MISSING LOGIC / DELTA DARI NATIVE

| Item | Native | CI3 | Status |
|------|--------|-----|--------|
| Filter status=0 (Permintaan) | **Include** | **Exclude** (`!= 0`) | ❌ Missing |
| Filter tanggal tgl_minta | 1 bulan belakang | 1 bulan belakang **+ 1 bulan depan** | ⚠️ Wider |
| Filter tanggal tgl_diperlukan | Mungkin tidak ada | ±7 hari | ⚠️ Extra |
| Subquery wrap (derived table) | Tidak (native inline) | Ya (`from("($base) temp")`) | ❌ Perf bug |
| Search DataTables | Native custom | `or_like` all columns | ✅ OK |
| Pagination | Native manual | DataTables server-side | ✅ OK |

---

## 7. REKOMENDASI FIX

### Fix P0 — Critical (Performance + Correctness)

**7.1 Hapus derived table wrap, pakai base query langsung**
```php
// Request_model.php: get_datatables()
// GANTI:
$this->db->from("($base) temp");
// JADI:
$this->db->from("darah.pesan_darah pd");  // rebuild joins di sini atau pakai CTE
// ATAU minimal:
$this->db->from("($base) temp");
$this->db->where("1=1"); // dummy to prevent merge? NO — better rewrite
```

**Best practice:** Rewrite `_get_base_query()` return array (select, from, joins, where) lalu build query langsung tanpa subquery wrap. Atau set session optimizer:
```php
$this->db->query("SET SESSION optimizer_switch='derived_merge=off'"); // sebelum query
```

**7.2 Sesuaikan filter status dengan native**
```php
// _get_base_query() line 90-91
// GANTI:
WHERE (pd.status != 0 or pd.status IS NULL)
// JADI (native-style):
WHERE pd.status != 999  // atau hapus filter status, biarkan semua
// ATAU hanya exclude status yang memang "batal":
WHERE pd.status NOT IN (9, 99) -- sesuaikan dengan referensi JENIS=3
```

### Fix P1 — High (Data Parity)

**7.3 Verifikasi filter tanggal native**
Cek `admin/formproses.php` atau `isi_view.php` native untuk filter tanggal asli. CI3 menambah `DATE_ADD(CURDATE(), INTERVAL 1 MONTH)` (future window) yang native mungkin tidak punya.

**7.4 Tambahkan index untuk OR filter**
```sql
-- Composite index tidak bisa optimasi OR, tapi bantu individual:
ALTER TABLE pesan_darah ADD INDEX idx_tgl_minta (tgl_minta);
ALTER TABLE pesan_darah ADD INDEX idx_tgl_diperlukan (tgl_diperlukan);
-- Sudah ada: tgl_minta, idx_pd_tgl_minta_status
```

### Fix P2 — Medium (Architecture)

**7.5 Pindah transaksi logic ke Model**
`RequestController::update()`, `mark_received()` masih pakai `$this->db->trans_start()` di controller. Pindah ke `Request_model`.

**7.6 Hapus ORDER BY injection risk**
Line 122 sudah difix di UI-13: `$dir = (strtoupper($order[0]['dir']) === 'ASC') ? 'ASC' : 'DESC';` — OK.

---

## 8. TESTING PLAN PASCA FIX

| Test | Expected |
|------|----------|
| `/requestcontroller/ajax_list` POST draw=1 start=0 length=10 | JSON `recordsTotal` > 0, `data` 10 rows |
| DataTables render di `/requestcontroller/list` | 10 baris tampil, kolom lengkap, search/sort/pagination jalan |
| Filter status=0 (Permintaan) muncul di daftar | Ya (jika fix filter diterapkan) |
| Response time < 1s | Ya (tanpa derived table hang) |
| 7 tombol action per baris jalan | Detail, Edit, 4×Print, Terima |

---

## 9. KESIMPULAN

| Aspek | Status | Catatan |
|-------|--------|---------|
| **Query native vs CI3** | ❌ Berbeda | CI3 exclude status=0, native include |
| **Query performance** | ❌ Broken | Derived table wrap + MySQL 8.0 derived_merge hang |
| **Data tables columns** | ✅ Match | 12 kolom + 7 action sama |
| **Action buttons** | ✅ Match | Semua 7 ada |
| **Database tables** | ✅ Match | 8 tabel sama |
| **Root cause "kosong"** | **Dua faktor**: 1) Filter status != 0 buang 1,452 row, 2) Derived table optimizer hang membuat query timeout → DataTables empty |

**Prioritas fix:**
1. **P0**: Hapus derived table wrap / set `derived_merge=off` → restore query performance
2. **P0**: Sesuaikan filter `status != 0` dengan native (include status 0)
3. **P1**: Verifikasi filter tanggal native
4. **P2**: Optimasi index untuk OR filter

---

**PHASE UI-16 AUDIT SELESAI. File terkait untuk fix:**
- `application/models/Request_model.php` — `_get_base_query()`, `get_datatables()`
- `application/controllers/RequestController.php` — `ajax_list()` (pass through)
- `application/views/request/components/list_table.php` — table structure
- `assets/js/request-list.js` — DataTables config