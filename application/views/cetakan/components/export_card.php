<?php defined('BASEPATH') OR exit('No direct script access allowed');
$flt = isset($filter) ? $filter : array();
?>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="fas fa-file-export me-2"></i>Pilihan Output</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="d-grid">
                    <a href="#" id="btnPrintLaporanLengkap" class="btn btn-danger btn-lg">
                        <i class="fas fa-print me-2"></i>Cetak Laporan Lengkap
                    </a>
                    <small class="text-muted d-block mt-2">Format: HTML → Browser Print (PDF/Printer)</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-grid">
                    <a href="#" id="btnPrintRekapAnalis" class="btn btn-primary btn-lg">
                        <i class="fas fa-chart-bar me-2"></i>Cetak Rekap Analis
                    </a>
                    <small class="text-muted d-block mt-2">Format: HTML → Browser Print (PDF/Printer)</small>
                </div>
            </div>
        </div>
    </div>
</div>