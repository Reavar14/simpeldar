<?php defined('BASEPATH') OR exit('No direct script access allowed');

$CI =& get_instance();

$userName = $CI->session->userdata('uname');
$level    = $CI->session->userdata('level');

$levelNames = array(
    '1' => 'Administrator',
    '2' => 'Petugas Rawat Inap',
    '3' => 'Petugas',
    '4' => 'Dokter'
);
$levelName = isset($levelNames[$level]) ? $levelNames[$level] : 'Pengguna';

/* --- Tanggal hari ini (Bahasa Indonesia) --- */
$hariId = array(
    'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat',
    'Saturday' => 'Sabtu'
);
$bulanId = array(
    'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
    'April' => 'April', 'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli',
    'August' => 'Agustus', 'September' => 'September', 'October' => 'Oktober',
    'November' => 'November', 'December' => 'Desember'
);
$tanggalIni = strtr(date('l'), $hariId) . ', ' . date('d') . ' ' . strtr(date('F'), $bulanId) . ' ' . date('Y');

/* --- URL aksi --- */
$bonUrl    = base_url('index.php/cetakan/bonminta?id=');
$detailUrl = base_url('index.php/requestcontroller/detail/');
$editUrl   = base_url('index.php/requestcontroller/edit/');

/* --- Helper: badge goldar sesuai halaman Permintaan --- */
if (!function_exists('dash_goldar_badge')) {
    function dash_goldar_badge($goldar)
    {
        $goldar = strtoupper(trim((string)$goldar));
        // Normalisasi: hapus slash (O/+ -> O+, A/- -> A-)
        $goldar = str_replace('/', '', $goldar);
        $map = array(
            'A+'  => 'purple', 'A-'  => 'purple',
            'B+'  => 'danger', 'B-'  => 'danger',
            'AB+' => 'success', 'AB-' => 'success',
            'O+'  => 'primary', 'O-'  => 'primary',
        );
        $cls = isset($map[$goldar]) ? $map[$goldar] : 'secondary';
        return '<span class="badge bg-' . $cls . '">' . htmlspecialchars(str_replace('/', '', (string)$goldar)) . '</span>';
    }
}

/* --- Helper: badge status sesuai spec --- */
/* Normalisasi: trim + lower agar cocok dengan master referensi JENIS=3
   yang dipakai dropdown Status pada form (mis. "masa simpan habis",
   "Permintaan Sebelumnya Belum Diambil "). */
if (!function_exists('dash_status_badge')) {
    function dash_status_badge($label)
    {
        $map = array(
            'permintaan'                        => 'primary',
            'sedang proses'                     => 'warning',
            'darah siap'                        => 'success',
            'perlu donor'                       => 'danger',
            'sampel baru'                       => 'info',
            'belum terima sampel darah'         => 'info',
            'incompatible'                      => 'secondary',
            'masa simpan habis'                 => 'warning',
            'belum diambil'                     => 'info',
            'sudah habis'                       => 'dark',
            'sudah terima sampel darah'         => 'success',
            'permintaan sebelumnya belum diambil' => 'warning',
        );
        $key   = strtolower(trim((string)$label));
        $cls   = isset($map[$key]) ? $map[$key] : 'secondary';
        $extra = ($cls === 'warning' || $cls === 'info') ? ' text-dark' : '';
        return '<span class="badge bg-' . $cls . $extra . '">' . htmlspecialchars(trim((string)$label)) . '</span>';
    }
}

if (!function_exists('dash_kelengkapan_badge')) {
    function dash_kelengkapan_badge($label)
    {
        if ($label === 'Sudah lengkap') {
            return '<span class="badge bg-success">Sudah lengkap</span>';
        }
        return '<span class="badge bg-danger">Tidak lengkap</span>';
    }
}

/* --- Helper: sel-sel umum --- */
if (!function_exists('dash_cells_base')) {
    function dash_cells_base($no, $r)
    {
        return array(
            '<td class="text-center text-muted">' . ((int)$no) . '</td>',
            '<td class="text-center font-monospace">' . htmlspecialchars((string)$r['no_permintaan']) . '</td>',
            '<td class="text-center">' . htmlspecialchars((string)$r['mr']) . '</td>',
            '<td class="fw-semibold">' . htmlspecialchars((string)$r['nama']) . '</td>'
        );
    }
}

/* --- Ringkasan jumlah per status (satu query dari Dashboard_model) --- */
$sum = array_merge(array(
    'permintaan'       => 0,
    'sedang_proses'    => 0,
    'darah_siap'       => 0,
    'perlu_donor'      => 0,
    'sampel_baru'      => 0,
    'incompatible'     => 0,
    'masa_simpan_habis'=> 0,
    'belum_diambil'    => 0,
    'sudah_habis'      => 0,
    'tidak_lengkap'    => 0,
), is_array($summary) ? $summary : array());

