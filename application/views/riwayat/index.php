<?php defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fas fa-folder-open text-primary me-2"></i>Riwayat Pasien</h1>
        <p class="page-subtitle">Pencarian riwayat permintaan darah pasien berdasarkan periode dan nomor MR</p>
    </div>
</div>

<?php $this->load->view('riwayat/components/filter_card'); ?>
<?php $this->load->view('riwayat/components/history_table'); ?>

<script>
window.RIWAYAT_CONFIG = {
    ajaxUrl: "<?php echo base_url('index.php/riwayat/ajax_list'); ?>",
    editUrl: "<?php echo base_url('index.php/requestcontroller/edit/'); ?>",
    formUrl: "<?php echo base_url('index.php/requestcontroller/print_form/'); ?>"
};
</script>
<script src="<?php echo base_url('assets/js/riwayat.js'); ?>"></script>
