<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="page-header mb-4">
    <h1 class="h3"><i class="fas fa-print me-2"></i>Cetakan</h1>
    <p class="text-muted">Pilih filter dan cetak laporan</p>
</div>

<div class="row">
    <div class="col-12">
        <?php $this->load->view('cetakan/components/filter_card'); ?>
        <?php $this->load->view('cetakan/components/export_card'); ?>
    </div>
</div>

<script src="<?php echo base_url('assets/js/cetakan.js'); ?>"></script>