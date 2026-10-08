<?php defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();
$level = $CI->session->userdata('level');
?>

<style>
    /* TABEL - Responsive */
    #tabelRawatInap {
        width: 100% !important;
        table-layout: fixed !important;
        border-collapse: collapse !important;
        border: 1px solid #dee2e6 !important;
        font-size: 13px;
    }

    /* BORDER TABEL */
    #tabelRawatInap th,
    #tabelRawatInap td {
        box-sizing: border-box !important;
        border: 1px solid #dee2e6 !important;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* HEADER */
    #tabelRawatInap thead th {
        padding: 7px 4px !important;
        text-align: center !important;
        vertical-align: middle !important;
        white-space: nowrap !important;
        font-size: 10px !important;
        font-weight: 600 !important;
        line-height: 1.25 !important;
        border: 1px solid #dee2e6 !important;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ISI */
    #tabelRawatInap tbody td {
        padding: 7px 4px !important;
        vertical-align: middle !important;
        white-space: nowrap !important;
        line-height: 1.35 !important;
        border: 1px solid #dee2e6 !important;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* 1. NOMOR */
    #tabelRawatInap th:nth-child(1),
    #tabelRawatInap td:nth-child(1) {
        width: 8% !important;
        text-align: center !important;
    }
    /* 2. NO. MR */
    #tabelRawatInap th:nth-child(2),
    #tabelRawatInap td:nth-child(2) {
        width: 8% !important;
        text-align: center !important;
    }
    /* 3. NAMA PASIEN */
    #tabelRawatInap th:nth-child(3),
    #tabelRawatInap td:nth-child(3) {
        width: 14% !important;
    }
    /* 4. TGL PERMINTAAN */
    #tabelRawatInap th:nth-child(4),
    #tabelRawatInap td:nth-child(4) {
        width: 8% !important;
        text-align: center !important;
    }
    /* 5. RUANGAN */
    #tabelRawatInap th:nth-child(5),
    #tabelRawatInap td:nth-child(5) {
        width: 8% !important;
    }
    /* 6. ALASAN */
    #tabelRawatInap th:nth-child(6),
    #tabelRawatInap td:nth-child(6) {
        width: 12% !important;
    }
    /* 7. TUJUAN */
    #tabelRawatInap th:nth-child(7),
    #tabelRawatInap td:nth-child(7) {
        width: 8% !important;
        text-align: center !important;
    }
    /* 8. GOL. DARAH */
    #tabelRawatInap th:nth-child(8),
    #tabelRawatInap td:nth-child(8) {
        width: 8% !important;
        text-align: center !important;
    }
    /* 9. TGL DIPERLUKAN */
    #tabelRawatInap th:nth-child(9),
    #tabelRawatInap td:nth-child(9) {
        width: 8% !important;
        text-align: center !important;
    }
    /* 10. STATUS */
    #tabelRawatInap th:nth-child(10),
    #tabelRawatInap td:nth-child(10) {
        width: 12% !important;
        white-space: normal !important;
        word-break: normal !important;
        line-height: 1.3 !important;
    }
    /* 11. KELENGKAPAN */
    #tabelRawatInap th:nth-child(11),
    #tabelRawatInap td:nth-child(11) {
        width: 8% !important;
        white-space: normal !important;
        word-break: normal !important;
        line-height: 1.3 !important;
    }
    /* 12. OPSI */
    #tabelRawatInap th:nth-child(12),
    #tabelRawatInap td:nth-child(12) {
        width: 8% !important;
        text-align: center !important;
    }
    /* TANGGAL */
    #tabelRawatInap td:nth-child(4),
    #tabelRawatInap td:nth-child(9) {
        font-size: 11px !important;
    }
    /* ALIGNMENT */
    #tabelRawatInap td:nth-child(2),
    #tabelRawatInap td:nth-child(4),
    #tabelRawatInap td:nth-child(5),
    #tabelRawatInap td:nth-child(7),
    #tabelRawatInap td:nth-child(8),
    #tabelRawatInap td:nth-child(9),
    #tabelRawatInap td:nth-child(11),
    #tabelRawatInap td:nth-child(12) {
        text-align: center !important;
    }
    /* TOMBOL OPSI */
    #tabelRawatInap .btn-group {
        display: inline-flex !important;
        flex-wrap: nowrap !important;
        justify-content: center !important;
    }
    #tabelRawatInap .btn-group .btn {
        padding: 3px 5px !important;
        font-size: 10px !important;
        line-height: 1.2 !important;
    }
    #tabelRawatInap .btn-group i {
        font-size: 11px !important;
    }
    /* DISABLE HORIZONTAL SCROLL */
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
    /* Form controls inside table */
    #tabelRawatInap .form-control,
    #tabelRawatInap .form-select {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }
</style>

<!-- DataTable Card -->
<div class="card">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
        <h5 class="card-title mb-0"><i class="fas fa-table me-2"></i>Daftar Permintaan Darah</h5>
        <div class="d-flex gap-2">
            <span class="text-muted small">Total: <span id="statTotal" class="fw-bold text-primary">-</span></span>
            <button type="button" id="btnRefreshTable" class="btn btn-sm btn-outline-secondary" title="Refresh Data">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="tabelRawatInap" class="table table-hover w-100">
                <thead>
                    <tr>
                        <th>Nomor</th>
                        <th>No. MR</th>
                        <th>Nama Pasien</th>
                        <th>Tgl Permintaan</th>
                        <th>Ruangan</th>
                        <th>Alasan</th>
                        <th>Tujuan</th>
                        <th class="text-center">Gol. Darah</th>
                        <th>Tgl Diperlukan</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Kelengkapan</th>
                        <th class="text-center">Opsi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
window.RAWAT_INAP_CONFIG = {
    ajaxUrl: "<?php echo base_url('index.php/darah/ajax_list_ri'); ?>",
    detailUrl: "<?php echo base_url('index.php/requestcontroller/detail/'); ?>",
    bonUrl: "http://192.168.7.138/cetakanhnf/simpeldar/admin/bonminta.php?id=",
    hasilUrl: "<?php echo base_url('index.php/requestcontroller/print_hasil_pemeriksaan/'); ?>",
    editUrl: "<?php echo base_url('index.php/requestcontroller/edit_rawat_inap/'); ?>"
};
</script>