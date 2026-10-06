/* ==========================================================================
   SIMPELDAR - Request List JavaScript
   Requires: jQuery, DataTables, SweetAlert2, app.js (SwalHelper)
   ========================================================================== */
(function ($) {
    'use strict';

    var cfg = window.REQUEST_LIST_CONFIG || {};
    var $table = $('#tabelPermintaan');
    var dtInstance = null;

    var statusBadgeMap = {
        'Permintaan': 'primary',
        'Sedang Proses': 'warning',
        'Darah Siap': 'success',
        'Perlu Donor': 'danger',
        'Sampel Baru': 'info',
        'Incompatible': 'secondary',
        'Masa Simpan Habis': 'warning',
        'Belum Diambil': 'info',
        'Sudah Habis': 'dark'
    };

    function htmlEscape(str) {
        return String(str).replace(/[&<>"']/g, function (m) {
            return ({ '&': '&', '<': '<', '>': '>', '"': '"', "'": "'" })[m];
        });
    }

    function buildActions(row) {
        var noPermintaan = htmlEscape(row[0]);
        var mr = htmlEscape(row[1]);
        var statusTerima = row[12];
        var isAccepted = (String(statusTerima) === '1');

        var btns = '';
        btns += '<button type="button" class="btn btn-sm btn-outline-info tblCetak" data-no="' + noPermintaan + '" data-bs-toggle="tooltip" title="Cetak Bon Darah"><i class="fas fa-print"></i></button>';
        btns += '<button type="button" class="btn btn-sm btn-outline-warning tblCetak1" data-no="' + noPermintaan + '" data-bs-toggle="tooltip" title="Cetak Form Darah"><i class="fas fa-file-medical"></i></button>';
        btns += '<button type="button" class="btn btn-sm btn-outline-primary tblView" data-no="' + noPermintaan + '" data-bs-toggle="tooltip" title="Lihat Data"><i class="fas fa-eye"></i></button>';
        btns += '<button type="button" class="btn btn-sm btn-outline-success tblEdit" data-no="' + noPermintaan + '" data-bs-toggle="tooltip" title="Edit Data"><i class="fas fa-edit"></i></button>';

        return '<div class="btn-group btn-group-sm" role="group">' + btns + '</div>';
    }

    function showFlashNotifications() {
        var search = window.location.search;
        if (search.indexOf('success=1') > -1 && window.SwalHelper) {
            SwalHelper.success('Berhasil!', 'Data permintaan darah berhasil diperbarui.');
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.pathname);
            }
        }
        if (search.indexOf('error=1') > -1 && window.SwalHelper) {
            SwalHelper.error('Gagal!', 'Terjadi kesalahan saat memperbarui data. Coba lagi.');
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.pathname);
            }
        }
    }

    function initDataTable() {
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
                    $('#statTotal').text(json.recordsTotal || 0);
                    return json.data;
                },
                error: function (xhr, error, thrown) {
                    console.error('DataTables error:', error, thrown);
                    if (window.SwalHelper) {
                        SwalHelper.error('Gagal memuat data', 'Periksa koneksi atau coba refresh halaman.');
                    }
                }
            },
            columnDefs: [
                {
                    targets: 7,
                    width: '140px',
                    className: 'text-center col-goldar'
                }
            ],
            columns: [
                { data: 0, className: 'font-monospace' },
                { data: 1, className: 'text-center' },
                { data: 2 },
                { data: 3 },
                { data: 4 },
                { data: 5 },
                { data: 6 },
                { data: 7, className: 'text-center', render: function (data) {
                    if (!data || data === '-') return '<span class="text-muted">-</span>';
                    var cls = window.getGolonganDarahBadgeClass ? getGolonganDarahBadgeClass(data) : 'danger';
                    // Khusus "Tidak diketahui": paksa nowrap, padding, min-width agar muat di satu baris
                    var isUnknown = String(data).toLowerCase().trim() === 'tidak diketahui';
                    var style = isUnknown ? ' style="white-space:nowrap;display:inline-flex;align-items:center;justify-content:center;padding:4px 8px;min-width:110px;border-radius:.375rem;"' : '';
                    return '<span class="badge bg-' + cls + '"' + style + '>' + htmlEscape(data) + '</span>';
                }},
                { data: 8 },
                { 
                    data: 9,
                    render: function (data) {
                        if (!data || data === '-') {
                            return '<span class="text-muted">-</span>';
                        }

                        return '<div class="small">' + data + '</div>';
                    }
                },
                { 
                    data: 10,
                    className: 'text-center',
                    render: function (data) {
                        if (!data || data === '-') {
                            return '-';
                        }

                        return htmlEscape(data);
                    }
                 },
                { 
                    data: 11,
                    className: 'text-center',
                    render: function (data) {
                        if (!data || data === '-') {
                            return '-';
                        }

                        return htmlEscape(data);
                    }
                 },
                { 
                    data: 12,
                    className: 'text-center',
                    orderable: false,
                    searchable: false,

                    render: function (data, type, row) {
                        return buildActions(row);
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

    function bindActions() {
        $table.on('click', '.tblEdit', function () {
            window.location.href = cfg.editUrl + $(this).data('no');
        });
        $table.on('click', '.tblView', function () {
            window.location.href = cfg.detailUrl + $(this).data('no');
        });
        $table.on('click', '.tblCetak', function () {
            window.open(cfg.bonUrl + $(this).data('no'), '_blank');
        });
        $table.on('click', '.tblCetak1', function () {
            window.open(cfg.formUrl + $(this).data('no'), '_blank');
        });
        $table.on('click', '.tblCetakDetail', function () {
            window.open(cfg.detailDarahUrl + $(this).data('no'), '_blank');
        });
        $table.on('click', '.tblCetakHasil', function () {
            window.open(cfg.hasilUrl + $(this).data('no'), '_blank');
        });

        $table.on('click', '.tblTerima', function () {
            var $btn = $(this);
            var noPermintaan = $btn.data('no');
            var noMr = $btn.data('mr');

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
                            if (window.SwalHelper) SwalHelper.success('Diterima', 'Form telah diterima.');
                            if (dtInstance) dtInstance.ajax.reload(null, false);
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

    $(function () {
        initDataTable();
        bindActions();
        showFlashNotifications();

        $('#btnRefreshTable').on('click', function () {
            if (dtInstance) dtInstance.ajax.reload();
        });
    });
})(jQuery);