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

if (!function_exists('fd_safe_date')) {
    function fd_safe_date($value) {
        $v = fd_norm($value);
        if ($v === '') {
            return '';
        }
        if (strpos($v, ' ') !== false) {
            $v = trim(substr($v, 0, strpos($v, ' ')));
        }
        if ($v === '' || preg_match('#^0{2,4}[-/]0{2}[-/]0{2,4}$#', $v)) {
            return '';
        }
        if (preg_match('#^(\d{1,2})[-/](\d{1,2})[-/](\d{4})$#', $v, $m)) {
            $d = (int)$m[1]; $mo = (int)$m[2]; $y = (int)$m[3];
            if (!checkdate($mo, $d, $y) || $y <= 1900 || $y >= 2999) {
                return '';
            }
            return sprintf('%02d-%02d-%04d', $d, $mo, $y);
        }
        if (preg_match('#^(\d{4})[-/](\d{1,2})[-/](\d{1,2})$#', $v, $m)) {
            $y = (int)$m[1]; $mo = (int)$m[2]; $d = (int)$m[3];
            if (!checkdate($mo, $d, $y) || $y <= 1900 || $y >= 2999) {
                return '';
            }
            return sprintf('%02d-%02d-%04d', $d, $mo, $y);
        }
        return '';
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
        @page { size: A4 portrait; margin: 20pt; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8pt;
            color: #000;
            margin: 0;
            padding: 0;
            width: 595pt;
            height: 842pt;
        }
        /* Page wrapper - exact JRXML page size (595pt x 842pt = A4) */
        .form-page {
            position: relative;
            width: 595pt;
            height: 842pt;
            overflow: hidden;
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        .title {
            position: absolute;
            top: 0; left: 0; right: 0;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            border-bottom: 1.5px solid #000;
            padding: 2px 0;
            margin: 0;
        }
        /* Labels: no border, bold, bottom-aligned, height 20pt */
        .lbl {
            position: absolute;
            font-weight: bold;
            font-size: 8pt;
            height: 20pt;
            line-height: 20pt;
            padding-left: 3px;
            vertical-align: bottom;
        }
        /* Fields: border 0.75pt, height 20pt, middle-aligned */
        .fld {
            position: absolute;
            border: 0.75px solid #000;
            font-size: 8pt;
            height: 20pt;
            line-height: 20pt;
            padding: 0 3px;
            vertical-align: middle;
            background: #fff;
        }
        /* Special field heights */
        .fld-tall { height: 50pt; line-height: 50pt; }
        .fld-tall-top { height: 50pt; line-height: 1.2; padding: 3px; vertical-align: top; white-space: pre-wrap; }
        /* Goldar big */
        .goldar-big {
            position: absolute;
            font-size: 36px;
            text-align: center;
            vertical-align: middle;
            border: 0.75px solid #000;
            border-top: 0;
            padding: 8px;
            line-height: 1;
        }
        /* Section header with borders (Histori/Golongan Darah) */
        .sec-hdr {
            position: absolute;
            font-weight: bold;
            font-size: 10px;
            height: 28pt;
            line-height: 28pt;
            padding-left: 3px;
            text-align: center;
            border: 0.75px solid #000;
            border-bottom: none;
            background: #fff;
        }
        /* Kantong table */
        .kantong-wrap {
            position: absolute;
            top: 468pt;
            left: 0;
            width: 555pt;
        }
        table.kantong {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        table.kantong th,
        table.kantong td {
            border: 0.75px solid #000;
            height: 20pt;
            font-size: 7.5pt;
            vertical-align: middle;
            padding: 0 2px;
        }
        table.kantong th {
            text-align: center;
            font-weight: bold;
            background: #cccccc;
            height: 26pt;
            font-size: 8pt;
            line-height: 8pt;
            padding: 0;
            vertical-align: middle;
        }
        table.kantong td { text-align: center; }
        table.kantong td.left { text-align: left; padding-left: 3px; }
        .col-no { width: 21px; }
        .col-nk { width: 117px; }
        .col-tgl { width: 75px; }
        .col-gol { width: 41px; }
        .col-vol { width: 40px; }
        .col-may { width: 40px; }
        .col-min { width: 41px; }
        .col-exp { width: 59px; }
        .col-ser { width: 60px; }
        .col-ter { width: 60px; }
        /* Riwayat cell */
        .riwayat-cell {
            border: 0.75px solid #000;
            border-top: 0;
            padding: 4px 6px;
            min-height: 50pt;
            white-space: pre-wrap;
            font-size: 9px;
            vertical-align: top;
        }
        .no-print { margin-bottom: 10px; }
        .btn-print {
            background: #337ab7;
            color: #fff;
            border: none;
            padding: 6px 14px;
            font-size: 12px;
            cursor: pointer;
            border-radius: 3px;
        }
        @media print {
            .no-print { display: none !important; }
            html, body { margin: 0; padding: 0; width: 595pt; height: 842pt; }
            @page { size: A4 portrait; margin: 0; }
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

    <div class="form-page">
        <div class="title">FORM DARAH</div>

        <?php if (!empty($d)) { ?>
        <?php
            $f = function ($k) use ($d) { return htmlspecialchars(fd_norm($d[$k] ?? null)); };
        ?>

        <!-- Labels Row 1: y=12pt -->
        <div class="lbl" style="top:12pt; left:0pt; width:279pt;">Nomor Permintaan</div>
        <div class="lbl" style="top:12pt; left:290pt; width:265pt;">Tanggal Permintaan</div>

        <!-- Fields Row 1: y=32pt -->
        <div class="fld" style="top:32pt; left:0pt; width:278pt;"><?php echo $f('no_permintaan'); ?></div>
        <div class="fld" style="top:32pt; left:290pt; width:264pt;"><?php echo $f('tgl_minta'); ?></div>

        <!-- Labels Row 2: y=52pt -->
        <div class="lbl" style="top:52pt; left:0pt; width:125pt;">Nomor MR</div>
        <div class="lbl" style="top:52pt; left:125pt; width:153pt;">Nama Pasien</div>
        <div class="lbl" style="top:52pt; left:290pt; width:110pt;">Jenis Kelamin</div>
        <div class="lbl" style="top:52pt; left:400pt; width:154pt;">Tanggal Lahir</div>

        <!-- Fields Row 2: y=72pt -->
        <div class="fld" style="top:72pt; left:0pt; width:125pt;"><?php echo $f('mr'); ?></div>
        <div class="fld" style="top:72pt; left:125pt; width:153pt;"><?php echo $f('nama'); ?></div>
        <div class="fld" style="top:72pt; left:290pt; width:110pt;"><?php echo $f('jenis_kelamin'); ?></div>
        <div class="fld" style="top:72pt; left:400pt; width:154pt;"><?php echo $f('tgl_lahir'); ?></div>

        <!-- Labels Row 3: y=92pt -->
        <div class="lbl" style="top:92pt; left:0pt; width:126pt;">Golongan Darah</div>
        <div class="lbl" style="top:92pt; left:290pt; width:110pt;">Jenis Darah</div>
        <div class="lbl" style="top:92pt; left:400pt; width:154pt;">Tipe Darah</div>

        <!-- Fields Row 3: y=112pt -->
        <div class="fld" style="top:112pt; left:0pt; width:125pt;"><?php echo $f('gol_darah'); ?></div>
        <div class="fld" style="top:112pt; left:290pt; width:110pt;"><?php echo $f('jenis_darah'); ?></div>
        <div class="fld" style="top:112pt; left:400pt; width:154pt;"><?php echo $f('bufycoat'); ?></div>

        <!-- Labels Row 4: y=132pt -->
        <div class="lbl" style="top:132pt; left:0pt; width:125pt;">Tanggal Diperlukan</div>
        <div class="lbl" style="top:132pt; left:136pt; width:142pt;">Tujuan</div>
        <div class="lbl" style="top:132pt; left:290pt; width:110pt;">Volume</div>

        <!-- Fields Row 4: y=152pt -->
        <div class="fld" style="top:152pt; left:0pt; width:125pt;"><?php echo $f('tgl_diperlukan'); ?></div>
        <div class="fld" style="top:152pt; left:290pt; width:110pt;"><?php echo $f('volume'); ?></div>

        <!-- Labels Row 5: y=173pt -->
        <div class="lbl" style="top:173pt; left:0pt; width:136pt;">Alasan</div>
        <div class="lbl" style="top:173pt; left:136pt; width:143pt;">Diagnosa Kanker</div>
        <div class="lbl" style="top:173pt; left:290pt; width:143pt;">Trombosit</div>
        <div class="lbl" style="top:173pt; left:433pt; width:121pt;">Kadar HB</div>

        <!-- Fields Row 5: y=193pt -->
        <div class="fld" style="top:193pt; left:0pt; width:137pt;"><?php echo $f('alasan'); ?></div>
        <div class="fld" style="top:193pt; left:136pt; width:142pt;"><?php echo $f('diagnosa'); ?></div>
        <div class="fld" style="top:193pt; left:290pt; width:143pt;"><?php echo $f('trombosit'); ?></div>
        <div class="fld" style="top:193pt; left:433pt; width:121pt;"><?php echo $f('kadar_hb'); ?></div>

        <!-- Labels Row 6: y=216pt -->
        <div class="lbl" style="top:216pt; left:0pt; width:279pt;">Dokter DPJP</div>
        <div class="lbl" style="top:216pt; left:290pt; width:265pt;">Status</div>

        <!-- Fields Row 6: y=236pt (only nama_dokter has field at this y) -->
        <div class="fld" style="top:236pt; left:0pt; width:279pt;"><?php echo $f('nama_dokter'); ?></div>

        <!-- Labels Row 7: y=256pt -->
        <div class="lbl" style="top:256pt; left:0pt; width:279pt;">Petugas Pengambil Contoh Darah</div>
        <div class="lbl" style="top:256pt; left:290pt; width:265pt;">Ruangan Rawat</div>

        <!-- Fields Row 7: y=276pt -->
        <div class="fld" style="top:276pt; left:0pt; width:279pt;"><?php echo $f('pengambildarah'); ?></div>
        <div class="fld" style="top:276pt; left:290pt; width:265pt;"><?php echo $f('RUANGAN'); ?></div>

        <!-- Labels Row 8: y=296pt -->
        <div class="lbl" style="top:296pt; left:0pt; width:279pt;">Nama Analis</div>
        <div class="lbl" style="top:296pt; left:290pt; width:143pt;">Hasil Pemeriksaan</div>
        <div class="lbl" style="top:296pt; left:433pt; width:122pt;">Perawat</div>

        <!-- Fields Row 8: y=316pt -->
        <div class="fld" style="top:316pt; left:0pt; width:279pt;"><?php echo $f('nama_analis'); ?></div>
        <div class="fld" style="top:316pt; left:290pt; width:143pt;"><?php echo $f('hasil'); ?></div>
        <div class="fld" style="top:316pt; left:433pt; width:122pt;"><?php echo $f('nama_perawat'); ?></div>

        <!-- Section Header Row 9: y=348pt (height 28pt) -->
        <div class="sec-hdr" style="top:348pt; left:0pt; width:418pt; border-left:0.75px solid #000; border-top:0.75px solid #000; border-right:0.75px solid #000;">Histori Riwayat Alergi Transfusi dan Catatan</div>
        <div class="sec-hdr" style="top:348pt; left:418pt; width:137pt; border-top:0.75px solid #000; border-right:0.75px solid #000;">Golongan Darah:</div>

        <!-- Fields Row 9: y=376pt (height 50pt) -->
        <div class="fld fld-tall-top" style="top:376pt; left:0pt; width:418pt; border-left:0.75px solid #000; border-bottom:0.75px solid #000; border-right:0.75px solid #000;"><?php echo $f('RIWAYAT'); ?></div>
        <div class="goldar-big" style="top:376pt; left:418pt; width:137pt; height:50pt; border-left:0.75px solid #000; border-bottom:0.75px solid #000; border-right:0.75px solid #000;"><?php echo $f('gol_darah'); ?></div>

        <!-- Row 10: y=432pt -->
        <div class="lbl" style="top:432pt; left:0pt; width:99pt;">Hasil Pemeriksaan :</div>
        <div class="fld" style="top:432pt; left:99pt; width:115pt; border:none;"><?php echo $f('nama_analis2'); ?></div>

        <!-- Kantong Table: starts at y=460pt -->
        <div class="kantong-wrap">
            <table class="kantong">
                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th class="col-nk">Nomor Kantong</th>
                        <th class="col-tgl">Tanggal Input Kantong</th>
                        <th class="col-gol">Gol. Darah Kantong</th>
                        <th class="col-vol">Volume</th>
                        <th class="col-may">Mayor</th>
                        <th class="col-min">Minor</th>
                        <th class="col-exp">EXP Date</th>
                        <th class="col-ser">Nama, Tanggal, Jam, Yg memberi</th>
                        <th class="col-ter">Nama, Alamat, Telp/Hp Penerima</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 1; $i <= 12; $i++) { ?>
                        <?php
                            $nk  = fd_norm($d['no_kantong_' . $i] ?? null);
                            $tgl = fd_safe_date($d['tglkantong' . $i] ?? null);
                            $vol = fd_norm($d['volume_' . $i] ?? null);
                            $may = fd_norm($d['mayor_' . $i] ?? null);
                            $min = fd_norm($d['minor_' . $i] ?? null);
                            $exp = fd_safe_date($d['exp_' . $i] ?? null);

                            // Fallback chain: kantong-specific → patient goldarah → empty
                            $gd = fd_norm($d['PRO_DESKDAR_' . $i] ?? null);
                            if ($gd === '') {
                                $gd = fd_norm($d['gol_darah'] ?? null);
                            }

                            if ($nk === '') {
                                $tgl = ''; $gd = ''; $vol = ''; $may = ''; $min = ''; $exp = '';
                            }

                            $serah_col  = ($i === 1) ? 'PETUGAS_SERAH'  : 'PETUGAS_SERAH_' . $i;
                            $terima_col = ($i === 1) ? 'PETUGAS_TERIMA' : 'PETUGAS_TERIMA_' . $i;
                            $serah  = fd_norm($d[$serah_col] ?? null);
                            $terima = fd_norm($d[$terima_col] ?? null);
                        ?>
                        <tr>
                            <td class="col-no"><?php echo $i; ?></td>
                            <td class="col-nk left"><?php echo htmlspecialchars($nk); ?></td>
                            <td class="col-tgl"><?php echo htmlspecialchars($tgl); ?></td>
                            <td class="col-gol"><?php echo htmlspecialchars($gd); ?></td>
                            <td class="col-vol"><?php echo htmlspecialchars($vol); ?></td>
                            <td class="col-may"><?php echo htmlspecialchars($may); ?></td>
                            <td class="col-min"><?php echo htmlspecialchars($min); ?></td>
                            <td class="col-exp"><?php echo htmlspecialchars($exp); ?></td>
                            <td class="col-ser"><?php echo htmlspecialchars($serah); ?></td>
                            <td class="col-ter"><?php echo htmlspecialchars($terima); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <?php } else { ?>
        <p>Data permintaan darah tidak ditemukan.</p>
        <?php } ?>
        </div>
    </div>
</body>
</html>