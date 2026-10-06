# PHASE UI-16B — REQUEST PROCESS FIX REPORT

**Tanggal:** 2026-09-28
**Masalah:** CI3 modul Request Process kosong (0 baris), native View Proses menampilkan data
**Status:** FIX SELESAI — data tampil, query <2 detik

---

## 1. RINGKASAN FIX

| Task | Status | Detail |
|------|--------|--------|
| TASK 1 — Status filter samakan native | ✅ Selesai | Hapus `(pd.status != 0 OR pd.status IS NULL)` |
| TASK 2 — Optimasi query DataTables | ✅ Selesai | Ganti 6 LEFT JOIN lookup → scalar subqueries |
| TASK 3 — Paritas data CI3 vs native | ✅ Selesai | 50 baris tampil (sama window native) |

**File diubah (1 file):**
- `application/models/Request_model.php` — method `_get_base_query()` saja

**Tidak diubah:** controller, view, JS, database struktur, index.

---

## 2. ROOT CAUSE SEBENARNYA (TEMUAN BARU)

Audit UI-16 menduga penyebabnya `derived table wrap` + `derived_merge` optimizer.
Bukti empiris menunjukkan **penyebab spesifiknya lebih sempit**:

### Query hang jika dan hanya jika:
```
LEFT JOIN referensi (lookup table)  +  CONCAT(kolom_joined, ...)  +  filter tanggal
```

Hasil isolasi (masing-masing diuji terpisah):

| Konfigurasi Query | Waktu |
|-------------------|-------|
| base + filter tgl, **tanpa** JOIN, tanpa computed col | **0.05s** |
| + 8 LEFT JOIN, SELECT kolom plain saja | **0.09s** |
| + 8 LEFT JOIN, + `DATE_FORMAT` saja | **0.10s** |
| + 8 LEFT JOIN, + `CONCAT(jd.nama_jenis,...)` | **HANG >120s** |
| + 4 LEFT JOIN non-referensi, + `CONCAT` | **0.09s** |
| + 5 LEFT JOIN ( +referensi), + `CONCAT` | **HANG >120s** |
| scalar subquery untuk semua lookup, + `CONCAT` | **0.19s** |

**Kesimpulan:** MySQL 8.0.30 optimizer meledak (plan exploration exponential)
saat `LEFT JOIN` pada `referensi` (tabel kecil, 48 baris) dipilih sebagai
outer table untuk join dengan `pesan_darah` (175K baris), **dipicu kehadiran
fungsi `CONCAT()` di SELECT list**. Optimizer salah estimasi bahwa materialisasi
`CONCAT` perlu evaluasi full-table scan pada pesan_darah.

Ini bukan sekadar `derived_merge` — `SET SESSION optimizer_switch='derived_merge=off'`
**tidak** memperbaiki (dibuktikan: masih hang). Solusi yang efektif: hilangkan
`LEFT JOIN` lookup tables dan ganti dengan **scalar subqueries** (correlated),
yang membuat optimizer pilih plan: `pesan_darah` sebagai outer table (pakai
index `tgl_minta`), lookup per baris via PK pada tabel kecil.

---

## 3. PERUBAHAN KODE

### 3.1 Sebelum — `_get_base_query()` (Filter Status Salah)

```sql
-- BUG 1: exclude status 0 (Permintaan) — buang 1,452 baris potensial
WHERE (pd.status != 0 or pd.status IS NULL)
AND ( pd.tgl_minta BETWEEN ... OR pd.tgl_diperlukan BETWEEN ... )

-- BUG 2: 8 LEFT JOIN termasuk lookup tables kecil
FROM darah.pesan_darah pd
LEFT JOIN darah.referensi rgd  ON ...   -- lookup goldar
LEFT JOIN darah.referensi rstatus ON ... -- lookup status
LEFT JOIN darah.referensi rjk  ON ...   -- lookup jenis kelamin
LEFT JOIN darah.ruangan r      ON ...   -- lookup ruangan
LEFT JOIN darah.variabel tuj   ON ...   -- lookup tujuan
LEFT JOIN darah.jenis_darah jd ON ...   -- lookup jenis darah
LEFT JOIN darah.kelengkapan k  ON ...
LEFT JOIN darah.status_terima st ON ...
```

