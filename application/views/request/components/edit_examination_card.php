<?php defined('BASEPATH') OR exit('No direct script access allowed');
$is_limited = isset($is_limited) ? (bool)$is_limited : false;
$dis = $is_limited ? ' disabled' : '';
?>

<!-- ============================================================
     CARD 3 - HASIL PEMERIKSAAN
     ============================================================ -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="fas fa-vial text-primary me-2"></i>Hasil Pemeriksaan</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="analis2">Analis PTTD 2</label>
                <select name="analis2" id="analis2" class="form-select select2" data-placeholder="Pilih Analis PTTD 2"<?php echo $dis; ?>>
                    <option value="">&nbsp;</option>
                    <?php foreach ($analis as $b): ?>
                        <option value="<?php echo $b['id_analis']; ?>"<?php if ($d['analis2'] == $b['id_analis']) echo ' selected'; ?>>
                            <?php echo htmlspecialchars($b['nama_analis']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="auto_kontrol">Auto Kontrol</label>
                <select name="auto_kontrol" id="auto_kontrol" class="form-select select2" data-placeholder="Pilih Auto Kontrol"<?php echo $dis; ?>>
                    <option value="">&nbsp;</option>
                    <?php foreach ($auto_kontrol as $b): ?>
                        <option value="<?php echo $b['ID']; ?>"<?php if ($d['auto_kontrol'] == $b['ID']) echo ' selected'; ?>>
                            <?php echo htmlspecialchars($b['DESKRIPSI']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
</div>