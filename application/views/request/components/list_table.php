<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<style>
    /* TABEL */
    #tabelPermintaan {
        width: 100% !important;
        table-layout: fixed !important;
        border-collapse: collapse !important;
        border: 1px solid #dee2e6 !important;
        font-size: 13px;
    }

    /* BORDER TABEL */
    #tabelPermintaan th,
    #tabelPermintaan td {
        box-sizing: border-box !important;
        border: 1px solid #dee2e6 !important;
    }

    /* HEADER */
    #tabelPermintaan thead th {
        padding: 7px 4px !important;
        text-align: center !important;
        vertical-align: middle !important;
        white-space: normal !important;
        word-break: normal !important;
        overflow-wrap: normal !important;
        hyphens: none !important;
        font-size: 10px !important;
        font-weight: 600 !important;
        line-height: 1.25 !important;
        border: 1px solid #dee2e6 !important;
    }

    /* ISI */
    #tabelPermintaan tbody td {
        padding: 7px 4px !important;
        vertical-align: middle !important;
        white-space: normal !important;
        word-break: normal !important;
        overflow-wrap: break-word !important;
        line-height: 1.35 !important;
        border: 1px solid #dee2e6 !important;
    }

    /* 1. NOMOR 1 BARIS */
    #tabelPermintaan th:nth-child(1),
    #tabelPermintaan td:nth-child(1) {
        width: 9% !important;

        white-space: nowrap !important;
        word-break: normal !important;
        overflow-wrap: normal !important;
    }
    /* 2. NOMOR MR */
    #tabelPermintaan th:nth-child(2),
    #tabelPermintaan td:nth-child(2) {
        width: 7% !important;
        white-space: nowrap !important;
        word-break: normal !important;
    }

    /* 3. NAMA PASIEN */
    #tabelPermintaan th:nth-child(3),
    #tabelPermintaan td:nth-child(3) {
        width: 9% !important;
    }
    /* 4. TGL PERMINTAAN */
    #tabelPermintaan th:nth-child(4),
    #tabelPermintaan td:nth-child(4) {
        width: 8% !important;
    }
    /* 5. RUANGAN */
    #tabelPermintaan th:nth-child(5),
    #tabelPermintaan td:nth-child(5) {
        width: 7% !important;
    }
    /* 6. ALASAN */
    #tabelPermintaan th:nth-child(6),
    #tabelPermintaan td:nth-child(6) {
        width: 6% !important;
    }
    /* 7. TUJUAN */
    #tabelPermintaan th:nth-child(7),
    #tabelPermintaan td:nth-child(7) {
        width: 6% !important;
    }
    /* 8. GOL DARAH */
    #tabelPermintaan th:nth-child(8),
    #tabelPermintaan td:nth-child(8) {
        width: 140px !important;
        min-width: 140px !important;
        max-width: 140px !important;
        text-align: center !important;
        white-space: nowrap !important;
    }
    /* 9. TGL DIPERLUKAN */
    #tabelPermintaan th:nth-child(9),
    #tabelPermintaan td:nth-child(9) {
        width: 8% !important;
    }
    /* 10. JENIS PERMINTAAN */
    #tabelPermintaan th:nth-child(10),
    #tabelPermintaan td:nth-child(10) {
        width: 9% !important;
    }
    /* 11. STATUS */
    #tabelPermintaan th:nth-child(11),
    #tabelPermintaan td:nth-child(11) {
        width: 7% !important;
    }
    /* 12. KELENGKAPAN */
    #tabelPermintaan th:nth-child(12),
    #tabelPermintaan td:nth-child(12) {
        width: 9% !important;
    }
    /* 13. OPSI */
    #tabelPermintaan th:nth-child(13),
    #tabelPermintaan td:nth-child(13) {
        width: 11% !important;
        white-space: nowrap !important;
        text-align: center !important;
    }
    /* TANGGAL */
    #tabelPermintaan td:nth-child(4),
    #tabelPermintaan td:nth-child(9) {
        white-space: nowrap !important;
        font-size: 11px !important;
    }
    /* ALIGNMENT */
    #tabelPermintaan td:nth-child(2),
    #tabelPermintaan td:nth-child(4),
    #tabelPermintaan td:nth-child(5),
    #tabelPermintaan td:nth-child(7),
    #tabelPermintaan td:nth-child(8),
    #tabelPermintaan td:nth-child(9),
    #tabelPermintaan td:nth-child(11),
    #tabelPermintaan td:nth-child(12),
    #tabelPermintaan td:nth-child(13) {
        text-align: center !important;
    }
    /* TOMBOL OPSI */
    #tabelPermintaan .btn-group {
        display: inline-flex !important;
        flex-wrap: nowrap !important;
        justify-content: center !important;
    }
    #tabelPermintaan .btn-group .btn {
        padding: 3px 5px !important;
        font-size: 10px !important;
        line-height: 1.2 !important;
    }
    #tabelPermintaan .btn-group i {
        font-size: 11px !important;
    }
    /* BADGE GOL DARAH - khusus "Tidak diketahui" */
    #tabelPermintaan .badge.goldar-unknown {
        white-space: nowrap !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 4px 8px !important;
        min-width: 110px !important;
        border-radius: .375rem !important;
    }
    /* HORIZONTAL SCROLL */
    .table-responsive {
        overflow-x: hidden !important;
    }
    .dataTables_wrapper {
        width: 100% !important;
        overflow-x: hidden !important;
    }
    .dataTables_scroll {
        overflow: hidden !important;
    }
</style>

<div class="card">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
        <h5 class="card-title mb-0">
            <i class="fas fa-tint me-2"></i>Info Proses Permintaan Darah
        </h5>
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small">Total: <span id="statTotal" class="fw-bold text-primary">-</span></span>
            <button type="button" id="btnRefreshTable" class="btn btn-sm btn-outline-secondary" title="Refresh Data">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="tabelPermintaan" class="table table-hover w-100">
                <thead>
                    <tr>
                        <th>Nomor</th>
                        <th>Nomor MR</th>
                        <th>Nama Pasien</th>
                        <th>Tgl Permintaan</th>
                        <th>Ruangan</th>
                        <th>Alasan</th>
                        <th>Tujuan</th>
                        <th class="text-center">Gol Darah</th>
                        <th>Tgl Diperlukan</th>
                        <th>Jenis Permintaan</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Kelengkapan</th>
                        <th class="text-center">Opsi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>