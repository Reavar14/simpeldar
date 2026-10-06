<?php
defined('BASEPATH') OR exit('No direct script access allowed');

date_default_timezone_set('Asia/Jakarta');

if (!function_exists('hp_norm')) {
    function hp_norm($value) {
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
    <title>Hasil Pemeriksaan - <?php echo htmlspecialchars(hp_norm($d['no_permintaan'])); ?></title>
    <style>
        @page { size: A4 portrait; margin: 15mm 18mm; }
        * { box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .report { width: 100%; }
        .header { width: 100%; border-bottom: 3px double #000; padding-bottom: 6px; margin-bottom: 10px; }
        .header:after { content: ""; display: block; clear: both; }
        .header .logo { float: left; width: 70px; height: 60px; }
        .header .logo img { width: 70px; height: 60px; }
        .header .identitas { float: left; width: calc(100% - 80px); margin-left: 10px; text-align: center; }
        .header .identitas h2 { margin: 2px 0; font-size: 15px; }
        .header .identitas p { margin: 2px 0; font-size: 11px; }
        .section-title { font-weight: bold; font-size: 11px; margin: 12px 0 4px; }
        table.form { width: 100%; border-collapse: collapse; }
        table.form td { vertical-align: top; padding: 1px 2px; }
        table.form td.label { width: 120px; }
        table.form td.sep { width: 8px; text-align: center; }
        table.grid { width: 100%; border-collapse: collapse; margin-top: 4px; }
        table.grid th, table.grid td {
            border: 0.75px solid #000;
            padding: 2px 3px;
            font-size: 10px;
            vertical-align: middle;
        }
        table.grid th { text-align: center; font-weight: bold; }
        table.grid td.no { text-align: center; width: 26px; }
        table.grid td.kantong { width: 90px; }
        table.grid td.jenis { width: 120px; }
        table.grid td.gol { text-align: center; width: 55px; }
        table.grid td.exp { width: 70px; }
        table.grid td.vol { width: 55px; }
        table.grid td.memberi, table.grid td.terima { width: 95px; }
        table.grid td.ket { text-align: center; width: 45px; }
        .signature-area { width: 100%; margin-top: 28px; border-collapse: collapse; }
        .signature-area td { width: 33%; text-align: center; vertical-align: top; }
        .signature-area .role { margin-top: 42px; font-weight: bold; }
        .signature-area .name { margin-top: 2px; }
        .no-print { margin-bottom: 12px; }
        .btn-print {
            background: #337ab7; color: #fff; border: none;
            padding: 6px 14px; font-size: 12px; cursor: pointer; border-radius: 3px;
        }
        @media print {
            .no-print { display: none; }
            body { font-size: 10px; }
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

        <?php if (!empty($d)) { ?>
        <!-- Identitas pasien / Penerimaan sampel -->
        <div class="section-title">PENERIMAAN SAMPEL (Diisi oleh petugas BDRS/UTDD)</div>
        <table class="form">
            <tr>
                <td class="label">Nama OS</td>
                <td class="sep">:</td>
                <td><strong><?php echo htmlspecialchars(hp_norm($d['nama'])); ?></strong></td>
                <td class="label">MR</td>
                <td class="sep">:</td>
                <td><strong><?php echo htmlspecialchars(hp_norm($d['mr'])); ?></strong></td>
            </tr>
            <tr>
                <td class="label">Diterima oleh</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(hp_norm($d['DE'])); ?></td>
                <td class="label">Tgl / Jam</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(hp_norm($d['tgl_minta'])); ?></td>
            </tr>
        </table>

        <!-- Detail pemeriksaan & pemberian darah -->
        <div class="section-title">PEMERIKSAAN DAN PEMBERIAN DARAH (Diisi oleh petugas BDRS/UTDD)</div>
        <table class="form">
            <tr>
                <td class="label">Diperiksa oleh</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(hp_norm($d['nama_analis'])); ?></td>
                <td class="label">Tgl / Jam</td>
                <td class="sep">:</td>
                <td><?php echo htmlspecialchars(hp_norm($d['jam_analis'])); ?></td>
            </tr>
            <tr>
                <td class="label">dengan hasil pemeriksaan</td>
                <td class="sep">:</td>
                <td colspan="4"><strong><?php echo htmlspecialchars(hp_norm($d['hasil'])); ?></strong></td>
            </tr>
            <tr>
                <td class="label">dengan perincian</td>
                <td class="sep">:</td>
                <td colspan="4">
                    Jenis Darah: <?php echo htmlspecialchars(hp_norm($d['jenis_darah'])); ?>
                    &nbsp;|&nbsp; Gol. Darah: <?php echo htmlspecialchars(hp_norm($d['gol_darah'])); ?>
                    &nbsp;|&nbsp; Volume: <?php echo htmlspecialchars(hp_norm($d['volume'])); ?> mL
                    &nbsp;|&nbsp; Tgl Diperlukan: <?php echo htmlspecialchars(hp_norm($d['tgl_diperlukan'])); ?>
                </td>
            </tr>
        </table>

        <!-- Tabel 12 kantong -->
        <table class="grid">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>No. Kantong</th>
                    <th>Jenis Darah</th>
                    <th>Gol Darah</th>
                    <th>EXP Date</th>
                    <th>Vol (mL)</th>
                    <th>Yang Memeberikan</th>
                    <th>Yang Menerima</th>
                    <th>KET</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($i = 1; $i <= 12; $i++) { ?>
                    <?php
                        $no_kantong = hp_norm($d['no_kantong_' . $i] ?? null);
                        $ada_kantong = ($no_kantong !== '');
                    ?>
                    <tr>
                        <td class="no"><?php echo $i; ?></td>
                        <td class="kantong"><?php echo htmlspecialchars($no_kantong); ?></td>
                        <td class="jenis"><?php echo $ada_kantong ? htmlspecialchars(hp_norm($d['jenis_darah'])) : ''; ?></td>
                        <td class="gol"><?php echo $ada_kantong ? htmlspecialchars(hp_norm($d['gol_darah'])) : ''; ?></td>
                        <td class="exp"><?php echo htmlspecialchars(hp_norm($d['exp_' . $i] ?? null)); ?></td>
                        <td class="vol"><?php echo htmlspecialchars(hp_norm($d['volume_' . $i] ?? null)); ?></td>
                        <td class="memberi"></td>
                        <td class="terima"></td>
                        <td class="ket"></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Signature area -->
        <table class="signature-area">
            <tr>
                <td>
                    <div class="role">Yang Menerima</div>
                    <div class="name">( <?php echo htmlspecialchars(hp_norm($d['pengambildarah'])); ?> )</div>
                </td>
                <td>
                    <div class="role">Analis</div>
                    <div class="name">( <?php echo htmlspecialchars(hp_norm($d['nama_analis'])); ?> )</div>
                </td>
                <td>
                    <div class="role">Dokter</div>
                    <div class="name">( <?php echo htmlspecialchars(hp_norm($d['nama_dokter'])); ?> )</div>
                </td>
            </tr>
        </table>
        <?php } else { ?>
        <p>Data permintaan darah tidak ditemukan.</p>
        <?php } ?>
    </div>
</body>
</html>
