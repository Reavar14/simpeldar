/* ==========================================================================
   SIMPELDAR - Laporan Module JavaScript
   Requires: jQuery, DataTables, Select2, Flatpickr, SweetAlert2, app.js (SwalHelper)
   ========================================================================== */
(function ($) {
    'use strict';

    var cfg = window.LAPORAN_CONFIG || {};

    var dtPelayanan = null;
    var dtPermintaan = null;
    var dtJenis = null;
    var dtBilling = null;

    function htmlEscape(str) {
        return String(str).replace(/[&<>"']/g, function (m) {
            return ({ '&': '&', '<': '<', '>': '>', '"': '"', "'": "'" })[m];
        });
    }

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

    function gatherPelayanan() {
        var params = {};
        $('#formPelayanan').find('input, select').each(function () {
            var $el = $(this);
            var val = $el.val();
            if (val !== '' && val !== null) {
                params[$el.attr('name')] = val;
            }
        });
        return params;
    }

    function gatherPermintaan() {
        var params = {};
        $('#formPermintaan').find('input, select').each(function () {
            var $el = $(this);
            var val = $el.val();
            if (val !== '' && val !== null) {
                params[$el.attr('name')] = val;
            }
        });
        return params;
    }

    function gatherJenis() {
        var params = {};
        $('#formJenis').find('input, select').each(function () {
            var $el = $(this);
            var val = $el.val();
            if (val !== '' && val !== null) {
                params[$el.attr('name')] = val;
            }
        });
        return params;
    }

    function gatherBilling() {
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

    function initPelayanan() {
        if (dtPelayanan) {
            dtPelayanan.ajax.reload();
            return;
        }

        dtPelayanan = $('#tabelPelayanan').DataTable({
            processing: true,
            serverSide: false, // model returns full result
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
                url: cfg.pelayananUrl,
                type: 'POST',
                data: function (d) {
                    $.extend(d, gatherPelayanan());
                },
                dataSrc: function (json) {
                    $('#statPelayanan').text(json.jml_tindakan || 0);
                    return json.data;
                },
                error: function (xhr, error, thrown) {
                    console.error('Pelayanan DataTables error:', error, thrown);
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
                { data: 7 },
                { data: 8, className: 'text-center', render: function (data) { return getStatusBadge(data); } }
            ],
            drawCallback: function () {
                $('#tabelPelayanan').find('[data-bs-toggle="tooltip"]').each(function () {
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

    function initPermintaan() {
        if (dtPermintaan) {
            dtPermintaan.ajax.reload();
            return;
        }

        dtPermintaan = $('#tabelPermintaan').DataTable({
            processing: true,
            serverSide: false,
            responsive: true,
            autoWidth: false,
            ordering: true,
            order: [[1, 'asc']],
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
                url: cfg.permintaanUrl,
                type: 'POST',
                data: function (d) {
                    $.extend(d, gatherPermintaan());
                },
                dataSrc: function (json) {
                    return json.data;
                },
                error: function (xhr, error, thrown) {
                    console.error('Permintaan DataTables error:', error, thrown);
                    if (window.SwalHelper) {
                        SwalHelper.error('Gagal memuat data', 'Periksa koneksi atau coba refresh halaman.');
                    }
                }
            },
            columns: [
                { data: 0, className: 'text-center text-muted' },
                { data: 1 },
                { data: 2, className: 'text-center' }
            ],
            initComplete: function () {
                var $wrapper = $(this.api().table().container());
                $wrapper.find('.dataTables_filter input').addClass('form-control form-control-sm');
                $wrapper.find('.dataTables_length select').addClass('form-select form-select-sm');
            }
        });
    }

    function initJenis() {
        if (dtJenis) {
            dtJenis.ajax.reload();
            return;
        }

        dtJenis = $('#tabelJenis').DataTable({
            processing: true,
            serverSide: false,
            responsive: true,
            autoWidth: false,
            ordering: true,
            order: [[1, 'asc']],
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
                url: cfg.jenisUrl,
                type: 'POST',
                data: function (d) {
                    $.extend(d, gatherJenis());
                },
                dataSrc: function (json) {
                    return json.data;
                },
                error: function (xhr, error, thrown) {
                    console.error('Jenis DataTables error:', error, thrown);
                    if (window.SwalHelper) {
                        SwalHelper.error('Gagal memuat data', 'Periksa koneksi atau coba refresh halaman.');
                    }
                }
            },
            columns: [
                { data: 0, className: 'text-center text-muted' },
                { data: 1 },
                { data: 2, className: 'text-center' }
            ],
            initComplete: function () {
                var $wrapper = $(this.api().table().container());
                $wrapper.find('.dataTables_filter input').addClass('form-control form-control-sm');
                $wrapper.find('.dataTables_length select').addClass('form-select form-select-sm');
            }
        });
    }

    function initBilling() {
        if (dtBilling) {
            dtBilling.ajax.reload();
            return;
        }

        dtBilling = $('#tabelBilling').DataTable({
            processing: true,
            serverSide: false,
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
                url: cfg.billingUrl,
                type: 'POST',
                data: function (d) {
                    $.extend(d, gatherBilling());
                },
                dataSrc: function (json) {
                    $('#statKantong').text(json.sum_kantong || 0);
                    $('#statBilled').text(json.sum_billed || 0);
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
                { data: 2, className: 'text-center' },
                { data: 3 },
                { data: 4 },
                { data: 5, className: 'text-center' },
                { data: 6, className: 'text-center' }
            ],
            initComplete: function () {
                var $wrapper = $(this.api().table().container());
                $wrapper.find('.dataTables_filter input').addClass('form-control form-control-sm');
                $wrapper.find('.dataTables_length select').addClass('form-select form-select-sm');
            }
        });
    }

    /* Initialize tab when shown */
    function onTabShow(e) {
        var $tab = $(e.target);
        var target = $tab.attr('data-bs-target') || $tab.attr('href');
        if (target === '#panelPelayanan') {
            initPelayanan();
        } else if (target === '#panelPermintaan') {
            initPermintaan();
        } else if (target === '#panelJenis') {
            initJenis();
        } else if (target === '#panelBilling') {
            initBilling();
        }
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

        /* Init Select2 */
        if ($.fn && $.fn.select2) {
            $('.select2').each(function () {
                var $el = $(this);
                if (!$el.hasClass('select2-hidden-accessible')) {
                    var opts = { theme: 'bootstrap-5', width: '100%' };
                    if ($el.data('placeholder')) {
                        opts.placeholder = $el.data('placeholder');
                        opts.allowClear = true;
                    }
                    $el.select2(opts);
                }
            });
        }

        /* Tab events */
        $('#laporanTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', onTabShow);

        /* Initial tab */
        initPelayanan();

        /* Refresh buttons */
        $(document).on('click', '.btnRefreshPelayanan', function () { if (dtPelayanan) dtPelayanan.ajax.reload(); });
        $(document).on('click', '.btnRefreshPermintaan', function () { if (dtPermintaan) dtPermintaan.ajax.reload(); });
        $(document).on('click', '.btnRefreshJenis', function () { if (dtJenis) dtJenis.ajax.reload(); });
        $(document).on('click', '.btnRefreshBilling', function () { if (dtBilling) dtBilling.ajax.reload(); });

        /* Enter key on filter inputs */
        $('#formPelayanan, #formPermintaan, #formJenis, #formBilling').find('input').on('keypress', function (e) {
            if (e.which === 13) {
                e.preventDefault();
                $(this).closest('form').submit();
            }
        });

        /* Auto-load flash toast */
        if (window.SwalHelper) {
            $('#flashToasts [data-toast]').each(function () {
                var $t = $(this);
                SwalHelper.toast($t.text().trim(), $t.data('toast') || 'info');
            });
        }
    });
})(jQuery);