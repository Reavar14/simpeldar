<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- ============================================================
     CARD 1 - DATA PASIEN & GOLONGAN DARAH
     ============================================================ -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="fas fa-user-injured text-primary me-2"></i>Data Pasien</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="no_permintaan">Nomor Permintaan</label>
                <input type="text" name="no_permintaan" id="no_permintaan" class="form-control font-monospace" value="<?php echo htmlspecialchars($d['no_permintaan']); ?>" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="tgl_minta">Tanggal Permintaan</label>
                <input type="text" name="tgl_minta" id="tgl_minta" class="form-control datetimepicker" value="<?php echo htmlspecialchars($d['tgl_minta']); ?>" placeholder="Tanggal Permintaan">
            </div>

            <div class="col-md-4">
                <label class="form-label" for="nomr">No. MR</label>
                <input type="text" name="mr" id="nomr" class="form-control" value="<?php echo htmlspecialchars($d['mr']); ?>" readonly>
            </div>
            <div class="col-md-5">
                <label class="form-label" for="nama">Nama Pasien</label>
                <input type="text" name="nama" id="nama" class="form-control" value="<?php echo htmlspecialchars($d['nama']); ?>" readonly>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="id_jenis_kelamin">Jenis Kelamin</label>
                <input type="text" name="id_jenis_kelamin" id="id_jenis_kelamin" class="form-control" value="<?php echo htmlspecialchars($d['jenis_kelamin']); ?>" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="tgl_lahir">Tanggal Lahir</label>
                <input type="text" name="tgl_lahir" id="tgl_lahir" class="form-control" value="<?php echo htmlspecialchars($d['tgl_lahir']); ?>" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="selectgoldarah">Golongan Darah</label>
                <?php
                $selectedGolId = '';
                $rawGol = isset($d['gol_darah']) ? trim((string)$d['gol_darah']) : '';
                $rawId  = isset($d['goldarah']) ? trim((string)$d['goldarah']) : (isset($d['id_gol_darah']) ? trim((string)$d['id_gol_darah']) : '');

                foreach ($goldarah as $b) {
                    $normT = str_replace(['/', ' '], '', strtoupper($rawGol));
                    $normB = str_replace(['/', ' '], '', strtoupper($b['DESKRIPSI']));

                    if (($rawId !== '' && (string)$b['ID'] === $rawId) ||
                        ($rawGol !== '' && ($b['DESKRIPSI'] === $rawGol || $normB === $normT || (strpos($normT, 'TIDAK') !== false && strpos($normB, 'TIDAK') !== false)))) {
                        $selectedGolId = (string)$b['ID'];
                        break;
                    }
                }
                ?>
                <select name="goldarah" id="selectgoldarah" class="form-select select2 idgoldar" data-placeholder="Pilih Golongan Darah">
                    <option value="">&nbsp;</option>
                    <?php foreach ($goldarah as $b): ?>
                        <option value="<?php echo $b['ID']; ?>"<?php if ((string)$b['ID'] === $selectedGolId) echo ' selected'; ?>>
                            <?php echo htmlspecialchars($b['DESKRIPSI']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="id_gol_darah" id="id_gol_darah" value="<?php echo htmlspecialchars($selectedGolId); ?>">
            </div>
        </div>
    </div>
</div>

<!-- Display Golongan Darah Pasien -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3 align-items-center">
            <div class="col-md-8">
                <label class="form-label">Histori Riwayat Alergi Transfusi</label>
                <textarea name="riwayattrans1" id="riwayattrans1" class="form-control" rows="3" readonly placeholder="Histori Riwayat Alergi Transfusi"></textarea>
            </div>
            <div class="col-md-4">
                <div class="goldar-display text-center">
                    <div class="text-muted small text-uppercase fw-bold mb-1">Golongan Darah</div>
                    <span class="goldarah pasien-goldarah"></span>
                    <span id="id-selected" class="small text-muted d-block mt-1"></span>
                </div>
            </div>
        </div>
    </div>
</div>