<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
// Mode edit: 'full_edit' (View Proses) | 'limited_edit' (View Proses Rawat Inap)
$edit_mode  = isset($edit_mode) ? $edit_mode : 'full_edit';
$is_limited = ($edit_mode === 'limited_edit');
$ro         = $is_limited ? ' readonly' : '';
$dis        = $is_limited ? ' disabled' : '';

// Goldar map for display
$goldarMap = array(
    '1'  => array('text' => 'A',   'color' => '#7c3aed'),
    '2'  => array('text' => 'B',   'color' => '#dc3545'),
    '3'  => array('text' => 'AB',  'color' => '#198754'),
    '4'  => array('text' => 'O',   'color' => '#0d6efd'),
    '5'  => array('text' => 'A+',  'color' => '#7c3aed'),
    '6'  => array('text' => 'A-',  'color' => '#7c3aed'),
    '7'  => array('text' => 'B+',  'color' => '#dc3545'),
    '8'  => array('text' => 'B-',  'color' => '#dc3545'),
    '9'  => array('text' => 'AB+', 'color' => '#198754'),
    '10' => array('text' => 'AB-', 'color' => '#198754'),
    '11' => array('text' => 'O-',  'color' => '#0d6efd'),
    '12' => array('text' => 'O+',  'color' => '#0d6efd'),
    '13' => array('text' => 'Tidak Tahu', 'color' => ''),
);

// Determine selected goldar ID for display
$selectedGolId = '';
$rawGol = isset($d['gol_darah']) ? trim((string)$d['gol_darah']) : '';
$rawId  = isset($d['goldarah']) ? trim((string)$d['goldarah']) : (isset($d['id_gol_darah']) ? trim((string)$d['id_gol_darah']) : '');

if ($goldarah ?? false) {
    foreach ($goldarah as $b) {
        $normT = str_replace(['/', ' '], '', strtoupper($rawGol));
        $normB = str_replace(['/', ' '], '', strtoupper($b['DESKRIPSI']));
        if (($rawId !== '' && (string)$b['ID'] === $rawId) ||
            ($rawGol !== '' && ($b['DESKRIPSI'] === $rawGol || $normB === $normT || (strpos($normT, 'TIDAK') !== false && strpos($normB, 'TIDAK') !== false)))) {
            $selectedGolId = (string)$b['ID'];
            break;
        }
    }
}
$initGoldar = isset($goldarMap[$selectedGolId]) ? $goldarMap[$selectedGolId] : null;
$initText   = $initGoldar ? $initGoldar['text'] : '';
$initColor  = $initGoldar ? $initGoldar['color'] : '';
$initSize   = ($selectedGolId === '13') ? '2rem' : '3rem';
?>

<?php if (!empty($d)): ?>
<style>
    /* ==================== FORM DARAH NATIVE STYLE (exact copy from darah/form.php) ==================== */
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
    <span><?php echo ($edit_mode === 'limited_edit') ? 'Edit Permintaan Darah (Rawat Inap)' : 'Edit Permintaan Darah'; ?></span>
</div>

