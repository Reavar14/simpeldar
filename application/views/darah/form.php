<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- ============================================================
     FORM PERMINTAAN DARAH (Bootstrap 5)
     View entry point - semua field POST dipertahankan
     ============================================================ -->
<form id="formPermintaanDarah"
      action="<?php echo base_url('index.php/darah/submit'); ?>"
      method="post"
      data-loading-submit
      novalidate>
    <input type="hidden"
           name="<?= $this->security->get_csrf_token_name(); ?>"
           value="<?= $this->security->get_csrf_hash(); ?>">

    <!-- CARD 1: Data Pasien -->
    <?php $this->load->view('darah/components/patient_card'); ?>

    <!-- CARD 2: Data Permintaan Darah -->
    <?php $this->load->view('darah/components/request_card'); ?>

    <!-- CARD 3: Data Klinis -->
    <?php $this->load->view('darah/components/examination_card'); ?>

    <!-- CARD 4: Data Kantong Darah (loop 1-12) -->
    <?php $this->load->view('darah/components/blood_component_card'); ?>

    <!-- Submit / Reset -->
    <?php $this->load->view('darah/components/action_button'); ?>

</form>

<!-- Config untuk form-darah.js -->
<script>
window.FORM_DARAH = {
    autofillUrl: "<?php echo base_url('index.php/darah/autofill_pasien'); ?>",
    lookupUrl:   "<?php echo base_url('index.php/darah/lookup_kantong'); ?>",
    formUrl:     "<?php echo base_url('index.php/darah/form'); ?>",
    submitUrl:   "<?php echo base_url('index.php/darah/submit'); ?>"
};
</script>

    <!-- Form logic (dipisah dari view) -->
    <script src="<?php echo base_url('assets/js/form-darah.js'); ?>?v=20241006"></script>