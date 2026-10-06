<?php defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fas fa-file-alt text-primary me-2"></i>Laporan Pelayanan Darah</h1>
        <p class="page-subtitle">Laporan pelayanan, jumlah permintaan, jenis permintaan, dan billing</p>
    </div>
</div>

<ul class="nav nav-tabs mb-4" id="laporanTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="tabPelayanan" data-bs-toggle="tab" data-bs-target="#panelPelayanan" type="button" role="tab">
            <i class="fas fa-file-medical me-1"></i> Laporan Pelayanan
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tabPermintaan" data-bs-toggle="tab" data-bs-target="#panelPermintaan" type="button" role="tab">
            <i class="fas fa-chart-bar me-1"></i> Jumlah Permintaan
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tabJenis" data-bs-toggle="tab" data-bs-target="#panelJenis" type="button" role="tab">
            <i class="fas fa-chart-pie me-1"></i> Jumlah Jenis
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tabBilling" data-bs-toggle="tab" data-bs-target="#panelBilling" type="button" role="tab">
            <i class="fas fa-file-invoice-dollar me-1"></i> Billing Kantong
        </button>
    </li>
</ul>

<div class="tab-content" id="laporanTabsContent">

    <div class="tab-pane fade show active" id="panelPelayanan" role="tabpanel">
        <?php $this->load->view('laporan/components/filter_card'); ?>
        <?php $this->load->view('laporan/components/pelayanan_table'); ?>
    </div>

    <div class="tab-pane fade" id="panelPermintaan" role="tabpanel">
        <?php $this->load->view('laporan/components/permintaan_table'); ?>
    </div>

    <div class="tab-pane fade" id="panelJenis" role="tabpanel">
        <?php $this->load->view('laporan/components/jenis_table'); ?>
    </div>

    <div class="tab-pane fade" id="panelBilling" role="tabpanel">
        <?php $this->load->view('laporan/components/billing_table'); ?>
    </div>

</div>

<script>
window.LAPORAN_CONFIG = {
    pelayananUrl:   "<?php echo base_url('index.php/laporan/ajax_pelayanan'); ?>",
    permintaanUrl:  "<?php echo base_url('index.php/laporan/ajax_jumlah_permintaan'); ?>",
    jenisUrl:       "<?php echo base_url('index.php/laporan/ajax_jumlah_jenis'); ?>",
    billingUrl:     "<?php echo base_url('index.php/laporan/ajax_billing'); ?>"
};
</script>
<script src="<?php echo base_url('assets/js/laporan.js'); ?>"></script>