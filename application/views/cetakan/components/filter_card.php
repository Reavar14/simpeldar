<?php defined('BASEPATH') OR exit('No direct script access allowed');
$flt = isset($filter) ? $filter : array();
$analis_list     = isset($analis_list) ? $analis_list : array();
$data_entry_list = isset($data_entry_list) ? $data_entry_list : array();
$dokter_list     = isset($dokter_list) ? $dokter_list : array();
$status_list     = isset($status_list) ? $status_list : array();
?>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="fas fa-search me-2"></i>Filter Cetakan</h5>
    </div>
    <div class="card-body">
        <form id="formCetakanLengkap" class="row g-3" method="get" action="<?php echo base_url('index.php/cetakan'); ?>">
            <div class="col-md-4">
                <label class="form-label" for="cetakanTglAwal">Tanggal Awal</label>
                <input type="text" name="tgl_awal" id="cetakanTglAwal" class="form-control datepicker" placeholder="Tanggal Awal" value="<?php echo htmlspecialchars($flt['tgl_awal']); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="cetakanTglAkhir">Tanggal Akhir</label>
                <input type="text" name="tgl_akhir" id="cetakanTglAkhir" class="form-control datepicker" placeholder="Tanggal Akhir" value="<?php echo htmlspecialchars($flt['tgl_akhir']); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="cetakanAnalis">Analis</label>
                <select name="analis" id="cetakanAnalis" class="form-select select2" data-placeholder="Semua Analis">
                    <option value="">&nbsp;</option>
                    <?php foreach ($analis_list as $a): ?>
                        <option value="<?php echo htmlspecialchars($a['id_analis']); ?>"<?php if ($flt['analis'] == $a['id_analis']) echo ' selected'; ?>>
                            <?php echo htmlspecialchars($a['nama_analis']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="cetakanDataEntry">Data Entry</label>
                <select name="data_entry" id="cetakanDataEntry" class="form-select select2" data-placeholder="Semua Data Entry">
                    <option value="">&nbsp;</option>
                    <?php foreach ($data_entry_list as $d): ?>
                        <option value="<?php echo htmlspecialchars($d['USRID']); ?>"<?php if ($flt['data_entry'] == $d['USRID']) echo ' selected'; ?>>
                            <?php echo htmlspecialchars($d['NAMA']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="cetakanDokter">Dokter</label>
                <select name="dokter_konsul" id="cetakanDokter" class="form-select select2" data-placeholder="Semua Dokter">
                    <option value="">&nbsp;</option>
                    <?php foreach ($dokter_list as $dk): ?>
                        <option value="<?php echo htmlspecialchars($dk['ID']); ?>"<?php if ($flt['dokter_konsul'] == $dk['ID']) echo ' selected'; ?>>
                            <?php echo htmlspecialchars($dk['dokter']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="cetakanStatus">Status</label>
                <select name="statusp" id="cetakanStatus" class="form-select select2" data-placeholder="Semua Status">
                    <option value="">&nbsp;</option>
                    <?php foreach ($status_list as $s): ?>
                        <option value="<?php echo htmlspecialchars($s['ID']); ?>"<?php if ($flt['statusp'] == $s['ID']) echo ' selected'; ?>>
                            <?php echo htmlspecialchars($s['DESKRIPSI']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>
</div>
