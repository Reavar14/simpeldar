$(function () {
    'use strict';

    var baseUrl = window.location.origin + '/index.php/';

    function getFilterValues() {
        return {
            tgl_awal: $('#cetakanTglAwal').val() || '',
            tgl_akhir: $('#cetakanTglAkhir').val() || '',
            analis: $('#cetakanAnalis').val() || '',
            data_entry: $('#cetakanDataEntry').val() || '',
            dokter_konsul: $('#cetakanDokter').val() || '',
            statusp: $('#cetakanStatus').val() || ''
        };
    }

    function submitFilter() {
        var params = new URLSearchParams();
        var vals = getFilterValues();
        Object.keys(vals).forEach(function (key) {
            if (vals[key]) params.append(key, vals[key]);
        });
        var qs = params.toString();
        window.location.href = baseUrl + 'cetakan' + (qs ? '?' + qs : '');
    }

    $('#cetakanAnalis, #cetakanDataEntry, #cetakanDokter, #cetakanStatus').on('change', submitFilter);

    $('#btnPrintLaporanLengkap').on('click', function (e) {
        e.preventDefault();
        var vals = getFilterValues();
        if (!vals.tgl_awal || !vals.tgl_akhir) {
            SwalHelper.error('Gagal', 'Tanggal awal dan akhir wajib diisi.');
            return;
        }
        var qs = '?tgl_awal=' + vals.tgl_awal + '&tgl_akhir=' + vals.tgl_akhir +
            (vals.analis ? '&ANALIS=' + vals.analis : '') +
            (vals.data_entry ? '&DATAENTRI=' + vals.data_entry : '') +
            (vals.dokter_konsul ? '&DOKTERKONSUL=' + vals.dokter_konsul : '') +
            (vals.statusp ? '&STATUSP=' + vals.statusp : '');
        window.open(baseUrl + 'RequestController/print_laporan_lengkap' + qs, '_blank');
    });

    $('#btnPrintRekapAnalis').on('click', function (e) {
        e.preventDefault();
        var vals = getFilterValues();
        if (!vals.tgl_awal || !vals.tgl_akhir) {
            SwalHelper.error('Gagal', 'Tanggal awal dan akhir wajib diisi.');
            return;
        }
        var qs = '?tgl_awal_rekap=' + vals.tgl_awal + '&tgl_akhir_rekap=' + vals.tgl_akhir;
        window.open(baseUrl + 'RequestController/print_rekap_analis' + qs, '_blank');
    });
});