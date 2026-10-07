<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!-- GOLONGAN DARAH + JENIS DARAH + TIPE DARAH -->
<div class="row g-2 mb-2">
    <!-- Golongan Darah -->
    <div class="col-md-6">
        <select name="selectgoldarah"
                id="selectgoldarah"
                class="form-select select2"
                data-placeholder="Golongan Darah">
            <option value="">&nbsp;</option>
            <?php foreach ($goldarah as $gd): ?>
                <option value="<?php echo $gd['ID']; ?>">
                    <?php echo htmlspecialchars($gd['DESKRIPSI']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="hidden"
               name="id_gol_darah"
               id="id_gol_darah">
    </div>
    <!-- Jenis Darah -->
    <div class="col-md-3">
        <select name="jenis_darah"
                id="jenis_darah"
                class="form-select select2"
                data-placeholder="Pilih Jenis Darah">
            <option value="">&nbsp;</option>
            <?php foreach ($jenis_darah as $jd): ?>
                <option value="<?php echo $jd['id']; ?>">
                    <?php echo htmlspecialchars($jd['nama_jenis']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <!-- Tipe Darah / Buffy Coat -->
    <div class="col-md-3">
        <select name="buffycoat"
                id="buffycoat"
                class="form-select select2"
                data-placeholder="Pilih Tipe Darah">
            <option value="">&nbsp;</option>
            <?php foreach ($buffycoat as $bc): ?>
                <option value="<?php echo $bc['id']; ?>">
                    <?php echo htmlspecialchars($bc['nama_jenis']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
<!-- TANGGAL DIPERLUKAN + VOLUME -->
<div class="row g-2 mb-2">
    <!-- Tanggal Diperlukan -->
    <div class="col-md-6">
        <div class="input-group">
            <input type="text"
                   name="tgl_diperlukan"
                   id="tgl_diperlukan"
                   class="form-control datepicker"
                   value="<?php echo date('Y-m-d'); ?>"
                   placeholder="Tanggal Diperlukan">
            <span class="input-group-text">
                <i class="fas fa-calendar-alt me-1"></i> Tgl Diperlukan
            </span>
        </div>
    </div>
    <!-- Volume -->
    <div class="col-md-3">
        <input type="text"
               name="volume"
               id="volume"
               class="form-control"
               placeholder="Volume">
    </div>
</div>
<!-- ALASAN + DIAGNOSA + TROMBOSIT + HB -->
<div class="row g-2 mb-2">
    <!-- Alasan -->
    <div class="col-md-3">
        <input type="text"
               name="alasan"
               id="alasan"
               class="form-control"
               placeholder="Alasan Permintaan">
    </div>
    <!-- Diagnosa -->
    <div class="col-md-3">
        <input type="text"
               name="diagnosa"
               id="diagnosa"
               class="form-control"
               placeholder="Diagnosa">
    </div>
    <!-- Trombosit -->
    <div class="col-md-3">
        <input type="text"
               name="trombosit"
               id="trombosit"
               class="form-control"
               placeholder="Trombosit">
    </div>
    <!-- Kadar Hb -->
    <div class="col-md-3">
        <input type="text"
               name="kadar_hb"
               id="kadar_hb"
               class="form-control"
               placeholder="Kadar Hb">
    </div>
</div>
<!-- DPJP + STATUS -->
<div class="row g-2 mb-2">
    <!-- DPJP -->
    <div class="col-md-6">
        <select name="dpjp"
                id="dpjp"
                class="form-select select2"
                data-placeholder="Pilih Dokter DPJP">
            <option value="">&nbsp;</option>
            <?php foreach ($dokter as $d): ?>
                <option value="<?php echo $d['ID']; ?>">
                    <?php echo htmlspecialchars($d['dokter']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <!-- Status -->
    <div class="col-md-6">
        <div class="status-native-wrapper">
            <div class="status-select">
                <select name="status"
                        id="status"
                        class="form-select select2"
                        data-placeholder="Pilih Status">
                    <option value="">&nbsp;</option>
                    <?php foreach ($status as $s): ?>
                        <option value="<?php echo $s['ID']; ?>">
                            <?php echo htmlspecialchars($s['DESKRIPSI']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <span class="input-group-text status-label">
                <i class="fas fa-cogs me-1"></i> Pilih Status
            </span>
        </div>
    </div>
</div>
<!-- ALERT STATUS DARAH -->
<div class="alert alert-danger d-flex align-items-center mb-2 d-none"
     id="alertStatusDarah"
     role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <div>
        <strong>Perhatian!</strong>
        Status Darah Masih Ada. Periksa Status Darah Sebelumnya.
    </div>
</div>
<!-- PETUGAS PENGAMBIL DARAH + RUANGAN -->
<div class="row g-2 mb-2">
    <!-- Pengambil Darah -->
    <div class="col-md-6">
        <input type="text"
               name="pengambil_darah"
               id="pengambil_darah"
               class="form-control"
               placeholder="Petugas Pengambil Contoh Darah">
    </div>
    <!-- Ruangan -->
    <div class="col-md-6">
        <select name="ruangan"
                id="ruangan"
                class="form-select select2"
                data-placeholder="Pilih Ruangan Rawat">
            <option value="">&nbsp;</option>
            <?php foreach ($ruangan as $r): ?>
                <option value="<?php echo $r['ID']; ?>">
                    <?php echo htmlspecialchars($r['DESKRIPSI']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>