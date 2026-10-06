<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!-- 1. NOMOR PERMINTAAN + TANGGAL PERMINTAAN -->
<div class="row g-2 mb-2">
    <!-- Nomor Permintaan -->
    <div class="col-md-6">
        <input type="text"
               name="no_permintaan"
               id="no_permintaan"
               class="form-control font-monospace"
               value="<?php echo htmlspecialchars($no_permintaan); ?>"
               readonly>
    </div>
    <!-- Tanggal Permintaan -->
    <div class="col-md-6">
        <div class="input-group">
            <input type="text"
                   name="tgl_minta"
                   id="tgl_minta"
                   class="form-control datetimepicker"
                   value="<?php echo date('Y-m-d H:i:s'); ?>"
                   placeholder="Tanggal Permintaan">
            <span class="input-group-text">
                <i class="fas fa-calendar-alt me-1"></i> Tgl Permintaan
            </span>
        </div>
    </div>
</div>
<!-- 2. DATA PASIEN -->
<div class="row g-2 mb-2">
    <!-- Nomor MR -->
    <div class="col-md-2">
        <div class="input-group">
            <input type="text"
                   name="mr"
                   id="nomr"
                   class="form-control"
                   placeholder="Nomor MR"
                   autocomplete="off">
            <button class="btn btn-outline-primary"
                    type="button"
                    id="btnAutofill"
                    data-bs-toggle="tooltip"
                    title="Cari data pasien">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>
    <!-- Nama Pasien -->
    <div class="col-md-4">
        <input type="text"
               name="nama"
               id="nama"
               class="form-control"
               placeholder="Nama Pasien"
               readonly>
    </div>
    <!-- Jenis Kelamin -->
    <div class="col-md-2">
        <input type="text"
               id="jenis_kelamin"
               class="form-control"
               placeholder="Jenis Kelamin"
               readonly>
        <input type="hidden"
               name="id_jenis_kelamin"
               id="id_jenis_kelamin">
    </div>
    <!-- Tanggal Lahir -->
    <div class="col-md-4">
        <div class="input-group">
            <input type="text"
                   id="TANGGAL_LAHIR"
                   class="form-control"
                   placeholder="Tanggal Lahir"
                   readonly>
            <span class="input-group-text">
                <i class="fas fa-calendar-alt me-1"></i> Tgl Lahir
            </span>
        </div>
        <input type="hidden"
               name="tgl_lahir"
               id="tgl_lahir">
    </div>
</div>
<!-- 3. UMUR -->
<input type="hidden"
       id="umur"
       value="">