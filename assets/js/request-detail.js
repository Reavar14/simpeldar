/* ==========================================================================
   SIMPELDAR - Request Detail JavaScript
   Requires: jQuery, SweetAlert2, app.js (SwalHelper)
   ========================================================================== */
(function ($) {
    'use strict';

    var cfg = window.REQUEST_DETAIL_CONFIG || {};

    function bindTerima() {
        $('.tblTerima').on('click', function () {
            var noPermintaan = $(this).data('no-permintaan');
            var noMr = $(this).data('no-mr');

            if (!window.SwalHelper || typeof Swal === 'undefined') {
                if (confirm('Apakah anda ingin menerima form?')) {
                    doPost();
                }
                return;
            }

            SwalHelper.confirm('Terima Sampel Darah?', 'Apakah Anda ingin menerima form permintaan "' + noPermintaan + '"?')
                .then(function (result) {
                    if (!result.isConfirmed) return;
                    SwalHelper.loading('Memproses penerimaan...');
                    doPost();
                });

            function doPost() {
                $.ajax({
                    url: cfg.terimaUrl,
                    method: 'POST',
                    data: { no_permintaan: noPermintaan, no_mr: noMr },
                    success: function (resp) {
                        var ok = false;
                        try { ok = resp && (resp.success === true || resp === '1'); } catch (e) {}
                        if (ok) {
                            if (window.SwalHelper) {
                                SwalHelper.success('Diterima', 'Sampel darah berhasil diterima.').then(function () {
                                    location.reload();
                                });
                            } else {
                                location.reload();
                            }
                        } else {
                            if (window.SwalHelper) SwalHelper.error('Gagal', 'Terjadi kesalahan saat menyimpan.');
                        }
                    },
                    error: function () {
                        if (window.SwalHelper) SwalHelper.error('Gagal', 'Terjadi kesalahan jaringan.');
                    }
                });
            }
        });
    }

    function bindCetakBon() {
        $(document).on('click', '.tblCetakBon', function () {
            var no = $(this).data('no-permintaan');
            window.open(cfg.bonUrl + no, '_blank');
        });
    }

    $(function () {
        bindTerima();
        bindCetakBon();
    });
})(jQuery);