/* --- Mapping key tab -> key summary --- */
$tabSummaryKey = array(
    'permintaan'    => 'permintaan',
    'sedang_proses' => 'sedang_proses',
    'siap'          => 'darah_siap',
    'donor'         => 'perlu_donor',
    'baru'          => 'sampel_baru',
    'incompatible'  => 'incompatible',
    'masasimpan'    => 'masa_simpan_habis',
    'belumambil'    => 'belum_diambil',
    'habis'         => 'sudah_habis',
    'tidaklengkap'  => 'tidak_lengkap',
);

/* --- Bangun definisi 10 tab --- */
$tabs = array();

/* 1. Permintaan (hanya level 1) */
if ($level == '1') {
    $rows = array();
    $no = 1;
    foreach ($permintaan as $r) {
        $cells = dash_cells_base($no, $r);
        $cells[] = '<td>' . htmlspecialchars((string)$r['tgl_minta']) . '</td>';
        $cells[] = '<td>' . htmlspecialchars((string)$r['ruangan']) . '</td>';
        $cells[] = '<td>' . htmlspecialchars((string)$r['alasan']) . '</td>';
        $cells[] = '<td>' . (!empty($r['TUJUAN']) ? htmlspecialchars($r['TUJUAN']) : '') . '</td>';
        $cells[] = '<td class="text-center">' . dash_goldar_badge($r['goldar']) . '</td>';
        $cells[] = '<td class="text-center">' . htmlspecialchars((string)$r['tgl_diperlukan']) . '</td>';
        $cells[] = '<td class="text-center small">' . $r['jenis_darah'] . '</td>';
        $cells[] = '<td class="text-center"><a href="' . $detailUrl . $r['no_permintaan'] . '" '
                 . 'class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Lihat detail permintaan">'
                 . '<i class="fas fa-eye"></i></a></td>';
        $rows[] = $cells;
        $no++;
    }
    $tabs['permintaan'] = array(
        'label'    => 'Permintaan',
        'icon'     => 'fa-clipboard-list',
        'color'    => 'primary',
        'columns'  => array('No', 'Nomor Permintaan', 'No MR', 'Nama Pasien', 'Tgl Permintaan',
                            'Ruangan', 'Alasan', 'Tujuan', 'Gol Darah', 'Tgl Diperlukan',
                            'Jenis Permintaan', 'Lihat'),
        'rows'     => $rows
    );
}

/* 2. Sedang Proses */
$rows = array();
$no = 1;
foreach ($sedang_proses as $r) {
    $cells = dash_cells_base($no, $r);
    $cells[] = '<td>' . htmlspecialchars((string)$r['tgl_minta']) . '</td>';
    $cells[] = '<td>' . htmlspecialchars((string)$r['ruangan']) . '</td>';
    $cells[] = '<td>' . htmlspecialchars((string)$r['alasan']) . '</td>';
    $cells[] = '<td class="text-center">' . dash_goldar_badge($r['goldar']) . '</td>';
    $cells[] = '<td class="text-center">' . htmlspecialchars((string)$r['tgl_diperlukan']) . '</td>';
    $cells[] = '<td class="text-center">' . dash_kelengkapan_badge($r['kelengkapan_status']) . '</td>';
    $cells[] = '<td class="text-center"><a href="' . $bonUrl . urlencode($r['no_permintaan']) . '" target="_blank" '
             . 'class="btn btn-sm btn-outline-warning">' . dash_status_badge($r['status_proses']) . '</a></td>';
    $rows[] = $cells;
    $no++;
}
$tabs['sedang_proses'] = array(
    'label'   => 'Sedang Proses',
    'icon'    => 'fa-spinner',
    'color'   => 'warning',
    'columns' => array('No', 'Nomor Permintaan', 'No MR', 'Nama Pasien', 'Tgl Permintaan',
                       'Ruangan', 'Alasan', 'Gol Darah', 'Tgl Diperlukan', 'Status', 'Proses'),
    'rows'    => $rows
);

