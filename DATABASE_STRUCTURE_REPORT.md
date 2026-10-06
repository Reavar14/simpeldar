# DATABASE STRUCTURE REPORT — SIMPELDAR CI3 MIGRATION

**Date:** 2026-09-27  
**Database:** `darah` (MySQL 8.0.30 Laragon, localhost)  
**Audit Scope:** All tables in schema `darah`

---

## TABLE INVENTORY

| Table | Rows | Primary Key | Used By Module |
|-------|------|-------------|----------------|
| analis | 39 | id_analis | Darah (dropdown), Request (detail), Laporan |
| cek_billing | 10 | (none) | Billing, Request (edit), Laporan |
| dokter | 191 | ID | Darah (dropdown), Request (detail/edit) |
| dsphis | 0 | (date, hostname) | (unused) |
| jenis_darah | 17 | (none) | Darah (dropdown), Request (detail/edit) |
| kantong_luar | 69,721 | ID | Darah (submit/lookup), Request (edit/print) |
| kelengkapan | 26,224 | ID | Dashboard, Request (edit) |
| numbset | 2 | (YEAR, MONTH) | (unused) |
| pasien2 | 214,027 | (none) | Darah (autofill) |
| pegawai | 1,736 | NIP | Darah (dokter join) |
| perawat | 989 | id_perawat | Darah (dropdown), Request (edit) |
| perawat_terima | 8,410 | ID | (legacy only) |
| pesan_darah | 175,323 | no_permintaan | **All modules** |
| petugas_serah_terima | 38,402 | ID | Darah (submit), Request (edit/print) |
| petugas_serah_terima_copy1 | 11,115 | ID | (backup) |
| referensi | 48 | (none) | All (dropdowns, status, golongan, jk, hasil) |
| ruangan | 433 | ID | All (filter, dropdown) |
| ruangan_copy1 | 0 | ID | (backup) |
| status_terima | 9,541 | (none) | Request (receive), Dashboard, Rawat Inap |
| usrmst | 87 | USRID | Auth (login), Darah (data entry, dokter), Request |
| usrmst_copy1 | 80 | USRID | (backup) |
| variabel | 7 | (id_variabel, id_referensi) | Darah (tujuan), Request (print) |

**Total: 22 tables** (3 copies: `*_copy1` + unused `dsphis`, `numbset`, `perawat_terima`)

---

## KEY SCHEMA OBSERVATIONS

### Missing Primary Keys (affect upsert logic)
- `cek_billing` — no PK, indexed on `no_permintaan`, `no_mr`, `no_kantong`
- `status_terima` — no PK, indexed on `no_permintaan`, `no_mr`, `oleh`
- `jenis_darah` — no PK, `id` not unique
- `pasien2` — no PK, `nomr` not unique
- `referensi` — no PK, `ID` is MUL (not unique within JENIS)

### Tables Referenced by CI3 Models That Do Not Exist
- **`darah.pasien`** — referenced in `Darah_model::update_pasien_goldar()` (lines 291-295), table **missing**. Native used `darah.pasien` with columns `NORM`, `GOLONGAN_DARAH`. Current schema has `darah.pasien2` (columns: `nomr`, `nama`, `jenis_kelamin`, `tgl_lahir`) — no golongan darah column.

### Column Type Mismatches (Strict Mode Impact)
| Table | Column | Type | CI3 Usage | Risk |
|-------|--------|------|-----------|------|
| pesan_darah | nm_creat | smallint | `Darah::submit()` uses `$_SESSION['uname']` (string) | **CRITICAL** — strict mode rejects string |
| pesan_darah | nm_edit | varchar(50) | `RequestController::update()` uses `$userLogin` (string) | OK |
| pesan_darah | goldarah | int | Used as FK to referensi.ID | OK |
| pesan_darah | mr | int | FK to pasien2.nomr (varchar) | Implicit cast works |
| pasien2 | jenis_kelamin | varchar(50) | Values '1'/'2' strings | Works but loose |

### Critical Referenced Columns (Present & Matching)
All columns used by CI3 models exist in DB with compatible types:
- `pesan_darah`: all 72 columns present
- `kantong_luar`: all NOMOR_KLx, GOLDAR_KLx present
- `referensi`: JENIS, ID, DESKRIPSI present
- `ruangan`: ID, JENIS, JENIS_KUNJUNGAN, DESKRIPSI present
- `usrmst`: USRID, USRNM, PASSWORD, NAMA, LEVEL, STATUS present
- `variabel`: id_variabel, variabel, id_referensi, status present
- `jenis_darah`: id, nama_jenis, jenis present
- `analis`: id_analis, nama_analis present
- `perawat`: id_perawat, nama_perawat present
- `dokter` + `pegawai`: ID, NIP, NAMA, GELAR_DEPAN, GELAR_BELAKANG present

---

## FOREIGN KEY RELATIONSHIPS (Logical, Not Enforced)

| From Table | From Column | To Table | To Column |
|------------|-------------|----------|-----------|
| pesan_darah | goldarah | referensi | ID (JENIS=1) |
| pesan_darah | status | referensi | ID (JENIS=3) |
| pesan_darah | id_jenis_kelamin | referensi | ID (JENIS=2) |
| pesan_darah | hasil_pemeriksaan | referensi | ID (JENIS=4) |
| pesan_darah | jenis_darah | jenis_darah | id (JENIS=1) |
| pesan_darah | buffycoat | jenis_darah | id (JENIS=2) |
| pesan_darah | ruangan | ruangan | ID |
| pesan_darah | dpjp | dokter | ID |
| pesan_darah | analis | analis | id_analis |
| pesan_darah | analis2 | analis | id_analis |
| pesan_darah | nm_creat | usrmst | USRID |
| pesan_darah | nm_edit | usrmst | USRID |
| pesan_darah | tujuan | variabel | id_variabel (id_referensi=1948) |
| kantong_luar | no_permintaan | pesan_darah | no_permintaan |
| kelengkapan | NO_PERMINTAAN | pesan_darah | no_permintaan |
| status_terima | no_permintaan | pesan_darah | no_permintaan |
| status_terima | oleh | usrmst | USRID |
| cek_billing | no_permintaan | pesan_darah | no_permintaan |
| cek_billing | no_kantong | pesan_darah | no_kantong_x |
| petugas_serah_terima | NO_PERMINTAAN | pesan_darah | no_permintaan |
| dokter | NIP | pegawai | NIP |
| pasien2 | nomr | pesan_darah | mr |

---

## DATA INTEGRITY NOTES

1. **`referensi` table** — 48 rows cover all JENIS:
   - JENIS=1 (Golongan Darah): 13 rows (IDs 1-13) — IDs 1-4 excluded by `get_golongan_darah()`
   - JENIS=2 (Jenis Kelamin): 2 rows (1=Laki-laki, 2=Perempuan)
   - JENIS=3 (Status Permintaan): 10 rows (1-10)
   - JENIS=4 (Hasil Pemeriksaan): 8 rows (1-8)
   - JENIS=5 (Mayor): 5 rows
   - JENIS=6 (Minor): 5 rows
   - JENIS=7 (Auto Kontrol): 5 rows

2. **`variabel` id_referensi=1948** — 7 tujuan options present (Tujuan Utama + 6 variants)

3. **`usrmst` STATUS=1** — 78 active users for data entry dropdown

4. **`pasien2`** — 214,027 records but MR range differs from `pesan_darah` (pasien2 has MR 1-5, pesan_darah has MR up to 344097). Autofill will often return empty for real MRs — handled gracefully by model.

5. **`perawat_terima`** — 8,410 rows, legacy table not used by CI3 models.