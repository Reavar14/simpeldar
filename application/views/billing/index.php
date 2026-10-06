<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fas fa-file-invoice-dollar text-primary me-2"></i>Jumlah Billing</h1>
    </div>
</div>

<?php $this->load->view('billing/components/billing_table'); ?>

<script>
window.BILLING_CONFIG = {
    ajaxUrl: "<?php echo base_url('index.php/billing/ajax_list'); ?>",
    detailUrl: "<?php echo base_url('index.php/requestcontroller/detail/'); ?>"
};
</script>
<script src="<?php echo base_url('assets/js/billing.js'); ?>"></script>
