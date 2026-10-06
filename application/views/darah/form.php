<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<style>
.form-darah-section-title {
    display: flex;
    align-items: center;
    gap: 10px;
    height: 50px;
    padding: 0 15px;
    margin-bottom: 10px;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px 4px 0 0;
    color: #1f2937;
    font-size: 16px;
    font-weight: 600;
}
.form-darah-section-title i {
    color: #0d6efd;
    font-size: 15px;
}
.form-darah-title i {
    font-size: 15px;
}
.form-darah-native {
    font-size: 13px;
}
.form-darah-native .row {
    margin-left: -5px;
    margin-right: -5px;
}
.form-darah-native [class*="col-"] {
    padding-left: 5px;
    padding-right: 5px;
}
/* Input dan select */
.form-darah-native .form-control,
.form-darah-native .form-select {
    height: 34px;
    min-height: 34px;
    border-radius: 3px;
    font-size: 13px;
}
/* Textarea */
.form-darah-native textarea.form-control {
    height: auto;
    min-height: 70px;
    resize: vertical;
}
/* Select2 */
.form-darah-native .select2-container {
    width: 100% !important;
}
.form-darah-native .select2-container .select2-selection--single {
    height: 34px !important;
    min-height: 34px !important;
    border-radius: 3px !important;
}
.form-darah-native
.select2-container
.select2-selection--single
.select2-selection__rendered {
    line-height: 32px !important;
    font-size: 13px;
    padding-left: 10px;
}
.form-darah-native
.select2-container
.select2-selection--single
.select2-selection__arrow {
    height: 32px !important;
}
/* Autofill */
.form-darah-native #btnAutofill {
    height: 34px;
}
/* Alert */
.form-darah-native #alertStatusDarah {
    margin-top: 4px;
    margin-bottom: 8px;
    padding: 10px 15px;
    border-radius: 3px;
    font-size: 13px;
}
/* Goldar pasien */
.form-darah-native .goldar-display {
    min-height: 70px;
}
.form-darah-native .pasien-goldarah {
    font-size: 3rem;
    line-height: 1.2;
}
.form-darah-native .status-native-wrapper {
    display: flex;
    width: 100%;
}
.form-darah-native .status-select {
    flex: 1;
    min-width: 0;
}
.form-darah-native .status-label {
    white-space: nowrap;
    border: 1px solid #ced4da;
    background-color: #f5f5f5;
}
/* Judul section */
.form-darah-native .native-section-title {
    margin-top: 12px;
    margin-bottom: 8px;
    padding: 7px 10px;
    border-top: 1px solid #ddd;
    border-bottom: 1px solid #ddd;
    background: #f5f5f5;
    font-size: 13px;
    font-weight: 600;
}
/* TABEL KANTONG */
.kantong-table-wrapper {
    width: 100%;
    overflow-x: auto;
    margin-top: 5px;
}
.kantong-table {
    width: 100%;
    min-width: 1100px;
    table-layout: fixed;
    border-collapse: collapse;
    font-size: 10px;
}
.kantong-table th {
    padding: 4px 3px;
    border: 1px solid #ccc;
    background: #f1f1f1;
    text-align: center;
    vertical-align: middle;
    white-space: nowrap;
    font-weight: 600;
}
.kantong-table td {
    padding: 3px;
    border: 1px solid #ccc;
    vertical-align: middle;
}
.kantong-table tbody tr:nth-child(odd) {
    background: #f8f8f8;
}
.kantong-table tbody tr:nth-child(even) {
    background: #fff;
}
.kantong-table input,
.kantong-table select {
    width: 100%;
    height: 28px;
    min-height: 28px;
    padding: 2px 4px;
    font-size: 10px;
    border-radius: 2px;
}
.kantong-table .select2-container {
    width: 100% !important;
}
.kantong-table
.select2-container
.select2-selection--single {
    height: 28px !important;
    min-height: 28px !important;
    font-size: 10px;
}
.kantong-table
.select2-container
.select2-selection--single
.select2-selection__rendered {
    line-height: 26px !important;
    font-size: 10px;
    padding-left: 4px;
    padding-right: 18px;
}
.kantong-table
.select2-container
.select2-selection--single
.select2-selection__arrow {
    height: 26px !important;
}
/* Lebar mengikuti grid native */
.kantong-table th:nth-child(1),
.kantong-table td:nth-child(1) { width: 14%; }

.kantong-table th:nth-child(2),
.kantong-table td:nth-child(2) { width: 5%; }

.kantong-table th:nth-child(3),
.kantong-table td:nth-child(3) { width: 12%; }

.kantong-table th:nth-child(4),
.kantong-table td:nth-child(4) { width: 15%; }

.kantong-table th:nth-child(5),
.kantong-table td:nth-child(5) { width: 6%; }

.kantong-table th:nth-child(6),
.kantong-table td:nth-child(6) { width: 8%; }

.kantong-table th:nth-child(7),
.kantong-table td:nth-child(7) { width: 8%; }

.kantong-table th:nth-child(8),
.kantong-table td:nth-child(8) { width: 16%; }

.kantong-table th:nth-child(9),
.kantong-table td:nth-child(9) { width: 7%; }

.kantong-table th:nth-child(10),
.kantong-table td:nth-child(10) { width: 7%; }
/* Tombol */
.form-darah-native .form-action {
    margin-top: 15px;
    padding-top: 10px;
    border-top: 1px solid #ddd;
    display: flex;
    gap: 8px;
}

/* SPACING */
.form-darah-native .mb-2 {
    margin-bottom: 5px !important;
}
.form-darah-native .mb-3 {
    margin-bottom: 8px !important;
}
</style>

<div class="form-darah-section-title">
    <i class="fas fa-user-injured"></i>
    <span>Form Input Darah</span>
</div>

<form id="formPermintaanDarah"
      action="<?php echo base_url('index.php/darah/submit'); ?>"
      method="post"
      data-loading-submit
      novalidate>

    <input type="hidden"
           name="<?= $this->security->get_csrf_token_name(); ?>"
           value="<?= $this->security->get_csrf_hash(); ?>">

    <div class="form-darah-native">
        <?php $this->load->view('darah/components/patient_card'); ?>
        <?php $this->load->view('darah/components/request_card'); ?>
        <?php $this->load->view('darah/components/examination_card'); ?>
        <?php $this->load->view('darah/components/blood_component_card'); ?>
        <?php $this->load->view('darah/components/action_button'); ?>

    </div>
</form>

<script>
window.FORM_DARAH = {
    autofillUrl: "<?php echo base_url('index.php/darah/autofill_pasien'); ?>",
    lookupUrl:   "<?php echo base_url('index.php/darah/lookup_kantong'); ?>",
    formUrl:     "<?php echo base_url('index.php/darah/form'); ?>",
    submitUrl:   "<?php echo base_url('index.php/darah/submit'); ?>"
};
</script>

<script src="<?php echo base_url('assets/js/form-darah.js'); ?>"></script>