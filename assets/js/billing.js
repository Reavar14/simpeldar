/* ==========================================================================
   SIMPELDAR - Billing Module JavaScript
   Requires: jQuery, DataTables, SweetAlert2, app.js (SwalHelper)
   ========================================================================== */
(function ($) {
    'use strict';

    var cfg = window.BILLING_CONFIG || {};
    var $table = $('#tabelBilling');
    var dtInstance = null;

    function initDataTable() {
        dtInstance = $table.DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ordering: true,
            order: [[3, 'desc']],
            language: {
                search: 'Cari:',
                searchPlaceholder: 'Kata kunci...',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data untuk ditampilkan',
                infoFiltered: '(disaring dari _MAX_ data keseluruhan)',
                zeroRecords: 'Data tidak ditemukan',
                emptyTable: 'Belum ada data dalam tabel',
                paginate: {
                    first: '<i class="fas fa-angles-left"></i>',
                    last: '<i class="fas fa-angles-right"></i>',
                    next: '<i class="fas fa-angle-right"></i>',
                    previous: '<i class="fas fa-angle-left"></i>'
                },
                processing: '<span class="spinner-border spinner-border-sm me-2"></span> Memuat data...'
            },
            ajax: {
                url: cfg.ajaxUrl,
                type: 'POST',
                dataSrc: function (json) {
                    return json.data;
                },
                error: function (xhr, error, thrown) {
                    console.error('Billing DataTables error:', error, thrown);
                    if (window.SwalHelper) {
                        SwalHelper.error('Gagal memuat data', 'Periksa koneksi atau coba refresh halaman.');
                    }
                }
            },
            columns: [
                { data: 0, className: 'text-center text-muted' },
                { data: 1, className: 'text-center font-monospace' },
                { data: 2 },
                { data: 3, className: 'text-center' },
                { data: 4, className: 'text-center' }
            ],
            initComplete: function () {
                var $wrapper = $(this.api().table().container());
                $wrapper.find('.dataTables_filter input').addClass('form-control form-control-sm');
                $wrapper.find('.dataTables_length select').addClass('form-select form-select-sm');
            }
        });
    }

    $(function () {
        initDataTable();

        $('#btnRefreshBilling').on('click', function () {
            if (dtInstance) dtInstance.ajax.reload();
        });
    });
})(jQuery);