<?php defined('BASEPATH') OR exit('No direct script access allowed');
$flt = isset($filter) ? $filter : array();
?>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="fas fa-filter me-2"></i>Filter Billing Kantong</h5>
    </div>
    <div class="card-body">
        <form id="formBilling" class="row g-3" method="get" action="<?php echo base_url('index.php/laporan'); ?>">
            <div class="col-md-4">
                <label class="form-label" for="billingTglAwal">Tanggal Awal</label>
                <input type="text" name="billing_tgl_awal" id="billingTglAwal" class="form-control datepicker" placeholder="Tanggal Awal" value="<?php echo htmlspecialchars($flt['billing_tgl_awal']); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="billingTglAkhir">Tanggal Akhir</label>
                <input type="text" name="billing_tgl_akhir" id="billingTglAkhir" class="form-control datepicker" placeholder="Tanggal Akhir" value="<?php echo htmlspecialchars($flt['billing_tgl_akhir']); ?>">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100" id="btnBilling">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
        <h5 class="card-title mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>Hasil Billing Kantong</h5>
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small">Total Kantong: <span id="statKantong" class="fw-bold text-primary">-</span></span>
            <span class="text-muted small">Sudah Billing: <span id="statBilled" class="fw-bold text-success">-</span></span>
            <button type="button" class="btn btn-sm btn-outline-secondary btnRefreshBilling" title="Refresh Data">
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
                        <th>No. Permintaan</th>
                        <th>No. MR</th>
                        <th>Nama Pasien</th>
                        <th>Tanggal</th>
                        <th class="text-center">Jumlah Kantong</th>
                        <th class="text-center">Sudah Dibilling</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
