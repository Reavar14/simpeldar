/* ==========================================================================
   SIMPELDAR - Billing Module JavaScript
   Requires: jQuery, DataTables, Select2, Flatpickr, SweetAlert2, app.js (SwalHelper)
   ========================================================================== */
(function ($) {
    'use strict';

    var cfg = window.BILLING_CONFIG || {};
    var $table = $('#tabelBilling');
    var dtInstance = null;

    function htmlEscape(str) {
        return String(str).replace(/[&<>"']/g, function (m) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[m];
        });
    }

    function getStatusBadge(label) {
        if (label === 'Sudah Billing') {
            return '<span class="badge bg-success">Sudah Billing</span>';
        }
        return '<span class="badge bg-warning text-dark">Belum Billing</span>';
    }

    function buildActions(row) {
        var noPermintaan = htmlEscape(row[9]);

        var btns = '';
        btns += '<button type="button" class="btn btn-sm btn-outline-primary tblView" data-no="' + noPermintaan + '" data-bs-toggle="tooltip" title="Lihat Detail"><i class="fas fa-eye"></i></button>';
        btns += '<button type="button" class="btn btn-sm btn-outline-success tblBilling" data-no="' + noPermintaan + '" data-bs-toggle="tooltip" title="Proses Billing"><i class="fas fa-file-invoice-dollar"></i></button>';

        return '<div class="btn-group btn-group-sm" role="group">' + btns + '</div>';
    }

    function gatherFilter() {
        var params = {};
        $('#formBilling').find('input, select').each(function () {
            var $el = $(this);
            var val = $el.val();
            if (val !== '' && val !== null) {
                params[$el.attr('name')] = val;
            }
        });
        return params;
    }

    function initDataTable() {
        dtInstance = $table.DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ordering: true,
            order: [[4, 'desc']],
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
                data: function (d) {
                    $.extend(d, gatherFilter());
                },
                dataSrc: function (json) {
                    $('#statTotal').text(json.recordsTotal || 0);
                    var billed = 0;
                    if (json.data && json.data.length) {
                        for (var i = 0; i < json.data.length; i++) {
                            if (json.data[i][8] === 'Sudah Billing') {
                                billed++;
                            }
                        }
                    }
                    $('#statBilled').text(billed);
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
                { data: 1, className: 'font-monospace' },
                { data: 2, className: 'text-center font-monospace' },
                { data: 3 },
                { data: 4, className: 'text-center' },
                { data: 5 },
                { data: 6 },
                { data: 7, className: 'text-center' },
                { data: 8, className: 'text-center', render: function (data) { return getStatusBadge(data); } },
                { data: 9, className: 'text-center', orderable: false, searchable: false,
                    render: function (data, type, row) { return buildActions(row); } }
            ],
            drawCallback: function () {
                $table.find('[data-bs-toggle="tooltip"]').each(function () {
                    if (window.bootstrap && bootstrap.Tooltip) {
                        new bootstrap.Tooltip(this);
                    }
                });
            },
            initComplete: function () {
                var $wrapper = $(this.api().table().container());
                $wrapper.find('.dataTables_filter input').addClass('form-control form-control-sm');
                $wrapper.find('.dataTables_length select').addClass('form-select form-select-sm');
            }
        });
    }

    function bindActions() {
        $table.on('click', '.tblView', function () {
            window.location.href = cfg.detailUrl + $(this).data('no');
        });

        $table.on('click', '.tblBilling', function () {
            var noPermintaan = $(this).data('no');
            if (window.SwalHelper) {
                SwalHelper.toast('Proses billing untuk "' + noPermintaan + '" belum tersedia.', 'info');
            }
        });
    }

    $(function () {
        initDataTable();
        bindActions();

        $('#btnRefreshBilling').on('click', function () {
            if (dtInstance) dtInstance.ajax.reload();
        });

        $('#formBilling').on('submit', function (e) {
            e.preventDefault();
            if (dtInstance) dtInstance.ajax.reload();
        });

        $('#formBilling').find('input').on('keypress', function (e) {
            if (e.which === 13) {
                e.preventDefault();
                $(this).closest('form').submit();
            }
        });
    });
})(jQuery);