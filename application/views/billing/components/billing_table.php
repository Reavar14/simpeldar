<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="card">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
        <h5 class="card-title mb-0">
            <i class="fas fa-file-invoice-dollar me-2"></i>Hasil Billing Kantong
        </h5>
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small">Total: <span id="statTotal" class="fw-bold text-primary">-</span></span>
            <span class="text-muted small">Sudah Billing: <span id="statBilled" class="fw-bold text-success">-</span></span>
            <button type="button" id="btnRefreshBilling" class="btn btn-sm btn-outline-secondary" title="Refresh Data">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="tabelBilling" class="table table-hover w-100">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No Permintaan</th>
                        <th>MR</th>
                        <th>Nama Pasien</th>
                        <th>Tanggal Permintaan</th>
                        <th>Ruangan</th>
                        <th>Jenis Darah</th>
                        <th class="text-center">Jumlah Kantong</th>
                        <th class="text-center">Status Billing</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>