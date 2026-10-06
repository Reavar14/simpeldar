<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!-- ANALIS 1 + TUJUAN + HASIL PEMERIKSAAN + PERAWAT -->
<div class="row g-2 mb-2">
    <!-- Analis 1 -->
    <div class="col-md-3">
        <select name="analis"
                id="analis"
                class="form-select select2"
                data-placeholder="Petugas Analis PTTD 1">
            <option value="">&nbsp;</option>
            <?php foreach ($analis as $a): ?>
                <option value="<?php echo $a['id_analis']; ?>">
                    <?php echo htmlspecialchars($a['nama_analis']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <!-- Tujuan -->
    <div class="col-md-3">
        <select name="tujuan"
                id="tujuan"
                class="form-select select2"
                data-placeholder="Tujuan">
            <option value="">&nbsp;</option>
            <?php foreach ($tujuan as $t): ?>
                <option value="<?php echo $t['id_variabel']; ?>">
                    <?php echo htmlspecialchars($t['variabel']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <!-- Hasil Pemeriksaan -->
    <div class="col-md-3">
        <select name="hasil_pemeriksaan"
                id="hasil_pemeriksaan"
                class="form-select select2"
                data-placeholder="Hasil Pemeriksaan">
            <option value="">&nbsp;</option>
            <?php foreach ($hasil_pemeriksaan as $hp): ?>
                <option value="<?php echo $hp['ID']; ?>">
                    <?php echo htmlspecialchars($hp['DESKRIPSI']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <!-- Perawat -->
    <div class="col-md-3">
        <select name="perawat"
                id="perawat"
                class="form-select select2"
                data-placeholder="Perawat">
            <option value="">&nbsp;</option>
            <?php foreach ($perawat as $p): ?>
                <option value="<?php echo $p['id_perawat']; ?>">
                    <?php echo htmlspecialchars($p['nama_perawat']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
<!-- HISTORI DATABASE + GOLONGAN DARAH DISPLAY -->
<div class="row g-2 mb-2">
    <!-- Histori dari database -->
    <div class="col-md-9">
        <textarea
            id="riwayattrans1"
            name="riwayattrans1"
            class="form-control"
            rows="4"
            placeholder="Histori Riwayat Alergi Transfusi dan Catatan"
            readonly></textarea>
    </div>
    <!-- Golongan Darah Display -->
    <div class="col-md-3">
        <div class="goldar-display h-100 d-flex flex-column justify-content-start">
            <div class="text-primary"
                 style="font-size: 18px; margin-bottom: 5px;">
                Golongan Darah :
            </div>
            <span class="goldarah pasien-goldarah fw-bolder"
                  style="font-size: 3.5rem; line-height: 1.1;">
            </span>
            <span id="id-selected" class="small text-muted">
            </span>
        </div>
    </div>
</div>
<!-- INPUT RIWAYAT ALERGI / CATATAN BARU -->
<div class="row g-2 mb-2">
    <div class="col-12">
        <input type="text"
               name="riwayattrans"
               id="riwayattrans"
               class="form-control"
               placeholder="Riwayat Alergi Transfusi dan Catatan">
    </div>
</div>
<!-- NOTIF STATUS -->
<input type="hidden"
       name="notifsatus"
       id="notifsatus">
<!-- PEMISAH -->
<hr>
<!-- HASIL PEMERIKSAAN -->
<div class="row g-2 mb-2 align-items-center">
    <!-- Judul -->
    <div class="col-md-3">
        <div style="font-size: 18px; font-weight: bold; text-decoration: underline; color: #337ab7;">
            HASIL PEMERIKSAAN:
        </div>
    </div>
    <!-- Analis 2 -->
    <div class="col-md-3">
        <select name="analis2"
                id="analis2"
                class="form-select select2"
                data-placeholder="Petugas Analis PTTD 2">
            <option value="">&nbsp;</option>
            <?php foreach ($analis as $a): ?>
                <option value="<?php echo $a['id_analis']; ?>">
                    <?php echo htmlspecialchars($a['nama_analis']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <!-- Auto Kontrol -->
    <div class="col-md-2">
        <select name="auto_kontrol"
                id="auto_kontrol"
                class="form-select select2"
                data-placeholder="Auto Kontrol">
            <option value="">&nbsp;</option>
            <?php foreach ($auto_kontrol as $ak): ?>
                <option value="<?php echo $ak['ID']; ?>">
                    <?php echo htmlspecialchars($ak['DESKRIPSI']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>