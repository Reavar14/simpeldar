<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-12">
        <?php $this->load->view('cetakan/components/entry_bank_darah_card'); ?>
        <?php $this->load->view('cetakan/components/rekap_analis_card'); ?>
    </div>
</div>

<script src="<?php echo base_url('assets/js/cetakan.js'); ?>"></script>