/* 3. Darah Siap */
$rows = array();
$no = 1;
foreach ($siap as $r) {
    $cells = dash_cells_base($no, $r);
    $cells[] = '<td>' . htmlspecialchars((string)$r['tgl_minta']) . '</td>';
    $cells[] = '<td>' . htmlspecialchars((string)$r['ruangan']) . '</td>';
    $cells[] = '<td>' . htmlspecialchars((string)$r['alasan']) . '</td>';
    $cells[] = '<td class="text-center">' . dash_goldar_badge($r['goldar']) . '</td>';
    $cells[] = '<td class="text-center">' . htmlspecialchars((string)$r['tgl_diperlukan']) . '</td>';
    $cells[] = '<td class="text-center">' . dash_kelengkapan_badge($r['kelengkapan_status']) . '</td>';
    $cells[] = '<td class="text-center"><a href="' . $bonUrl . urlencode($r['no_permintaan']) . '" target="_blank" '
             . 'class="btn btn-sm btn-outline-success">' . dash_status_badge($r['status_proses']) . '</a></td>';
    $rows[] = $cells;
    $no++;
}
$tabs['siap'] = array(
    'label'   => 'Darah Siap',
    'icon'    => 'fa-check-circle',
    'color'   => 'success',
    'columns' => array('No', 'Nomor Permintaan', 'No MR', 'Nama Pasien', 'Tgl Permintaan',
                       'Ruangan', 'Alasan', 'Gol Darah', 'Tgl Diperlukan', 'Status', 'Keterangan'),
    'rows'    => $rows
);

/* 4. Perlu Donor */
$rows = array();
$no = 1;
foreach ($donor as $r) {
    $cells = dash_cells_base($no, $r);
    $cells[] = '<td>' . htmlspecialchars((string)$r['tgl_minta']) . '</td>';
    $cells[] = '<td>' . htmlspecialchars((string)$r['ruangan']) . '</td>';
    $cells[] = '<td>' . htmlspecialchars((string)$r['alasan']) . '</td>';
    $cells[] = '<td class="text-center">' . dash_goldar_badge($r['goldar']) . '</td>';
    $cells[] = '<td class="text-center">' . htmlspecialchars((string)$r['tgl_diperlukan']) . '</td>';
    $cells[] = '<td class="text-center"><a href="' . $bonUrl . urlencode($r['no_permintaan']) . '" target="_blank" '
             . 'class="btn btn-sm btn-outline-danger">' . dash_status_badge($r['status_proses']) . '</a></td>';
    $rows[] = $cells;
    $no++;
}
$tabs['donor'] = array(
    'label'   => 'Perlu Donor',
    'icon'    => 'fa-heart',
    'color'   => 'danger',
    'columns' => array('No', 'Nomor Permintaan', 'No MR', 'Nama Pasien', 'Tgl Permintaan',
                       'Ruangan', 'Alasan', 'Gol Darah', 'Tgl Diperlukan', 'Status'),
    'rows'    => $rows
);

/* 5. Sampel Baru */
$rows = array();
$no = 1;
foreach ($baru as $r) {
    $cells = dash_cells_base($no, $r);
    $cells[] = '<td>' . htmlspecialchars((string)$r['RUANGAN']) . '</td>';
    $cells[] = '<td>' . htmlspecialchars((string)$r['alasan']) . '</td>';
    $cells[] = '<td class="text-center">' . htmlspecialchars((string)$r['tgl_diperlukan']) . '</td>';
    $cells[] = '<td class="text-center"><a href="' . $bonUrl . urlencode($r['no_permintaan']) . '" target="_blank" '
             . 'class="btn btn-sm btn-outline-primary">' . dash_status_badge($r['status_proses']) . '</a></td>';
    $rows[] = $cells;
    $no++;
}
$tabs['baru'] = array(
    'label'   => 'Sampel Baru',
    'icon'    => 'fa-vial',
    'color'   => 'info',
    'columns' => array('No', 'Nomor Permintaan', 'No MR', 'Nama Pasien',
                       'Ruangan', 'Alasan', 'Tgl Diperlukan', 'Status'),
    'rows'    => $rows
);

/* 6. Incompatible */
$rows = array();
$no = 1;
foreach ($incompatible as $r) {
    $cells = dash_cells_base($no, $r);
    $cells[] = '<td>' . htmlspecialchars((string)$r['RUANGAN']) . '</td>';
    $cells[] = '<td>' . htmlspecialchars((string)$r['alasan']) . '</td>';
    $cells[] = '<td class="text-center">' . htmlspecialchars((string)$r['tgl_diperlukan']) . '</td>';
    $cells[] = '<td class="text-center"><a href="' . $bonUrl . urlencode($r['no_permintaan']) . '" target="_blank" '
             . 'class="btn btn-sm btn-outline-secondary">' . dash_status_badge($r['status_proses']) . '</a></td>';
    $rows[] = $cells;
    $no++;
}
$tabs['incompatible'] = array(
    'label'   => 'Incompatible',
    'icon'    => 'fa-exclamation-triangle',
    'color'   => 'secondary',
    'columns' => array('No', 'Nomor Permintaan', 'No MR', 'Nama Pasien',
                       'Ruangan', 'Alasan', 'Tgl Diperlukan', 'Status'),
    'rows'    => $rows
);

