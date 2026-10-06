<?php
defined('BASEPATH') OR exit('No direct script access allowed');

date_default_timezone_set('Asia/Jakarta');

if (!function_exists('fd_norm')) {
    function fd_norm($value) {
        if ($value === null || $value === false) {
            return '';
        }
        return trim((string)$value);
    }
}

$d = isset($d) ? $d : array();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Form Darah - <?php echo htmlspecialchars(fd_norm($d['no_permintaan'])); ?></title>
    <style>
        @page { size: A4 portrait; margin: 12mm 14mm; }
        * { box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .report { width: 100%; }
        .header { width: 100%; border-bottom: 3px double #000; padding-bottom: 5px; margin-bottom: 8px; }
        .header:after { content: ""; display: block; clear: both; }
        .header .logo { float: left; width: 60px; height: 52px; }
        .header .logo img { width: 60px; height: 52px; }
        .header .identitas { float: left; width: calc(100% - 70px); margin-left: 10px; text-align: center; }
        .header .identitas h2 { margin: 1px 0; font-size: 14px; }
        .header .identitas p { margin: 1px 0; font-size: 9px; }
        .title {
            text-align: center; font-weight: bold; font-size: 12px;
            border-bottom: 1.5px solid #000; padding-bottom: 3px; margin-bottom: 8px;
        }
        table.form { width: 100%; border-collapse: collapse; }
        table.form td { vertical-align: top; padding: 1px 2px; }
        table.form td.label { width: 130px; }
        table.form td.sep { width: 6px; text-align: center; }
        .section-title { font-weight: bold; font-size: 10px; margin: 8px 0 3px; }
        table.grid { width: 100%; border-collapse: collapse; }
        table.grid th, table.grid td {
            border: 0.75px solid #000;
            padding: 2px 3px;
            font-size: 9px;
            vertical-align: middle;
        }
        table.grid th { text-align: center; font-weight: bold; }
        table.grid td.no { text-align: center; width: 24px; }
        table.grid td.kantong { width: 88px; }
        table.grid td.gol { text-align: center; width: 52px; }
        table.grid td.tgl { text-align: center; width: 68px; }
        table.grid td.vol { text-align: center; width: 42px; }
        table.grid td.mayor { width: 62px; }
        table.grid td.minor { width: 62px; }
        table.grid td.exp { text-align: center; width: 68px; }
        .riwayat { border: 0.75px solid #000; padding: 4px 6px; min-height: 34px; margin-top: 3px; white-space: pre-line; }
        .signature-area { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .signature-area td { width: 50%; text-align: center; vertical-align: top; }
        .signature-area .role { font-weight: bold; }
        .signature-area .space { height: 38px; }
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
            <div class="logo">
                <img src="<?php echo base_url('assets/logo/logo-dharmais.jpg'); ?>" alt="Logo">
            </div>
            <div class="identitas">
                <h2>RUMAH SAKIT KANKER &quot;DHARMAIS&quot;</h2>
                <p>JL LET.JEND. S.PARMAN KAV.84-86 SLIPI, JAKARTA BARAT 11420</p>
                <p>Telp: 021-5681570 Faximile: 021-5681579</p>
            </div>
        </div>

        <div class="title">FORM PERMINTAAN DARAH</div>

        <?php if (!empty($d)) { ?>
        <!-- Identitas pasien & permintaan -->
        <table class="form">
            <tr>
                <td class="label">Nomor Permintaan</td>
                <td class="sep">:</td>
                <td><strong><?php echo htmlspecialchars(fd_norm($d['no_permintaan'])); ?></strong></td>
                <td class="label">Tanggal Permintaan</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(fd_norm($d['tgl_minta'])); ?></td>
            </tr>
            <tr>
                <td class="label">Nomor MR</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(fd_norm($d['mr'])); ?></td>
                <td class="label">Tanggal Diperlukan</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(fd_norm($d['tgl_diperlukan'])); ?></td>
            </tr>
            <tr>
                <td class="label">Nama Pasien</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(fd_norm($d['nama'])); ?></td>
                <td class="label">Jenis Kelamin</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(fd_norm($d['jenis_kelamin'])); ?></td>
            </tr>
            <tr>
                <td class="label">Tanggal Lahir</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(fd_norm($d['tgl_lahir'])); ?> (<?php echo htmlspecialchars(fd_norm($d['usia'])); ?> th)</td>
                <td class="label">Golongan Darah</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(fd_norm($d['gol_darah'])); ?></td>
            </tr>
            <tr>
                <td class="label">Ruangan Rawat</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(fd_norm($d['RUANGAN'])); ?></td>
                <td class="label">Status</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(fd_norm($d['status_proses'])); ?></td>
            </tr>
            <tr>
                <td class="label">Dokter DPJP</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(fd_norm($d['nama_dokter'])); ?></td>
                <td class="label">Tujuan</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(fd_norm($d['TUJUAN'])); ?></td>
            </tr>
        </table>

        <div class="section-title">Detail Permintaan Darah</div>
        <table class="form">
            <tr>
                <td class="label">Jenis Darah</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(fd_norm($d['jenis_darah'])); ?></td>
                <td class="label">Tipe Darah</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(fd_norm($d['bufycoat'])); ?></td>
            </tr>
            <tr>
                <td class="label">Volume</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(fd_norm($d['volume'])); ?></td>
                <td class="label">Diagnosa Kanker</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(fd_norm($d['diagnosa'])); ?></td>
            </tr>
            <tr>
                <td class="label">Alasan</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(fd_norm($d['alasan'])); ?></td>
                <td class="label">Kadar HB</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(fd_norm($d['kadar_hb'])); ?></td>
            </tr>
            <tr>
                <td class="label">Trombosit</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(fd_norm($d['trombosit'])); ?></td>
                <td class="label">Hasil Pemeriksaan</td>
                <td class="sep">:</td>
                <td><strong><?php echo htmlspecialchars(fd_norm($d['hasil'])); ?></strong></td>
            </tr>
            <tr>
                <td class="label">Nama Analis</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(fd_norm($d['nama_analis'])); ?></td>
                <td class="label">Analis 2</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(fd_norm($d['nama_analis2'])); ?></td>
            </tr>
            <tr>
                <td class="label">Perawat</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(fd_norm($d['nama_perawat'])); ?></td>
                <td class="label">Auto Kontrol</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(fd_norm($d['autkon'])); ?></td>
            </tr>
            <tr>
                <td class="label">Petugas Pengambil Contoh</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(fd_norm($d['pengambildarah'])); ?></td>
                <td class="label">Tanggal/Jam Analis</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(fd_norm($d['jam_analis'])); ?></td>
            </tr>
        </table>

        <!-- Histori riwayat -->
        <div class="section-title">Histori Riwayat Alergi Transfusi dan Catatan</div>
        <div class="riwayat"><?php echo htmlspecialchars(fd_norm($d['RIWAYAT'])); ?>&nbsp;</div>

        <!-- Tabel 12 kantong -->
        <div class="section-title">Detail Komponen Darah</div>
        <table class="grid">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor Kantong</th>
                    <th>Gol. Darah Kantong</th>
                    <th>Tanggal Input Kantong</th>
                    <th>Volume</th>
                    <th>Mayor</th>
                    <th>Minor</th>
                    <th>EXP Date</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($i = 1; $i <= 12; $i++) { ?>
                    <?php
                        $no_kantong = fd_norm($d['no_kantong_' . $i] ?? null);
                        $gol_kantong = fd_norm($d['GOLDAR_KL' . $i] ?? null);
                        $tgl_input = fd_norm($d['tglkantong' . $i] ?? null);
                        if ($tgl_input !== '' && preg_match('/^\d{4}-\d{2}-\d{2}/', $tgl_input)) {
                            $tgl_input = date('d-m-Y', strtotime($tgl_input));
                        }
                        $vol = fd_norm($d['volume_' . $i] ?? null);
                        $exp = fd_norm($d['exp_' . $i] ?? null);
                        $mayor = fd_norm($d['mayor_' . $i] ?? null);
                        $minor = fd_norm($d['minor_' . $i] ?? null);
                    ?>
                    <tr>
                        <td class="no"><?php echo $i; ?></td>
                        <td class="kantong"><?php echo htmlspecialchars($no_kantong); ?></td>
                        <td class="gol"><?php echo htmlspecialchars($gol_kantong); ?></td>
                        <td class="tgl"><?php echo htmlspecialchars($tgl_input); ?></td>
                        <td class="vol"><?php echo htmlspecialchars($vol); ?></td>
                        <td class="mayor"><?php echo htmlspecialchars($mayor); ?></td>
                        <td class="minor"><?php echo htmlspecialchars($minor); ?></td>
                        <td class="exp"><?php echo htmlspecialchars($exp); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Area tanda tangan -->
        <table class="signature-area">
            <tr>
                <td>
                    <div class="role">Nama, Tanggal, Jam, Yang Memberi</div>
                    <div class="space"></div>
                    <div class="name">( <?php echo htmlspecialchars(fd_norm($d['nama_analis'])); ?> )</div>
                </td>
                <td>
                    <div class="role">Nama, Alamat, Telp/Hp Penerima</div>
                    <div class="space"></div>
                    <div class="name">( <?php echo htmlspecialchars(fd_norm($d['pengambildarah'])); ?> )</div>
                </td>
            </tr>
        </table>
        <?php } else { ?>
        <p>Data permintaan darah tidak ditemukan.</p>
        <?php } ?>
    </div>
</body>
</html>
