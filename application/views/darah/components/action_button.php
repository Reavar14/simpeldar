<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- ============================================================
     ACTION BUTTONS
     ============================================================ -->
<div class="card border-primary">
    <div class="card-body d-flex flex-wrap gap-2 align-items-center">
        <button type="submit"
                name="submit"
                id="btnSimpan"
                class="btn btn-primary btn-lg px-5"
                data-loading-text="Menyimpan...">
            <i class="fas fa-floppy-disk me-1"></i> Simpan Permintaan
        </button>
        <button type="reset" class="btn btn-outline-danger btn-lg" id="btnReset">
            <i class="fas fa-rotate-left me-1"></i> Reset
        </button>
        <div class="small text-muted ms-auto">
            <i class="fas fa-info-circle me-1"></i> Pastikan data pasien &amp; permintaan terisi sebelum menyimpan
        </div>
    </div>
</div>