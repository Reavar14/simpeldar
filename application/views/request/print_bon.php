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
        /* 
           Native JRXML bonminta.jrxml exact specs:
           pageWidth=297pt pageHeight=421pt
           leftMargin=20pt rightMargin=20pt topMargin=0 bottomMargin=10pt
           columnWidth=257pt
           All coordinates in points (1pt = 1/72 inch)
        */
        @page {
            size: 104.4mm 148.2mm;
            margin: 0mm 7.05mm 3.52mm 7.05mm;
        }
        * { box-sizing: border-box; }
        html, body {
            font-family: 'DejaVu Sans', 'SansSerif', Arial, Helvetica, sans-serif;
            font-size: 9pt;
            line-height: 1.2;
            color: #000;
            margin: 0;
            padding: 0;
            width: 104.4mm;
            height: 148.2mm;
        }
        /* Page wrapper - exact JRXML page size */
        .bon-page {
            width: 104.4mm;
            height: 148.2mm;
            position: relative;
            overflow: hidden;
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        /* Content area = columnWidth = 257pt = 90.7mm */
        .report { width: 90.7mm; }

        /* Header: matches pageHeader band (height 54pt) */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            height: 54pt;
            padding-top: 0;
        }
        .bon-header {
            position: relative;
            top: 5pt;
        }
        .header-left {
            width: 127pt;
        }
        .header-left p {
            margin: 0;
            font-size: 5pt;
            line-height: 7pt;
            height: 7pt;
            font-family: 'DejaVu Sans', 'SansSerif', Arial, Helvetica, sans-serif;
        }
        .header-right {
            width: 58pt;
            text-align: center;
            margin-left: auto;
            margin-top: 5pt; /* y=5 in JRXML */
        }
        .header-right .frm-line {
            font-size: 3pt;
            font-style: italic;
            font-family: 'DejaVu Sans', 'SansSerif', Arial, Helvetica, sans-serif;
            line-height: 1.0;
            display: block;
            margin: 0;
        }
        .header-right .frm-line:first-child {
            margin-bottom: 0; /* y=5 to y=13 = 8pt gap */
        }

        /* Title: matches detail band y=2, h=17, border-bottom 1.5pt */
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            font-family: 'DejaVu Sans', 'SansSerif', Arial, Helvetica, sans-serif;
            border-bottom: 1.5pt solid #000;
            padding: 2pt 0;
            margin: 2pt 0 4pt 0;
            height: 17pt;
            line-height: 1.2;
        }

        /* Form table: matches detail field positions */
        table.form {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            font-family: 'DejaVu Sans', 'SansSerif', Arial, Helvetica, sans-serif;
            margin: 0;
        }
        table.form td {
            vertical-align: middle;
            padding: 0;
            margin: 0;
        }
        /* Label column: x=10 to x=117 = 107pt */
        table.form td.label {
            width: 107pt;
            padding-right: 4pt;
        }
        /* Colon column: x=117, w=10pt */
        table.form td.sep {
            width: 10pt;
            text-align: center;
            font-weight: bold;
        }
        /* Value column: x=127 onward */
        table.form td.value {
            width: auto;
        }
        /* Bold for No. Permintaan value */
        table.form tr:first-child td.value { font-weight: bold; }

        /* Row heights matching JRXML y positions */
        table.form tr:nth-child(1) td { height: 20pt; }  /* y=29, h=20 */
        table.form tr:nth-child(2) td { height: 15pt; }  /* y=49, h=15 */
        table.form tr:nth-child(3) td { height: 15pt; }  /* y=64, h=15 */
        table.form tr:nth-child(4) td { height: 15pt; }  /* y=79, h=15 */
        table.form tr:nth-child(5) td { height: 15pt; }  /* y=94, h=15 */
        table.form tr:nth-child(6) td { height: 15pt; }  /* y=109, h=15 */
        table.form tr:nth-child(7) td { height: 15pt; }  /* y=125, h=15 */
        table.form tr:nth-child(8) td { height: 15pt; }  /* y=140, h=15 */
        table.form tr:nth-child(9) td { height: 15pt; }  /* y=157, h=15 */
        table.form tr:nth-child(10) td { height: 15pt; } /* y=174, h=15 */
        table.form tr:nth-child(11) td { height: 15pt; } /* y=191, h=15 */
        table.form tr:nth-child(12) td { height: 15pt; } /* y=208, h=15 */

        /* Umur/Kelamin special: value split */
        table.form td.value-split { width: 25pt; }
        table.form td.sep-slash { width: 11pt; text-align: center; font-weight: bold; }
        table.form td.value-kelamin { width: 13pt; }

        /* Note section - matches JRXML y=234, h=16, font 12pt bold italic center */
        .note {
            margin-top: 16pt; /* gap from Note row (y=208+15=223) to E-MR (y=234) = 11pt, but table spacing adds */
            font-size: 12pt;
            font-weight: bold;
            font-style: italic;
            font-family: 'DejaVu Sans', 'SansSerif', Arial, Helvetica, sans-serif;
            text-align: center;
            width: 100%;
        }

        .no-print { margin-bottom: 10pt; }
        .btn-print {
            background: #337ab7; color: #fff; border: none;
            padding: 6px 14px; font-size: 12pt; cursor: pointer; border-radius: 3px;
        }
        @media print {
            .no-print { display: none !important; }
            html, body { margin: 0; padding: 0; width: 104.4mm; height: 148.2mm; }
            @page { size: 104.4mm 148.2mm; margin: 0mm 7.05mm 3.52mm 7.05mm; }
            * { margin: 0; padding: 0; }
        }
    </style>
