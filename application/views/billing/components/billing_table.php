<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="card">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
        <h5 class="card-title mb-0">
            <i class="fas fa-file-invoice-dollar me-2"></i>Jumlah Billing
        </h5>
        <div class="d-flex align-items-center gap-2">
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
                        <th class="text-center">NO</th>
                        <th>NO MR</th>
                        <th>NAMA PASIEN</th>
                        <th class="text-center">TGL BILLING</th>
                        <th class="text-center">JUMLAH BILLING</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