/* 7. Masa Simpan Habis */
$rows = array();
$no = 1;
foreach ($masasimpan as $r) {
    $cells = dash_cells_base($no, $r);
    $cells[] = '<td>' . htmlspecialchars((string)$r['RUANGAN']) . '</td>';
    $cells[] = '<td>' . htmlspecialchars((string)$r['alasan']) . '</td>';
    $cells[] = '<td class="text-center">' . htmlspecialchars((string)$r['tgl_diperlukan']) . '</td>';
    $cells[] = '<td class="text-center"><a href="' . $bonUrl . urlencode($r['no_permintaan']) . '" target="_blank" '
             . 'class="btn btn-sm btn-outline-warning" data-bs-toggle="tooltip" title="Cetak masa simpan">'
             . '<i class="fas fa-print me-1"></i>Masa Simpan</a></td>';
    $rows[] = $cells;
    $no++;
}
$tabs['masasimpan'] = array(
    'label'   => 'Masa Simpan Habis',
    'icon'    => 'fa-clock',
    'color'   => 'warning',
    'columns' => array('No', 'Nomor Permintaan', 'No MR', 'Nama Pasien',
                       'Ruangan', 'Alasan', 'Tgl Diperlukan', 'Status'),
    'rows'    => $rows
);

/* 8. Belum Diambil */
$rows = array();
$no = 1;
foreach ($belumambil as $r) {
    $cells = dash_cells_base($no, $r);
    $cells[] = '<td>' . htmlspecialchars((string)$r['RUANGAN']) . '</td>';
    $cells[] = '<td>' . htmlspecialchars((string)$r['alasan']) . '</td>';
    $cells[] = '<td class="text-center">' . htmlspecialchars((string)$r['tgl_diperlukan']) . '</td>';
    $cells[] = '<td class="text-center"><a href="' . $bonUrl . urlencode($r['no_permintaan']) . '" target="_blank" '
             . 'class="btn btn-sm btn-outline-info">' . dash_status_badge($r['status_proses']) . '</a></td>';
    $rows[] = $cells;
    $no++;
}
$tabs['belumambil'] = array(
    'label'   => 'Belum Diambil',
    'icon'    => 'fa-box-open',
    'color'   => 'info',
    'columns' => array('No', 'Nomor Permintaan', 'No MR', 'Nama Pasien',
                       'Ruangan', 'Alasan', 'Tgl Diperlukan', 'Status'),
    'rows'    => $rows
);

/* 9. Sudah Habis */
$rows = array();
$no = 1;
foreach ($habis as $r) {
    $cells = dash_cells_base($no, $r);
    $cells[] = '<td>' . htmlspecialchars((string)$r['tgl_minta']) . '</td>';
    $cells[] = '<td>' . htmlspecialchars((string)$r['ruangan']) . '</td>';
    $cells[] = '<td>' . htmlspecialchars((string)$r['alasan']) . '</td>';
    $cells[] = '<td class="text-center">' . dash_goldar_badge($r['goldar']) . '</td>';
    $cells[] = '<td class="text-center">' . htmlspecialchars((string)$r['tgl_diperlukan']) . '</td>';
    $cells[] = '<td class="text-center"><a href="' . $bonUrl . urlencode($r['no_permintaan']) . '" target="_blank" '
             . 'class="btn btn-sm btn-outline-dark">' . dash_status_badge($r['status_proses']) . '</a></td>';
    $rows[] = $cells;
    $no++;
}
$tabs['habis'] = array(
    'label'   => 'Sudah Habis',
    'icon'    => 'fa-times-circle',
    'color'   => 'dark',
    'columns' => array('No', 'Nomor Permintaan', 'No MR', 'Nama Pasien', 'Tgl Permintaan',
                       'Ruangan', 'Alasan', 'Gol Darah', 'Tgl Diperlukan', 'Status'),
    'rows'    => $rows
);

/* 10. Tidak Lengkap */
$rows = array();
$no = 1;
foreach ($tidaklengkap as $r) {
    $cells = dash_cells_base($no, $r);
    $cells[] = '<td>' . htmlspecialchars((string)$r['tgl_minta']) . '</td>';
    $cells[] = '<td>' . htmlspecialchars((string)$r['ruangan']) . '</td>';
    $cells[] = '<td>' . htmlspecialchars((string)$r['alasan']) . '</td>';
    $cells[] = '<td class="text-center">' . dash_goldar_badge($r['goldar']) . '</td>';
    $cells[] = '<td class="text-center">' . htmlspecialchars((string)$r['tgl_diperlukan']) . '</td>';
    $cells[] = '<td class="text-center">' . dash_status_badge($r['status_proses']) . '</td>';
    $cells[] = '<td class="text-center col-kelengkapan"><a href="' . $editUrl . urlencode($r['no_permintaan']) . '" '
             . 'class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Lengkapi data permintaan">'
             . dash_kelengkapan_badge($r['kelengkapan_status']) . '</a></td>';
    $rows[] = $cells;
    $no++;
}
$tabs['tidaklengkap'] = array(
    'label'   => 'Tidak Lengkap',
    'icon'    => 'fa-exclamation-circle',
    'color'   => 'danger',
    'columns' => array('No', 'Nomor Permintaan', 'No MR', 'Nama Pasien', 'Tgl Permintaan',
                       'Ruangan', 'Alasan', 'Gol Darah', 'Tgl Diperlukan', 'Status', 'Kelengkapan'),
    'rows'    => $rows
);

