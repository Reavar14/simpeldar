<?php defined('BASEPATH') OR exit('No direct script access allowed');
$flt = isset($filter) ? $filter : array();
$status_list = isset($status_list) ? $status_list : array();
?>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="fas fa-filter me-2"></i>Filter Jumlah Permintaan</h5>
    </div>
    <div class="card-body">
        <form id="formPermintaan" class="row g-3" method="get" action="<?php echo base_url('index.php/laporan'); ?>">
            <div class="col-md-4">
                <label class="form-label" for="permintaanTglAwal">Tanggal Awal</label>
                <input type="text" name="permintaan_tgl_awal" id="permintaanTglAwal" class="form-control datepicker" placeholder="Tanggal Awal" value="<?php echo htmlspecialchars($flt['permintaan_tgl_awal']); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="permintaanTglAkhir">Tanggal Akhir</label>
                <input type="text" name="permintaan_tgl_akhir" id="permintaanTglAkhir" class="form-control datepicker" placeholder="Tanggal Akhir" value="<?php echo htmlspecialchars($flt['permintaan_tgl_akhir']); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label" for="permintaanStatus">Status</label>
                <select name="permintaan_status" id="permintaanStatus" class="form-select select2" data-placeholder="Semua Status">
                    <option value="">&nbsp;</option>
                    <?php foreach ($status_list as $s): ?>
                        <option value="<?php echo htmlspecialchars($s['ID']); ?>"<?php if ($flt['permintaan_status'] == $s['ID']) echo ' selected'; ?>>
                            <?php echo htmlspecialchars($s['DESKRIPSI']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100" id="btnPermintaan">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
        <h5 class="card-title mb-0"><i class="fas fa-chart-bar me-2"></i>Hasil Jumlah Permintaan Darah</h5>
        <button type="button" class="btn btn-sm btn-outline-secondary btnRefreshPermintaan" title="Refresh Data">
            <i class="fas fa-sync-alt"></i>
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="tabelPermintaan" class="table table-hover w-100">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Status Permintaan</th>
                        <th class="text-center">Jumlah</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
