/*!
 * SIMPELDAR - Form Permintaan Darah
 *
 * Berisi:
 *   - Autofill data pasien berdasarkan Nomor MR
 *   - Lookup nomor kantong darah
 *   - Update tampilan golongan darah
 *   - Cek kompatibilitas golongan darah kantong vs pasien
 *   - Validasi form sebelum submit
 *   - Loading state tombol submit
 *   - Notifikasi sukses/gagal dari URL parameter
 *
 * Bergantung pada: jQuery, Bootstrap 5, SweetAlert2 (via assets/js/app.js)
 *
 * Catatan: script ini dimuat di dalam view content, SEBELUM jQuery
 * dimuat di footer. Karena itu semua penggunaan $ berada di dalam
 * function yang dipanggil via DOMContentLoaded (menjamin jQuery
 * sudah tersedia saat dieksekusi).
 */
(function () {
    'use strict';

    /* ==================================================================
       Konfigurasi endpoint (dari window.FORM_DARAH)
       ================================================================== */
    function cfg(key) {
        return (window.FORM_DARAH && window.FORM_DARAH[key]) || '';
    }

    /* ==================================================================
       Peta golongan darah (ID -> tampilan & warna)
       ================================================================== */
    var GOLONGAN_DARAH = {
        '1':  { text: 'A',   color: '#7c3aed' },
        '2':  { text: 'B',   color: '#dc3545' },
        '3':  { text: 'AB',  color: '#198754' },
        '4':  { text: 'O',   color: '#0d6efd' },
        '5':  { text: 'A+',  color: '#7c3aed' },
        '6':  { text: 'A-',  color: '#7c3aed' },
        '7':  { text: 'B+',  color: '#dc3545' },
        '8':  { text: 'B-',  color: '#dc3545' },
        '9':  { text: 'AB+', color: '#198754' },
        '10': { text: 'AB-', color: '#198754' },
        '11': { text: 'O-',  color: '#0d6efd' },
        '12': { text: 'O+',  color: '#0d6efd' },
        '13': { text: 'Tidak Tahu', color: null }
    };

    /* ==================================================================
       Update span golongan darah pasien (tampilan besar)
       ================================================================== */
    function updateGolonganDarahSpan(golId) {
        var $span = $('.pasien-goldarah');
        var data = GOLONGAN_DARAH[golId];

        if (data) {
            $span.css('font-size', golId === '13' ? '2rem' : '3rem');
            $span.text(data.text);
            $span.css('color', data.color || '');
            $('#id-selected').text('ID: ' + golId);
        } else {
            $span.text('').css('color', '').css('font-size', '3rem');
            $('#id-selected').text('');
        }
    }

    /* ==================================================================
       Hitung umur dari tanggal lahir (Y-m-d atau d-m-Y)
       ================================================================== */
    function hitungUmur(tgl) {
        if (!tgl) {
            return '';
        }

        var p = String(tgl).trim().split(/[-/]/);
        var y, m, d;

        if (p.length !== 3) {
            return '';
        }

        /* deteksi format d-m-Y vs Y-m-d */
        if (p[0].length === 4) {
            y = +p[0]; m = +p[1]; d = +p[2];
        } else {
            d = +p[0]; m = +p[1]; y = +p[2];
        }

        var birth = new Date(y, m - 1, d);
        if (isNaN(birth.getTime()) || y < 1900) {
            return '';
        }

        var now = new Date();
        var umur = now.getFullYear() - birth.getFullYear();
        var monthDiff = now.getMonth() - birth.getMonth();

        if (monthDiff < 0 || (monthDiff === 0 && now.getDate() < birth.getDate())) {
            umur--;
        }

        if (umur < 0) {
            return '';
        }

        return umur + ' Tahun';
    }

    /* ==================================================================
       Autofill data pasien via AJAX (endpoint autofill_pasien)
       ================================================================== */
    var autofillTimer = null;

    function autofillPasien() {
        var nomr = ($('#nomr').val() || '').trim();

        if (!nomr) {
            return;
        }

        $.ajax({
            type: 'POST',
            url: cfg('autofillUrl'),
            data: { nomr: nomr },
            dataType: 'json',
            beforeSend: function () {
                $('#btnAutofill').prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm"></span>');
            },
            complete: function () {
                $('#btnAutofill').prop('disabled', false)
                    .html('<i class="fas fa-search"></i>');
            }
        }).done(function (data) {
            if (!data) {
                return;
            }

            if (data.error) {
                if (window.SwalHelper) {
                    SwalHelper.toast(data.error, 'error');
                }
                resetPasienFields();
                return;
            }

            $('#nama').val(data.nama || '');
            $('#jenis_kelamin').val(data.jk || '');
            $('#id_jenis_kelamin').val(data.id_jenis_kelamin || '');
            $('#tgl_lahir').val(data.tgl_lahir || '');
            $('#TANGGAL_LAHIR').val(data.TANGGAL_LAHIR || '');
            $('#umur').val(hitungUmur(data.tgl_lahir));
            $('#id_gol_darah').val(data.id_gol_darah || '');

            var golId = data.id_gol_darah || '';
            window.setGolonganDarahProgrammatic(golId);

            $('#riwayattrans1').val(data.riwayattrans1 || '');
            $('#notifsatus').val(data.statusdarah || '');

            /* Tampilkan alert jika status darah sebelumnya masih ada */
            var notif = data.statusdarah;
            var adaMasalah = (notif !== null && notif !== undefined && String(notif) !== '8' && String(notif) !== '');
            $('#alertStatusDarah').toggleClass('d-none', !adaMasalah);

            /* Re-cek semua kantong setelah goldar pasien berubah */
            for (var i = 1; i <= 12; i++) {
                cekGoldarKantong(i);
            }

            if (window.SwalHelper && data.nama) {
                SwalHelper.toast('Data pasien ditemukan: ' + data.nama, 'success');
            }
        }).fail(function (xhr) {
            var msg = 'Gagal mengambil data pasien';
            if (xhr.responseJSON && xhr.responseJSON.error) {
                msg = xhr.responseJSON.error;
            }
            if (window.SwalHelper) {
                SwalHelper.toast(msg, 'error');
            }
            resetPasienFields();
        });
    }

    function resetPasienFields() {
        $('#nama').val('');
        $('#jenis_kelamin').val('');
        $('#id_jenis_kelamin').val('');
        $('#tgl_lahir').val('');
        $('#TANGGAL_LAHIR').val('');
        $('#umur').val('');
        $('#id_gol_darah').val('');
        $('#selectgoldarah').val('').trigger('change');
        $('#riwayattrans1').val('');
        $('#notifsatus').val('');
        updateGolonganDarahSpan(null);
        $('#alertStatusDarah').addClass('d-none');
    }

    /* ==================================================================
       Lookup nomor kantong via AJAX (endpoint lookup_kantong)
       ================================================================== */
    function lookupKantong(index, nomer) {
        var $nomer = $('#nomer' + index);
        var $cc = $('#cc' + index);
        var $exp = $('#exp' + index);
        var $goldaroto = $('#goldaroto' + index);
        var $goldarah = $('#goldarah' + index);

        nomer = (nomer || '').trim();

        if (!nomer) {
            $cc.val('');
            $exp.val('');
            $goldaroto.val('').prop('readonly', false);
            $goldarah.text('').addClass('d-none');
            $nomer.removeData('goldaroto');
            cekGoldarKantong(index);
            return;
        }

        $.ajax({
            type: 'POST',
            url: cfg('lookupUrl'),
            data: { nomer: nomer },
            dataType: 'json'
        }).done(function (data) {
            if (!data) {
                return;
            }

            if (data.error) {
                if (window.SwalHelper) {
                    SwalHelper.toast(data.error, 'warning');
                }
                return;
            }

            $cc.val(data.cc || '');
            $exp.val(data.exp || '');

            if (data.deskdar && data.deskdar !== '-') {
                $goldaroto.val(data.deskdar).prop('readonly', true);
                $goldarah.text(data.deskdar).removeClass('d-none');
            } else {
                $goldaroto.val('').prop('readonly', false);
                $goldarah.text('').addClass('d-none');
            }

            $nomer.data('goldaroto', data.goldaroto);
            cekGoldarKantong(index);
        }).fail(function () {
            if (window.SwalHelper) {
                SwalHelper.toast('Gagal melakukan lookup nomor kantong', 'error');
            }
        });
    }

    /* ==================================================================
       Cek kompatibilitas golongan darah kantong vs pasien
       ================================================================== */
    function cekGoldarKantong(index) {
        var $goldarPasien = $('.pasien-goldarah');
        var goldarPasien = ($goldarPasien.text() || '').trim();
        var $input = $('#goldaroto' + index);
        var goldarKantong = ($input.val() || '').trim();

        var mismatch = (goldarPasien !== '' && goldarKantong !== '' && goldarPasien !== goldarKantong);

        $input.removeClass('text-danger fw-bold');
        if (mismatch) {
            $input.addClass('text-danger fw-bold');
        }
    }

    /* ==================================================================
       Update badge jumlah kantong terisi
       ================================================================== */
    function updateKantongCount() {
        var filled = 0;
        for (var i = 1; i <= 12; i++) {
            if (($('#nomer' + i).val() || '').trim() !== '') {
                filled++;
            }
        }
        var $badge = $('#kantongFilledCount');
        if ($badge.length) {
            $badge.text(filled + ' / 12 terisi');
            $badge.removeClass('bg-primary bg-success bg-warning');
            $badge.addClass(filled === 0 ? 'bg-primary' : (filled >= 3 ? 'bg-success' : 'bg-warning'));
        }
    }

    /* ==================================================================
       Validasi sebelum submit
       ================================================================== */
    function validateForm() {
        var missing = [];

        if (!($('#nomr').val() || '').trim()) {
            missing.push('Nomor MR');
            markInvalid('#nomr');
        } else {
            markValid('#nomr');
        }

        if (!($('#nama').val() || '').trim()) {
            missing.push('Nama Pasien (lakukan autofill MR)');
            markInvalid('#nomr');
        }

        if (!($('#tgl_diperlukan').val() || '').trim()) {
            missing.push('Tanggal Diperlukan');
            markInvalid('#tgl_diperlukan');
        } else {
            markValid('#tgl_diperlukan');
        }

        if (!($('#status').val() || '').trim()) {
            missing.push('Status');
            markInvalid('#status');
        } else {
            markValid('#status');
        }

        // Ruangan tidak wajib - tidak ada validasi required
        
        return missing;
    }

    function markInvalid(selector) {
        $(selector).addClass('is-invalid');
    }

    function markValid(selector) {
        $(selector).removeClass('is-invalid');
    }

    /* ==================================================================
       Notifikasi dari URL parameter (?success=1 / ?error=1)
       ================================================================== */
    function handleUrlStatus() {
        var search = window.location.search || '';
        var url = window.location.href;

        if (search.indexOf('success=1') > -1) {
            if (window.SwalHelper) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Data permintaan darah berhasil disimpan.',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#0d6efd'
                }).then(function () {
                    window.location.href = cfg('formUrl');
                });
            }
            cleanUrl(url, 'success=1');
        }

        if (search.indexOf('error=1') > -1) {
            if (window.SwalHelper) {
                Swal.fire({
                    title: 'Gagal!',
                    text: 'Terjadi kesalahan saat menyimpan data. Coba lagi.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc3545'
                });
            }
            cleanUrl(url, 'error=1');
        }
    }

    function cleanUrl(url, token) {
        if (!window.history || !window.history.replaceState) {
            return;
        }
        var cleaned = url.replace('?' + token, '').replace('&' + token, '');
        window.history.replaceState(null, null, cleaned);
    }

    /* ==================================================================
       Inisialisasi
       ================================================================== */
    function init() {

        /* --- Autofill MR: debounce pada input + tombol cari --- */
        $('#nomr').on('input', function () {
            clearTimeout(autofillTimer);
            autofillTimer = setTimeout(function () {
                autofillPasien();
            }, 600);
        });

        $('#btnAutofill').on('click', function (e) {
            e.preventDefault();
            autofillPasien();
        });

        /* Enter pada MR memicu autofill, bukan submit */
        $('#nomr').on('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(autofillTimer);
                autofillPasien();
            }
        });

        /* --- Golongan darah manual change --- */
        var goldarProgrammatic = false;
        var lastGolId = $('#selectgoldarah').val() || '';

        $('#selectgoldarah').on('change', async function () {
            var golId = $(this).val();

            /* Jika perubahan dari autofill (programmatic), lewati konfirmasi */
            if (goldarProgrammatic) {
                goldarProgrammatic = false;
                lastGolId = golId;
                updateGolonganDarahSpan(golId);
                $('#id_gol_darah').val(golId);
                for (var i = 1; i <= 12; i++) {
                    cekGoldarKantong(i);
                }
                return;
            }

            /* Konfirmasi perubahan manual golongan darah pasien */
            if (golId && golId !== lastGolId) {
                var result = await SwalHelper.confirm(
                    'Ubah Golongan Darah?',
                    'Apakah Anda yakin ingin melakukan perubahan golongan darah pasien?',
                    {
                        icon: 'warning',
                        confirmButtonText: 'Ya, Ubah',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }
                );

                if (!result.isConfirmed) {
                    /* Batal: kembalikan ke nilai sebelumnya */
                    $(this).val(lastGolId).trigger('change.select2');
                    return;
                }
                lastGolId = golId;
            }

            updateGolonganDarahSpan(golId);
            $('#id_gol_darah').val(golId);
            for (var i = 1; i <= 12; i++) {
                cekGoldarKantong(i);
            }
        });

        /* Expose flag untuk autofill (dipanggil dari autofillPasien) */
        window.setGolonganDarahProgrammatic = function (golId) {
            goldarProgrammatic = true;
            lastGolId = golId;
            $('#selectgoldarah').val(golId).trigger('change');
        };

        /* --- Lookup kantong: delegate untuk 12 input --- */
        var lookupTimers = {};
        $(document).on('input', '[data-kantong-lookup]', function () {
            var idx = $(this).data('kantong-lookup');
            var nomer = $(this).val();
            clearTimeout(lookupTimers[idx]);
            lookupTimers[idx] = setTimeout(function () {
                lookupKantong(idx, nomer);
            }, 600);
        });

        /* Enter pada nomor kantong langsung lookup */
        $(document).on('keydown', '[data-kantong-lookup]', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                var idx = $(this).data('kantong-lookup');
                clearTimeout(lookupTimers[idx]);
                lookupKantong(idx, $(this).val());
            }
        });

        /* --- Update counter kantong & label tombol collapse --- */
        $(document).on('input', '[data-kantong-lookup]', updateKantongCount);

        var $toggle = $('#btnToggleKantong');
        var $collapsible = $('#kantongLainnya');
        if ($collapsible.length && window.bootstrap && bootstrap.Collapse) {
            var collapseInstance = new bootstrap.Collapse($collapsible[0], { toggle: false });
            $toggle.on('click', function () {
                collapseInstance.toggle();
            });
        }
        $collapsible.on('show.bs.collapse', function () {
            $toggle.html('<i class="fas fa-chevron-up me-1"></i> Sembunyikan Kantong 4–12');
        });
        $collapsible.on('hide.bs.collapse', function () {
            $toggle.html('<i class="fas fa-chevron-down me-1"></i> Tampilkan Kantong 4–12');
        });

        /* --- Submit: validasi + loading state --- */
        $('#formPermintaanDarah').on('submit', function (e) {
            var missing = validateForm();

            if (missing.length) {
                e.preventDefault();
                if (window.SwalHelper) {
                    SwalHelper.error('Validasi Gagal', 'Lengkapi data wajib: ' + missing.join(', ') + '.');
                }
                return;
            }

            /* Loading state tombol simpan */
            if (window.btnLoading) {
                btnLoading($('#btnSimpan'), 'Menyimpan...');
            }
        });

        /* --- Reset bersihkan field turunan --- */
        $('#btnReset').on('click', function () {
            setTimeout(function () {
                resetPasienFields();
                for (var i = 1; i <= 12; i++) {
                    $('#goldarah' + i).text('').addClass('d-none');
                    $('#goldaroto' + i).prop('readonly', false);
                }
                $('#tgl_minta').val(new Date().toISOString().slice(0, 19).replace('T', ' '));
                $('#tgl_diperlukan').val(new Date().toISOString().slice(0, 10));
                updateKantongCount();
            }, 0);
        });

        /* --- Notifikasi URL --- */
        handleUrlStatus();

        /* --- Init awal --- */
        updateKantongCount();
    }

    document.addEventListener('DOMContentLoaded', init);

})();