/* Tab default: permintaan (level 1) atau sedang proses */
$defaultTab = ($level == '1' && isset($tabs['permintaan'])) ? 'permintaan' : 'sedang_proses';
if (!isset($tabs[$defaultTab])) {
    $tabKeys = array_keys($tabs);
    $defaultTab = $tabKeys[0];
}
?>
    <style>
        /* TABEL PERMINTAAN - DASHBOARD*/
        #tab-dashboard .table-responsive {
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: hidden !important;
            overflow-y: visible !important;
        }
        #tab-dashboard .dataTables_wrapper {
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: hidden !important;
        }
        #tab-dashboard .dataTables_scroll {
            width: 100% !important;
            overflow-x: hidden !important;
        }
        /* TABLE */
        #dashboardTable {
            width: 100% !important;
            max-width: 100% !important;
            table-layout: fixed !important;
            border-collapse: collapse !important;
            border: 1px solid #dee2e6 !important;
            font-size: 12px;
        }
        /* BORDER SEMUA CELL */
        #dashboardTable th,
        #dashboardTable td {
            border: 1px solid #dee2e6 !important;
            box-sizing: border-box !important;
            vertical-align: middle !important;
            padding: 6px 5px !important;
        }
        /* HEADER */
        #dashboardTable thead th {
            font-size: 11px !important;
            font-weight: 600 !important;
            text-align: center !important;
            white-space: normal !important;
            word-break: normal !important;
            overflow-wrap: break-word !important;
            line-height: 1.25 !important;
            padding: 7px 4px !important;
        }
        /* BODY */
        #dashboardTable tbody td {
            font-size: 12px !important;
            white-space: normal !important;
            word-break: normal !important;
            overflow-wrap: break-word !important;
            line-height: 1.35 !important;
        }
        /* KOLOM NO */
        #dashboardTable th:nth-child(1),
        #dashboardTable td:nth-child(1) {
            width: 4% !important;
            text-align: center !important;
            white-space: nowrap !important;
        }
        /* NOMOR PERMINTAAN */
        #dashboardTable th:nth-child(2),
        #dashboardTable td:nth-child(2) {
            width: 10% !important;
        }
        /* NO MR */
        #dashboardTable th:nth-child(3),
        #dashboardTable td:nth-child(3) {
            width: 6% !important;
        }
        /* NAMA PASIEN */
        #dashboardTable th:nth-child(4),
        #dashboardTable td:nth-child(4) {
            width: 13% !important;
        }
        /* TGL PERMINTAAN */
        #dashboardTable th:nth-child(5),
        #dashboardTable td:nth-child(5) {
            width: 8% !important;
        }
        /* RUANGAN */
        #dashboardTable th:nth-child(6),
        #dashboardTable td:nth-child(6) {
            width: 8% !important;
        }
        /* ALASAN */
        #dashboardTable th:nth-child(7),
        #dashboardTable td:nth-child(7) {
            width: 8% !important;
        }
        /* TUJUAN */
        #dashboardTable th:nth-child(8),
        #dashboardTable td:nth-child(8) {
            width: 7% !important;
        }
        /* GOLONGAN DARAH */
        #dashboardTable th:nth-child(9),
        #dashboardTable td:nth-child(9) {
            width: 8% !important;
            text-align: center !important;
        }
        /* TGL DIPERLUKAN */
        #dashboardTable th:nth-child(10),
        #dashboardTable td:nth-child(10) {
            width: 10% !important;
        }
        /* JENIS PERMINTAAN */
        #dashboardTable th:nth-child(11),
        #dashboardTable td:nth-child(11) {
            width: 150px !important;
            min-width: 150px !important;
            max-width: 150px !important;
            white-space: normal !important;
            word-break: normal !important;
            overflow-wrap: break-word !important;
        }
        /* LIHAT */
        #dashboardTable th:nth-child(12),
        #dashboardTable td:nth-child(12) {
            width: 70px !important;
            min-width: 70px !important;
            max-width: 70px !important;
            text-align: center !important;
            white-space: nowrap !important;
        }
        /* LINK / BUTTON DI KOLOM LIHAT */
        #dashboardTable td:nth-child(12) a,
        #dashboardTable td:nth-child(12) button {
            white-space: nowrap !important;
        }

        /* ============================================================
           TABEL SEDANG PROSES (11 kolom)
           ============================================================ */
        #dashboardTable.tab-sedang_proses {
            table-layout: fixed !important;
            width: 100% !important;
        }
        #dashboardTable.tab-sedang_proses th,
        #dashboardTable.tab-sedang_proses td {
            white-space: normal !important;
            word-wrap: break-word !important;
            word-break: normal !important;
            overflow-wrap: break-word !important;
        }
        /* NO */
        #dashboardTable.tab-sedang_proses th:nth-child(1),
        #dashboardTable.tab-sedang_proses td:nth-child(1) {
            width: 4% !important;
            text-align: center !important;
            white-space: nowrap !important;
        }
        /* NOMOR PERMINTAAN */
        #dashboardTable.tab-sedang_proses th:nth-child(2),
        #dashboardTable.tab-sedang_proses td:nth-child(2) {
            width: 10% !important;
        }
        /* NO MR */
        #dashboardTable.tab-sedang_proses th:nth-child(3),
        #dashboardTable.tab-sedang_proses td:nth-child(3) {
            width: 6% !important;
        }
        /* NAMA PASIEN */
        #dashboardTable.tab-sedang_proses th:nth-child(4),
        #dashboardTable.tab-sedang_proses td:nth-child(4) {
            width: 12% !important;
        }
        /* TGL PERMINTAAN */
        #dashboardTable.tab-sedang_proses th:nth-child(5),
        #dashboardTable.tab-sedang_proses td:nth-child(5) {
            width: 8% !important;
        }
        /* RUANGAN */
        #dashboardTable.tab-sedang_proses th:nth-child(6),
        #dashboardTable.tab-sedang_proses td:nth-child(6) {
            width: 8% !important;
        }
        /* ALASAN */
        #dashboardTable.tab-sedang_proses th:nth-child(7),
        #dashboardTable.tab-sedang_proses td:nth-child(7) {
            width: 13% !important;
        }
        /* GOL DARAH */
        #dashboardTable.tab-sedang_proses th:nth-child(8),
        #dashboardTable.tab-sedang_proses td:nth-child(8) {
            width: 8% !important;
            min-width: 90px !important;
            text-align: center !important;
        }
        /* TGL DIPERLUKAN */
        #dashboardTable.tab-sedang_proses th:nth-child(9),
        #dashboardTable.tab-sedang_proses td:nth-child(9) {
            width: 8% !important;
        }
        /* STATUS */
        #dashboardTable.tab-sedang_proses th:nth-child(10),
        #dashboardTable.tab-sedang_proses td:nth-child(10) {
            width: 10% !important;
        }
        /* PROSES */
        #dashboardTable.tab-sedang_proses th:nth-child(11),
        #dashboardTable.tab-sedang_proses td:nth-child(11) {
            width: 13% !important;
            min-width: 120px !important;
            text-align: center !important;
            white-space: nowrap !important;
        }
        /* TEXT WRAP untuk kolom panjang */
        #dashboardTable.tab-sedang_proses td:nth-child(4),
        #dashboardTable.tab-sedang_proses td:nth-child(6),
        #dashboardTable.tab-sedang_proses td:nth-child(7) {
            white-space: normal !important;
            word-break: normal !important;
            overflow-wrap: break-word !important;
            line-height: 1.35 !important;
        }

        /* ============================================================
           TABEL TIDAK LENGKAP (11 kolom)
           Ruang kosong kolom KELENGKAPAN didistribusikan ke
           NAMA PASIEN, RUANGAN, dan ALASAN.
           ============================================================ */
        #dashboardTable.tab-tidaklengkap {
            table-layout: fixed !important;
            width: 100% !important;
        }
        #dashboardTable.tab-tidaklengkap th,
        #dashboardTable.tab-tidaklengkap td {
            white-space: normal !important;
            word-wrap: break-word !important;
            word-break: normal !important;
            overflow-wrap: break-word !important;
        }
        /* NO */
        #dashboardTable.tab-tidaklengkap th:nth-child(1),
        #dashboardTable.tab-tidaklengkap td:nth-child(1) {
            width: 4% !important;
            text-align: center !important;
            white-space: nowrap !important;
        }
        /* NOMOR PERMINTAAN */
        #dashboardTable.tab-tidaklengkap th:nth-child(2),
        #dashboardTable.tab-tidaklengkap td:nth-child(2) {
            width: 10% !important;
        }
        /* NO MR */
        #dashboardTable.tab-tidaklengkap th:nth-child(3),
        #dashboardTable.tab-tidaklengkap td:nth-child(3) {
            width: 6% !important;
        }
        /* NAMA PASIEN */
        #dashboardTable.tab-tidaklengkap th:nth-child(4),
        #dashboardTable.tab-tidaklengkap td:nth-child(4) {
            width: 16% !important;
        }
        /* TGL PERMINTAAN */
        #dashboardTable.tab-tidaklengkap th:nth-child(5),
        #dashboardTable.tab-tidaklengkap td:nth-child(5) {
            width: 8% !important;
        }
        /* RUANGAN */
        #dashboardTable.tab-tidaklengkap th:nth-child(6),
        #dashboardTable.tab-tidaklengkap td:nth-child(6) {
            width: 11% !important;
        }
        /* ALASAN */
        #dashboardTable.tab-tidaklengkap th:nth-child(7),
        #dashboardTable.tab-tidaklengkap td:nth-child(7) {
            width: 16% !important;
        }
        /* GOL DARAH */
        #dashboardTable.tab-tidaklengkap th:nth-child(8),
        #dashboardTable.tab-tidaklengkap td:nth-child(8) {
            width: 8% !important;
            min-width: 90px !important;
            text-align: center !important;
        }
        /* TGL DIPERLUKAN */
        #dashboardTable.tab-tidaklengkap th:nth-child(9),
        #dashboardTable.tab-tidaklengkap td:nth-child(9) {
            width: 9% !important;
        }
        /* STATUS */
        #dashboardTable.tab-tidaklengkap th:nth-child(10),
        #dashboardTable.tab-tidaklengkap td:nth-child(10) {
            width: 10% !important;
        }
        /* KELENGKAPAN - compact, hanya cukup untuk badge */
        #dashboardTable.tab-tidaklengkap th:nth-child(11),
        #dashboardTable.tab-tidaklengkap td:nth-child(11) {
            width: 130px !important;
            min-width: 130px !important;
            max-width: 130px !important;
            text-align: center !important;
            white-space: nowrap !important;
        }
        /* TEXT WRAP untuk kolom panjang */
        #dashboardTable.tab-tidaklengkap td:nth-child(4),
        #dashboardTable.tab-tidaklengkap td:nth-child(6),
        #dashboardTable.tab-tidaklengkap td:nth-child(7) {
            white-space: normal !important;
            word-break: normal !important;
            overflow-wrap: break-word !important;
            line-height: 1.35 !important;
        }

        /* BADGE GOLONGAN DARAH */
        #dashboardTable .badge {
            font-size: 10px !important;
            padding: 3px 6px !important;

            white-space: normal !important;
            display: inline-block !important;

            max-width: 100% !important;

            text-align: center !important;
            line-height: 1.2 !important;

            overflow-wrap: break-word !important;
            word-break: normal !important;
        }
        /* DATATABLES HEADER */
        #dashboardTable_wrapper {
            width: 100% !important;
            max-width: 100% !important;
        }
        #dashboardTable_wrapper .dataTables_scrollHead,
        #dashboardTable_wrapper .dataTables_scrollBody {
            width: 100% !important;
        }
        /* HILANGKAN HORIZONTAL SCROLL */
        #dashboardTable_wrapper .dataTables_scrollBody {
            overflow-x: hidden !important;
        }
        /* RESPONSIVE - LAYAR KECIL */
        @media (max-width: 1200px) {
            #dashboardTable {
                font-size: 11px !important;
            }
            #dashboardTable th,
            #dashboardTable td {
                padding: 5px 3px !important;
            }
            #dashboardTable thead th {
                font-size: 10px !important;
            }
            #dashboardTable tbody td {
                font-size: 11px !important;
            }
        }
        /* LAYAR LEBIH KECIL\ */
        @media (max-width: 992px) {

            #dashboardTable th,
            #dashboardTable td {
                padding: 4px 3px !important;
            }
            #dashboardTable thead th {
                font-size: 9px !important;
            }
            #dashboardTable tbody td {
                font-size: 10px !important;
            }
            #dashboardTable .badge {
                font-size: 9px !important;
                padding: 2px 4px !important;
            }
        }
    </style>