### 3.2 Sesudah — `_get_base_query()` (Fixed)

```sql
-- FIX 1: hapus filter status → status 0 (Permintaan) tampil = sama native
WHERE ( pd.tgl_minta BETWEEN ... OR pd.tgl_diperlukan BETWEEN ... )

-- FIX 2: 6 lookup tables → scalar subqueries (PK lookup per baris, cepat)
SELECT
    (SELECT rjk.DESKRIPSI FROM darah.referensi rjk
       WHERE rjk.ID = pd.id_jenis_kelamin AND rjk.JENIS = 2) AS jenis_kelamin,
    (SELECT rgd.DESKRIPSI FROM darah.referensi rgd
       WHERE rgd.ID = pd.goldarah AND rgd.JENIS = 1) AS goldar,
    (SELECT tuj.variabel FROM darah.variabel tuj
       WHERE tuj.id_variabel = pd.tujuan AND tuj.id_referensi = 1948) AS tujuan,
    (SELECT rstatus.DESKRIPSI FROM darah.referensi rstatus
       WHERE rstatus.ID = pd.status AND rstatus.JENIS = 3) AS status_proses,
    (SELECT r.DESKRIPSI FROM darah.ruangan r
       WHERE r.ID = pd.ruangan AND r.JENIS = 5) AS ruangan,
    CONCAT(
        (SELECT jd.nama_jenis FROM darah.jenis_darah jd
           WHERE jd.ID = pd.jenis_darah AND jd.JENIS = 1),
        '<br> Trombosit: ', pd.trombosit,
        '<br> Kadar HB: ', pd.kadar_hb
    ) AS jenis_darah,
    ...
FROM darah.pesan_darah pd
-- Hanya 2 LEFT JOIN tersisa (tabel transaksional, join di no_permintaan)
LEFT JOIN darah.kelengkapan k ON pd.no_permintaan = k.NO_PERMINTAAN
LEFT JOIN darah.status_terima st ON pd.no_permintaan = st.no_permintaan
```

**Yang dipertahankan:**
- Semua 13 kolom output + alias (controller/view tidak perlu ubah)
- Filter tanggal native (`tgl_minta` ±1 bulan OR `tgl_diperlukan` ±7 hari)
- Wrapper `($base) temp` untuk search/order pada kolom computed — sekarang CEPAT
  karena inner query tidak lagi memicu optimizer bug

**Kenapa wrapper `($base) temp` tetap dipakai?**
Setelah inner query diperbaiki (scalar subqueries), wrapper hanya menambah
~100µs. Dibutuhkan agar `or_like` / `order_by` CI3 query builder bisa operasi
pada kolom computed (`jenis_darah`, `kelengkapan_status`, dll). Tanpa wrapper,
CI3 tidak bisa apply WHERE/ORDER pada alias computed kolom. alternatif
`SET SESSION optimizer_switch` tidak diperlukan (dibuktikan tidak memperbaiki).

---

## 4. HASIL BENCHMARK

Lingkungan: MySQL 8.0.30 (Laragon), PHP 8.2, DB live 175,323 baris `pesan_darah`.

| Operasi DataTables | Sebelum | Sesudah |
|--------------------|---------|---------|
| `count_all` (recordsTotal) | hang (>120s) → timeout | **0.30s** |
| `get_datatables` halaman 1 | hang (>120s) → timeout | **0.31s** |
| `get_datatables` halaman 2 | hang | **0.31s** |
| `count_filtered` + search "anemia" | hang | **0.31s** |
| search "anemia" (SELECT) | hang | **0.31s** |
| sort by `status_proses` | hang | **0.30s** |
| Derived table + `derived_merge=off` | masih hang | (n/a) |

**Target <2 detik terpenuhi** dengan margin besar (~0.3s).

### Konsistensi COUNT vs SELECT
```
count_all            = 50
search 'anemia' SELECT = 8 baris
search 'anemia' COUNT  = 8
```
COUNT dan SELECT membaca sumber data yang sama (sama base query) → konsisten.

