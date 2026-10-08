<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
date_default_timezone_set('Asia/Jakarta');

function normalize_display_text($value) {
    if (!is_string($value) || $value === '') {
        return $value;
    }
    if (preg_match('//u', $value)) {
        return $value;
    }
    if (function_exists('mb_convert_encoding')) {
        return mb_convert_encoding($value, 'UTF-8', 'Windows-1252,ISO-8859-1');
    }
    if (function_exists('iconv')) {
        $converted = @iconv('Windows-1252', 'UTF-8//IGNORE', $value);
        if ($converted !== false) {
            return $converted;
        }
    }
    return utf8_encode($value);
}
?>

<?php if (!empty($d)): ?>
    <?php
    $alasan = htmlspecialchars(normalize_display_text($d['alasan']), ENT_QUOTES, 'UTF-8');
    $trombosit = htmlspecialchars(normalize_display_text($d['trombosit']), ENT_QUOTES, 'UTF-8');
    ?>
    <div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h1 class="page-title">
                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                Permintaan Darah No: <?php echo htmlspecialchars($d['no_permintaan']); ?>
            </h1>
            <p class="page-subtitle mb-0">
                <?php echo htmlspecialchars($d['nama'] ?? '-'); ?> &middot;
                MR: <?php echo htmlspecialchars($d['mr'] ?? '-'); ?>
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <?php
            $status_pesan     = (int)($d['status_pesan'] ?? 0);
            $sudahTerimaId    = (int)($status_sudah_terima_id ?? 0);
            $sudahDiterima    = $sudahTerimaId > 0 && $status_pesan === $sudahTerimaId;
            ?>
            <?php if ($sudahDiterima): ?>
                <button type="button" class="btn btn-outline-info tblCetakBon" data-no-permintaan="<?php echo htmlspecialchars($d['no_permintaan']); ?>" data-bs-toggle="tooltip" title="Cetak Bon Darah">
                    <i class="fas fa-print me-1"></i> Cetak Bon
                </button>
                <button type="button" class="btn btn-outline-secondary" disabled title="Sudah Diterima">
                    <i class="fas fa-check-circle me-1"></i> Sudah Diterima
                </button>
            <?php else: ?>
                <button type="button" class="btn btn-success tblTerima" data-no-permintaan="<?php echo htmlspecialchars($d['no_permintaan']); ?>" data-no-mr="<?php echo htmlspecialchars($d['mr']); ?>" data-bs-toggle="tooltip" title="Terima Sampel Darah">
                    <i class="fas fa-arrow-circle-down me-1"></i> Terima
                </button>
            <?php endif; ?>
            <a href="<?php echo base_url('index.php/requestcontroller/list'); ?>" class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Kembali">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <?php $this->load->view('request/components/detail_info', [
        'alasan' => $alasan,
        'trombosit' => $trombosit,
        'status_belum_terima_id' => $status_belum_terima_id ?? null,
        'status_sudah_terima_id' => $status_sudah_terima_id ?? null,
    ]); ?>

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
window.REQUEST_DETAIL_CONFIG = {
    terimaUrl: "<?php echo base_url('index.php/requestcontroller/mark_received'); ?>",
    bonUrl: "http://192.168.7.138/cetakanhnf/simpeldar/admin/bonminta.php?id="
};
</script>
<script src="<?php echo base_url('assets/js/request-detail.js'); ?>" defer></script>