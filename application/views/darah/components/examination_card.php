<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- ============================================================
     CARD 3 - DATA KLINIS
     ============================================================ -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-notes-medical text-primary me-2"></i> Data Klinis
        </h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="diagnosa">Diagnosa</label>
                <input type="text" name="diagnosa" id="diagnosa" class="form-control" placeholder="Diagnosa">
            </div>
            <div class="col-md-6">
                <label class="form-label" for="alasan">Alasan Permintaan</label>
                <input type="text" name="alasan" id="alasan" class="form-control" placeholder="Alasan Permintaan">
            </div>
            <div class="col-md-6">
                <label class="form-label" for="kadar_hb">Kadar Hb</label>
                <input type="text" name="kadar_hb" id="kadar_hb" class="form-control" placeholder="Kadar Hb">
            </div>
            <div class="col-md-6">
                <label class="form-label" for="trombosit">Trombosit</label>
                <input type="text" name="trombosit" id="trombosit" class="form-control" placeholder="Trombosit">
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     CARD 5 - PEMERIKSAAN
     ============================================================ -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-microscope text-primary me-2"></i> Pemeriksaan
        </h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label" for="analis">Analis</label>
                <select name="analis" id="analis" class="form-select select2" data-placeholder="Petugas Analis PTTD 1">
                    <option value="">&nbsp;</option>
                    <?php foreach ($analis as $a): ?>
                        <option value="<?php echo $a['id_analis']; ?>"><?php echo htmlspecialchars($a['nama_analis']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="analis2">Analis 2</label>
                <select name="analis2" id="analis2" class="form-select select2" data-placeholder="Petugas Analis PTTD 2">
                    <option value="">&nbsp;</option>
                    <?php foreach ($analis as $a): ?>
                        <option value="<?php echo $a['id_analis']; ?>"><?php echo htmlspecialchars($a['nama_analis']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="perawat">Perawat</label>
                <select name="perawat" id="perawat" class="form-select select2" data-placeholder="Perawat">
                    <option value="">&nbsp;</option>
                    <?php foreach ($perawat as $p): ?>
                        <option value="<?php echo $p['id_perawat']; ?>"><?php echo htmlspecialchars($p['nama_perawat']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label" for="auto_kontrol">Auto Kontrol</label>
                <select name="auto_kontrol" id="auto_kontrol" class="form-select select2" data-placeholder="Auto Kontrol">
                    <option value="">&nbsp;</option>
                    <?php foreach ($auto_kontrol as $ak): ?>
                        <option value="<?php echo $ak['ID']; ?>"><?php echo htmlspecialchars($ak['DESKRIPSI']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="hasil_pemeriksaan">Hasil Pemeriksaan</label>
                <select name="hasil_pemeriksaan" id="hasil_pemeriksaan" class="form-select select2" data-placeholder="Hasil Pemeriksaan">
                    <option value="">&nbsp;</option>
                    <?php foreach ($hasil_pemeriksaan as $hp): ?>
                        <option value="<?php echo $hp['ID']; ?>"><?php echo htmlspecialchars($hp['DESKRIPSI']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="status">Status</label>
                <select name="status" id="status" class="form-select select2" data-placeholder="Pilih Status">
                    <option value="">&nbsp;</option>
                    <?php foreach ($status as $s): ?>
                        <option value="<?php echo $s['ID']; ?>"><?php echo htmlspecialchars($s['DESKRIPSI']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
</div>