<!-- ============================================================
     TAB STATUS + TABLE
     ============================================================ -->
<ul class="nav nav-tabs dashboard-tabs mb-0 overflow-auto flex-nowrap" id="dashboardTabs"
    role="tablist" data-default-tab="<?php echo $defaultTab; ?>">
    <?php foreach ($tabs as $key => $t): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?php echo ($key === $defaultTab) ? 'active' : ''; ?>"
                    id="dash-tab-<?php echo $key; ?>"
                    type="button" role="tab"
                    data-bs-toggle="tab"
                    data-bs-target="#tab-dashboard"
                    data-tab-key="<?php echo $key; ?>">
                <i class="fas <?php echo $t['icon']; ?> me-1"></i><?php echo htmlspecialchars($t['label']); ?>
                <span class="badge bg-<?php echo $t['color']; ?> ms-1"><?php echo isset($tabSummaryKey[$key]) ? (int)$sum[$tabSummaryKey[$key]] : count($t['rows']); ?></span>
            </button>
        </li>
    <?php endforeach; ?>
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active" id="tab-dashboard" role="tabpanel" tabindex="0">
        <div class="card border-top-0 card-tab-flush">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-table me-2"></i><span id="dashboardTableTitle"><?php echo htmlspecialchars($tabs[$defaultTab]['label']); ?></span>
                </h5>
                <div class="text-muted small d-none d-md-block">
                    <i class="fas fa-info-circle me-1"></i>Gunakan kolom pencarian untuk menyaring data
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="dashboardTable" class="table table-hover w-100">
                        <thead></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     DASHBOARD SCRIPT (single init, no duplication)
     ============================================================ -->
