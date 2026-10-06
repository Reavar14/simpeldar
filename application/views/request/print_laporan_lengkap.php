<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Lengkap Permintaan Darah</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            margin: 5px;
        }
        
        h2 {
            text-align: center;
            margin-bottom: 3px;
            font-size: 14px;
        }
        
        h3 {
            text-align: center;
            margin-top: 2px;
            margin-bottom: 8px;
            font-size: 11px;
        }
        
        .filter-info {
            margin-bottom: 10px;
            padding: 8px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            font-size: 8px;
        }
        
        .filter-form {
            margin-bottom: 10px;
            padding: 8px;
            background-color: #e8f4f8;
            border: 1px solid #b8d4e8;
        }
        
        .filter-form table {
            width: 100%;
            font-size: 8px;
        }
        
        .filter-form td {
            padding: 3px;
        }
        
        .filter-form input[type="date"],
        .filter-form input[type="text"] {
            width: 180px;
            padding: 3px;
            font-size: 8px;
        }
        
        .filter-form button {
            padding: 3px 10px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 8px;
        }
        
        .filter-form button:hover {
            background-color: #0056b3;
        }
        
        .report-container {
            overflow-x: auto;
            margin-top: 5px;
        }
        
        table.report {
            border-collapse: collapse;
            font-size: 7px;
            min-width: 100%;
        }
        
        table.report th,
        table.report td {
            border: 1px solid #000;
            padding: 2px 3px;
            text-align: left;
        }
        
        table.report th {
            background-color: #d3d3d3;
            font-weight: bold;
            text-align: center;
            height: 20px;
            word-break: break-word;
        }
        
        table.report td {
            height: 18px;
            vertical-align: middle;
        }
        
        table.report td.num,
        table.report th.num {
            text-align: center;
            width: 25px;
        }
        
        table.report td.kantong {
            width: 35px;
        }
        
        table.report th.kantong {
            width: 35px;
        }
        
        .no-data {
            text-align: center;
            padding: 15px;
            color: #888;
        }
        
        .btn-print {
            margin: 5px 0;
            padding: 5px 15px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 11px;
        }
        
        .btn-print:hover {
            background-color: #218838;
        }
        
        .footer-text {
            margin-top: 10px;
            font-size: 7px;
            color: #666;
        }
        
        @media print {
            .filter-form,
            .filter-info,
            .btn-print {
                display: none;
            }
            
            body {
                margin: 0;
                font-size: 7px;
            }
            
            h2 { font-size: 12px; }
            h3 { font-size: 10px; }
            
            table.report {
                page-break-inside: auto;
            }
            
            table.report tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            @page {
                size: A3 landscape;
                margin: 8mm;
            }
        }
    </style>
</head>
<body>

<!-- Filter Form -->
<div class="filter-form">
    <form method="GET" action="<?php echo base_url('index.php/RequestController/print_laporan_lengkap'); ?>">
        <table>
            <tr>
                <td style="width: 100px;"><strong>Tanggal Awal:</strong></td>
                <td style="width: 250px;">
                    <input type="date" name="tgl_awal" value="<?php echo htmlspecialchars($filter['tgl_awal']); ?>" required>
                </td>
                <td style="width: 100px;"><strong>Tanggal Akhir:</strong></td>
                <td style="width: 250px;">
                    <input type="date" name="tgl_akhir" value="<?php echo htmlspecialchars($filter['tgl_akhir']); ?>" required>
                </td>
            </tr>
            <tr>
                <td><strong>Analis:</strong></td>
                <td>
                    <input type="text" name="ANALIS" value="<?php echo htmlspecialchars($filter['analis']); ?>" placeholder="ID Analis (kosongkan untuk semua)">
                </td>
                <td><strong>Data Entry:</strong></td>
                <td>
                    <input type="text" name="DATAENTRI" value="<?php echo htmlspecialchars($filter['data_entry']); ?>" placeholder="USRID (kosongkan untuk semua)">
                </td>
            </tr>
            <tr>
                <td><strong>Dokter Konsul:</strong></td>
                <td>
                    <input type="text" name="DOKTERKONSUL" value="<?php echo htmlspecialchars($filter['dokter_konsul']); ?>" placeholder="ID Dokter (kosongkan untuk semua)">
                </td>
                <td colspan="2">
                    <button type="submit">Tampilkan Laporan</button>
                </td>
            </tr>
        </table>
    </form>
</div>

<!-- Filter Info (printed header) -->
<div class="filter-info">
    <strong>Periode:</strong> <?php echo htmlspecialchars($filter['tgl_awal']); ?> s/d <?php echo htmlspecialchars($filter['tgl_akhir']); ?>
    <?php if (!empty($filter['analis'])): ?>
        | <strong>Analis:</strong> <?php echo htmlspecialchars($filter['analis']); ?>
    <?php endif; ?>
    <?php if (!empty($filter['data_entry'])): ?>
        | <strong>Data Entry:</strong> <?php echo htmlspecialchars($filter['data_entry']); ?>
    <?php endif; ?>
    <?php if (!empty($filter['dokter_konsul'])): ?>
        | <strong>Dokter:</strong> <?php echo htmlspecialchars($filter['dokter_konsul']); ?>
    <?php endif; ?>
    | <strong>Total Data:</strong> <?php echo count($laporan); ?>
</div>

<!-- Print Button -->
<button class="btn-print" onclick="window.print()">Cetak / Print</button>

