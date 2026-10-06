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

    var goldarProgrammatic = false;
    var lastGolId = '';
    var updatingSelect2 = false;

    function syncGoldarahValues(golId) {
        golId = golId ? String(golId) : '';
        var $select = $('#selectgoldarah');
        if (!$select.length) {
            $select = $('select[name="goldarah"]');
        }

        // 1. Value asli select berubah
        if ($select.length && $select.val() !== golId) {
            $select.val(golId);
        }

        // 2. Select2 refresh
        if ($select.length) {
            updatingSelect2 = true;
            $select.trigger('change.select2');
            updatingSelect2 = false;
        }

        // 3. Preview ikut update
        updateGolonganDarahSpan(golId);

        // 4. Sinkronkan hidden input & form elements
        $('#id_gol_darah').val(golId);
        $('[name="id_gol_darah"]').val(golId);

        // 5. Cek ulang kesesuaian kantong darah
        for (var i = 1; i <= 12; i++) {
            cekGoldarKantong(i);
        }
    }

    /* Expose function untuk perubahan programmatic (load, reset, dll) */
    window.setGolonganDarahProgrammatic = function (golId) {
        goldarProgrammatic = true;
        lastGolId = golId ? String(golId) : '';
        syncGoldarahValues(lastGolId);
        goldarProgrammatic = false;
    };

    function bindGoldarahChange() {
        console.log('[bindGoldarahChange] Function called');
        var $select = $('#selectgoldarah');
        if (!$select.length) {
            $select = $('select[name="goldarah"]');
        }
        if (!$select.length) {
            console.warn('[bindGoldarahChange] Select element not found');
            return;
        }

        lastGolId = $select.val() || '';
        console.log('[bindGoldarahChange] Initial lastGolId:', lastGolId);

        $select.on('change', async function () {
            if (updatingSelect2) {
                return;
            }

            var golId = $(this).val();
            console.log('[selectgoldarah] Change event fired. lastGolId:', lastGolId, 'golId:', golId);

            /* Jika perubahan dari programmatic, sinkronkan dan lewati konfirmasi */
            if (goldarProgrammatic) {
                console.log('[selectgoldarah] Programmatic change, skipping confirm');
                goldarProgrammatic = false;
                lastGolId = golId;
                syncGoldarahValues(golId);
                return;
            }

            /* Jika nilai sama dengan sebelumnya, abaikan */
            if (golId === lastGolId) {
                return;
            }

            /* Konfirmasi perubahan manual golongan darah pasien jika nilai awal sudah ada */
            if (golId && lastGolId && golId !== lastGolId) {
                console.log('[selectgoldarah] Manual change detected, showing confirm');
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
                    console.log('[selectgoldarah] User cancelled, reverting to:', lastGolId);
                    /* Batal: kembalikan ke nilai sebelumnya */
                    goldarProgrammatic = true;
                    syncGoldarahValues(lastGolId);
                    goldarProgrammatic = false;
                    return;
                }

                console.log('[selectgoldarah] User confirmed change to:', golId);
                lastGolId = golId;
            } else {
                lastGolId = golId;
            }

            // Jalankan sinkronisasi lengkap:
            // 1. Value asli select berubah
            // 2. Select2 refresh
            // 3. Preview ikut update
            syncGoldarahValues(golId);
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
            var $select = $('#selectgoldarah');
            if (!$select.length) {
                $select = $('select[name="goldarah"]');
            }
            var currentGoldar = $select.val() || '';

            // DEBUG SEMENTARA: Cek value dan text selectgoldarah sebelum submit
            console.log('[DEBUG SUBMIT] #selectgoldarah val():', currentGoldar);
            console.log('[DEBUG SUBMIT] #selectgoldarah option:selected text():', $select.find('option:selected').text().trim());

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
        
        var $select = $('#selectgoldarah');
        if (!$select.length) {
            $select = $('select[name="goldarah"]');
        }
        var initialGolId = $select.val() || '';

        // Sinkronisasi awal saat halaman edit dibuka agar Select2 membaca ulang dan preview sesuai database
        if (initialGolId) {
            syncGoldarahValues(initialGolId);
        } else {
            updateGolonganDarahSpan('');
        }

        bindGoldarahChange();
        autofillPasien();
        bindKantongLookup();
        bindFormSubmit();
        initFilledKantong();
        console.log('[request-edit.js] Initialization complete. Initial goldarah:', initialGolId);
    });
})(jQuery);