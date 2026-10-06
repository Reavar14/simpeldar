<?php defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="card">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
        <h5 class="card-title mb-0"><i class="fas fa-table me-2"></i>Riwayat Permintaan</h5>
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small">Jumlah Kunjungan: <span id="statKunjungan" class="fw-bold text-primary">-</span></span>
            <span class="text-muted small">Total: <span id="statTotal" class="fw-bold text-primary">-</span></span>
            <button type="button" id="btnRefreshTable" class="btn btn-sm btn-outline-secondary" title="Refresh Data">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="tabelRiwayat" class="table table-hover w-100">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No. Permintaan</th>
                        <th>No. MR</th>
                        <th>Nama Pasien</th>
                        <th>Tgl Permintaan</th>
                        <th>Ruangan</th>
                        <th>Tujuan</th>
                        <th class="text-center">Gol. Darah</th>
                        <th>Jenis Permintaan</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Opsi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
