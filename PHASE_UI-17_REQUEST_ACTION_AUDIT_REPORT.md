# PHASE UI-17 — REQUEST PROCESS ACTION AUDIT REPORT

**Tanggal:** 2026-09-28
**Modul:** Proses Permintaan Darah (`/requestcontroller/list`)
**Status:** AUDIT SELESAI — semua 7 tombol action valid, 0 tombol native/404

---

## 1. MAPPING TOMBOL ACTION (DataTables column `Opsi`)

| No | Tombol (JS Class) | Label Tooltip | URL Config (JS) | Route CI3 | Controller Method | Valid? |
|----|-------------------|---------------|-----------------|-----------|-------------------|--------|
| 1 | `tblView` (👁) | Lihat Data | `detailUrl` | `/requestcontroller/detail/{no}` | `detail()` | ✅ |
| 2 | `tblEdit` (✏️) | Edit Data | `editUrl` | `/requestcontroller/edit/{no}` | `edit()` | ✅ |
| 3 | `tblCetak` (🖨) | Cetak Bon Darah | `bonUrl` | `/requestcontroller/print_bon/{no}` | `print_bon()` | ✅ |
| 4 | `tblCetak1` (📋) | Cetak Form Darah | `formUrl` | `/requestcontroller/print_form/{no}` | `print_form()` | ✅ |
| 5 | `tblCetakDetail` (📄) | Cetak Detail Darah | `detailDarahUrl` | `/requestcontroller/print_detail_darah/{no}` | `print_detail_darah()` | ✅ |
| 6 | `tblCetakHasil` (📝) | Cetak Hasil Pemeriksaan | `hasilUrl` | `/requestcontroller/print_hasil_pemeriksaan/{no}` | `print_hasil_pemeriksaan()` | ✅ |
| 7 | `tblTerima` (⬇️) | Terima Sampel | `terimaUrl` (POST) | `/requestcontroller/mark_received` | `mark_received()` | ✅ |

**Semua 7 tombol → route CI3 valid, method controller ada, tidak ada URL native lama.**

---

## 2. RUTE CI3 TERKAIT (RequestController.php)

| Method | HTTP | Route | Auth Level | Deskripsi |
|--------|------|-------|------------|-----------|
| `list()` | GET | `/requestcontroller/list` | 1 | Halaman utama list |
| `ajax_list()` | POST | `/requestcontroller/ajax_list` | 1 | DataTables server-side |
| `detail()` | GET | `/requestcontroller/detail/{no}` | 1 | Detail view |
| `edit()` | GET | `/requestcontroller/edit/{no}` | 1 | Form edit |
| `update()` | POST | `/requestcontroller/update` | 1 | Proses update (form submit) |
| `print_bon()` | GET | `/requestcontroller/print_bon/{no}` | 1 | Cetak Bon |
| `print_form()` | GET | `/requestcontroller/print_form/{no}` | 1 | Cetak Form Darah |
| `print_detail_darah()` | GET | `/requestcontroller/print_detail_darah/{no}` | 1 | Cetak Detail Darah |
| `print_hasil_pemeriksaan()` | GET | `/requestcontroller/print_hasil_pemeriksaan/{no}` | 1 | Cetak Hasil |
| `mark_received()` | POST | `/requestcontroller/mark_received` | 1 | AJAX terima sampel |
| `print_laporan_lengkap()` | GET | `/requestcontroller/print_laporan_lengkap` | 1 | Laporan lengkap (bukan di DataTables) |
| `print_rekap_analis()` | GET | `/requestcontroller/print_rekap_analis` | 1 | Rekap analis (bukan di DataTables) |

---

## 3. PERBANDINGAN NATIVE vs CI3

### Native (`admin/formproses.php`)
| Aksi | Native URL | Parameter |
|------|------------|-----------|
| Detail | `proses.php?act=detail&no=...` | `no` |
| Edit | `formproses.php?act=edit&no=...` | `no` |
| Cetak Bon | `bonminta.php?no=...` | `no` |
| Cetak Form | `formdarah.php?no=...` | `no` |
| Cetak Detail | `detaildarah.php?no=...` | `no` |
| Cetak Hasil | `hasilpemeriksaan.php?no=...` | `no` |
| Terima | `proses_terima.php` (POST) | `no_permintaan`, `no_mr` |

