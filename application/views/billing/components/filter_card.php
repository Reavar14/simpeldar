<?php defined('BASEPATH') OR exit('No direct script access allowed');
$flt = isset($filter) ? $filter : array();
$ruangan_list = isset($ruangan_list) ? $ruangan_list : array();
$status_list = isset($status_list) ? $status_list : array();
?>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="fas fa-filter me-2"></i>Filter Billing</h5>
    </div>
    <div class="card-body">
        <form id="formBilling" class="row g-3" method="get" action="<?php echo base_url('index.php/billing'); ?>">
            <div class="col-md-3">
                <label class="form-label" for="billingTglAwal">Tanggal Awal</label>
                <input type="text" name="tgl_awal" id="billingTglAwal" class="form-control datepicker" placeholder="Tanggal Awal" value="<?php echo htmlspecialchars($flt['tgl_awal']); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label" for="billingTglAkhir">Tanggal Akhir</label>
                <input type="text" name="tgl_akhir" id="billingTglAkhir" class="form-control datepicker" placeholder="Tanggal Akhir" value="<?php echo htmlspecialchars($flt['tgl_akhir']); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label" for="billingRuangan">Ruangan</label>
                <select name="ruangan" id="billingRuangan" class="form-select select2" data-placeholder="Semua Ruangan">
                    <option value=""></option>
                    <?php foreach ($ruangan_list as $r): ?>
                        <option value="<?php echo htmlspecialchars($r['ID']); ?>" <?php echo ($flt['ruangan'] === $r['ID']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($r['DESKRIPSI']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="billingStatus">Status Billing</label>
                <select name="status_billing" id="billingStatus" class="form-select select2" data-placeholder="Semua Status">
                    <option value=""></option>
                    <?php foreach ($status_list as $s): ?>
                        <option value="<?php echo htmlspecialchars($s['id']); ?>" <?php echo ($flt['status_billing'] === $s['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($s['nama']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
                <button type="submit" class="btn btn-primary" id="btnBillingFilter">
                    <i class="fas fa-search me-1"></i> Filter
                </button>
                <a href="<?php echo base_url('index.php/billing'); ?>" class="btn btn-outline-secondary" id="btnBillingReset">
                    <i class="fas fa-undo me-1"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>