<script>
/* Data tabel dikirim dari PHP (satu sumber, tidak ada duplikasi script) */
window.dashboardData = <?php echo json_encode(
    $tabs,
    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
); ?>;

/**
 * Render ulang #dashboardTable sesuai tab aktif lalu (re)inisialisasi
 * DataTables. Memakai konfigurasi global dari assets/js/app.js
 * (bahasa Indonesia, responsive, paging, sorting).
 */
function renderDashboardTab(key) {
    var cfg = window.dashboardData[key];
    if (!cfg) {
        return;
    }

    var $table = $('#dashboardTable');
    if (!$.fn.dataTable) {
        return;
    }

    /* Hancurkan instance lama agar header & kolom baru bersih */
    if ($.fn.dataTable.isDataTable($table[0])) {
        $table.DataTable().clear().destroy();
    }

    /* Bangun header & body sesuai dataset tab */
    var kelengkapanIdx = (key === 'tidaklengkap') ? cfg.columns.length - 1 : -1;

    var headHtml = '<tr>' + cfg.columns.map(function (c, i) {
        var cls = (i === kelengkapanIdx) ? ' class="col-kelengkapan"' : '';
        return '<th' + cls + '>' + c + '</th>';
    }).join('') + '</tr>';

    var bodyHtml = cfg.rows.map(function (row) {
        return '<tr>' + row.join('') + '</tr>';
    }).join('');

    $table.find('thead').html(headHtml);
    $table.find('tbody').html(bodyHtml);

    /* Tambah class tab-specific untuk CSS kolom per tab */
    $table.removeClass('tab-permintaan tab-sedang_proses tab-siap tab-donor tab-baru tab-incompatible tab-masasimpan tab-belumambil tab-habis tab-tidaklengkap');
    $table.addClass('tab-' + key);

    /* columnDefs: tambah width compact khusus kolom KELENGKAPAN */
    var columnDefs = [
        {
            targets: 0,
            className: 'text-center'
        },
        {
            targets: -1,
            className: 'text-center'
        }
    ];
    if (kelengkapanIdx >= 0) {
        columnDefs.push({
            targets: kelengkapanIdx,
            width: '130px',
            className: 'text-center col-kelengkapan'
        });
    }

    /* Inisialisasi DataTables satu kali per render */
    $table.DataTable({
        responsive: false,
        processing: true,
        autoWidth: false,
        scrollX: false,

        columnDefs: columnDefs,

        drawCallback: function () {
            $table.find('[data-bs-toggle="tooltip"]').each(function () {
                if (window.bootstrap && bootstrap.Tooltip) {
                    new bootstrap.Tooltip(this);
                }
            });
        }
    });

    var $title = $('#dashboardTableTitle');
    if ($title.length) {
        $title.text(cfg.label);
    }
}

