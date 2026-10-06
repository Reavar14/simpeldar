/* ==========================================================================
   SIMPELDAR - Rawat Inap Page JavaScript
   Requires: jQuery, DataTables, Select2, Flatpickr, SweetAlert2, app.js (SwalHelper)
   ========================================================================== */
(function ($) {
    'use strict';

    console.log("rawat-inap.js loaded - version:", new Date().toISOString());

    var cfg = window.RAWAT_INAP_CONFIG || {};
    var $table = $('#tabelRawatInap');
    var dtInstance = null;

    /* Status badge mapping */
    var statusColorMap = {};

    function getStatusText(label) {
        return htmlEscape(label);
    }

    function getKelengkapanText(label) {
        return htmlEscape(label);
    }

    function htmlEscape(str) {
        return String(str).replace(/[&<>"']/g, function (m) {
            return ({ '&': '&', '<': '<', '>': '>', '"': '"', "'": "'" })[m];
        });
    }

    function formatGolDarah(val) {
        if (!val || val === '-') return '<span class="text-muted">-</span>';
        var cls = (window.getGolonganDarahBadgeClass) ? getGolonganDarahBadgeClass(val) : 'danger';
        return '<span class="badge bg-' + cls + '">' + htmlEscape(val) + '</span>';
    }

    function buildActions(noPermintaan, mr, statusTerima) {
        var btns = '';
        btns += '<a href="' + cfg.editUrl + htmlEscape(noPermintaan) + '" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></a>';
        return '<div class="btn-group btn-group-sm" role="group">' + btns + '</div>';
    }

    function initDataTable() {
        if (dtInstance) {
            dtInstance.ajax.url(cfg.ajaxUrl).load();
            return;
        }

        dtInstance = $table.DataTable({
            processing: true,
            serverSide: true,
            responsive: false,
            autoWidth: false,
            ordering: true,
            order: [[0, 'desc']],
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
                    console.log("AJAX URL:", cfg.ajaxUrl);
                    console.log("AJAX response:", json);
                    $('#statTotal').text(json.recordsTotal || 0);
                    return json.data;
                },
                error: function (xhr, error, thrown) {
                    console.error('DataTables error:', error, thrown);
                    console.error('Response text:', xhr.responseText);
                    if (window.SwalHelper) {
                        SwalHelper.error('Gagal memuat data', 'Periksa koneksi atau coba refresh halaman.');
                    }
                }
            },
            columns: [
                { data: 0, className: 'text-center text-muted' },
                { data: 1, className: 'text-center' },
                { data: 2 },
                { data: 3 },
                { data: 4 },
                { data: 5 },
                { data: 6 },
                { data: 7, className: 'text-center', render: function (data) { return formatGolDarah(data); } },
                { data: 8 },
                { data: 10, className: 'text-center', render: function (data) { return getStatusText(data); } },
                { data: 11, className: 'text-center', render: function (data) { return getKelengkapanText(data); } },
                { data: 12, className: 'text-center', orderable: false, searchable: false,
                    render: function (data, type, row) {
                        return buildActions(row[0], row[1], data);
                    }
                }
            ],
            initComplete: function () {
                console.log("DataTables columns count:", this.api().columns().count());
                console.log('TH count:', $('#tabelRawatInap thead th').length);
                console.log('Columns config:', this.api().settings()[0].aoColumns.length);
                var $wrapper = $(this.api().table().container());
                $wrapper.find('.dataTables_filter input').addClass('form-control form-control-sm');
                $wrapper.find('.dataTables_length select').addClass('form-select form-select-sm');
            },
            drawCallback: function () {
                $table.find('[data-bs-toggle="tooltip"]').each(function () {
                    if (window.bootstrap && bootstrap.Tooltip) {
                        new bootstrap.Tooltip(this);
                    }
                });
            }
        });
    }

    $(function () {
        /* Init DataTable */
        initDataTable();

        /* Events */
        $('#btnRefreshTable').on('click', function () {
            if (dtInstance) dtInstance.ajax.reload();
        });

        /* Auto-load flash toast if any */
        if (window.SwalHelper) {
            $('#flashToasts [data-toast]').each(function () {
                var $t = $(this);
                SwalHelper.toast($t.text().trim(), $t.data('toast') || 'info');
            });
        }
    });
})(jQuery);