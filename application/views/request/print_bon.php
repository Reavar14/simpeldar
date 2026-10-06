<?php
defined('BASEPATH') OR exit('No direct script access allowed');

date_default_timezone_set('Asia/Jakarta');

if (!function_exists('bn_norm')) {
    function bn_norm($value) {
        if ($value === null || $value === false) {
            return '';
        }
        return trim((string)$value);
    }
}

$d = isset($d) ? $d : array();

$petugas_terima = bn_norm($d['PETUGAS_TERIMA'] ?? null);
if ($petugas_terima === '') {
    $petugas_terima = bn_norm($d['NAMA_CREATE'] ?? null);
}

$tanggal_terima = bn_norm($d['tanggal_terima'] ?? null);
$label_tanggal = ($tanggal_terima === '') ? 'Tanggal Permintaan' : 'Tanggal Terima Sampel';
if ($tanggal_terima === '') {
    $tanggal_terima = bn_norm($d['tgl_minta'] ?? null);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bon Permintaan Darah - <?php echo htmlspecialchars(bn_norm($d['no_permintaan'])); ?></title>
    <style>
        @page { size: A5 portrait; margin: 8mm 10mm; }
        * { box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .report { width: 100%; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 5px; margin-bottom: 8px; }
        .header h2 { margin: 0; font-size: 13px; }
        .header p { margin: 1px 0; font-size: 9px; }
        .header .form-code { font-size: 8px; font-style: italic; color: #444; }
        .title {
            text-align: center; font-weight: bold; font-size: 13px;
            border-bottom: 1.5px solid #000; padding-bottom: 4px; margin-bottom: 8px;
        }
        table.form { width: 100%; border-collapse: collapse; }
        table.form td { vertical-align: top; padding: 1px 2px; }
        table.form td.label { width: 105px; }
        table.form td.sep { width: 8px; text-align: center; }
        table.grid { width: 100%; border-collapse: collapse; margin-top: 4px; }
        table.grid th, table.grid td {
            border: 0.75px solid #000;
            padding: 3px 4px;
            font-size: 10px;
            vertical-align: middle;
        }
        table.grid th { text-align: center; font-weight: bold; background: #f0f0f0; }
        .note { margin-top: 8px; font-size: 10px; }
        .note .warn { font-weight: bold; }
        .signature-area { width: 100%; border-collapse: collapse; margin-top: 24px; }
        .signature-area td { width: 50%; text-align: center; vertical-align: top; }
        .signature-area .role { font-weight: bold; }
        .signature-area .space { height: 42px; }
        .signature-area .name { margin-top: 2px; }
        .no-print { margin-bottom: 10px; }
        .btn-print {
            background: #337ab7; color: #fff; border: none;
            padding: 6px 14px; font-size: 12px; cursor: pointer; border-radius: 3px;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print();">
    <div class="no-print">
        <button class="btn-print" type="button" onclick="window.print();">
            <i class="fa fa-print"></i> Cetak / Simpan PDF
        </button>
    </div>

    <div class="report">
        <!-- Header report -->
        <div class="header">
            <h2>RUMAH SAKIT KANKER DHARMAIS</h2>
            <p>JL. LETJEND. S. PARMAN KAV. 84-86</p>
            <p>SLIPI - JAKARTA BARAT</p>
            <p>Telp. 021-5681570 ext 2239</p>
            <div class="form-code">FRM.IBD.002.Rev.02</div>
        </div>

        <div class="title">BON NOMOR PERMINTAAN DARAH</div>

        <?php if (!empty($d)) { ?>
        <!-- Identitas pasien -->
        <table class="form">
            <tr>
                <td class="label">No. Permintaan</td>
                <td class="sep">:</td>
                <td><strong><?php echo htmlspecialchars(bn_norm($d['no_permintaan'])); ?></strong></td>
            </tr>
            <tr>
                <td class="label">Nomor MR</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(bn_norm($d['mr'])); ?></td>
            </tr>
            <tr>
                <td class="label">Nama Pasien</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(bn_norm($d['nama'])); ?></td>
            </tr>
            <tr>
                <td class="label">Umur / Kelamin</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(bn_norm($d['umur'])); ?> tahun / <?php echo htmlspecialchars(bn_norm($d['kelamin'])); ?></td>
            </tr>
            <tr>
                <td class="label">Ruangan</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(bn_norm($d['ruangan'])); ?></td>
            </tr>
            <tr>
                <td class="label">Dokter Peminta</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(bn_norm($d['nama_dokter'])); ?></td>
            </tr>
        </table>

        <!-- Tabel kebutuhan darah -->
        <table class="grid">
            <thead>
                <tr>
                    <th>Jenis Darah</th>
                    <th>Gol. Darah</th>
                    <th>Volume</th>
                    <th>Tgl Diperlukan</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo htmlspecialchars(bn_norm($d['nama_jenis'])); ?></td>
                    <td style="text-align:center;"><?php echo htmlspecialchars(bn_norm($d['gol_darah'])); ?></td>
                    <td style="text-align:center;"><?php echo htmlspecialchars(bn_norm($d['volume'])); ?></td>
                    <td style="text-align:center;"><?php echo htmlspecialchars(bn_norm($d['tgl_diperlukan'])); ?></td>
                    <td style="text-align:center;"><?php echo htmlspecialchars(bn_norm($d['status_proses'])); ?></td>
                </tr>
            </tbody>
        </table>

        <!-- Detail permintaan -->
        <table class="form">
            <tr>
                <td class="label">Tujuan</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(bn_norm($d['TUJUAN'])); ?></td>
            </tr>
            <tr>
                <td class="label">Alasan</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(bn_norm($d['alasan'])); ?></td>
            </tr>
            <tr>
                <td class="label">Petugas IBD</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(bn_norm($d['nama_analis'])); ?></td>
            </tr>
            <tr>
                <td class="label"><?php echo $label_tanggal; ?></td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars($tanggal_terima); ?></td>
            </tr>
            <tr>
                <td class="label">Petugas Terima Sampel</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars($petugas_terima); ?></td>
            </tr>
        </table>

        <div class="note">
            <div class="warn">Note : Masa simpan darah 3 hari</div>
            <div>* Setiap ambil darah wajib isi E-MR</div>
        </div>

        <!-- Area tanda tangan -->
        <table class="signature-area">
            <tr>
                <td>
                    <div class="role">Petugas Pemberi Darah</div>
                    <div class="space"></div>
                    <div class="name">( <?php echo htmlspecialchars(bn_norm($d['nama_analis'])); ?> )</div>
                </td>
                <td>
                    <div class="role">Petugas Penerima</div>
                    <div class="space"></div>
                    <div class="name">( <?php echo htmlspecialchars($petugas_terima); ?> )</div>
                </td>
            </tr>
        </table>
        <?php } else { ?>
        <p>Data permintaan darah tidak ditemukan.</p>
        <?php } ?>
    </div>
</body>
</html>
