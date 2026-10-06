<?php defined('BASEPATH') OR exit('No direct script access allowed');
$flt = isset($filter) ? $filter : array();
?>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="fas fa-filter me-2"></i>Filter Jenis Permintaan</h5>
    </div>
    <div class="card-body">
        <form id="formJenis" class="row g-3" method="get" action="<?php echo base_url('index.php/laporan'); ?>">
            <div class="col-md-4">
                <label class="form-label" for="jenisTglAwal">Tanggal Awal</label>
                <input type="text" name="jenis_tgl_awal" id="jenisTglAwal" class="form-control datepicker" placeholder="Tanggal Awal" value="<?php echo htmlspecialchars($flt['jenis_tgl_awal']); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="jenisTglAkhir">Tanggal Akhir</label>
                <input type="text" name="jenis_tgl_akhir" id="jenisTglAkhir" class="form-control datepicker" placeholder="Tanggal Akhir" value="<?php echo htmlspecialchars($flt['jenis_tgl_akhir']); ?>">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100" id="btnJenis">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
        <h5 class="card-title mb-0"><i class="fas fa-chart-pie me-2"></i>Hasil Jumlah Jenis Permintaan</h5>
        <button type="button" class="btn btn-sm btn-outline-secondary btnRefreshJenis" title="Refresh Data">
            <i class="fas fa-sync-alt"></i>
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="tabelJenis" class="table table-hover w-100">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Jenis Permintaan Darah</th>
                        <th class="text-center">Jumlah</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
