<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekap Petugas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 5px;
        }
        
        .header h3 {
            margin: 3px 0;
            font-size: 14px;
        }
        
        .header p {
            margin: 2px 0;
            font-size: 11px;
        }
        
        h2 {
            text-align: center;
            margin: 15px 0 5px 0;
            font-size: 14px;
        }
        
        .periode {
            margin: 10px 0 15px 0;
            font-size: 11px;
        }
        
        .filter-form {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #e8f4f8;
            border: 1px solid #b8d4e8;
        }
        
        .filter-form table {
            width: 100%;
        }
        
        .filter-form td {
            padding: 5px;
        }
        
        .filter-form input[type="date"] {
            width: 200px;
            padding: 5px;
        }
        
        .filter-form button {
            padding: 5px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        
        .filter-form button:hover {
            background-color: #0056b3;
        }
        
        table.report {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        table.report th,
        table.report td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
        
        table.report th {
            background-color: #e0e0e0;
            font-weight: bold;
        }
        
        table.report td.left {
            text-align: left;
            padding-left: 10px;
        }
        
        .no-data {
            text-align: center;
            padding: 20px;
            color: #888;
        }
        
        .btn-print {
            margin: 10px 0;
            padding: 8px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn-print:hover {
            background-color: #218838;
        }
        
        @media print {
            .filter-form,
            .btn-print {
                display: none;
            }
            
            body {
                margin: 10mm;
            }
            
            table.report {
                page-break-inside: auto;
            }
            
            table.report tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            @page {
                size: A4 portrait;
                margin: 15mm;
            }
        }
    </style>
</head>
<body>

<!-- Filter Form -->
<div class="filter-form">
    <form method="GET" action="<?php echo base_url('index.php/RequestController/print_rekap_analis'); ?>">
        <table>
            <tr>
                <td style="width: 150px;"><strong>Tanggal Awal:</strong></td>
                <td>
                    <input type="date" name="tgl_awal_rekap" value="<?php echo htmlspecialchars($filter['tgl_awal']); ?>" required>
                </td>
            </tr>
            <tr>
                <td><strong>Tanggal Akhir:</strong></td>
                <td>
                    <input type="date" name="tgl_akhir_rekap" value="<?php echo htmlspecialchars($filter['tgl_akhir']); ?>" required>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <button type="submit">Tampilkan Laporan</button>
                </td>
            </tr>
        </table>
    </form>
</div>

<!-- Print Button -->
<button class="btn-print" onclick="window.print()">Cetak / Print</button>

<!-- Report Header -->
<div class="header">
    <h3>RUMAH SAKIT KANKER DHARMAIS</h3>
    <p>Jl. Letjen S. Parman Kav. 84-86, Slipi, Jakarta Barat 11420</p>
    <p>Telp: (021) 5681570 | Fax: (021) 5681579</p>
</div>

<!-- Report Title -->
<h2>LAPORAN JUMLAH REKAP PETUGAS</h2>

<!-- Periode -->
<div class="periode">
    <strong>Periode :</strong> 
    <?php 
        echo date('d/m/Y', strtotime($filter['tgl_awal'])); 
        echo ' s/d '; 
        echo date('d/m/Y', strtotime($filter['tgl_akhir'])); 
    ?>
</div>

<!-- Report Table -->
<table class="report">
    <thead>
        <tr>
            <th style="width: 50px;">NO</th>
            <th>NAMA ANALIS</th>
            <th style="width: 150px;">JUMLAH PETUGAS 1</th>
            <th style="width: 150px;">JUMLAH PETUGAS 2</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($rekap) > 0): ?>
            <?php $no = 1; foreach ($rekap as $row): ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td class="left"><?php echo htmlspecialchars($row['nama_analis']); ?></td>
                <td><?php echo htmlspecialchars($row['JUMLAH1']); ?></td>
                <td><?php echo htmlspecialchars($row['JUMLAH2']); ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" class="no-data">Tidak ada data untuk periode yang dipilih.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<p style="margin-top: 20px; font-size: 10px; color: #666;">
    Dicetak pada: <?php echo date('d-m-Y H:i:s'); ?>
</p>

</body>
</html>