### CI3 (Migrasi)
| Aksi | CI3 URL | Parameter |
|------|---------|-----------|
| Detail | `/requestcontroller/detail/{no}` | path param |
| Edit | `/requestcontroller/edit/{no}` | path param |
| Cetak Bon | `/requestcontroller/print_bon/{no}` | path param |
| Cetak Form | `/requestcontroller/print_form/{no}` | path param |
| Cetak Detail | `/requestcontroller/print_detail_darah/{no}` | path param |
| Cetak Hasil | `/requestcontroller/print_hasil_pemeriksaan/{no}` | path param |
| Terima | `/requestcontroller/mark_received` (POST) | JSON body `no_permintaan`, `no_mr` |

**✅ Semua URL native telah diganti dengan route CI3. Tidak ada referensi `admin/`, `proses.php`, `formproses.php`, `bonminta.php`, dll di JS atau view.**

---

## 4. VERIFIKASI FORM EDIT (POST SUBMIT)

Edit view (`request/edit.php` line 5):
```php
action="<?php echo base_url('index.php/requestcontroller/update'); ?>"
```

Controller `update()` method exists (line 262) — valid.

---

## 5. CEK KEAMANAN & VALIDASI

| Aspek | Status | Catatan |
|-------|--------|---------|
| CSRF token di form edit | ✅ | `$this->security->get_csrf_token_name()` |
| `require_level('1')` di semua method | ✅ | Semua method action cek level 1 |
| Validasi `$no_permintaan` tidak kosong | ✅ | `show_404()` jika kosong |
| Validasi data ditemukan di DB | ✅ | `show_404()` jika model return empty |
| Session check di print methods | ✅ | Cek `$_SESSION['uname']` + redirect `auth` |
| JSON response di `mark_received` | ✅ | `Content-Type: application/json` |

---

## 6. CEK VIEW FILES (PRINT)

| View File | Controller Method | Status |
|-----------|-------------------|--------|
| `request/detail.php` | `detail()` | ✅ Ada |
| `request/edit.php` | `edit()` | ✅ Ada |
| `request/print_bon.php` | `print_bon()` | ✅ Ada |
| `request/print_form.php` | `print_form()` | ✅ Ada |
| `request/print_detail_darah.php` | `print_detail_darah()` | ✅ Ada |
| `request/print_hasil_pemeriksaan.php` | `print_hasil_pemeriksaan()` | ✅ Ada |

Semua view print ada (tidak 404).

---

## 7. POTENSI MASALAH (NON-BLOCKING)

| Item | Deskripsi | Risiko |
|------|-----------|--------|
| `update()` pakai `$this->db->trans_start()` di controller | Transaction handling di controller bukan model | ⚠️ Low (worked so far) |
| `mark_received()` pakai `$this->session->userdata('login')` untuk USRID | `login` session key harus numeric USRID | ⚠️ Low (sudah dipakai native) |
| Print methods cek `$_SESSION['uname']` manual + `require_level` | Duplikasi cek session | ⚠️ Low |
| `norm_post()` handle empty→NULL untuk STRICT_TRANS_TABLES | Working as designed | ✅ OK |

---

## 8. KESIMPULAN

| Kriteria | Hasil |
|----------|-------|
| **Semua 7 tombol action punya route CI3 valid** | ✅ YA |
| **Tidak ada URL native (`admin/`, `.php?act=`) di JS/view** | ✅ YA |
| **Tidak ada tombol menghasilkan 404** | ✅ YA (view file semua ada) |
| **Form edit submit ke route valid** | ✅ YA (`/requestcontroller/update`) |
| **AJAX terima sampel ke endpoint valid** | ✅ YA (`/requestcontroller/mark_received`) |
| **Auth level 1 enforced di semua endpoint** | ✅ YA |

**PHASE UI-17 AUDIT SELESAI. Tidak ada action item — modul siap pakai.**