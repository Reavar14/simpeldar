# PHASE UI-20E — EDIT GOLONGAN DARAH SWEETALERT2
## Status: COMPLETE
## Date: 2026-10-01

### TARGET
Halaman Edit Permintaan: `/index.php/requestcontroller/edit/XXXXXXXX`

### PROBLEM
User mengubah golongan darah di halaman edit — perlu konfirmasi SweetAlert2 seperti form-darah.js, bukan native confirm().

### FILE MODIFIED
- `assets/js/request-edit.js`

### CHANGES MADE

**1. Function: `bindGoldarahChange()` (lines 149–188)**

Pattern sama dengan `form-darah.js`:
- Simpan nilai awal goldarah saat load
- Flag programmatic untuk skip confirm saat autofill
- Async change handler dengan SwalHelper.confirm()
- Cancel: revert select2 ke nilai sebelumnya
- Confirm: lanjutkan update golongan darah span

**2. Function: `autofillPasien()` (lines 126–146)**

Ubah dari langsung set `$('#id_gol_darah').val()` menjadi gunakan `window.setGolonganDarahProgrammatic()`:
- Skip confirm dialog saat autofill
- Hanya tampilkan confirm saat user manual mengubah

**3. Expose function: `window.setGolonganDarahProgrammatic()` (lines 191–195)**

Global function untuk autofill dan programmatic changes:
- Set flag goldarProgrammatic = true
- Set lastGolId
- Trigger change event

### BEHAVIOR

**Saat halaman dibuka:**
- Load data existing golongan darah
- Flag programmatic skip confirm
- Tidak ada alert

**Saat user manual ubah golongan darah:**
1. SweetAlert2 modal muncul di tengah
2. Title: "Ubah Golongan Darah?"
3. Text: "Apakah Anda yakin ingin melakukan perubahan golongan darah pasien?"
4. Icon: warning
5. Buttons: "Batal" | "Ya, Ubah" (reverse order)

**Jika klik "Batal":**
- Select2 revert ke nilai sebelumnya
- Tidak ada update

**Jika klik "Ya, Ubah":**
- Update golongan darah span
- Lanjutkan form processing

### VALIDATION

✅ **Single handler** — Hanya satu `$('#goldarah').on('change', ...)` at line 153
✅ **Syntax check** — `node -c` no errors
✅ **No native confirm()** — Hanya SwalHelper.confirm()
✅ **No controller changes** — Pure JavaScript
✅ **No model changes** — Pure JavaScript
✅ **No database changes** — Pure JavaScript
✅ **No AJAX changes** — Same form submit
✅ **No CSRF changes** — Unchanged
✅ **Matches form-darah.js pattern** — Identical logic & UI

### TECHNICAL DETAILS

**Initial state preservation:**
- Line 151: `lastGolId = $('#goldarah').val()` — store initial select value
- Not from hidden input (may be empty on load)

**Programmatic flag:**
- Line 150: `goldarProgrammatic = false`
- Line 157–163: Skip confirm when flag = true
- Line 192–195: Expose function for external callers

**Autofill integration:**
- Line 142: Call `window.setGolonganDarahProgrammatic(obj.id_gol_darah)` instead of direct set
- Ensures programmatic changes skip confirmation dialog

### TESTING CHECKLIST
- [ ] Load halaman edit
- [ ] Golongan darah awal dimuat tanpa alert
- [ ] User ubah golongan darah → SweetAlert2 muncul
- [ ] Klik "Batal" → select revert
- [ ] Klik "Ya, Ubah" → golongan darah updated
- [ ] Autofill MR → golongan darah berubah tanpa alert
- [ ] No console errors
- [ ] No native browser confirm popup

### INTEGRATION NOTES
- Halaman form permintaan (`form-darah.js`) sudah live dengan pola ini
- Halaman edit (`request-edit.js`) sekarang matching pattern
- Konsisten UI/UX di seluruh aplikasi

**Completed:** Halaman edit permintaan sekarang menggunakan SweetAlert2 untuk konfirmasi perubahan golongan darah.