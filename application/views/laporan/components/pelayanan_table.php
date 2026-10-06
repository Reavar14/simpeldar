<?php defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="card">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
        <h5 class="card-title mb-0"><i class="fas fa-file-medical me-2"></i>Hasil Laporan Pelayanan</h5>
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small">Jumlah Tindakan: <span id="statPelayanan" class="fw-bold text-primary">-</span></span>
            <button type="button" class="btn btn-sm btn-outline-secondary btnRefreshPelayanan" title="Refresh Data">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="tabelPelayanan" class="table table-hover w-100">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No. Permintaan</th>
                        <th>No. MR</th>
                        <th>Nama Pasien</th>
                        <th>Tgl Permintaan</th>
                        <th>Tgl Diperlukan</th>
                        <th>Ruangan</th>
                        <th>Analis</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
