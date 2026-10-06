<?php defined('BASEPATH') OR exit('No direct script access allowed');
$is_limited = isset($is_limited) ? (bool)$is_limited : false;
$ro  = $is_limited ? ' readonly' : '';
$dis = $is_limited ? ' disabled' : '';
?>

<!-- ============================================================
     CARD 2 - DATA PERMINTAAN DARAH
     ============================================================ -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="fas fa-tint text-primary me-2"></i>Data Permintaan Darah</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label" for="jenis_darah">Jenis Darah</label>
                <select name="jenis_darah" id="jenis_darah" class="form-select select2" data-placeholder="Pilih Jenis Darah"<?php echo $dis; ?>>
                    <option value="">&nbsp;</option>
                    <?php foreach ($jenis_darah as $b): ?>
                        <option value="<?php echo $b['id']; ?>"<?php if ($d['jenis_darah'] == $b['id']) echo ' selected'; ?>>
                            <?php echo htmlspecialchars($b['nama_jenis']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="buffycoat">Tipe Darah (Buffy Coat)</label>
                <select name="buffycoat" id="buffycoat" class="form-select select2" data-placeholder="Pilih Tipe Darah"<?php echo $dis; ?>>
                    <option value="">&nbsp;</option>
                    <?php foreach ($buffycoat as $b): ?>
                        <option value="<?php echo $b['id']; ?>"<?php if ($d['buffycoat'] == $b['id']) echo ' selected'; ?>>
                            <?php echo htmlspecialchars($b['nama_jenis']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="volume">Volume</label>
                <input type="text" name="volume" id="volume" class="form-control" value="<?php echo htmlspecialchars($d['volume']); ?>" placeholder="Volume"<?php echo $ro; ?>>
            </div>

            <div class="col-md-6">
                <label class="form-label" for="tgl_diperlukan">Tanggal Diperlukan</label>
                <input type="text" name="tgl_diperlukan" id="tgl_diperlukan" class="form-control datepicker" value="<?php echo htmlspecialchars($d['tgl_diperlukan']); ?>" placeholder="Tanggal Diperlukan">
            </div>
            <div class="col-md-6">
                <label class="form-label" for="status">Status</label>
                <select name="status" id="status" class="form-select select2" data-placeholder="Pilih Status"<?php echo $dis; ?>>
                    <option value="">&nbsp;</option>
                    <?php foreach ($status as $b): ?>
                        <option value="<?php echo $b['ID']; ?>"<?php if ($d['status_proses'] == $b['DESKRIPSI']) echo ' selected'; ?>>
                            <?php echo htmlspecialchars($b['DESKRIPSI']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label" for="dpjp">Dokter DPJP</label>
                <select name="dpjp" id="dpjp" class="form-select select2" data-placeholder="Pilih Dokter DPJP"<?php echo $dis; ?>>
                    <option value="">&nbsp;</option>
                    <?php foreach ($dokter as $b): ?>
                        <option value="<?php echo $b['ID']; ?>"<?php if ($d['dpjp'] == $b['ID']) echo ' selected'; ?>>
                            <?php echo htmlspecialchars($b['dokter']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="ruangan">Ruangan Rawat</label>
                <select name="ruangan" id="ruangan" class="form-select select2" data-placeholder="Pilih Ruangan Rawat"<?php echo $dis; ?>>
                    <option value="">&nbsp;</option>
                    <?php foreach ($ruangan as $b): ?>
                        <option value="<?php echo $b['ID']; ?>"<?php if ($d['ruangan'] == $b['ID']) echo ' selected'; ?>>
                            <?php echo htmlspecialchars($b['DESKRIPSI']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label" for="alasan">Alasan</label>
                <input type="text" name="alasan" id="alasan" class="form-control" value="<?php echo htmlspecialchars($d['alasan']); ?>" placeholder="Alasan"<?php echo $ro; ?>>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="diagnosa">Diagnosa</label>
                <input type="text" name="diagnosa" id="diagnosa" class="form-control" value="<?php echo htmlspecialchars($d['diagnosa']); ?>" placeholder="Diagnosa"<?php echo $ro; ?>>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="trombosit">Trombosit</label>
                <input type="text" name="trombosit" id="trombosit" class="form-control" value="<?php echo htmlspecialchars($d['trombosit']); ?>" placeholder="Trombosit"<?php echo $ro; ?>>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="kadar_hb">Kadar HB</label>
                <input type="text" name="kadar_hb" id="kadar_hb" class="form-control" value="<?php echo htmlspecialchars($d['kadar_hb']); ?>" placeholder="Kadar HB"<?php echo $ro; ?>>
            </div>

            <div class="col-md-3">
                <label class="form-label" for="analis">Analis PTTD 1</label>
                <select name="analis" id="analis" class="form-select select2" data-placeholder="Pilih Analis"<?php echo $dis; ?>>
                    <option value="">&nbsp;</option>
                    <?php foreach ($analis as $b): ?>
                        <option value="<?php echo $b['id_analis']; ?>"<?php if ($d['analis'] == $b['id_analis']) echo ' selected'; ?>>
                            <?php echo htmlspecialchars($b['nama_analis']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="tujuan">Tujuan</label>
                <select name="tujuan" id="tujuan" class="form-select select2" data-placeholder="Pilih Tujuan"<?php echo $dis; ?>>
                    <option value="">&nbsp;</option>
                    <?php foreach ($tujuan as $b): ?>
                        <option value="<?php echo $b['id_variabel']; ?>"<?php if ($d['ID_TUJUAN'] == $b['id_variabel']) echo ' selected'; ?>>
                            <?php echo htmlspecialchars($b['variabel']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="hasil_pemeriksaan">Hasil Pemeriksaan</label>
                <select name="hasil_pemeriksaan" id="hasil_pemeriksaan" class="form-select select2" data-placeholder="Pilih Hasil Pemeriksaan"<?php echo $dis; ?>>
                    <option value="">&nbsp;</option>
                    <?php foreach ($hasil_pemeriksaan as $b): ?>
                        <option value="<?php echo $b['ID']; ?>"<?php if ($d['hasil_pemeriksaan'] == $b['ID']) echo ' selected'; ?>>
                            <?php echo htmlspecialchars($b['DESKRIPSI']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="perawat">Perawat</label>
                <select name="perawat" id="perawat" class="form-select select2" data-placeholder="Pilih Perawat"<?php echo $dis; ?>>
                    <option value="">&nbsp;</option>
                    <?php foreach ($perawat as $b): ?>
                        <option value="<?php echo $b['id_perawat']; ?>"<?php if ($d['perawat'] == $b['id_perawat']) echo ' selected'; ?>>
                            <?php echo htmlspecialchars($b['nama_perawat']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label" for="pengambil_darah">Petugas Pengambil Contoh Darah</label>
                <input type="text" name="pengambil_darah" id="pengambil_darah" class="form-control" value="<?php echo htmlspecialchars($d['pengambil_darah']); ?>" placeholder="Petugas Pengambil Contoh Darah"<?php echo $ro; ?>>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="riwayattrans">Riwayat Alergi Transfusi & Catatan</label>
                <input type="text" name="riwayattrans" id="riwayattrans" class="form-control" value="<?php echo htmlspecialchars($d['riwayattrans']); ?>" placeholder="Riwayat Alergi Transfusi dan Catatan"<?php echo $ro; ?>>
            </div>

            <div class="col-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="kelengkapan" id="kelengkapan" value="1"<?php if ($d['kelengkapan'] == 1) echo ' checked'; ?><?php echo $dis; ?>>
                    <label class="form-check-label" for="kelengkapan">Sudah Lengkap</label>
                </div>
            </div>
        </div>
    </div>
</div>