</head>
<body onload="window.print();">
    <div class="no-print">
        <button class="btn-print" type="button" onclick="window.print();">
            Cetak / Simpan PDF
        </button>
    </div>

    <div class="bon-page">
        <div class="report">
        <div class="header bon-header">
            <div class="header-left">
                <p>RUMAH SAKIT KANKER DHARMAIS</p>
                <p>JL.LETJEND. S.PARMAN KAV.84-86</p>
                <p>SLIPI-JAKARTA BARAT</p>
                <p>Telp. 021-5681570 ext 2239</p>
            </div>
            <div class="header-right">
                <span class="frm-line">FRM.IBD.002.Rev.02</span>
                <span class="frm-line">2 April 2012</span>
            </div>
        </div>

        <div class="title">BON NOMOR PERMINTAAN DARAH</div>

        <?php if (!empty($d)) { ?>
        <table class="form">
            <tr>
                <td class="label">No. Permintaan</td>
                <td class="sep">:</td>
                <td class="value"><strong><?php echo htmlspecialchars(bn_norm($d['no_permintaan'])); ?></strong></td>
            </tr>
            <tr>
                <td class="label">Nomor MR</td>
                <td class="sep">:</td>
                <td class="value"><?php echo htmlspecialchars(bn_norm($d['mr'])); ?></td>
            </tr>
            <tr>
                <td class="label">Nama Pasien</td>
                <td class="sep">:</td>
                <td class="value"><?php echo htmlspecialchars(bn_norm($d['nama'])); ?></td>
            </tr>
            <tr>
                <td class="label">Umur</td>
                <td class="sep">:</td>
                <td class="value value-split"><?php echo htmlspecialchars(bn_norm($d['umur'])); ?></td>
                <td class="sep sep-slash">/</td>
                <td class="value value-kelamin"><?php echo htmlspecialchars(bn_norm($d['kelamin'])); ?></td>
            </tr>
            <tr>
                <td class="label">Ruangan</td>
                <td class="sep">:</td>
                <td class="value"><?php echo htmlspecialchars(bn_norm($d['ruangan'])); ?></td>
            </tr>
            <tr>
                <td class="label">Jenis Darah</td>
                <td class="sep">:</td>
                <td class="value"><?php echo htmlspecialchars(bn_norm($d['nama_jenis'])); ?></td>
            </tr>
            <tr>
                <td class="label">Volume</td>
                <td class="sep">:</td>
                <td class="value"><?php echo htmlspecialchars(bn_norm($d['volume'])); ?></td>
            </tr>
            <tr>
                <td class="label">Tujuan</td>
                <td class="sep">:</td>
                <td class="value"><?php echo htmlspecialchars(bn_norm($d['TUJUAN'])); ?></td>
            </tr>
            <tr>
                <td class="label">Rencana Transfusi</td>
                <td class="sep">:</td>
                <td class="value"><?php echo htmlspecialchars(bn_norm($d['tgl_diperlukan'])); ?></td>
            </tr>
            <tr>
                <td class="label">Petugas Terima Sampel</td>
                <td class="sep">:</td>
                <td class="value"><?php echo htmlspecialchars($petugas_terima); ?></td>
            </tr>
            <tr>
                <td class="label">Note</td>
                <td class="sep">:</td>
                <td class="value">Masa simpan darah 3 hari</td>
            </tr>
        </table>

        <div class="note">
            <div class="emr">* Setiap ambil darah wajib isi E-MR</div>
        </div>
        <?php } else { ?>
        <p>Data permintaan darah tidak ditemukan.</p>
        <?php } ?>
        </div>
    </div>
</body>
</html>