/**
 * Inisialisasi seluruh tabel dashboard:
 * - render tab default
 * - bind event ganti tab
 * - bind klik stat card
 */
window.initDashboardTables = function () {
    var $tabs = $('#dashboardTabs');
    var defaultKey = $tabs.data('default-tab') || 'permintaan';

    renderDashboardTab(defaultKey);

    /* Ganti tab -> render ulang tabel */
    $tabs.find('.nav-link').on('shown.bs.tab', function (e) {
        var key = $(e.currentTarget).data('tab-key');
        if (key) {
            renderDashboardTab(key);
        }
    });

    /* Klik stat card -> buka tab terkait */
    $('[data-stat-tab]').on('click keydown', function (e) {
        if (e.type === 'keydown' && e.key !== 'Enter' && e.key !== ' ') {
            return;
        }
        e.preventDefault();
        var key = $(this).data('stat-tab');
        var $link = $tabs.find('.nav-link[data-tab-key="' + key + '"]');
        if ($link.length && window.bootstrap && bootstrap.Tab) {
            new bootstrap.Tab($link[0]).show();
        } else if (window.dashboardData[key]) {
            renderDashboardTab(key);
        }
    });
};

/* jQuery & vendor script dimuat di footer; jalankan setelah DOM siap */
document.addEventListener('DOMContentLoaded', function () {
    window.initDashboardTables();
});
</script>
