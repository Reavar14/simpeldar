<?php defined('BASEPATH') OR exit('No direct script access allowed');

$flt = isset($filter) ? $filter : array('tgl_awal' => '', 'tgl_akhir' => '', 'mr' => '');
?>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="fas fa-search me-2"></i>Cari Riwayat Pasien</h5>
    </div>
    <div class="card-body">
        <form id="formRiwayat" class="row g-3" method="get" action="<?php echo base_url('index.php/riwayat'); ?>">
            <div class="col-md-4">
                <label class="form-label" for="filterMR">Nomor MR</label>
                <input type="text" name="mr" id="filterMR" class="form-control" placeholder="Nomor Rekam Medis" value="<?php echo htmlspecialchars($flt['mr']); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label" for="filterTglAwal">Tanggal Awal</label>
                <input type="text" name="tgl_awal" id="filterTglAwal" class="form-control datepicker" placeholder="Tanggal Awal" value="<?php echo htmlspecialchars($flt['tgl_awal']); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label" for="filterTglAkhir">Tanggal Akhir</label>
                <input type="text" name="tgl_akhir" id="filterTglAkhir" class="form-control datepicker" placeholder="Tanggal Akhir" value="<?php echo htmlspecialchars($flt['tgl_akhir']); ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100" id="btnCariRiwayat">
                    <i class="fas fa-search me-1"></i> Cari
                </button>
            </div>
        </form>
    </div>
</div>