<form id="formEditPermintaan"
      action="<?php echo base_url('index.php/requestcontroller/update'); ?>"
      method="post"
      data-loading-submit
      data-edit-mode="<?php echo htmlspecialchars($edit_mode); ?>"
      novalidate>
    <input type="hidden"
           name="<?= $this->security->get_csrf_token_name(); ?>"
           value="<?= $this->security->get_csrf_hash(); ?>">
    <input type="hidden" name="edit_mode" value="<?php echo htmlspecialchars($edit_mode); ?>">

    <div class="form-darah-native">
        <!-- ==================== 1. DATA PASIEN (patient_card) ==================== -->
        <!-- NOMOR PERMINTAAN + TANGGAL PERMINTAAN -->
        <div class="row g-2 mb-2">
            <!-- Nomor Permintaan -->
            <div class="col-md-6">
                <input type="text"
                       name="no_permintaan"
                       id="no_permintaan"
                       class="form-control font-monospace"
                       value="<?php echo htmlspecialchars($d['no_permintaan'] ?? ''); ?>"
                       readonly>
            </div>
            <!-- Tanggal Permintaan -->
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text"
                           name="tgl_minta"
                           id="tgl_minta"
                           class="form-control datetimepicker"
                           value="<?php echo htmlspecialchars($d['tgl_minta'] ?? ''); ?>"
                           placeholder="Tanggal Permintaan"<?php echo $ro; ?>>
                    <span class="input-group-text">
                        <i class="fas fa-calendar-alt me-1"></i> Tgl Permintaan
                    </span>
                </div>
            </div>
        </div>
        <!-- DATA PASIEN -->
        <div class="row g-2 mb-2">
            <!-- Nomor MR -->
            <div class="col-md-2">
                <div class="input-group">
                    <input type="text"
                           name="mr"
                           id="nomr"
                           class="form-control"
                           value="<?php echo htmlspecialchars($d['mr'] ?? ''); ?>"
                           placeholder="Nomor MR"
                           autocomplete="off"<?php echo $ro; ?>>
                    <button class="btn btn-outline-primary"
                            type="button"
                            id="btnAutofill"
                            data-bs-toggle="tooltip"
                            title="Cari data pasien"<?php echo $dis; ?>>
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <!-- Nama Pasien -->
            <div class="col-md-4">
                <input type="text"
                       name="nama"
                       id="nama"
                       class="form-control"
                       value="<?php echo htmlspecialchars($d['nama'] ?? ''); ?>"
                       placeholder="Nama Pasien"
                       readonly>
            </div>
            <!-- Jenis Kelamin -->
            <div class="col-md-2">
                <input type="text"
                       id="jenis_kelamin"
                       class="form-control"
                       value="<?php echo htmlspecialchars($d['jenis_kelamin'] ?? ''); ?>"
                       placeholder="Jenis Kelamin"
                       readonly>
                <input type="hidden"
                       name="id_jenis_kelamin"
                       id="id_jenis_kelamin"
                       value="<?php echo htmlspecialchars($d['id_jenis_kelamin'] ?? ''); ?>">
            </div>
            <!-- Tanggal Lahir -->
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text"
                           id="TANGGAL_LAHIR"
                           class="form-control"
                           value="<?php echo htmlspecialchars($d['tgl_lahir'] ?? ''); ?>"
                           placeholder="Tanggal Lahir"
                           readonly>
                    <span class="input-group-text">
                        <i class="fas fa-calendar-alt me-1"></i> Tgl Lahir
                    </span>
                </div>
                <input type="hidden"
                       name="tgl_lahir"
                       id="tgl_lahir"
                       value="<?php echo htmlspecialchars($d['tgl_lahir'] ?? ''); ?>">
            </div>
        </div>
        <!-- UMUR -->
        <input type="hidden"
               id="umur"
               value="">

        <!-- ==================== 2. DATA PERMINTAAN DARAH (request_card) ==================== -->
        <!-- GOLONGAN DARAH + JENIS DARAH + TIPE DARAH -->
        <div class="row g-2 mb-2">
            <!-- Golongan Darah -->
            <div class="col-md-6">
                <select name="goldarah"
                        id="selectgoldarah"
                        class="form-select select2 idgoldar"
                        data-placeholder="Golongan Darah"<?php echo $dis; ?>>
                    <option value="">&nbsp;</option>
                    <?php
                    $selectedGolId = '';
                    $rawGol = isset($d['gol_darah']) ? trim((string)$d['gol_darah']) : '';
                    $rawId  = isset($d['goldarah']) ? trim((string)$d['goldarah']) : (isset($d['id_gol_darah']) ? trim((string)$d['id_gol_darah']) : '');

                    if ($goldarah ?? false) {
                        foreach ($goldarah as $b) {
                            $normT = str_replace(['/', ' '], '', strtoupper($rawGol));
                            $normB = str_replace(['/', ' '], '', strtoupper($b['DESKRIPSI']));

                            if (($rawId !== '' && (string)$b['ID'] === $rawId) ||
                                ($rawGol !== '' && ($b['DESKRIPSI'] === $rawGol || $normB === $normT || (strpos($normT, 'TIDAK') !== false && strpos($normB, 'TIDAK') !== false)))) {
                                $selectedGolId = (string)$b['ID'];
                                break;
                            }
                        }
                    }
                    ?>
                    <option value="">&nbsp;</option>
                    <?php foreach ($goldarah as $b): ?>
                        <option value="<?php echo $b['ID']; ?>"<?php if ((string)$b['ID'] === $selectedGolId) echo ' selected'; ?>>
                            <?php echo htmlspecialchars($b['DESKRIPSI']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden"
                       name="id_gol_darah"
                       id="id_gol_darah"
                       value="<?php echo htmlspecialchars($selectedGolId); ?>">
            </div>
            <!-- Jenis Darah -->
            <div class="col-md-3">
                <select name="jenis_darah"
                        id="jenis_darah"
                        class="form-select select2"
                        data-placeholder="Pilih Jenis Darah"<?php echo $dis; ?>>
                    <option value="">&nbsp;</option>
                    <?php foreach ($jenis_darah as $jd): ?>
                        <option value="<?php echo $jd['id']; ?>"<?php if ((string)$jd['id'] === (string)($d['jenis_darah'] ?? '')) echo ' selected'; ?>>
                            <?php echo htmlspecialchars($jd['nama_jenis']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Tipe Darah / Buffy Coat -->
            <div class="col-md-3">
                <select name="buffycoat"
                        id="buffycoat"
                        class="form-select select2"
                        data-placeholder="Pilih Tipe Darah"<?php echo $dis; ?>>
                    <option value="">&nbsp;</option>
                    <?php foreach ($buffycoat as $bc): ?>
                        <option value="<?php echo $bc['id']; ?>"<?php if ((string)$bc['id'] === (string)($d['buffycoat'] ?? '')) echo ' selected'; ?>>
                            <?php echo htmlspecialchars($bc['nama_jenis']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <!-- TANGGAL DIPERLUKAN + VOLUME -->
        <div class="row g-2 mb-2">
            <!-- Tanggal Diperlukan -->
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text"
                           name="tgl_diperlukan"
                           id="tgl_diperlukan"
                           class="form-control datepicker"
                           value="<?php echo htmlspecialchars($d['tgl_diperlukan'] ?? ''); ?>"
                           placeholder="Tanggal Diperlukan">
                    <span class="input-group-text">
                        <i class="fas fa-calendar-alt me-1"></i> Tgl Diperlukan
                    </span>
                </div>
            </div>
            <!-- Volume -->
            <div class="col-md-3">
                <input type="text"
                       name="volume"
                       id="volume"
                       class="form-control"
                       value="<?php echo htmlspecialchars($d['volume'] ?? ''); ?>"
                       placeholder="Volume"<?php echo $ro; ?>>
            </div>
        </div>
        <!-- ALASAN + DIAGNOSA + TROMBOSIT + HB -->
        <div class="row g-2 mb-2">
            <!-- Alasan -->
            <div class="col-md-3">
                <input type="text"
                       name="alasan"
                       id="alasan"
                       class="form-control"
                       value="<?php echo htmlspecialchars($d['alasan'] ?? ''); ?>"
                       placeholder="Alasan Permintaan"<?php echo $ro; ?>>
            </div>
            <!-- Diagnosa -->
            <div class="col-md-3">
                <input type="text"
                       name="diagnosa"
                       id="diagnosa"
                       class="form-control"
                       value="<?php echo htmlspecialchars($d['diagnosa'] ?? ''); ?>"
                       placeholder="Diagnosa"<?php echo $ro; ?>>
            </div>
            <!-- Trombosit -->
            <div class="col-md-3">
                <input type="text"
                       name="trombosit"
                       id="trombosit"
                       class="form-control"
                       value="<?php echo htmlspecialchars($d['trombosit'] ?? ''); ?>"
                       placeholder="Trombosit"<?php echo $ro; ?>>
            </div>
            <!-- Kadar Hb -->
            <div class="col-md-3">
                <input type="text"
                       name="kadar_hb"
                       id="kadar_hb"
                       class="form-control"
                       value="<?php echo htmlspecialchars($d['kadar_hb'] ?? ''); ?>"
                       placeholder="Kadar Hb"<?php echo $ro; ?>>
            </div>
        </div>
        <!-- DPJP + STATUS -->
        <div class="row g-2 mb-2">
            <!-- DPJP -->
            <div class="col-md-6">
                <select name="dpjp"
                        id="dpjp"
                        class="form-select select2"
                        data-placeholder="Pilih Dokter DPJP"<?php echo $dis; ?>>
                    <option value="">&nbsp;</option>
                    <?php foreach ($dokter as $doc): ?>
                        <option value="<?php echo $doc['ID']; ?>"<?php if ((string)$doc['ID'] === (string)($d['dpjp'] ?? '')) echo ' selected'; ?>>
                            <?php echo htmlspecialchars($doc['dokter']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Status -->
            <div class="col-md-6">
                <div class="status-native-wrapper">
                    <div class="status-select">
                        <select name="status"
                                id="status"
                                class="form-select select2"
                                data-placeholder="Pilih Status"<?php echo $dis; ?>>
                            <option value="">&nbsp;</option>
                            <?php foreach ($status as $s): ?>
                                <option value="<?php echo $s['ID']; ?>"<?php if ((string)$s['ID'] === (string)($d['status'] ?? '')) echo ' selected'; ?>>
                                    <?php echo htmlspecialchars($s['DESKRIPSI']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <span class="input-group-text status-label">
                        <i class="fas fa-cogs me-1"></i> Pilih Status
                    </span>
                </div>
            </div>
        </div>
        <!-- ALERT STATUS DARAH -->
        <div class="alert alert-danger d-flex align-items-center mb-2 d-none"
             id="alertStatusDarah"
             role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <div>
                <strong>Perhatian!</strong>
                Status Darah Masih Ada. Periksa Status Darah Sebelumnya.
            </div>
        </div>
        <!-- PETUGAS PENGAMBIL DARAH + RUANGAN -->
        <div class="row g-2 mb-2">
            <!-- Pengambil Darah -->
            <div class="col-md-6">
                <input type="text"
                       name="pengambil_darah"
                       id="pengambil_darah"
                       class="form-control"
                       value="<?php echo htmlspecialchars($d['pengambil_darah'] ?? ''); ?>"
                       placeholder="Petugas Pengambil Contoh Darah"<?php echo $ro; ?>>
            </div>
            <!-- Ruangan -->
            <div class="col-md-6">
                <select name="ruangan"
                        id="ruangan"
                        class="form-select select2"
                        data-placeholder="Pilih Ruangan Rawat"<?php echo $dis; ?>>
                    <option value="">&nbsp;</option>
                    <?php foreach ($ruangan as $r): ?>
                        <option value="<?php echo $r['ID']; ?>"<?php if ((string)$r['ID'] === (string)($d['ruangan'] ?? '')) echo ' selected'; ?>>
                            <?php echo htmlspecialchars($r['DESKRIPSI']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- ==================== 3. HASIL PEMERIKSAAN (examination_card) ==================== -->
        <!-- ANALIS 1 + TUJUAN + HASIL PEMERIKSAAN + PERAWAT -->
        <div class="row g-2 mb-2">
            <!-- Analis 1 -->
            <div class="col-md-3">
                <select name="analis"
                        id="analis"
                        class="form-select select2"
                        data-placeholder="Petugas Analis PTTD 1"<?php echo $dis; ?>>
                    <option value="">&nbsp;</option>
                    <?php foreach ($analis as $a): ?>
                        <option value="<?php echo $a['id_analis']; ?>"<?php if ((string)$a['id_analis'] === (string)($d['analis'] ?? '')) echo ' selected'; ?>>
                            <?php echo htmlspecialchars($a['nama_analis']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Tujuan -->
            <div class="col-md-3">
                <select name="tujuan"
                        id="tujuan"
                        class="form-select select2"
                        data-placeholder="Tujuan"<?php echo $dis; ?>>
                    <option value="">&nbsp;</option>
                    <?php foreach ($tujuan as $t): ?>
                        <option value="<?php echo $t['id_variabel']; ?>"<?php if ((string)$t['id_variabel'] === (string)($d['tujuan'] ?? $d['ID_TUJUAN'] ?? '')) echo ' selected'; ?>>
                            <?php echo htmlspecialchars($t['variabel']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Hasil Pemeriksaan -->
            <div class="col-md-3">
                <select name="hasil_pemeriksaan"
                        id="hasil_pemeriksaan"
                        class="form-select select2"
                        data-placeholder="Hasil Pemeriksaan"<?php echo $dis; ?>>
                    <option value="">&nbsp;</option>
                    <?php foreach ($hasil_pemeriksaan as $hp): ?>
                        <option value="<?php echo $hp['ID']; ?>"<?php if ((string)$hp['ID'] === (string)($d['hasil_pemeriksaan'] ?? '')) echo ' selected'; ?>>
                            <?php echo htmlspecialchars($hp['DESKRIPSI']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Perawat -->
            <div class="col-md-3">
                <select name="perawat"
                        id="perawat"
                        class="form-select select2"
                        data-placeholder="Perawat"<?php echo $dis; ?>>
                    <option value="">&nbsp;</option>
                    <?php foreach ($perawat as $p): ?>
                        <option value="<?php echo $p['id_perawat']; ?>"<?php if ((string)$p['id_perawat'] === (string)($d['perawat'] ?? '')) echo ' selected'; ?>>
                            <?php echo htmlspecialchars($p['nama_perawat']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <!-- HISTORI DATABASE + GOLONGAN DARAH DISPLAY -->
        <div class="row g-2 mb-2">
            <!-- Histori dari database -->
            <div class="col-md-9">
                <textarea
                    id="riwayattrans1"
                    name="riwayattrans1"
                    class="form-control"
                    rows="4"
                    placeholder="Histori Riwayat Alergi Transfusi dan Catatan"
                    readonly><?php echo htmlspecialchars($d['riwayattrans'] ?? ''); ?></textarea>
            </div>
            <!-- Golongan Darah Display -->
            <div class="col-md-3">
                <div class="goldar-display h-100 d-flex flex-column justify-content-start">
                    <div class="text-primary"
                         style="font-size: 18px; margin-bottom: 5px;">
                        Golongan Darah :
                    </div>
                    <span class="goldarah pasien-goldarah fw-bolder"
                          style="<?php if ($initText !== '') echo 'font-size: ' . $initSize . '; ' . ($initColor ? 'color: ' . $initColor . '; ' : ''); ?>line-height: 1.1;">
                        <?php echo htmlspecialchars($initText); ?>
                    </span>
                    <span id="id-selected" class="small text-muted">
                    </span>
                </div>
            </div>
        </div>
        <!-- INPUT RIWAYAT ALERGI / CATATAN BARU -->
        <div class="row g-2 mb-2">
            <div class="col-12">
                <input type="text"
                       name="riwayattrans"
                       id="riwayattrans"
                       class="form-control"
                       value="<?php echo htmlspecialchars($d['riwayattrans'] ?? ''); ?>"
                       placeholder="Riwayat Alergi Transfusi dan Catatan">
            </div>
        </div>
        <!-- NOTIF STATUS -->
        <input type="hidden"
               name="notifsatus"
               id="notifsatus">
        <!-- PEMISAH -->
        <hr>
        <!-- HASIL PEMERIKSAAN -->
        <div class="row g-2 mb-2 align-items-center">
            <!-- Judul -->
            <div class="col-md-3">
                <div style="font-size: 18px; font-weight: bold; text-decoration: underline; color: #337ab7;">
                    HASIL PEMERIKSAAN:
                </div>
            </div>
            <!-- Analis 2 -->
            <div class="col-md-3">
                <select name="analis2"
                        id="analis2"
                        class="form-select select2"
                        data-placeholder="Petugas Analis PTTD 2"<?php echo $dis; ?>>
                    <option value="">&nbsp;</option>
                    <?php foreach ($analis as $a): ?>
                        <option value="<?php echo $a['id_analis']; ?>"<?php if ((string)$a['id_analis'] === (string)($d['analis2'] ?? '')) echo ' selected'; ?>>
                            <?php echo htmlspecialchars($a['nama_analis']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Auto Kontrol -->
            <div class="col-md-2">
                <select name="auto_kontrol"
                        id="auto_kontrol"
                        class="form-select select2"
                        data-placeholder="Auto Kontrol"<?php echo $dis; ?>>
                    <option value="">&nbsp;</option>
                    <?php foreach ($auto_kontrol as $ak): ?>
                        <option value="<?php echo $ak['ID']; ?>"<?php if ((string)$ak['ID'] === (string)($d['auto_kontrol'] ?? '')) echo ' selected'; ?>>
                            <?php echo htmlspecialchars($ak['DESKRIPSI']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- ==================== 4. DATA KANTONG DARAH (blood_component_card) ==================== -->
        <div class="native-section-title d-flex align-items-center justify-content-between">
            <span>DATA KANTONG DARAH</span>
        </div>
        <div class="kantong-table-wrapper">
            <table class="kantong-table">
                <thead>
                    <tr>
                        <th>No. Kantong</th>
                        <th>Gol. Darah</th>
                        <th>Exp Date</th>
                        <th>Tgl. Input</th>
                        <th>Volume</th>
                        <th>MAYOR</th>
                        <th>MINOR</th>
                        <th>Tgl. Serah</th>
                        <th>Petugas Serah</th>
                        <th>Petugas Terima</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                        <?php
                        $serah_key  = ($i === 1) ? 'PETUGAS_SERAH'  : 'PETUGAS_SERAH_' . $i;
                        $terima_key = ($i === 1) ? 'PETUGAS_TERIMA' : 'PETUGAS_TERIMA_' . $i;
                        $tgl_serah_key = ($i === 1) ? 'TGL_SERAH' : 'TGL_SERAH' . $i;
                        ?>
                        <tr>
                            <td>
                                <input type="text" name="no_kantong_<?php echo $i; ?>" id="nomer<?php echo $i; ?>" class="form-control form-control-sm"
                                       data-kantong-lookup="<?php echo $i; ?>" placeholder="No. Kantong <?php echo $i; ?>" autocomplete="off"
                                       value="<?php echo htmlspecialchars($d['no_kantong_' . $i] ?? ''); ?>"<?php echo $ro; ?>>
                            </td>
                            <td>
                                <input type="text" id="goldaroto<?php echo $i; ?>" name="goldaroto<?php echo $i; ?>"
                                       class="form-control form-control-sm" readonly placeholder="Gol"
                                       value="<?php echo htmlspecialchars($d['PRO_DESKDAR_' . $i] ?? ''); ?>"<?php echo $dis; ?>>
                            </td>
                            <td>
                                <input type="text" id="exp<?php echo $i; ?>" name="exp_<?php echo $i; ?>"
                                       class="form-control form-control-sm datepicker" placeholder="Exp Date"
                                       value="<?php echo htmlspecialchars($d['exp_' . $i] ?? ''); ?>"<?php echo $ro; ?>>
                            </td>
                            <td>
                                <input type="text" name="tglkantong<?php echo $i; ?>" id="tglkantong<?php echo $i; ?>"
                                       class="form-control form-control-sm datetimepicker" placeholder="Tgl. Input"
                                       value="<?php echo htmlspecialchars($d['tglkantong' . $i] ?? ''); ?>"<?php echo $ro; ?>>
                            </td>
                            <td>
                                <input type="text" id="cc<?php echo $i; ?>" name="volume_<?php echo $i; ?>"
                                       class="form-control form-control-sm" placeholder="Volume"
                                       value="<?php echo htmlspecialchars($d['volume_' . $i] ?? ''); ?>"<?php echo $ro; ?>>
                            </td>
                            <td>
                                <select name="myr_<?php echo $i; ?>" class="form-select form-select-sm select2" data-placeholder="MAYOR"<?php echo $dis; ?>>
                                    <option value="">&nbsp;</option>
                                    <?php foreach ($mayor as $m): ?>
                                        <option value="<?php echo $m['ID']; ?>"<?php if ((string)$m['ID'] === (string)($d['myr_' . $i] ?? '')) echo ' selected'; ?>>
                                            <?php echo htmlspecialchars($m['DESKRIPSI']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <select name="mnr_<?php echo $i; ?>" class="form-select form-select-sm select2" data-placeholder="MINOR"<?php echo $dis; ?>>
                                    <option value="">&nbsp;</option>
                                    <?php foreach ($minor as $m): ?>
                                        <option value="<?php echo $m['ID']; ?>"<?php if ((string)$m['ID'] === (string)($d['mnr_' . $i] ?? '')) echo ' selected'; ?>>
                                            <?php echo htmlspecialchars($m['DESKRIPSI']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="tgl_serah_<?php echo $i; ?>" id="tgl_serah_<?php echo $i; ?>"
                                       class="form-control form-control-sm datetimepicker" placeholder="Tgl. Serah"
                                       value="<?php echo htmlspecialchars($d['TGL_SERAH' . ($i === 1 ? '' : $i)] ?? ''); ?>"<?php echo $ro; ?>>
                            </td>
                            <td>
                                <input type="text" name="petugas_serah_<?php echo $i; ?>" id="petugas_serah_<?php echo $i; ?>"
                                       class="form-control form-control-sm" placeholder="Serah - Petugas"
                                       value="<?php echo htmlspecialchars($d['PETUGAS_SERAH' . ($i === 1 ? '' : '_' . $i)] ?? ''); ?>"<?php echo $ro; ?>>
                            </td>
                            <td>
                                <input type="text" name="petugas_terima_<?php echo $i; ?>" id="petugas_terima_<?php echo $i; ?>"
                                       class="form-control form-control-sm" placeholder="Terima - Petugas"
                                       value="<?php echo htmlspecialchars($d['PETUGAS_TERIMA' . ($i === 1 ? '' : '_' . $i)] ?? ''); ?>"<?php echo $ro; ?>>
                            </td>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>

        <!-- ==================== 5. ACTION BUTTONS ==================== -->
        <div class="form-action">
            <button type="submit" name="submit" class="btn btn-primary" data-loading-submit data-loading-text="Menyimpan...">
                <i class="fas fa-save me-1"></i> Simpan dan Cetak
            </button>
            <button type="reset" class="btn btn-outline-danger">
                <i class="fas fa-undo me-1"></i> Reset
            </button>
            <a href="<?php echo base_url('index.php/requestcontroller/list'); ?>" class="btn btn-secondary ms-auto">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>

    </div>
</form>

<script>
window.REQUEST_EDIT_CONFIG = {
    autofillUrl: "<?php echo base_url('index.php/darah/autofill_pasien'); ?>",
    lookupUrl:   "<?php echo base_url('index.php/darah/lookup_kantong'); ?>",
    printFormUrl: "<?php echo base_url('index.php/requestcontroller/print_form/'); ?>",
    editMode:    "<?php echo htmlspecialchars($edit_mode); ?>"
};
</script>
<script src="<?php echo base_url('assets/js/form-darah.js'); ?>?v=20241006"></script>
<script src="<?php echo base_url('assets/js/request-edit.js'); ?>" defer></script>

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