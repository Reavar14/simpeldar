<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php if (!empty($d)): ?>
<form id="formEditPermintaan"
      action="<?php echo base_url('index.php/requestcontroller/update'); ?>"
      method="post"
      data-loading-submit
      novalidate>
    <input type="hidden"
           name="<?= $this->security->get_csrf_token_name(); ?>"
           value="<?= $this->security->get_csrf_hash(); ?>">

    <?php $this->load->view('request/components/edit_patient_card'); ?>
    <?php $this->load->view('request/components/edit_request_card'); ?>
    <?php $this->load->view('request/components/edit_examination_card'); ?>
    <?php $this->load->view('request/components/edit_bag_card'); ?>
    <?php $this->load->view('request/components/edit_action_button'); ?>

</form>

<?php else: ?>
<div class="card">
    <div class="card-body text-center py-5">
        <div class="empty-state">
            <i class="fas fa-exclamation-triangle empty-icon"></i>
            <h5>Data Tidak Ditemukan</h5>
            <p>Permintaan darah tidak ditemukan.</p>
            <a href="<?php echo base_url('index.php/requestcontroller/list'); ?>" class="btn btn-primary mt-2">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
window.REQUEST_EDIT_CONFIG = {
    autofillUrl: "<?php echo base_url('index.php/darah/autofill_pasien'); ?>",
    lookupUrl:   "<?php echo base_url('index.php/darah/lookup_kantong'); ?>",
    printFormUrl: "<?php echo base_url('index.php/requestcontroller/print_form/'); ?>"
};
</script>
<script src="<?php echo base_url('assets/js/request-edit.js'); ?>" defer></script>