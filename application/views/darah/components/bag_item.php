<?php defined('BASEPATH') OR exit('No direct script access allowed');
$i = isset($i) ? (int)$i : 1; ?>

<!-- ============================================================
     BAG ITEM - Kantong #<?php echo $i; ?>
     ============================================================ -->
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
                           placeholder="Nomor Kantong <?php echo $i; ?>"
                           autocomplete="off">
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="goldaroto<?php echo $i; ?>">Gol Darah</label>
                    <input type="text"
                           id="goldaroto<?php echo $i; ?>"
                           name="goldaroto<?php echo $i; ?>"
                           class="form-control form-control-sm"
                           readonly
                           placeholder="Gol Darah">
                    <span id="goldarah<?php echo $i; ?>" class="small d-none"></span>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="cc<?php echo $i; ?>">Volume (cc)</label>
                    <input type="text"
                           id="cc<?php echo $i; ?>"
                           name="volume_<?php echo $i; ?>"
                           class="form-control form-control-sm"
                           placeholder="Volume">
                </div>

                <div class="col-12">
                    <label class="form-label" for="exp<?php echo $i; ?>">Expired Date</label>
                    <input type="text"
                           id="exp<?php echo $i; ?>"
                           name="exp_<?php echo $i; ?>"
                           class="form-control form-control-sm datepicker"
                           placeholder="Expired Date">
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="myr_<?php echo $i; ?>">Mayor</label>
                    <select name="myr_<?php echo $i; ?>" class="form-select form-select-sm select2" data-placeholder="MAYOR">
                        <option value="">&nbsp;</option>
                        <?php foreach ($mayor as $m): ?>
                            <option value="<?php echo $m['ID']; ?>"><?php echo htmlspecialchars($m['DESKRIPSI']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="mnr_<?php echo $i; ?>">Minor</label>
                    <select name="mnr_<?php echo $i; ?>" class="form-select form-select-sm select2" data-placeholder="MINOR">
                        <option value="">&nbsp;</option>
                        <?php foreach ($minor as $m): ?>
                            <option value="<?php echo $m['ID']; ?>"><?php echo htmlspecialchars($m['DESKRIPSI']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label" for="tglkantong<?php echo $i; ?>">Tgl Input Kantong</label>
                    <input type="text"
                           name="tglkantong<?php echo $i; ?>"
                           id="tglkantong<?php echo $i; ?>"
                           class="form-control form-control-sm datetimepicker"
                           placeholder="Tanggal Input Kantong">
                </div>

                <div class="col-12">
                    <label class="form-label" for="tgl_serah_<?php echo $i; ?>">Tgl Serah</label>
                    <input type="text"
                           name="tgl_serah_<?php echo $i; ?>"
                           id="tgl_serah_<?php echo $i; ?>"
                           class="form-control form-control-sm datetimepicker"
                           placeholder="Tgl Serah">
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="petugas_serah_<?php echo $i; ?>">Petugas Serah</label>
                    <input type="text"
                           name="petugas_serah_<?php echo $i; ?>"
                           id="petugas_serah_<?php echo $i; ?>"
                           class="form-control form-control-sm"
                           placeholder="Petugas Yang Menyerahkan">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="petugas_terima_<?php echo $i; ?>">Petugas Terima</label>
                    <input type="text"
                           name="petugas_terima_<?php echo $i; ?>"
                           id="petugas_terima_<?php echo $i; ?>"
                           class="form-control form-control-sm"
                           placeholder="Petugas Yang Menerima">
                </div>
            </div>
        </div>
    </div>
</div>