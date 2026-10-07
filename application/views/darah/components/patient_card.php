<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- ============================================================
     CARD 1 - DATA PASIEN
     ============================================================ -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-user-injured text-primary me-2"></i> Data Pasien
        </h5>
    </div>
    <div class="card-body">

        <!-- Alert status darah sebelumnya -->
        <div class="alert alert-danger d-flex align-items-center mb-3 d-none" id="alertStatusDarah" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <div><strong>Perhatian!</strong> Status Darah Masih Ada. Periksa Status Darah Sebelumnya.</div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="nomr">Nomor MR <span class="required">*</span></label>
                <div class="input-group">
                    <input type="text" name="mr" id="nomr" class="form-control" placeholder="Nomor MR" autocomplete="off">
                    <button class="btn btn-outline-primary" type="button" id="btnAutofill" data-bs-toggle="tooltip" title="Cari data pasien">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="nama">Nama Pasien</label>
                <input type="text" name="nama" id="nama" class="form-control" placeholder="Nama Pasien" readonly>
            </div>

            <div class="col-md-6">
                <label class="form-label" for="jenis_kelamin">Jenis Kelamin</label>
                <input type="text" id="jenis_kelamin" class="form-control" placeholder="Jenis Kelamin" readonly>
                <input type="hidden" name="id_jenis_kelamin" id="id_jenis_kelamin">
            </div>
            <div class="col-md-6">
                <label class="form-label" for="TANGGAL_LAHIR">Tanggal Lahir</label>
                <input type="text" id="TANGGAL_LAHIR" class="form-control" placeholder="Tanggal Lahir" readonly>
                <input type="hidden" name="tgl_lahir" id="tgl_lahir">
            </div>

            <div class="col-md-6">
                <label class="form-label" for="umur">Umur</label>
                <input type="text" id="umur" class="form-control" placeholder="Umur" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="selectgoldarah">Golongan Darah</label>
                <select name="selectgoldarah" id="selectgoldarah" class="form-select select2" data-placeholder="Pilih Golongan Darah">
                    <option value="">&nbsp;</option>
                    <?php foreach ($goldarah as $gd): ?>
                        <option value="<?php echo $gd['ID']; ?>"><?php echo htmlspecialchars($gd['DESKRIPSI']); ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="id_gol_darah" id="id_gol_darah">
            </div>
        </div>

        <hr class="my-4">

        <div class="row g-3 align-items-stretch">
            <div class="col-md-8">
                <label class="form-label" for="riwayattrans1">Histori Riwayat Alergi Transfusi &amp; Catatan</label>
                <textarea class="form-control" rows="3" id="riwayattrans1" name="riwayattrans1" placeholder="Histori Riwayat Alergi Transfusi dan Catatan" readonly></textarea>

                <label class="form-label mt-3" for="riwayattrans">Riwayat Alergi Transfusi dan Catatan</label>
                <input type="text" name="riwayattrans" id="riwayattrans" class="form-control" placeholder="Riwayat Alergi Transfusi dan Catatan">
                <input type="hidden" name="notifsatus" id="notifsatus">
            </div>
            <div class="col-md-4">
                <div class="goldar-display h-100 d-flex flex-column align-items-center justify-content-center text-center">
                    <div class="small text-muted fw-bold text-uppercase mb-1">Golongan Darah</div>
                    <span class="goldarah pasien-goldarah fw-bolder" style="font-size: 3rem; line-height: 1.2;"></span>
                    <span id="id-selected" class="small text-muted"></span>
                </div>
            </div>
        </div>
    </div>
</div>
