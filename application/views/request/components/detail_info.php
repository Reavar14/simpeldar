<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="fas fa-user me-2"></i>Data Pasien</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6"><strong>Nama Pasien</strong><br><?php echo htmlspecialchars($d['nama'] ?? '-'); ?></div>
            <div class="col-md-6"><strong>Tanggal Permintaan</strong><br><?php echo htmlspecialchars($d['tgl_minta'] ?? '-'); ?></div>
            <div class="col-md-6"><strong>No. MR</strong><br><?php echo htmlspecialchars($d['mr'] ?? '-'); ?></div>
            <div class="col-md-6"><strong>Ruangan</strong><br><?php echo htmlspecialchars($d['RUANGAN'] ?? '-'); ?></div>
            <div class="col-md-6"><strong>Jenis Kelamin</strong><br><?php echo htmlspecialchars($d['jenis_kelamin'] ?? '-'); ?></div>
            <div class="col-md-6"><strong>Golongan Darah</strong><br><?php $gd = htmlspecialchars($d['gol_darah'] ?? '-'); $cls = 'secondary'; if ($gd !== '-') { $norm = strtoupper(str_replace('/', '', $gd)); $map = array('A+'=>'purple','A-'=>'purple','B+'=>'danger','B-'=>'danger','AB+'=>'success','AB-'=>'success','O+'=>'primary','O-'=>'primary'); if (isset($map[$norm])) $cls = $map[$norm]; } echo '<span class="badge bg-'.$cls.'">'.$gd.'</span>'; ?></div>
            <div class="col-md-6"><strong>Jenis Darah</strong><br><?php echo htmlspecialchars($d['jenis_darah'] ?? '-'); ?></div>
            <div class="col-md-6"><strong>Volume</strong><br><?php echo htmlspecialchars($d['volume'] ?? '-'); ?></div>
            <div class="col-md-6"><strong>Tipe Darah</strong><br><?php echo htmlspecialchars($d['bufycoat'] ?? '-'); ?></div>
            <div class="col-md-6"><strong>Diagnosa</strong><br><?php echo htmlspecialchars($d['diagnosa'] ?? '-'); ?></div>
            <div class="col-md-6"><strong>Tanggal Diperlukan</strong><br><?php echo htmlspecialchars($d['tgl_diperlukan'] ?? '-'); ?></div>
            <div class="col-md-6"><strong>Kadar HB</strong><br><?php echo htmlspecialchars($d['kadar_hb'] ?? '-'); ?></div>
            <div class="col-md-6"><strong>Alasan</strong><br><?php echo $alasan; ?></div>
            <div class="col-md-6"><strong>Trombosit</strong><br><?php echo $trombosit; ?></div>
            <div class="col-md-6"><strong>Tujuan</strong><br><?php echo !empty($d['TUJUAN']) ? htmlspecialchars($d['TUJUAN']) : ''; ?></div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="fas fa-user-md me-2"></i>Dokter & Petugas</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6"><strong>Dokter Peminta Darah</strong><br><?php echo htmlspecialchars($d['nama_dokter'] ?? '-'); ?></div>
            <div class="col-md-6"><strong>Tanggal Terima</strong><br><?php echo htmlspecialchars($d['tanggal_terima'] ?? '-'); ?></div>
            <div class="col-md-6"><strong>Petugas Pengambil Contoh Darah</strong><br><?php echo htmlspecialchars($d['pengambildarah'] ?? '-'); ?></div>
            <div class="col-md-6"><strong>Petugas Terima</strong><br><?php echo htmlspecialchars($d['petugas_terima'] ?? '-'); ?></div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i>Status Layanan</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-12">
                <strong>Status Proses:</strong>
                <?php
                $status_pesan = (int)($d['status_pesan'] ?? 0);
                $status_label = htmlspecialchars($d['status_proses'] ?? '-');
                $belumTerimaId = (int)($status_belum_terima_id ?? 0);
                $sudahTerimaId = (int)($status_sudah_terima_id ?? 0);
                if ($belumTerimaId > 0 && $status_pesan === $belumTerimaId) {
                    echo '<span class="badge bg-danger ms-2 fs-6">' . $status_label . '</span>';
                } elseif ($sudahTerimaId > 0 && $status_pesan === $sudahTerimaId) {
                    echo '<span class="badge bg-success ms-2 fs-6">' . $status_label . '</span>';
                } else {
                    echo '<span class="badge bg-secondary ms-2 fs-6">' . $status_label . '</span>';
                }
                ?>
            </div>
        </div>
    </div>
</div>