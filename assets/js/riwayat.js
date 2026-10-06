/* ==========================================================================
   SIMPELDAR - Riwayat Pasien Page JavaScript
   Requires: jQuery, DataTables, Select2, Flatpickr, SweetAlert2, app.js (SwalHelper)
   ========================================================================== */
(function ($) {
    'use strict';

    var cfg = window.RIWAYAT_CONFIG || {};
    var $table = $('#tabelRiwayat');
    var dtInstance = null;

    function getStatusBadge(label) {
        var cls = 'secondary';
        var dark = '';
        switch (label) {
            case 'Permintaan': cls = 'primary'; break;
            case 'Sedang Proses': cls = 'warning'; dark = ' text-dark'; break;
            case 'Darah Siap': cls = 'success'; break;
            case 'Perlu Donor': cls = 'danger'; break;
            case 'Sampel Baru': cls = 'info'; dark = ' text-dark'; break;
            case 'Incompatible': cls = 'secondary'; break;
            case 'Masa Simpan Habis': cls = 'warning'; dark = ' text-dark'; break;
            case 'Belum Diambil': cls = 'info'; dark = ' text-dark'; break;
            case 'Sudah Habis': cls = 'dark'; break;
        }
        return '<span class="badge bg-' + cls + dark + '">' + htmlEscape(label) + '</span>';
    }

    function formatGolDarah(val) {
        if (!val || val === '-') return '<span class="text-muted">-</span>';
        return '<span class="badge bg-danger">' + htmlEscape(val) + '</span>';
    }

    function htmlEscape(str) {
        return String(str).replace(/[&<>"']/g, function (m) {
            return ({ '&': '&', '<': '<', '>': '>', '"': '"', "'": "'" })[m];
        });
    }

    function buildActions(noPermintaan) {
        var btns = '';
        btns += '<a href="' + cfg.editUrl + htmlEscape(noPermintaan) + '" class="btn btn-sm btn-outline-warning" data-bs-toggle="tooltip" title="Edit Permintaan"><i class="fas fa-edit"></i></a> ';
        btns += '<a href="' + cfg.formUrl + htmlEscape(noPermintaan) + '" target="_blank" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="Cetak Form Darah"><i class="fas fa-print"></i></a>';
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
            responsive: true,
            autoWidth: false,
            ordering: true,
            order: [[1, 'desc']],
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
                    $.extend(d, gatherFilters());
                },
                dataSrc: function (json) {
                    $('#statTotal').text(json.recordsTotal || 0);
                    $('#statKunjungan').text(json.kunjungan || 0);
                    return json.data;
                },
                error: function (xhr, error, thrown) {
                    console.error('DataTables error:', error, thrown);
                    if (window.SwalHelper) {
                        SwalHelper.error('Gagal memuat data', 'Periksa koneksi atau coba refresh halaman.');
                    }
                }
            },
            columns: [
                { data: 0, className: 'text-center text-muted' },
                { data: 1, className: 'text-center font-monospace' },
                { data: 2, className: 'text-center' },
                { data: 3 },
                { data: 4 },
                { data: 5 },
                { data: 6 },
                { data: 7, className: 'text-center', render: function (data) { return formatGolDarah(data); } },
                { data: 8 },
                { data: 9, className: 'text-center', render: function (data) { return getStatusBadge(data); } },
                { data: 10, className: 'text-center', orderable: false, searchable: false,
                    render: function (data, type, row) {
                        return buildActions(row[1]);
                    }
                }
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

    function gatherFilters() {
        var params = {};
        $('#formRiwayat').find('input, select').each(function () {
            var $el = $(this);
            var val = $el.val();
            if (val !== '' && val !== null) {
                params[$el.attr('name')] = val;
            }
        });
        return params;
    }

    $(function () {
        /* Init Flatpickr datepicker */
        if (window.flatpickr) {
            $('.datepicker').each(function () {
                if (!this._flatpickr) {
                    flatpickr(this, { enableTime: false, dateFormat: 'Y-m-d', altInput: true, altFormat: 'd/m/Y' });
                }
            });
        }

        /* Init DataTable */
        initDataTable();

        /* Events */
        $('#btnRefreshTable').on('click', function () {
            if (dtInstance) dtInstance.ajax.reload();
        });

        /* Enter key on filter inputs */
        $('#formRiwayat input').on('keypress', function (e) {
            if (e.which === 13) {
                e.preventDefault();
                $('#formRiwayat').submit();
            }
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