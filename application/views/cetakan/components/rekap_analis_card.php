<?php defined('BASEPATH') OR exit('No direct script access allowed');
$flt = isset($filter) ? $filter : array();
$analis_list     = isset($analis_list) ? $analis_list : array();
?>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="fas fa-file-medical-alt me-2"></i>Cetakan Rekap Data Analis</h5>
    </div>
    <div class="card-body">
        <form id="formCetakanRekap" class="row g-3" method="get" action="<?php echo base_url('index.php/cetakan'); ?>">
            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                <label class="form-label" for="cetakanRekapTglAwal">Tanggal Awal</label>
                <input type="text" name="tgl_awal_rekap" id="cetakanRekapTglAwal" class="form-control datepicker" placeholder="Tanggal Awal" value="<?php echo htmlspecialchars($flt['tgl_awal_rekap']); ?>">
            </div>
            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                <label class="form-label" for="cetakanRekapTglAkhir">Tanggal Akhir</label>
                <input type="text" name="tgl_akhir_rekap" id="cetakanRekapTglAkhir" class="form-control datepicker" placeholder="Tanggal Akhir" value="<?php echo htmlspecialchars($flt['tgl_akhir_rekap']); ?>">
            </div>
            <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12">
                <label class="form-label" for="cetakanRekapAnalis">Analis</label>
                <select name="analis_rekap" id="cetakanRekapAnalis" class="form-select select2" data-placeholder="Semua Analis">
                    <option value="">&nbsp;</option>
                    <?php foreach ($analis_list as $a): ?>
                        <option value="<?php echo htmlspecialchars($a['id_analis']); ?>"<?php if ($flt['analis_rekap'] == $a['id_analis']) echo ' selected'; ?>>
                            <?php echo htmlspecialchars($a['nama_analis']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-xl-2 col-lg-2 col-md-2 col-sm-6">
                <label class="form-label d-none d-md-block">&nbsp;</label>
                <a href="#" id="btnPrintRekapAnalis" class="btn btn-danger w-100">
                    <i class="fas fa-file-pdf me-2"></i>Export PDF
                </a>
            </div>
            <div class="col-xl-2 col-lg-2 col-md-2 col-sm-6">
                <label class="form-label d-none d-md-block">&nbsp;</label>
                <a href="#" id="btnExcelRekapAnalis" class="btn btn-success w-100">
                    <i class="fas fa-file-excel me-2"></i>Export Excel
                </a>
            </div>
        </form>
    </div>
</div>

<style>
    /* Layout cetakan rekap: select2 & input ikut lebar parent. Scoped. */
    #formCetakanRekap > [class*="col-"] {
        min-width: 0;
    }

    #formCetakanRekap .select2-container {
        width: 100% !important;
        max-width: 100% !important;
    }

    #formCetakanRekap .select2-container .select2-selection {
        width: 100% !important;
    }

    #formCetakanRekap .form-control,
    #formCetakanRekap .form-select,
    #formCetakanRekap .select2-container {
        min-width: 0;
    }
</style>
