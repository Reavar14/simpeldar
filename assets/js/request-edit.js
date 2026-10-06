/* ==========================================================================
   SIMPELDAR - Request Edit Form JavaScript
   Requires: jQuery, Select2, SweetAlert2, app.js (SwalHelper)
   ========================================================================== */
(function ($) {
    'use strict';

    console.log('[request-edit.js] File loaded');

    var cfg = window.REQUEST_EDIT_CONFIG || {};

    var golonganDarahMap = {
        '1':  { text: 'A',         cls: 'text-purple' },
        '2':  { text: 'B',         cls: 'text-danger' },
        '3':  { text: 'AB',        cls: 'text-success' },
        '4':  { text: 'O',         cls: 'text-primary' },
        '5':  { text: 'A+',        cls: 'text-purple' },
        '6':  { text: 'A-',        cls: 'text-purple' },
        '7':  { text: 'B+',        cls: 'text-danger' },
        '8':  { text: 'B-',        cls: 'text-danger' },
        '9':  { text: 'AB+',       cls: 'text-success' },
        '10': { text: 'AB-',       cls: 'text-success' },
        '11': { text: 'O-',        cls: 'text-primary' },
        '12': { text: 'O+',        cls: 'text-primary' },
        '13': { text: 'Tidak Tahu', cls: 'text-secondary' }
    };

    function updateGolonganDarahSpan(golId) {
        var $span = $('.pasien-goldarah');
        var $selected = $('#id-selected');

        $span.removeClass(function (index, className) {
            return (className.match(/(^|\s)text-\S+/g) || []).join(' ');
        });
        $span.removeClass('goldar-small');
        /* Bersihkan style inline dari render awal PHP agar class warna/ukuran berlaku */
        $span.css('color', '');
        $span.css('font-size', '');

        var data = golonganDarahMap[golId];
        if (!data) {
            $span.text('');
            $selected.text('');
            return;
        }

        if (golId === '13') {
            $span.addClass('goldar-small');
        }
        $span.addClass(data.cls);
        $span.text(data.text);
        $selected.text('ID: ' + golId);
    }

    function cekGoldarKantong(i) {
        var goldarPasien = $('.goldarah.pasien-goldarah').text().trim();
        var $goldarKantong = $('#goldaroto' + i + 'edit');
        if (!$goldarKantong.length) return;

        var goldarKantong = $goldarKantong.val().trim();
        $('#goldaroto' + i + '_hidden').val(goldarKantong);

        $goldarKantong.removeClass('text-danger');
        if (goldarPasien !== '' && goldarKantong !== '' && goldarPasien !== goldarKantong) {
            $goldarKantong.addClass('text-danger');
        }
    }

    function lookupKantong(i, forceRefresh) {
        var $nomer = $('#nomer' + i);
        if (!$nomer.length) return;

        var nomer = $nomer.val();
        forceRefresh = forceRefresh === true;
        var originalNomer = ($nomer.data('original') || '').toString().trim();
        var isChangedNomer = nomer && nomer.trim() !== '' && nomer.trim() !== originalNomer;
        var isKantongLuar = !!(nomer && nomer.trim().length === 11);

        $.ajax({
            type: 'POST',
            url: cfg.lookupUrl,
            data: { nomer: nomer },
            success: function (data) {
                var obj;
                try { obj = JSON.parse(data); } catch (e) { return; }

                /* Volume */
                var $cc = $('#cc' + i + 'edit');
                if (obj.cc && obj.cc !== '' && obj.cc !== '-') {
                    $cc.val(obj.cc);
                } else if (!isKantongLuar && (!obj.cc || obj.cc === '' || obj.cc === '-')) {
                    if (!$cc.val()) $cc.val('-');
                }

                /* Expired date */
                var $exp = $('#exp' + i + 'edit');
                if (obj.exp && obj.exp !== '' && obj.exp !== '-') {
                    $exp.val(obj.exp);
                } else if (!isKantongLuar && (!obj.exp || obj.exp === '' || obj.exp === '-')) {
                    if (!$exp.val()) $exp.val('-');
                }

                /* Golongan darah kantong */
                var $goldar = $('#goldaroto' + i + 'edit');
                var $goldarHidden = $('#goldaroto' + i + '_hidden');
                if (obj.deskdar && obj.deskdar !== '-') {
                    var existingGoldar = $goldar.val();
                    if (forceRefresh || isChangedNomer || !existingGoldar || existingGoldar.trim() === '' || existingGoldar.trim() === '-') {
                        $goldar.val(obj.deskdar);
                        $goldarHidden.val(obj.deskdar);
                        $goldar.prop('readonly', true);
                    } else {
                        $goldarHidden.val(existingGoldar);
                        $goldar.prop('readonly', true);
                    }
                } else {
                    if (!$goldar.val() || $goldar.val().trim() === '') {
                        $goldar.val('');
                        $goldarHidden.val('');
                        $goldar.prop('readonly', false);
                    } else {
                        $goldarHidden.val($goldar.val());
                    }
                }

                cekGoldarKantong(i);
            }
        });
    }

    function autofillPasien() {
        var nomr = $('#nomr').val();
        if (!nomr) return;

        $.ajax({
            type: 'POST',
            url: cfg.autofillUrl,
            data: { nomr: nomr },
            success: function (data) {
                var obj;
                try { obj = JSON.parse(data); } catch (e) { return; }

                if (obj.nama && !$('#nama').val()) $('#nama').val(obj.nama);
                if (obj.jk && !$('#id_jenis_kelamin').val()) $('#id_jenis_kelamin').val(obj.jk);
                if (obj.riwayattrans1 && !$('#riwayattrans1').val()) $('#riwayattrans1').val(obj.riwayattrans1);

                // Pada halaman edit, golongan darah tidak ditimpa oleh data master pasien
            }
        });
    }

    var lastGolId = '';
    var goldarApplying = false;

    function getGoldarSelect() {
        var $select = $('#selectgoldarah');
        if (!$select.length) {
            $select = $('select[name="goldarah"]');
        }
        return $select;
    }

    /* Terapkan nilai golongan darah: select + Select2 + preview + hidden + cek kantong */
    function applyGoldarah(golId) {
        golId = golId ? String(golId) : '';

        var $select = getGoldarSelect();
        if ($select.length) {
            goldarApplying = true;
            if ($select.val() !== golId) {
                $select.val(golId);
            }
            /* Refresh Select2 agar tampilan dropdown ikut nilai terbaru */
            $select.trigger('change.select2');
            goldarApplying = false;
        }

        updateGolonganDarahSpan(golId);
        $('#id_gol_darah').val(golId);
        $('[name="id_gol_darah"]').val(golId);

        for (var i = 1; i <= 12; i++) {
            cekGoldarKantong(i);
        }
    }

    /* Expose function untuk perubahan programmatic (load, reset, dll) */
    window.setGolonganDarahProgrammatic = function (golId) {
        lastGolId = golId ? String(golId) : '';
        applyGoldarah(lastGolId);
    };

    function bindGoldarahChange() {
        var $select = getGoldarSelect();
        if (!$select.length) {
            console.warn('[bindGoldarahChange] Select element not found');
            return;
        }

        /* Nilai aktif awal = nilai database yang sudah tampil */
        lastGolId = $select.val() || '';

        $select.on('change', async function () {
            if (goldarApplying) {
                return;
            }

            var golId = $(this).val() || '';

            /* Tidak ada perubahan nilai */
            if (golId === lastGolId) {
                return;
            }

            /* Dikosongkan tanpa konfirmasi */
            if (golId === '') {
                lastGolId = '';
                updateGolonganDarahSpan('');
                $('#id_gol_darah').val('');
                $('[name="id_gol_darah"]').val('');
                return;
            }

            /* Konfirmasi perubahan golongan darah pasien */
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
                /* Batal: kembalikan dropdown + Select2 + preview ke nilai lama */
                applyGoldarah(lastGolId);
                return;
            }

            /* Ya: simpan nilai baru sebagai nilai aktif lalu update preview */
            lastGolId = golId;
            updateGolonganDarahSpan(golId);
            $('#id_gol_darah').val(golId);
            $('[name="id_gol_darah"]').val(golId);

            for (var i = 1; i <= 12; i++) {
                cekGoldarKantong(i);
            }
        });
    }

    function bindKantongLookup() {
        $(document).on('keyup', '[data-kantong-lookup]', function () {
            var i = $(this).data('kantong-lookup');
            if (i) lookupKantong(i);
        });
    }

    function bindFormSubmit() {
        $('#formEditPermintaan').on('submit', function () {
            var currentGoldar = getGoldarSelect().val() || '';

            // Pastikan nilai hidden id_gol_darah tersinkron ke field POST
            if (currentGoldar) {
                $('[name="id_gol_darah"]').val(currentGoldar);
                $('#id_gol_darah').val(currentGoldar);
            }

            /* Sinkronkan hidden goldaroto dengan input tampilan */
            for (var i = 1; i <= 12; i++) {
                var $goldar = $('#goldaroto' + i + 'edit');
                if ($goldar.length) {
                    $('#goldaroto' + i + '_hidden').val($goldar.val());
                }
            }

            /* Buka form darah di tab baru setelah simpan */
            var id = $('#no_permintaan').val();
            if (id) {
                window.open(cfg.printFormUrl + encodeURIComponent(id), '_blank');
            }
        });
    }

    function initFilledKantong() {
        for (var i = 1; i <= 12; i++) {
            var $nomer = $('#nomer' + i);
            if ($nomer.length && $nomer.val() && $nomer.val().toString().trim() !== '') {
                lookupKantong(i, true);
            }
        }

        /* Cek kembali golongan kantong setelah lookup awal selesai */
        setTimeout(function () {
            for (var i = 1; i <= 12; i++) {
                var $goldar = $('#goldaroto' + i + 'edit');
                if ($goldar.length && $goldar.val().trim() !== '') {
                    cekGoldarKantong(i);
                }
            }
        }, 500);
    }

    $(function () {
        console.log('[request-edit.js] DOM ready, initializing');

        var $select = getGoldarSelect();
        var initialGolId = $select.length ? ($select.val() || '') : '';

        /* Sinkronisasi awal: tampilkan preview sesuai nilai database
           tanpa perlu user memilih ulang dropdown */
        window.setGolonganDarahProgrammatic(initialGolId);

        bindGoldarahChange();
        autofillPasien();
        bindKantongLookup();
        bindFormSubmit();
        initFilledKantong();
        console.log('[request-edit.js] Initialization complete. Initial goldarah:', initialGolId);
    });
})(jQuery);