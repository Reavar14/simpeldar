<?php defined('BASEPATH') OR exit('No direct script access allowed');
$flt = isset($filter) ? $filter : array();
$analis_list = isset($analis_list) ? $analis_list : array();
?>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="fas fa-search me-2"></i>Filter Laporan Pelayanan</h5>
    </div>
    <div class="card-body">
        <form id="formPelayanan" class="row g-3" method="get" action="<?php echo base_url('index.php/laporan'); ?>">
            <div class="col-md-4">
                <label class="form-label" for="pelayananTglAwal">Tanggal Awal</label>
                <input type="text" name="pelayanan_tgl_awal" id="pelayananTglAwal" class="form-control datepicker" placeholder="Tanggal Awal" value="<?php echo htmlspecialchars($flt['pelayanan_tgl_awal']); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="pelayananTglAkhir">Tanggal Akhir</label>
                <input type="text" name="pelayanan_tgl_akhir" id="pelayananTglAkhir" class="form-control datepicker" placeholder="Tanggal Akhir" value="<?php echo htmlspecialchars($flt['pelayanan_tgl_akhir']); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label" for="pelayananAnalis">Analis</label>
                <select name="pelayanan_analis" id="pelayananAnalis" class="form-select select2" data-placeholder="Semua Analis">
                    <option value="">&nbsp;</option>
                    <?php foreach ($analis_list as $a): ?>
                        <option value="<?php echo htmlspecialchars($a['id_analis']); ?>"<?php if ($flt['pelayanan_analis'] == $a['id_analis']) echo ' selected'; ?>>
                            <?php echo htmlspecialchars($a['nama_analis']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100" id="btnPelayanan">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>