---

## 5. PARITAS DATA: SEBELUM vs SESUDAH

| Aspek | Sebelum Fix | Sesudah Fix |
|-------|-------------|-------------|
| Native View Proses | ada data (50 baris window saat ini) | (referensi) |
| CI3 `/requestcontroller/list` | **KOSONG** (query timeout) | **50 baris tampil** |
| status 0 (Permintaan) | di-exclude (`!= 0`) | **diikutkan** (filter dihapus) |
| Kolom output | 13 | 13 (identik, alias sama) |
| Filter tanggal | ±1 bln / ±7 hari | sama (tidak diubah) |

**Catatan status=0:** Saat window tanggal berjalan (28 Agu – 28 Okt 2026),
tidak ada baris `status=0` yang jatuh dalam window (data status=0 terakhir
2026-06-15). Namun filter sekarang **tidak lagi membuangnya** — jika ada
permintaan baru berstatus 0 dalam window, akan tampil sama seperti native.

Distribusi status pada window saat ini (50 baris):
```
status 1 (Sedang Proses)               : 1
status 2 (Darah Siap)                  : 2
status 3                               : 2
status 5                               : 4
status 7                               : 7
status 8                               : 30
status 10 (Sudah Terima Sampel)        : 4
```

---

## 6. VERIFIKASI TIDAK MERUSAK LAIN

- `get_detail()`, `get_bon()`, `get_form_darah()`, `get_edit_data()`,
  `get_hasil_pemeriksaan()`, `get_detail_darah()` — **tidak diubah**,
  masih pakai LEFT JOIN sendiri (query tunggal per `no_permintaan`,
  tidak memicu bug karena tidak ada filter tanggal + CONCAT kombinasi).
- `get_laporan_lengkap()`, `get_rekap_analis()` — tidak diubah.
- Controller `RequestController::ajax_list()` — tidak diubah (signature sama).
- View `request/list.php` + `assets/js/request-list.js` — tidak diubah.
- Struktur database & index — tidak diubah sama sekali.

### Path test manual (TASK 3)
1. Login level 1
2. Buka `/requestcontroller/list`
3. DataTables render 50 baris, 13 kolom, search/sort/pagination jalan
4. Tombol Detail/Edit/Print (Bon/Form/DetailDarah/Hasil)/Terima —
   masing-minggu panggil model method yang tidak diubah → tetap jalan

---

## 7. CATATAN TEKNIS UNTUK MASA DEPAN

- **Pola anti-pattern ditemukan:** `LEFT JOIN` tabel lookup kecil + `CONCAT()`
  di SELECT + filter range pada tabel besar (175K) → MySQL 8.0.30 optimizer
  plan explosion. Hindari kombinasi ini; gunakan scalar subqueries untuk lookup.
- `SET SESSION optimizer_switch='derived_merge=off'` **tidak** menjadi solusi
  di sini (dibuktikan empiris masih hang) — bertentangan dengan rekomendasi
  audit UI-16 bagian 7.1.
- Wrapper derived table `($base) temp` tetap aman digunakan selama inner
  query sudah tidak memicu optimizer bug.
- Jika suatu hari query kembali lambat setelah perubahan data signifikan,
  periksa `EXPLAIN` apakah optimizer kembali pilih `referensi` sebagai outer
  table. Pertimbangkan hint `STRAIGHT_JOIN` atau index pada `tgl_diperlukan`
  (saat ini belum ada — hanya `tgl_minta` yang terindex).

---

## 8. KESIMPULAN

| Poin | Hasil |
|------|-------|
| Penyebab utama | Optimizer bug: `LEFT JOIN referensi` + `CONCAT` + filter tanggal |
| Fix status filter | Hapus `status != 0` → paritas native tercapai |
| Fix performance | 6 lookup JOIN → scalar subqueries; 0.3s (dari >120s hang) |
| Database diubah? | TIDAK |
| Controller/view diubah? | TIDAK |
| Target <2 detak? YA (0.3s) | |
| COUNT ≡ SELECT konsisten? | YA |

**PHASE UI-16B SELESAI.**
