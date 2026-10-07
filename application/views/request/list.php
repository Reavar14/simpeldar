<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php $this->load->view('request/components/list_table'); ?>

<script>
window.REQUEST_LIST_CONFIG = {
    ajaxUrl: "<?php echo base_url('index.php/requestcontroller/ajax_list'); ?>",
    editUrl: "<?php echo base_url('index.php/requestcontroller/edit/'); ?>",
    detailUrl: "<?php echo base_url('index.php/requestcontroller/detail/'); ?>",
    bonUrl: "<?php echo base_url('index.php/requestcontroller/cetak_bon_darah?id='); ?>",
    formUrl: "<?php echo base_url('index.php/requestcontroller/cetak_form_darah?id='); ?>",
    detailDarahUrl: "<?php echo base_url('index.php/requestcontroller/print_detail_darah/'); ?>",
    hasilUrl: "<?php echo base_url('index.php/requestcontroller/print_hasil_pemeriksaan/'); ?>",
    terimaUrl: "<?php echo base_url('index.php/requestcontroller/mark_received'); ?>"
};
</script>
<script src="<?php echo base_url('assets/js/request-list.js'); ?>" defer></script>