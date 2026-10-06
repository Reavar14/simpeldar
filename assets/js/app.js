/* ==========================================================================
   SIMPELDAR - Custom Application JavaScript
   Requires: jQuery 3.7+, Bootstrap 5.3, DataTables, Select2, SweetAlert2,
             Flatpickr
   ========================================================================== */

(function ($) {
    'use strict';

    /* ======================================================================
       1. DataTables Default Configuration
       ====================================================================== */
    if ($.fn && $.fn.dataTable) {
        $.extend(true, $.fn.dataTable.defaults, {
            responsive: true,
            autoWidth: false,
            lengthMenu: [10, 25, 50, 100],
            pageLength: 10,
            ordering: true,
            order: [],
            searching: true,
            processing: true,
            stateSave: false,
            language: {
                search: 'Cari:',
                searchPlaceholder: 'Kata kunci...',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data untuk ditampilkan',
                infoFiltered: '(disaring dari _MAX_ data keseluruhan)',
                infoPostFix: '',
                zeroRecords: 'Data tidak ditemukan',
                emptyTable: 'Belum ada data dalam tabel',
                paginate: {
                    first: '<i class="fas fa-angles-left"></i>',
                    last: '<i class="fas fa-angles-right"></i>',
                    next: '<i class="fas fa-angle-right"></i>',
                    previous: '<i class="fas fa-angle-left"></i>'
                },
                aria: {
                    sortAscending: ': klik untuk mengurutkan menaik',
                    sortDescending: ': klik untuk mengurutkan menurun'
                },
                processing: '<span class="spinner-border spinner-border-sm me-2"></span> Memuat data...'
            },
            initComplete: function (settings, json) {
                var $wrapper = $(this).closest('.dataTables_wrapper');
                $wrapper.find('.dataTables_filter input').addClass('form-control form-control-sm');
                $wrapper.find('.dataTables_length select').addClass('form-select form-select-sm');
            }
        });
    }

    /* ======================================================================
       2. Sidebar Toggle (Show / Hide model)
       sidebarOpen: true  = sidebar tampil (250px, icon + teks)
                    false = sidebar tersembunyi sepenuhnya
       ====================================================================== */
    var SIDEBAR_KEY = 'simpeldar_sidebar_open';

    function isMobile() {
        return window.matchMedia('(max-width: 991.98px)').matches;
    }

    function getStoredState() {
        try {
            return localStorage.getItem(SIDEBAR_KEY) === 'true';
        } catch (e) {
            return false;
        }
    }

    function storeState(open) {
        try {
            localStorage.setItem(SIDEBAR_KEY, open ? 'true' : 'false');
        } catch (e) {
            /* storage unavailable */
        }
    }

    function openSidebar() {
        $('body').addClass('sidebar-open');
        storeState(true);

        if (isMobile()) {
            $('#sidebarOverlay').addClass('show');
            $('body').css('overflow', 'hidden');
        }
    }

    function closeSidebar() {
        $('body').removeClass('sidebar-open');
        $('#sidebarOverlay').removeClass('show');
        $('body').css('overflow', '');
        storeState(false);
    }

    function toggleSidebar() {
        if ($('body').hasClass('sidebar-open')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    }

    /* Restore persisted state on load */
    if (getStoredState()) {
        openSidebar();
    }

    /* ======================================================================
       3. Active Menu Otomatis Berdasarkan URL
       ====================================================================== */
    function setActiveMenu() {
        var path = window.location.pathname.replace(/^\/+/, '');
        var hash = window.location.hash;

        /* normalize: remove trailing slash */
        if (path.charAt(path.length - 1) === '/') {
            path = path.slice(0, -1);
        }

        var bestMatch = null;
        var bestLen = 0;

        $('.app-sidebar .nav-item').each(function () {
            var $item = $(this);
            var href = $item.attr('href');

            if (!href || href === '#' || href.charAt(0) === '#') {
                return;
            }

            /* resolve absolute href to pathname */
            var link = document.createElement('a');
            link.href = href;
            var linkPath = link.pathname.replace(/^\/+/, '');

            if (linkPath.charAt(linkPath.length - 1) === '/') {
                linkPath = linkPath.slice(0, -1);
            }

            var score = 0;

            if (linkPath === path) {
                score = linkPath.length + 100;
            } else if (path.indexOf(linkPath + '/') === 0) {
                /* child route: e.g. /requestcontroller/detail matches /requestcontroller/list */
                score = linkPath.length;
            }

            if (score > bestLen) {
                bestLen = score;
                bestMatch = $item;
            }

            $item.removeClass('active');
        });

        if (bestMatch) {
            bestMatch.addClass('active');
        }

        /* fallback: hash match */
        if (!bestMatch && hash) {
            $('.app-sidebar .nav-item[href="' + hash + '"]').addClass('active');
        }
    }

    /* ======================================================================
       4. Bootstrap Tooltip Initialisation
       ====================================================================== */
    function initTooltips() {
        if (window.bootstrap && bootstrap.Tooltip) {
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
                var existing = bootstrap.Tooltip.getInstance(el);
                if (existing) {
                    existing.dispose();
                }
                new bootstrap.Tooltip(el, {
                    delay: { show: 250, hide: 80 },
                    trigger: 'hover focus'
                });
            });
        }
    }

    /* ======================================================================
       5. SweetAlert2 Helper
       ====================================================================== */
    var SwalHelper = {
        /**
         * Toast notification singkat.
         * @param {string} message - Pesan yang ditampilkan.
         * @param {string} type - success | error | warning | info | question.
         * @param {object} extra - Konfigurasi tambahan SweetAlert2.
         */
        toast: function (message, type, extra) {
            if (typeof Swal === 'undefined') {
                console.warn('[SwalHelper] SweetAlert2 not loaded.');
                return;
            }

            var config = {
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                title: message,
                icon: type || 'success',
                didOpen: function (toast) {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            };

            if (extra && typeof extra === 'object') {
                $.extend(config, extra);
            }

            return Swal.fire(config);
        },

        /**
         * Dialog konfirmasi dengan tombol Ya / Batal.
         * @param {string} title - Judul dialog.
         * @param {string} text - Deskripsi/pertanyaan.
         * @param {object} extra - Konfigurasi tambahan.
         * @returns {Promise} Promise SweetAlert2 result.
         */
        confirm: function (title, text, extra) {
            if (typeof Swal === 'undefined') {
                console.warn('[SwalHelper] SweetAlert2 not loaded.');
                return Promise.resolve({ isConfirmed: window.confirm(title + '\n' + (text || '')) });
            }

            var config = {
                title: title || 'Konfirmasi',
                text: text || 'Apakah Anda yakin?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-check me-1"></i> Ya',
                cancelButtonText: '<i class="fas fa-times me-1"></i> Batal',
                reverseButtons: true,
                focusCancel: true,
                allowOutsideClick: false
            };

            if (extra && typeof extra === 'object') {
                $.extend(config, extra);
            }

            return Swal.fire(config);
        },

        /**
         * Dialog konfirmasi tipe bahaya (hapus, hapus permanen, dll).
         * @param {string} title - Judul dialog.
         * @param {string} text - Deskripsi/pertanyaan.
         * @param {object} extra - Konfigurasi tambahan.
         * @returns {Promise} Promise SweetAlert2 result.
         */
        confirmDanger: function (title, text, extra) {
            var config = {
                title: title || 'Hapus Data?',
                text: text || 'Data yang dihapus tidak dapat dikembalikan.',
                icon: 'warning',
                confirmButtonText: '<i class="fas fa-trash me-1"></i> Hapus',
                confirmButtonColor: '#dc3545'
            };

            if (extra && typeof extra === 'object') {
                $.extend(config, extra);
            }

            return this.confirm(title, text, config);
        },

        /**
         * Dialog sukses.
         * @param {string} title - Judul.
         * @param {string} text - Pesan opsional.
         * @param {object} extra - Konfigurasi tambahan.
         */
        success: function (title, text, extra) {
            if (typeof Swal === 'undefined') {
                return;
            }

            var config = {
                title: title || 'Berhasil',
                text: text || '',
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#0d6efd'
            };

            if (extra && typeof extra === 'object') {
                $.extend(config, extra);
            }

            return Swal.fire(config);
        },

        /**
         * Dialog error.
         * @param {string} title - Judul.
         * @param {string} text - Pesan opsional.
         * @param {object} extra - Konfigurasi tambahan.
         */
        error: function (title, text, extra) {
            if (typeof Swal === 'undefined') {
                return;
            }

            var config = {
                title: title || 'Terjadi Kesalahan',
                text: text || 'Silakan coba lagi nanti.',
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc3545'
            };

            if (extra && typeof extra === 'object') {
                $.extend(config, extra);
            }

            return Swal.fire(config);
        },

        /**
         * Loading spinner tanpa tombol.
         * @param {string} title - Judul opsional.
         */
        loading: function (title) {
            if (typeof Swal === 'undefined') {
                return;
            }

            return Swal.fire({
                title: title || 'Memproses...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: function () {
                    Swal.showLoading();
                }
            });
        },

        /**
         * Tutup dialog aktif.
         */
        close: function () {
            if (typeof Swal !== 'undefined') {
                Swal.close();
            }
        }
    };

    /* Expose globally */
    window.SwalHelper = SwalHelper;

    /* ======================================================================
       5. Helper: Golongan Darah Badge Class Mapping
       ====================================================================== */
    window.getGolonganDarahBadgeClass = function (goldar) {
        if (!goldar || goldar === '-') {
            return 'secondary';
        }
        var normalized = String(goldar).toUpperCase().trim();
        normalized = normalized.replace(/\//g, '');

        var map = {
            'A+': 'purple',  'A-': 'purple',
            'B+': 'danger',  'B-': 'danger',
            'AB+': 'success', 'AB-': 'success',
            'O+': 'primary', 'O-': 'primary'
        };

        // Khusus: "Tidak diketahui" (case-insensitive) → abu-abu + class khusus
        var lower = String(goldar).toLowerCase().trim();
        if (lower === 'tidak diketahui') {
            return 'secondary goldar-unknown';
        }

        return map[normalized] || 'secondary';
    };

    /* ======================================================================
       6. Flatpickr Default Configuration
       ====================================================================== */
    function initFlatpickr() {
        if (!window.flatpickr) {
            return;
        }

        flatpickr.setDefaults({
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd/m/Y',
            allowInput: true,
            locale: {
                firstDayOfWeek: 1,
                weekdays: {
                    shorthand: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                    longhand: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
                },
                months: {
                    shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                    longhand: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                               'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
                }
            }
        });

        /* datepicker biasa */
        $('.datepicker, [data-flatpickr="date"]').each(function () {
            if (!this._flatpickr && !$(this).is('[readonly]')) {
                flatpickr(this, { enableTime: false });
            }
        });

        /* datetimepicker */
        $('.datetimepicker, [data-flatpickr="datetime"]').each(function () {
            if (!this._flatpickr && !$(this).is('[readonly]')) {
                flatpickr(this, {
                    enableTime: true,
                    dateFormat: 'Y-m-d H:i:S',
                    altFormat: 'd/m/Y H:i:S',
                    time_24hr: true
                });
            }
        });

        /* timepicker */
        $('.timepicker, [data-flatpickr="time"]').each(function () {
            if (!this._flatpickr) {
                flatpickr(this, {
                    noCalendar: true,
                    enableTime: true,
                    dateFormat: 'H:i:S',
                    altFormat: 'H:i:S',
                    time_24hr: true
                });
            }
        });
    }

    /* ======================================================================
       7. Select2 Default Configuration
       ====================================================================== */
    function initSelect2() {
        if (!$.fn || !$.fn.select2) {
            return;
        }

        var defaults = {
            theme: 'bootstrap-5',
            width: '100%',
            language: {
                noResults: function () {
                    return 'Data tidak ditemukan';
                },
                searching: function () {
                    return 'Mencari...';
                },
                inputTooShort: function (args) {
                    var remaining = args.minimum - args.input.length;
                    return 'Ketik ' + remaining + ' karakter lagi';
                },
                errorLoading: function () {
                    return 'Gagal memuat data';
                },
                loadingMore: function () {
                    return 'Memuat data lainnya...';
                },
                maximumSelected: function (args) {
                    return 'Anda hanya dapat memilih ' + args.maximum + ' item';
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        };

        $.extend($.fn.select2.defaults, defaults);

        /* auto-init elements */
        $('.select2, [data-select2]').each(function () {
            var $el = $(this);
            if (!$el.hasClass('select2-hidden-accessible')) {
                var opts = {};
                if ($el.data('placeholder')) {
                    opts.placeholder = $el.data('placeholder');
                    opts.allowClear = true;
                }
                $el.select2(opts);
            }
        });
    }

    /* ======================================================================
       8. Flash message -> SweetAlert toast (opsional)
       ====================================================================== */
    function showFlashToasts() {
        if (typeof Swal === 'undefined') {
            return;
        }

        var $container = $('#flashToasts');

        if (!$container.length) {
            return;
        }

        $container.find('[data-toast]').each(function () {
            var $toast = $(this);
            var type = $toast.data('toast') || 'info';
            var message = $toast.text().trim();

            if (message) {
                SwalHelper.toast(message, type);
            }
        });
    }

    /* ======================================================================
       9. Misc: number formatting, button loading state
       ====================================================================== */

    /**
     * Format angka dengan pemisah ribuan (id-ID).
     * @param {number|string} num
     * @returns {string}
     */
    window.formatNumber = function (num) {
        if (num === null || num === undefined || num === '') {
            return '';
        }
        return String(num).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    };

    /**
     * Set tombol ke state loading.
     * @param {jQuery} $btn - Tombol target.
     * @param {string} loadingText - Teks saat loading.
     */
    window.btnLoading = function ($btn, loadingText) {
        if (!$btn || !$btn.length) {
            return;
        }

        if ($btn.data('loading')) {
            return;
        }

        $btn.data('loading', true);
        $btn.data('original-html', $btn.html());
        $btn.prop('disabled', true);
        $btn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>' +
                  (loadingText || 'Memproses...'));
    };

    /**
     * Kembalikan tombol dari state loading.
     * @param {jQuery} $btn - Tombol target.
     */
    window.btnReset = function ($btn) {
        if (!$btn || !$btn.length) {
            return;
        }

        if (!$btn.data('loading')) {
            return;
        }

        $btn.data('loading', false);
        $btn.prop('disabled', false);
        $btn.html($btn.data('original-html'));
    };

    /* ======================================================================
       10. CSRF Token Helpers (global)
       Token source: <meta name="csrf-token-hash"> injected by layout.
       The csrf_cookie is HttpOnly (never read from document.cookie).
       CI3 regenerates the hash on every POST; the meta tag is kept in sync
       via the X-CSRF-Hash response header (post_controller hook) and the
       /auth/csrf endpoint (403 fallback).
       ====================================================================== */

    /**
     * Get current CSRF token name (CI3 config csrf_token_name).
     * @returns {string}
     */
    window.getCsrfTokenName = function () {
        return $('meta[name="csrf-token-name"]').attr('content') || 'csrf_token';
    };

    /**
     * Get current CSRF token hash from meta tag (NOT from cookie).
     * @returns {string}
     */
    window.getCsrfToken = function () {
        return $('meta[name="csrf-token-hash"]').attr('content') || '';
    };

    /**
     * Set both token name and hash in meta tags.
     */
    window.setCsrfToken = function (name, hash) {
        if (name) {
            $('meta[name="csrf-token-name"]').attr('content', name);
        }
        if (hash) {
            $('meta[name="csrf-token-hash"]').attr('content', hash);
        }
    };

    /**
     * Replace or append a query-string parameter.
     * Returns updated query string with param set to value.
     */
    function replaceOrAppendParam(qs, name, value) {
        var encName = encodeURIComponent(name);
        var re = new RegExp('(^|&)' + encName.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '=[^&]*');
        if (re.test(qs)) {
            return qs.replace(re, '$1' + encName + '=' + encodeURIComponent(value));
        }
        return qs + '&' + encName + '=' + encodeURIComponent(value);
    }

    /**
     * CSRF token refresh endpoint URL (injected via meta tag).
     */
    var CSRF_REFRESH_URL = $('meta[name="csrf-refresh-url"]').attr('content') || '';

    /**
     * Refresh CSRF token from /auth/csrf endpoint.
     * Returns Promise resolving to {csrf_token_name, csrf_hash}.
     * Updates meta tags on success.
     */
    window.refreshCsrfToken = function () {
        if (!CSRF_REFRESH_URL) {
            return Promise.reject('csrf-refresh-url not configured');
        }
        return $.ajax({
            url: CSRF_REFRESH_URL,
            type: 'GET',
            dataType: 'json'
        }).done(function (data) {
            if (data && data.csrf_token_name && data.csrf_hash) {
                window.setCsrfToken(data.csrf_token_name, data.csrf_hash);
            }
        });
    };

    /**
     * Sync meta token hash from X-CSRF-Hash response header.
     * Primary mechanism (hook emits fresh hash on every successful response).
     */
    $(document).ajaxSuccess(function (event, jqXHR) {
        var newHash = jqXHR.getResponseHeader('X-CSRF-Hash');
        if (newHash) {
            $('meta[name="csrf-token-hash"]').attr('content', newHash);
        }
    });

    /**
     * jQuery ajaxPrefilter: inject CSRF token into POST data payload.
     * Always overwrites stale token value; handles string, object, array.
     */
    $.ajaxPrefilter(function (options, originalOptions, jqXHR) {
        if (!options.type || options.type.toUpperCase() !== 'POST') {
            return;
        }
        var tokenName = window.getCsrfTokenName();
        var tokenValue = window.getCsrfToken();
        if (!tokenName || !tokenValue) {
            return;
        }

        if (options.data == null || options.data === '') {
            options.data = tokenName + '=' + encodeURIComponent(tokenValue);
        } else if (typeof options.data === 'string') {
            options.data = replaceOrAppendParam(options.data, tokenName, tokenValue);
        } else if ($.isPlainObject(options.data)) {
            options.data[tokenName] = tokenValue;
        } else if ($.isArray(options.data)) {
            var found = false;
            for (var i = 0; i < options.data.length; i++) {
                if (options.data[i] && options.data[i].name === tokenName) {
                    options.data[i].value = tokenValue;
                    found = true;
                    break;
                }
            }
            if (!found) {
                options.data.push({ name: tokenName, value: tokenValue });
            }
        }
    });

    /**
     * Global 403 handler: CSRF failure recovery.
     * - Auth 403 (JSON from deny_access) is left to per-request handlers.
     * - CSRF 403 (HTML page): fetch fresh token via endpoint, retry once.
     */
    $(document).ajaxError(function (event, jqXHR, settings) {
        if (jqXHR.status !== 403) {
            return;
        }

        // Authorization 403 (JSON response from deny_access) — not CSRF; skip.
        if (jqXHR.responseJSON) {
            return;
        }

        // Already retried once — don't loop; surface the error.
        if (settings._csrfRetried) {
            if (window.SwalHelper) {
                SwalHelper.error('Session keamanan berubah',
                    'Silakan refresh halaman dan coba lagi.');
            }
            return;
        }

        // Only POST requests carry CSRF tokens; GET 403s are not recoverable.
        if (!settings.type || String(settings.type).toUpperCase() !== 'POST') {
            if (window.SwalHelper) {
                SwalHelper.error('Session keamanan berubah',
                    'Silakan refresh halaman dan coba lagi.');
            }
            return;
        }

        // CSRF failure: mark as retried, fetch fresh token, retry once.
        settings._csrfRetried = true;
        window.refreshCsrfToken().done(function () {
            $.ajax(settings);
        }).fail(function () {
            if (window.SwalHelper) {
                SwalHelper.error('Session keamanan berubah',
                    'Silakan refresh halaman dan coba lagi.');
            }
        });
    });

    /* ======================================================================
       Document Ready
       ====================================================================== */
    $(function () {

        /* --- Sidebar toggle (navbar hamburger only) --- */
        $(document).on('click', '#sidebarToggle', function (e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSidebar();
        });

        $(document).on('click', '#sidebarOverlay', function (e) {
            e.preventDefault();
            closeSidebar();
        });

        /* close sidebar on Escape key */
        $(document).on('keydown', function (e) {
            if (e.key === 'Escape' && $('body').hasClass('sidebar-open')) {
                closeSidebar();
            }
        });

        /* sync mobile overlay on resize */
        var resizeTimer;
        $(window).on('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function () {
                if (isMobile()) {
                    var open = $('body').hasClass('sidebar-open');
                    $('#sidebarOverlay').toggleClass('show', open);
                    $('body').css('overflow', open ? 'hidden' : '');
                } else {
                    /* desktop: never show overlay, never lock body scroll */
                    $('#sidebarOverlay').removeClass('show');
                    $('body').css('overflow', '');
                }
            }, 150);
        });

        /* --- Active menu --- */
        setActiveMenu();

        /* --- Tooltips (sidebar tooltips are excluded) --- */
        initTooltips();

        /* --- Select2 --- */
        initSelect2();

        /* --- Flatpickr --- */
        initFlatpickr();

        /* --- Flash toasts --- */
        showFlashToasts();

        /* --- Auto-dismiss alerts after 6s --- */
        setTimeout(function () {
            $('.alert-dismissible.auto-dismiss').fadeTo(400, 0, function () {
                $(this).alert('close');
            });
        }, 6000);

        /* --- Prevent double submit on forms with data-loading --- */
        $(document).on('submit', 'form[data-loading-submit]', function () {
            var $form = $(this);
            var $btn = $form.find('button[type="submit"], button:not([type])').first();

            if ($btn.length) {
                btnLoading($btn, $btn.data('loading-text'));
            }
        });

        /* --- Console watermark --- */
        if (window.console && console.info) {
            console.info('%c SIMPELDAR ', 'background:#0d6efd;color:#fff;padding:3px 8px;border-radius:4px;font-weight:bold;',
                         'Sistem Informasi Manajemen Pelayanan Darah');
        }
    });

})(jQuery);