<!-- Report Header -->
<h2>LAPORAN LENGKAP PERMINTAAN DARAH</h2>
<h3>PERIODE <?php echo htmlspecialchars($filter['tgl_awal']); ?> S/D <?php echo htmlspecialchars($filter['tgl_akhir']); ?></h3>

<!-- Report Table Container -->
<div class="report-container">
<table class="report">
    <thead>
        <tr>
            <th class="num">No.</th>
            <th class="kantong">No. RM</th>
            <th style="width: 70px;">Nama Pasien</th>
            <th class="kantong">JK</th>
            <th class="kantong">Tgl Lahir</th>
            <th class="kantong">No Permintaan</th>
            <th class="kantong">Tgl Minta</th>
            <th class="kantong">Gol Darah</th>
            <th class="kantong">Jenis Darah</th>
            <th class="kantong">Bufycoat</th>
            <th class="kantong">Volume</th>
            <th class="kantong">Tgl Diperlukan</th>
            <th style="width: 50px;">Diagnosa</th>
            <th style="width: 50px;">Alasan</th>
            <th class="kantong">Kadar Hb</th>
            <th class="kantong">Trombosit</th>
            <th class="kantong">Status</th>
            <th class="kantong">DE</th>
            <th class="kantong">Analis</th>
            <th class="kantong">Ruangan</th>
            <th style="width: 70px;">Dokter</th>
            <th style="width: 60px;">Riwayat Trans</th>
            <!-- Kantong 1-12 Headers -->
            <th class="kantong">K1</th>
            <th class="num">V1</th>
            <th class="num">E1</th>
            <th class="kantong">K2</th>
            <th class="num">V2</th>
            <th class="num">E2</th>
            <th class="kantong">K3</th>
            <th class="num">V3</th>
            <th class="num">E3</th>
            <th class="kantong">K4</th>
            <th class="num">V4</th>
            <th class="num">E4</th>
            <th class="kantong">K5</th>
            <th class="num">V5</th>
            <th class="num">E5</th>
            <th class="kantong">K6</th>
            <th class="num">V6</th>
            <th class="num">E6</th>
            <th class="kantong">K7</th>
            <th class="num">V7</th>
            <th class="num">E7</th>
            <th class="kantong">K8</th>
            <th class="num">V8</th>
            <th class="num">E8</th>
            <th class="kantong">K9</th>
            <th class="num">V9</th>
            <th class="num">E9</th>
            <th class="kantong">K10</th>
            <th class="num">V10</th>
            <th class="num">E10</th>
            <th class="kantong">K11</th>
            <th class="num">V11</th>
            <th class="num">E11</th>
            <th class="kantong">K12</th>
            <th class="num">V12</th>
            <th class="num">E12</th>
            <th class="kantong">Jam Analis</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($laporan) > 0): ?>
            <?php $no = 1; foreach ($laporan as $row): ?>
            <tr>
                <td class="num"><?php echo $no++; ?></td>
                <td class="kantong"><?php echo htmlspecialchars($row['mr']); ?></td>
                <td><?php echo htmlspecialchars($row['nama']); ?></td>
                <td class="kantong"><?php echo htmlspecialchars(substr($row['jenis_kelamin'], 0, 1)); ?></td>
                <td class="kantong"><?php echo htmlspecialchars($row['tgl_lahir']); ?></td>
                <td class="kantong"><?php echo htmlspecialchars($row['no_permintaan']); ?></td>
                <td class="kantong"><?php echo htmlspecialchars($row['tgl_minta']); ?></td>
                <td class="kantong"><?php echo htmlspecialchars($row['gol_darah']); ?></td>
                <td class="kantong"><?php echo htmlspecialchars($row['jenis_darah']); ?></td>
                <td class="kantong"><?php echo htmlspecialchars($row['bufycoat']); ?></td>
                <td class="kantong"><?php echo htmlspecialchars($row['volume']); ?></td>
                <td class="kantong"><?php echo htmlspecialchars($row['tgl_diperlukan']); ?></td>
                <td><?php echo htmlspecialchars($row['diagnosa']); ?></td>
                <td><?php echo htmlspecialchars($row['alasan']); ?></td>
                <td class="kantong"><?php echo htmlspecialchars($row['kadar_hb']); ?></td>
                <td class="kantong"><?php echo htmlspecialchars($row['trombosit']); ?></td>
                <td class="kantong"><?php echo htmlspecialchars($row['status_proses']); ?></td>
                <td class="kantong"><?php echo htmlspecialchars($row['DE']); ?></td>
                <td class="kantong"><?php echo htmlspecialchars($row['nama_analis']); ?></td>
                <td class="kantong"><?php echo htmlspecialchars($row['RUANGAN']); ?></td>
                <td><?php echo htmlspecialchars($row['nama_dokter']); ?></td>
                <td><?php echo htmlspecialchars($row['riwayattrans']); ?></td>
                <!-- Kantong 1-12 Data -->
                <?php for ($i = 1; $i <= 12; $i++): ?>
                <td class="kantong"><?php echo htmlspecialchars($row['no_kantong_' . $i]); ?></td>
                <td class="num"><?php echo htmlspecialchars($row['volume_' . $i]); ?></td>
                <td class="num"><?php echo htmlspecialchars($row['exp_' . $i]); ?></td>
                <?php endfor; ?>
                <td class="kantong"><?php echo htmlspecialchars($row['jam_analis']); ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="63" class="no-data">Tidak ada data untuk periode dan filter yang dipilih.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
</div>

<p class="footer-text">
    Dicetak pada: <?php echo date('d-m-Y H:i:s'); ?>
</p>

</body>
</html>

