<?php defined('BASEPATH') OR exit('No direct script access allowed');
$i = isset($i) ? (int)$i : 1;
$is_limited = isset($is_limited) ? (bool)$is_limited : false;
$ro  = $is_limited ? ' readonly' : '';
$dis = $is_limited ? ' disabled' : '';

$serah_key  = ($i === 1) ? 'PETUGAS_SERAH'  : 'PETUGAS_SERAH_' . $i;
$terima_key = ($i === 1) ? 'PETUGAS_TERIMA' : 'PETUGAS_TERIMA_' . $i;
$tgl_serah_key = ($i === 1) ? 'TGL_SERAH' : 'TGL_SERAH' . $i;
$perawat_key = ($i === 1) ? 'PERAWAT_TERIMA' : 'PERAWAT_TERIMA_' . $i;
?>

<!-- Kantong #<?php echo $i; ?> -->
<div class="col-12 col-md-6 col-xxl-4">
    <div class="card bag-card h-100">
        <div class="card-header py-2 px-3">
            <span class="fw-bold">
                <i class="fas fa-prescription-bottle-medical text-danger me-1"></i> Kantong #<?php echo $i; ?>
            </span>
        </div>
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-12">
                    <label class="form-label" for="nomer<?php echo $i; ?>">Nomor Kantong</label>
                    <input type="text"
                           name="no_kantong_<?php echo $i; ?>"
                           id="nomer<?php echo $i; ?>"
                           class="form-control form-control-sm"
                           data-kantong-lookup="<?php echo $i; ?>"
                           data-original="<?php echo htmlspecialchars($d['no_kantong_' . $i]); ?>"
                           value="<?php echo htmlspecialchars($d['no_kantong_' . $i]); ?>"
                           placeholder="Nomor Kantong <?php echo $i; ?>"
                           autocomplete="off"<?php echo $ro; ?>>
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="goldaroto<?php echo $i; ?>edit">Gol. Darah</label>
                    <input type="text"
                           id="goldaroto<?php echo $i; ?>edit"
                           class="form-control form-control-sm"
                           value="<?php echo htmlspecialchars($d['PRO_DESKDAR_' . $i]); ?>"
                           placeholder="Gol. Darah"
                           readonly>
                    <input type="hidden"
                           name="goldaroto<?php echo $i; ?>"
                           id="goldaroto<?php echo $i; ?>_hidden"
                           value="<?php echo htmlspecialchars($d['PRO_DESKDAR_' . $i]); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="cc<?php echo $i; ?>edit">Volume (cc)</label>
                    <input type="text"
                           name="volume_<?php echo $i; ?>"
                           id="cc<?php echo $i; ?>edit"
                           class="form-control form-control-sm text-center"
                           value="<?php echo htmlspecialchars($d['volume_' . $i]); ?>"
                           placeholder="Volume"<?php echo $ro; ?>>
                </div>

                <div class="col-12">
                    <label class="form-label" for="exp<?php echo $i; ?>edit">Expired Date</label>
                    <input type="text"
                           name="exp_<?php echo $i; ?>"
                           id="exp<?php echo $i; ?>edit"
                           class="form-control form-control-sm"
                           value="<?php echo htmlspecialchars($d['exp_' . $i]); ?>"
                           placeholder="Expired Date"<?php echo $ro; ?>>
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="myr_<?php echo $i; ?>">Mayor</label>
                    <select name="myr_<?php echo $i; ?>" id="myr_<?php echo $i; ?>" class="form-select form-select-sm select2" data-placeholder="MAYOR"<?php echo $dis; ?>>
                        <option value="">&nbsp;</option>
                        <?php foreach ($mayor as $b): ?>
                            <option value="<?php echo $b['ID']; ?>"<?php if ($d['myr_' . $i] == $b['ID']) echo ' selected'; ?>>
                                <?php echo htmlspecialchars($b['DESKRIPSI']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="mnr_<?php echo $i; ?>">Minor</label>
                    <select name="mnr_<?php echo $i; ?>" id="mnr_<?php echo $i; ?>" class="form-select form-select-sm select2" data-placeholder="MINOR"<?php echo $dis; ?>>
                        <option value="">&nbsp;</option>
                        <?php foreach ($minor as $b): ?>
                            <option value="<?php echo $b['ID']; ?>"<?php if ($d['mnr_' . $i] == $b['ID']) echo ' selected'; ?>>
                                <?php echo htmlspecialchars($b['DESKRIPSI']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label" for="tglkantong<?php echo $i; ?>">Tgl Input Kantong</label>
                    <input type="text"
                           name="tglkantong<?php echo $i; ?>"
                           id="tglkantong<?php echo $i; ?>"
                           class="form-control form-control-sm datetimepicker"
                           value="<?php echo htmlspecialchars($d['tglkantong' . $i]); ?>"
                           placeholder="Tanggal Input Kantong <?php echo $i; ?>"<?php echo $ro; ?>>
                </div>

                <div class="col-12">
                    <label class="form-label" for="tgl_serah_edit_<?php echo $i; ?>">Tgl Serah</label>
                    <input type="text"
                           name="tgl_serah_edit_<?php echo $i; ?>"
                           id="tgl_serah_edit_<?php echo $i; ?>"
                           class="form-control form-control-sm datetimepicker"
                           value="<?php echo htmlspecialchars($d[$tgl_serah_key]); ?>"
                           placeholder="Tgl Serah">
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="petugas_serah_edit_<?php echo $i; ?>">Petugas Serah</label>
                    <input type="text"
                           name="petugas_serah_edit_<?php echo $i; ?>"
                           id="petugas_serah_edit_<?php echo $i; ?>"
                           class="form-control form-control-sm"
                           value="<?php echo htmlspecialchars($d[$serah_key]); ?>"
                           placeholder="Petugas Yang Menyerahkan">
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="petugas_terima_edit_<?php echo $i; ?>">Petugas Terima</label>
                    <input type="text"
                           name="petugas_terima_edit_<?php echo $i; ?>"
                           id="petugas_terima_edit_<?php echo $i; ?>"
                           class="form-control form-control-sm"
                           value="<?php echo htmlspecialchars($d[$terima_key]); ?>"
                           placeholder="Petugas Yang Menerima">
                </div>
                <?php if ($is_limited): ?>
                <div class="col-md-4">
                    <label class="form-label" for="perawat_edit_<?php echo $i; ?>">Perawat</label>
                    <input type="text"
                           name="perawat_edit_<?php echo $i; ?>"
                           id="perawat_edit_<?php echo $i; ?>"
                           class="form-control form-control-sm"
                           value="<?php echo htmlspecialchars($d[$perawat_key] ?? ''); ?>"
                           placeholder="Perawat">
                </div>
<?php endif; ?>
            </div>
        </div>
    </div>
</div>
