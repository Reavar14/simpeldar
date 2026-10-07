<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- ============================================================
     CARD 2 - DATA PERMINTAAN DARAH
     ============================================================ -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-tint text-primary me-2"></i> Data Permintaan Darah
        </h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="no_permintaan">Nomor Permintaan</label>
                <input type="text" name="no_permintaan" id="no_permintaan" class="form-control font-monospace" value="<?php echo htmlspecialchars($no_permintaan); ?>" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="tgl_minta">Tanggal Permintaan</label>
                <input type="text" name="tgl_minta" id="tgl_minta" class="form-control datetimepicker" value="<?php echo date('Y-m-d H:i:s'); ?>" placeholder="Tanggal Permintaan">
            </div>

            <div class="col-md-6">
                <label class="form-label" for="ruangan">Ruangan</label>
                <select name="ruangan" id="ruangan" class="form-select select2" data-placeholder="Pilih Ruangan Rawat">
                    <option value="">&nbsp;</option>
                    <?php foreach ($ruangan as $r): ?>
                        <option value="<?php echo $r['ID']; ?>"><?php echo htmlspecialchars($r['DESKRIPSI']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="dpjp">Dokter DPJP</label>
                <select name="dpjp" id="dpjp" class="form-select select2" data-placeholder="Pilih Dokter DPJP">
                    <option value="">&nbsp;</option>
                    <?php foreach ($dokter as $d): ?>
                        <option value="<?php echo $d['ID']; ?>"><?php echo htmlspecialchars($d['dokter']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label" for="tujuan">Tujuan</label>
                <select name="tujuan" id="tujuan" class="form-select select2" data-placeholder="Pilih Tujuan">
                    <option value="">&nbsp;</option>
                    <?php foreach ($tujuan as $t): ?>
                        <option value="<?php echo $t['id_variabel']; ?>"><?php echo htmlspecialchars($t['variabel']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="tgl_diperlukan">Tanggal Diperlukan</label>
                <input type="text" name="tgl_diperlukan" id="tgl_diperlukan" class="form-control datepicker" value="<?php echo date('Y-m-d'); ?>" placeholder="Tanggal Diperlukan">
            </div>

            <div class="col-md-4">
                <label class="form-label" for="jenis_darah">Jenis Darah</label>
                <select name="jenis_darah" id="jenis_darah" class="form-select select2" data-placeholder="Pilih Jenis Darah">
                    <option value="">&nbsp;</option>
                    <?php foreach ($jenis_darah as $jd): ?>
                        <option value="<?php echo $jd['id']; ?>"><?php echo htmlspecialchars($jd['nama_jenis']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="buffycoat">Buffy Coat</label>
                <select name="buffycoat" id="buffycoat" class="form-select select2" data-placeholder="Pilih Tipe Darah">
                    <option value="">&nbsp;</option>
                    <?php foreach ($buffycoat as $bc): ?>
                        <option value="<?php echo $bc['id']; ?>"><?php echo htmlspecialchars($bc['nama_jenis']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="volume">Volume</label>
                <input type="text" name="volume" id="volume" class="form-control" placeholder="Volume">
            </div>

            <div class="col-12">
                <label class="form-label" for="pengambil_darah">Petugas Pengambil Contoh Darah</label>
                <input type="text" name="pengambil_darah" id="pengambil_darah" class="form-control" placeholder="Petugas Pengambil Contoh Darah">
            </div>
        </div>
    </div>
</div>
