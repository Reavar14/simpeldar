<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Request_model extends CI_Model {

    // Kolom yang bisa di-sort & search (mapping index DataTable -> nama kolom)
    private $column_order = array(
        'no_permintaan',
        'mr',
        'nama',
        'tgl_minta',
        'ruangan',
        'alasan',
        'tujuan',
        'goldar',
        'tgl_diperlukan',
        'jenis_darah',
        'status_proses',
        'kelengkapan_status',
        'status_terima'
    );

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Subquery dasar dari isi_view.php (native)
     * Dipertahankan: kolom output, filter native
     * Diperbaiki: ganti LEFT JOIN lookup tables dengan scalar subqueries
     * untuk menghindari MySQL 8.0.30 optimizer bug (plan explosion)
     * dan hapus filter status != 0 agar status 0 (Permintaan) ikut tampil
     */
    private function _get_base_query()
    {
        $sql = "SELECT 
            pd.no_permintaan,
            pd.mr,
            pd.nama,
            (SELECT rjk.DESKRIPSI FROM darah.referensi rjk WHERE rjk.ID = pd.id_jenis_kelamin AND rjk.JENIS = 2) AS jenis_kelamin,
            (SELECT rgd.DESKRIPSI FROM darah.referensi rgd WHERE rgd.ID = pd.goldarah AND rgd.JENIS = 1) AS goldar,

            DATE_FORMAT(pd.tgl_lahir,'%d-%m-%Y') AS tgl_lahir,
            DATE_FORMAT(pd.tgl_minta,'%d-%m-%Y') AS tgl_minta,
            DATE_FORMAT(pd.tgl_diperlukan,'%d-%m-%Y') AS tgl_diperlukan,

            pd.alasan,
            (SELECT NULLIF(tuj.variabel, 'Tidak Ditentukan') FROM darah.variabel tuj WHERE tuj.id_variabel = pd.tujuan AND tuj.id_referensi = 1948) AS tujuan,
            (SELECT rstatus.DESKRIPSI FROM darah.referensi rstatus WHERE rstatus.ID = pd.status AND rstatus.JENIS = 3) AS status_proses,
            (SELECT r.DESKRIPSI FROM darah.ruangan r WHERE r.ID = pd.ruangan AND r.JENIS = 5) AS ruangan,
            pd.trombosit, pd.kadar_hb,
            CONCAT(
                (SELECT jd.nama_jenis FROM darah.jenis_darah jd WHERE jd.ID = pd.jenis_darah AND jd.JENIS = 1),
                '<br> Trombosit: ', pd.trombosit,
                '<br> Kadar HB: ', pd.kadar_hb
            ) AS jenis_darah,

            CASE 
                WHEN k.kelengkapan = 1 THEN 'Sudah lengkap'
                ELSE 'Tidak lengkap'
            END AS kelengkapan_status,

            IFNULL(st.status,0) AS status_terima

            FROM darah.pesan_darah pd

            LEFT JOIN darah.kelengkapan k 
                ON pd.no_permintaan = k.NO_PERMINTAAN

            LEFT JOIN darah.status_terima st 
                ON pd.no_permintaan = st.no_permintaan

            WHERE 
            (
                pd.tgl_minta BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 MONTH) AND DATE_ADD(CURDATE(), INTERVAL 1 MONTH)
                OR pd.tgl_diperlukan BETWEEN DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
            )";

        return $sql;
    }

    /**
     * Ambil data DataTables: search, order, pagination
     */
    public function get_datatables($search = '', $order = array(), $start = 0, $length = 10)
    {
        $base = $this->_get_base_query();

        // Bungkus subquery agar kolom computed bisa di-search/order
        $this->db->from("($base) temp");

        // Search global
        if (!empty($search)) {
            $this->db->group_start();
            foreach ($this->column_order as $col) {
                $this->db->or_like($col, $search);
            }
            $this->db->group_end();
        }

        // Order
        if (!empty($order)) {
            $col_index = $order[0]['column'];
            $dir = (strtoupper($order[0]['dir']) === 'ASC') ? 'ASC' : 'DESC';
            if (isset($this->column_order[$col_index])) {
                $this->db->order_by($this->column_order[$col_index], $dir);
            }
        } else {
            $this->db->order_by('no_permintaan', 'DESC');
        }

        // Pagination
        if ($length != -1) {
            $this->db->limit($length, $start);
        }

        return $this->db->get()->result_array();
    }

    /**
     * Count data setelah filter search
     */
    public function count_filtered($search = '')
    {
        $base = $this->_get_base_query();
        $this->db->from("($base) temp");

        if (!empty($search)) {
            $this->db->group_start();
            foreach ($this->column_order as $col) {
                $this->db->or_like($col, $search);
            }
            $this->db->group_end();
        }

        return $this->db->count_all_results();
    }

    /**
     * Count total data tanpa filter
     */
    public function count_all()
    {
        $base = $this->_get_base_query();
        $this->db->from("($base) temp");
        return $this->db->count_all_results();
    }

    /**
     * Ambil detail satu permintaan darah
     * Migrasi query dari admin/detail_view.php (native)
     * Dipertahankan: semua JOIN, kolom output, referensi
     * (dokter, ruangan, jenis darah, status, kantong)
     */
    public function get_detail($no_permintaan)
    {
        $sql = "SELECT pd.no_permintaan,
            DATE_FORMAT(pd.tgl_minta, '%d-%m-%Y %H:%i:%s') tgl_minta,
            pd.mr, pd.nama, rjk.DESKRIPSI jenis_kelamin, jd.nama_jenis jenis_darah, j.nama_jenis bufycoat,
            pd.volume, DATE_FORMAT(pd.tgl_diperlukan, '%d-%m-%Y') tgl_diperlukan, pd.diagnosa, pd.alasan,
            pd.kadar_hb, pd.trombosit, YEAR(CURDATE())-YEAR(pd.tgl_lahir) usia,
            rgd.DESKRIPSI gol_darah, rstatus.DESKRIPSI status_proses, pd.`status`, a.nama_analis,
            pd.tgl_lahir, us.NAMA,
            CONCAT(IF(p.GELAR_DEPAN='' OR p.GELAR_DEPAN IS NULL,'',CONCAT(p.GELAR_DEPAN,'. ')),UPPER(p.NAMA),IF(p.GELAR_BELAKANG='' OR p.GELAR_BELAKANG IS NULL,'',CONCAT(', ',p.GELAR_BELAKANG))) nama_dokter,
            r.DESKRIPSI RUANGAN, pd.pengambil_darah pengambildarah, rhp.DESKRIPSI hasil, pd.riwayattrans riwayat_transfusi,
            pd.no_kantong_1, pd.no_kantong_2, pd.no_kantong_3, pd.no_kantong_4, pd.no_kantong_5, pd.no_kantong_6,
            pd.no_kantong_7, pd.no_kantong_8, pd.no_kantong_9, pd.no_kantong_10, pd.no_kantong_11, pd.no_kantong_12,
            st.status AS status_terima, pd.status AS status_pesan,
            DATE_FORMAT(st.tanggal_terima, '%d-%m-%Y %H:%i:%s') tanggal_terima, pterima.NAMA petugas_terima,
            NULLIF(tuj.variabel, 'Tidak Ditentukan') AS TUJUAN
            FROM darah.pesan_darah pd
            LEFT JOIN darah.referensi rgd ON pd.goldarah=rgd.ID AND rgd.JENIS=1
            LEFT JOIN darah.referensi rstatus ON pd.`status`=rstatus.ID AND rstatus.JENIS=3
            LEFT JOIN darah.referensi rjk ON pd.id_jenis_kelamin=rjk.ID AND rjk.JENIS=2
            LEFT JOIN darah.referensi rhp ON pd.hasil_pemeriksaan=rhp.ID AND rhp.JENIS=4
            LEFT JOIN darah.dokter d ON pd.dpjp=d.ID
            LEFT JOIN darah.pegawai p ON p.NIP=d.NIP
            LEFT JOIN darah.analis a ON a.id_analis=pd.analis
            LEFT JOIN darah.jenis_darah jd ON jd.id=pd.jenis_darah AND jd.jenis=1
            LEFT JOIN darah.jenis_darah j ON j.id=pd.buffycoat AND j.jenis=2
            LEFT JOIN darah.usrmst us ON us.USRID=pd.nm_creat
            LEFT JOIN darah.ruangan r ON r.ID=pd.ruangan AND r.JENIS=5
            LEFT JOIN darah.status_terima st ON pd.no_permintaan = st.no_permintaan AND st.status=1
            LEFT JOIN darah.usrmst pterima ON pterima.USRID = st.oleh
            LEFT JOIN darah.variabel tuj ON tuj.id_variabel = pd.tujuan AND tuj.id_referensi=1948
            WHERE pd.no_permintaan = ?";

        return $this->db->query($sql, array($no_permintaan))->row_array();
    }

    /**
     * Ambil data laporan Hasil Pemeriksaan untuk satu permintaan darah
     * Migrasi query report dari admin/hasilpemeriksaan.jrxml (native)
     *
     * Database: localhost/darah (tidak ada koneksi server remote).
     * Dipertahankan: seluruh JOIN native (referensi goldar/status/jenis
     * kelamin/hasil, dokter, pegawai, analis, jenis_darah, usrmst, ruangan),
     * kolom no_kantong_1..12, volume_1..12, exp_1..12, jam_analis.
     */
    public function get_hasil_pemeriksaan($no_permintaan)
    {
        $kantong_fields = array();
        for ($i = 1; $i <= 12; $i++) {
            $kantong_fields[] = "pd.no_kantong_{$i}";
            $kantong_fields[] = "pd.volume_{$i}";
            $kantong_fields[] = "pd.exp_{$i}";
        }

        $sql = "SELECT pd.no_permintaan,
            DATE_FORMAT(pd.tgl_minta, '%d-%m-%Y') tgl_minta,
            pd.mr, pd.nama, rjk.DESKRIPSI jenis_kelamin, jd.nama_jenis jenis_darah, j.nama_jenis bufycoat,
            pd.volume, DATE_FORMAT(pd.tgl_diperlukan, '%d-%m-%Y') tgl_diperlukan, pd.diagnosa, pd.alasan,
            pd.kadar_hb, pd.trombosit, YEAR(CURDATE())-YEAR(pd.tgl_lahir) usia,
            rgd.DESKRIPSI gol_darah, rstatus.DESKRIPSI status_proses, pd.`status`, a.nama_analis,
            DATE_FORMAT(pd.tgl_lahir, '%d-%m-%Y') tgl_lahir, us.NAMA DE,
            CONCAT(IF(p.GELAR_DEPAN='' OR p.GELAR_DEPAN IS NULL,'',CONCAT(p.GELAR_DEPAN,'. ')),UPPER(p.NAMA),IF(p.GELAR_BELAKANG='' OR p.GELAR_BELAKANG IS NULL,'',CONCAT(', ',p.GELAR_BELAKANG))) nama_dokter,
            r.DESKRIPSI RUANGAN, pd.pengambil_darah pengambildarah, rhp.DESKRIPSI hasil,
            " . implode(",\n            ", $kantong_fields) . ",
            pd.jam_analis
            FROM darah.pesan_darah pd
            LEFT JOIN darah.referensi rgd ON pd.goldarah=rgd.ID AND rgd.JENIS=1
            LEFT JOIN darah.referensi rstatus ON pd.`status`=rstatus.ID AND rstatus.JENIS=3
            LEFT JOIN darah.referensi rjk ON pd.id_jenis_kelamin=rjk.ID AND rjk.JENIS=2
            LEFT JOIN darah.referensi rhp ON pd.hasil_pemeriksaan=rhp.ID AND rhp.JENIS=4
            LEFT JOIN darah.dokter d ON pd.dpjp=d.ID
            LEFT JOIN darah.pegawai p ON p.NIP=d.NIP
            LEFT JOIN darah.analis a ON a.id_analis=pd.analis
            LEFT JOIN darah.jenis_darah jd ON jd.id=pd.jenis_darah AND jd.jenis=1
            LEFT JOIN darah.jenis_darah j ON j.id=pd.buffycoat AND j.jenis=2
            LEFT JOIN darah.usrmst us ON us.USRID=pd.nm_creat
            LEFT JOIN darah.ruangan r ON r.ID=pd.ruangan AND r.JENIS=5 AND r.JENIS_KUNJUNGAN IN(1,2,3,4,5,6,13,14)
            WHERE pd.no_permintaan = ?";

        return $this->db->query($sql, array($no_permintaan))->row_array();
    }

    /**
     * Ambil data Bon Permintaan Darah
     * Migrasi query dari admin/bonminta.jrxml (native)
     *
     * Database: localhost/darah. Semua tabel remote native
     * (master.ruangan, db_master.variabel) diganti dengan schema
     * `darah` (darah.ruangan, darah.variabel) yang tersedia lokal.
     *
     * Ditambahkan dibanding native: nama_dokter & status_proses
     * (sesuai kebutuhan tampilan bon CI3).
     */
    public function get_bon($no_permintaan)
    {
        $sql = "SELECT pd.no_permintaan,
            DATE_FORMAT(pd.tgl_minta, '%d-%m-%Y %H:%i:%s') tgl_minta,
            IF(r.DESKRIPSI='Laki-laki','L','P') kelamin,
            pd.mr, pd.nama,
            DATE_FORMAT(pd.tgl_diperlukan, '%d-%m-%Y') tgl_diperlukan,
            jd.nama_jenis, pd.volume, pd.goldarah, rgd.DESKRIPSI gol_darah,
            a.nama_analis, u.NAMA NAMA_DE,
            TIMESTAMPDIFF(YEAR, pd.tgl_lahir, CURDATE()) umur,
            DATE_FORMAT(st.tanggal_terima, '%d-%m-%Y %H:%i:%s') tanggal_terima,
            ru.DESKRIPSI ruangan, pterima.NAMA PETUGAS_TERIMA, uc.NAMA NAMA_CREATE,
            pd.alasan, NULLIF(tuj.variabel, 'Tidak Ditentukan') TUJUAN,
            rstatus.DESKRIPSI status_proses,
            CONCAT(IF(p.GELAR_DEPAN='' OR p.GELAR_DEPAN IS NULL,'',CONCAT(p.GELAR_DEPAN,'. ')),UPPER(p.NAMA),IF(p.GELAR_BELAKANG='' OR p.GELAR_BELAKANG IS NULL,'',CONCAT(', ',p.GELAR_BELAKANG))) nama_dokter
            FROM darah.pesan_darah pd
            LEFT JOIN darah.referensi r ON r.ID=pd.id_jenis_kelamin AND r.JENIS=2
            LEFT JOIN darah.referensi rgd ON pd.goldarah=rgd.ID AND rgd.JENIS=1
            LEFT JOIN darah.referensi rstatus ON pd.`status`=rstatus.ID AND rstatus.JENIS=3
            LEFT JOIN darah.analis a ON a.id_analis=pd.analis
            LEFT JOIN darah.jenis_darah jd ON jd.id=pd.jenis_darah AND jd.jenis=1
            LEFT JOIN darah.usrmst u ON u.USRID=pd.nm_edit
            LEFT JOIN darah.ruangan ru ON pd.ruangan=ru.ID AND ru.JENIS=5
            LEFT JOIN darah.status_terima st ON st.no_permintaan=pd.no_permintaan AND st.status=1
            LEFT JOIN darah.usrmst pterima ON pterima.USRID=st.oleh
            LEFT JOIN darah.usrmst uc ON uc.USRID=pd.nm_creat
            LEFT JOIN darah.variabel tuj ON tuj.id_variabel=pd.tujuan AND tuj.id_referensi=1948
            LEFT JOIN darah.dokter d ON pd.dpjp=d.ID
            LEFT JOIN darah.pegawai p ON p.NIP=d.NIP
            WHERE pd.no_permintaan = ?";

        return $this->db->query($sql, array($no_permintaan))->row_array();
    }

    /**
     * Ambil data Form Darah lengkap (12 kantong) untuk laporan cetak
     * Migrasi query dari admin/FormDarah.jrxml (native)
     *
     * Database: localhost/darah. Semua tabel remote native
     * (db_darah.tb_prolis, db_darah.periksa_hb_bridging,
     * db_darah.tb_produksi, db_darah.aftap, db_darah.tb_pooled_detil,
     * db_master.variabel, master.dokter) TIDAK digunakan.
     *
     * Sumber lokal pengganti:
     *   - darah.pesan_darah      : data pasien, klinis, no_kantong/volume/exp
     *   - darah.kantong_luar     : GOLDAR_KL1..12 (gol darah per kantong)
     *   - darah.petugas_serah_terima : data petugas
     *   - darah.referensi        : status, goldar, jenis kelamin,
     *                               mayor (JENIS=5), minor (JENIS=6),
     *                               auto kontrol (JENIS=7)
     *
     * EXP date diambil dari pd.exp_1..12 (native menghitung dinamis dari
     * tabel produksi remote; lokal tidak tersedia).
     */
    public function get_form_darah($no_permintaan)
    {
        $kantong_fields = array();
        for ($i = 1; $i <= 12; $i++) {
            $kantong_fields[] = "pd.no_kantong_{$i}";
            $kantong_fields[] = "pd.volume_{$i}";
            $kantong_fields[] = "pd.exp_{$i}";
            $kantong_fields[] = "pd.tglkantong{$i}";
            $kantong_fields[] = "pd.myr_{$i}";
            $kantong_fields[] = "pd.mnr_{$i}";
            $kantong_fields[] = "kl.GOLDAR_KL{$i}";
            $kantong_fields[] = "kl.NOMOR_KL{$i}";
        }

        $case_pro_deskdar = array();
        for ($i = 1; $i <= 12; $i++) {
            $case_pro_deskdar[] = "CASE WHEN CHAR_LENGTH(TRIM(IFNULL(pd.no_kantong_{$i}, ''))) = 11 AND kl.NOMOR_KL{$i} IS NOT NULL AND TRIM(kl.NOMOR_KL{$i}) <> '' THEN kl.GOLDAR_KL{$i} ELSE '' END AS PRO_DESKDAR_{$i}";
        }

        $sql = "SELECT pd.no_permintaan,
            DATE_FORMAT(pd.tgl_minta, '%d-%m-%Y %H:%i:%s') tgl_minta,
            pd.mr, pd.nama, rjk.DESKRIPSI jenis_kelamin,
            DATE_FORMAT(pd.tgl_lahir, '%d-%m-%Y') tgl_lahir,
            YEAR(CURDATE())-YEAR(pd.tgl_lahir) usia,
            rgd.DESKRIPSI gol_darah,
            jd.nama_jenis jenis_darah, j.nama_jenis bufycoat,
            pd.volume,
            DATE_FORMAT(pd.tgl_diperlukan, '%d-%m-%Y') tgl_diperlukan,
            pd.diagnosa, pd.alasan, pd.kadar_hb, pd.trombosit,
            rstatus.DESKRIPSI status_proses,
            rhp.DESKRIPSI hasil,
            a.nama_analis, a2.nama_analis AS nama_analis2,
            dp.nama_perawat,
            CONCAT(IF(p.GELAR_DEPAN='' OR p.GELAR_DEPAN IS NULL,'',CONCAT(p.GELAR_DEPAN,'. ')),UPPER(p.NAMA),IF(p.GELAR_BELAKANG='' OR p.GELAR_BELAKANG IS NULL,'',CONCAT(', ',p.GELAR_BELAKANG))) nama_dokter,
            r.DESKRIPSI RUANGAN, pd.pengambil_darah pengambildarah,
            NULLIF(tuj.variabel, 'Tidak Ditentukan') TUJUAN,
            us.NAMA DE, ue.NAMA NAMA_EDIT,
            pd.auto_kontrol,
            (SELECT rak.DESKRIPSI FROM darah.referensi rak WHERE rak.ID=pd.auto_kontrol AND rak.JENIS=7) autkon,
            DATE_FORMAT(pd.jam_analis, '%d-%m-%Y %H:%i:%s') jam_analis,
            (SELECT GROUP_CONCAT(CASE WHEN riw.riwayattrans IS NULL OR TRIM(riw.riwayattrans)='' THEN NULL ELSE CONCAT('- ', TRIM(riw.riwayattrans)) END ORDER BY riw.tgl_creat DESC, riw.no_permintaan DESC SEPARATOR '\n')
                FROM darah.pesan_darah riw WHERE riw.mr = pd.mr) RIWAYAT,
            " . implode(",\n            ", $kantong_fields) . ",
            " . implode(",\n            ", $case_pro_deskdar) . ",
            pts.PETUGAS_SERAH, pts.PETUGAS_TERIMA, pts.TGL_SERAH,
            pts.PETUGAS_SERAH_2, pts.PETUGAS_TERIMA_2, pts.TGL_SERAH2,
            pts.PETUGAS_SERAH_3, pts.PETUGAS_TERIMA_3, pts.TGL_SERAH3,
            pts.PETUGAS_SERAH_4, pts.PETUGAS_TERIMA_4, pts.TGL_SERAH4,
            pts.PETUGAS_SERAH_5, pts.PETUGAS_TERIMA_5, pts.TGL_SERAH5,
            pts.PETUGAS_SERAH_6, pts.PETUGAS_TERIMA_6, pts.TGL_SERAH6,
            pts.PETUGAS_SERAH_7, pts.PETUGAS_TERIMA_7, pts.TGL_SERAH7,
            pts.PETUGAS_SERAH_8, pts.PETUGAS_TERIMA_8, pts.TGL_SERAH8,
            pts.PETUGAS_SERAH_9, pts.PETUGAS_TERIMA_9, pts.TGL_SERAH9,
            pts.PETUGAS_SERAH_10, pts.PETUGAS_TERIMA_10, pts.TGL_SERAH10,
            pts.PETUGAS_SERAH_11, pts.PETUGAS_TERIMA_11, pts.TGL_SERAH11,
            pts.PETUGAS_SERAH_12, pts.PETUGAS_TERIMA_12, pts.TGL_SERAH12
            FROM darah.pesan_darah pd
            LEFT JOIN darah.referensi rgd ON pd.goldarah=rgd.ID AND rgd.JENIS=1
            LEFT JOIN darah.referensi rstatus ON pd.`status`=rstatus.ID AND rstatus.JENIS=3
            LEFT JOIN darah.referensi rjk ON pd.id_jenis_kelamin=rjk.ID AND rjk.JENIS=2
            LEFT JOIN darah.referensi rhp ON pd.hasil_pemeriksaan=rhp.ID AND rhp.JENIS=4
            LEFT JOIN darah.dokter d ON pd.dpjp=d.ID
            LEFT JOIN darah.pegawai p ON p.NIP=d.NIP
            LEFT JOIN darah.analis a ON a.id_analis=pd.analis
            LEFT JOIN darah.analis a2 ON a2.id_analis=pd.analis2
            LEFT JOIN darah.jenis_darah jd ON jd.id=pd.jenis_darah AND jd.jenis=1
            LEFT JOIN darah.jenis_darah j ON j.id=pd.buffycoat AND j.jenis=2
            LEFT JOIN darah.usrmst us ON us.USRID=pd.nm_creat
            LEFT JOIN darah.usrmst ue ON ue.USRID=pd.nm_edit
            LEFT JOIN darah.ruangan r ON r.ID=pd.ruangan AND r.JENIS=5 AND r.JENIS_KUNJUNGAN IN(1,2,3,4,5,6,13,14)
            LEFT JOIN darah.perawat dp ON dp.id_perawat=pd.perawat
            LEFT JOIN darah.kantong_luar kl ON kl.no_permintaan=pd.no_permintaan
            LEFT JOIN darah.petugas_serah_terima pts ON pts.NO_PERMINTAAN=pd.no_permintaan
            LEFT JOIN darah.variabel tuj ON tuj.id_variabel=pd.tujuan AND tuj.id_referensi=1948
            WHERE pd.no_permintaan = ?";

        $row = $this->db->query($sql, array($no_permintaan))->row_array();

        if (empty($row)) {
            return null;
        }

        $ref = $this->db->query(
            "SELECT ID, JENIS, DESKRIPSI FROM darah.referensi WHERE JENIS IN (5, 6) ORDER BY ID"
        )->result_array();

        $mayor_map = array();
        $minor_map = array();
        foreach ($ref as $rr) {
            if ((int)$rr['JENIS'] === 5) {
                $mayor_map[(int)$rr['ID']] = $rr['DESKRIPSI'];
            } elseif ((int)$rr['JENIS'] === 6) {
                $minor_map[(int)$rr['ID']] = $rr['DESKRIPSI'];
            }
        }

        for ($i = 1; $i <= 12; $i++) {
            $mayor_id = (int)($row['myr_' . $i] ?? 0);
            $minor_id = (int)($row['mnr_' . $i] ?? 0);
            $row['mayor_' . $i]     = $mayor_id > 0 && isset($mayor_map[$mayor_id]) ? $mayor_map[$mayor_id] : '';
            $row['minor_' . $i]     = $minor_id > 0 && isset($minor_map[$minor_id]) ? $minor_map[$minor_id] : '';
        }

        return $row;
    }

    /**
     * Ambil data Detail Darah untuk laporan cetak
     * Migrasi query dari admin/detaildarah.jrxml (native)
     *
     * Database: localhost/darah. Query native detaildarah.jrxml persis
     * sama dengan hasilpemeriksaan.jrxml (tanpa volume_1..12, exp_1..12,
     * jam_analis). Requirement meminta EXP, volume, jam_analis juga →
     * disertakan dari pesan_darah (sumber lokal).
     */
    public function get_detail_darah($no_permintaan)
    {
        // Reuse query dari get_hasil_pemeriksaan (identik konsep)
        $kantong_fields = array();
        for ($i = 1; $i <= 12; $i++) {
            $kantong_fields[] = "pd.no_kantong_{$i}";
            $kantong_fields[] = "pd.volume_{$i}";
            $kantong_fields[] = "pd.exp_{$i}";
        }

        $sql = "SELECT pd.no_permintaan,
            DATE_FORMAT(pd.tgl_minta, '%d-%m-%Y') tgl_minta,
            pd.mr, pd.nama, rjk.DESKRIPSI jenis_kelamin, jd.nama_jenis jenis_darah, j.nama_jenis bufycoat,
            pd.volume, DATE_FORMAT(pd.tgl_diperlukan, '%d-%m-%Y') tgl_diperlukan, pd.diagnosa, pd.alasan,
            pd.kadar_hb, pd.trombosit, YEAR(CURDATE())-YEAR(pd.tgl_lahir) usia,
            rgd.DESKRIPSI gol_darah, rstatus.DESKRIPSI status_proses, pd.`status`, a.nama_analis,
            DATE_FORMAT(pd.tgl_lahir, '%d-%m-%Y') tgl_lahir, us.NAMA DE,
            CONCAT(IF(p.GELAR_DEPAN='' OR p.GELAR_DEPAN IS NULL,'',CONCAT(p.GELAR_DEPAN,'. ')),UPPER(p.NAMA),IF(p.GELAR_BELAKANG='' OR p.GELAR_BELAKANG IS NULL,'',CONCAT(', ',p.GELAR_BELAKANG))) nama_dokter,
            r.DESKRIPSI RUANGAN, pd.pengambil_darah pengambildarah, rhp.DESKRIPSI hasil,
            " . implode(",\n            ", $kantong_fields) . ",
            pd.jam_analis
            FROM darah.pesan_darah pd
            LEFT JOIN darah.referensi rgd ON pd.goldarah=rgd.ID AND rgd.JENIS=1
            LEFT JOIN darah.referensi rstatus ON pd.`status`=rstatus.ID AND rstatus.JENIS=3
            LEFT JOIN darah.referensi rjk ON pd.id_jenis_kelamin=rjk.ID AND rjk.JENIS=2
            LEFT JOIN darah.referensi rhp ON pd.hasil_pemeriksaan=rhp.ID AND rhp.JENIS=4
            LEFT JOIN darah.dokter d ON pd.dpjp=d.ID
            LEFT JOIN darah.pegawai p ON p.NIP=d.NIP
            LEFT JOIN darah.analis a ON a.id_analis=pd.analis
            LEFT JOIN darah.jenis_darah jd ON jd.id=pd.jenis_darah AND jd.jenis=1
            LEFT JOIN darah.jenis_darah j ON j.id=pd.buffycoat AND j.jenis=2
            LEFT JOIN darah.usrmst us ON us.USRID=pd.nm_creat
            LEFT JOIN darah.ruangan r ON r.ID=pd.ruangan AND r.JENIS=5 AND r.JENIS_KUNJUNGAN IN(1,2,3,4,5,6,13,14)
            WHERE pd.no_permintaan = ?";

        return $this->db->query($sql, array($no_permintaan))->row_array();
    }

    /**
     * Ambil data permintaan darah untuk form Edit
     * Migrasi query dari admin/edit_permintaan.php (native)
     *
     * NOTE: Native mengambil goldar kantong dari db_darah.tb_prolis /
     * periksa_hb_bridging (server RS). Database lokal hanya memiliki
     * schema `darah`, sehingga PRO_DESKDAR_x diambil dari
     * darah.kantong_luar.GOLDAR_KLx (sumber lokal).
     *
     * Dipertahankan: JOIN referensi, cek_billing_x, data pasien,
     * dokter, ruangan, jenis darah, kantong, petugas serah-terima, tujuan
     */
    public function get_edit_data($no_permintaan)
    {
        $sql = <<<'SQL'
SELECT
    pd.no_permintaan, pd.mr, pd.nama, rjk.DESKRIPSI AS jenis_kelamin, DATE_FORMAT(pd.tgl_lahir, '%d-%m-%Y') AS tgl_lahir,
    pd.ruangan, pd.tgl_minta, pd.jenis_darah, pd.buffycoat, pd.volume, pd.tgl_diperlukan, pd.diagnosa, pd.alasan,
    pd.kadar_hb, pd.trombosit, rgd.DESKRIPSI AS gol_darah, rstatus.DESKRIPSI AS status_proses, pd.status,
    pd.pengambil_darah, pd.hasil_pemeriksaan, pd.dpjp, pd.riwayattrans, klp.kelengkapan,
    CONCAT(IF(p.GELAR_DEPAN = '' OR p.GELAR_DEPAN IS NULL, '', CONCAT(p.GELAR_DEPAN, '. ')), UPPER(p.NAMA), IF(p.GELAR_BELAKANG = '' OR p.GELAR_BELAKANG IS NULL, '', CONCAT(', ', p.GELAR_BELAKANG))) AS nama_dokter,
    pd.analis,
    GROUP_CONCAT(pd.riwayattrans SEPARATOR ', ') AS RIWAYAT,

    pd.no_kantong_1, kl.GOLDAR_KL1 AS PRO_DESKDAR_1,
    pd.no_kantong_2, kl.GOLDAR_KL2 AS PRO_DESKDAR_2,
    pd.no_kantong_3, kl.GOLDAR_KL3 AS PRO_DESKDAR_3,
    pd.no_kantong_4, kl.GOLDAR_KL4 AS PRO_DESKDAR_4,
    pd.no_kantong_5, kl.GOLDAR_KL5 AS PRO_DESKDAR_5,
    pd.no_kantong_6, kl.GOLDAR_KL6 AS PRO_DESKDAR_6,
    pd.no_kantong_7, kl.GOLDAR_KL7 AS PRO_DESKDAR_7,
    pd.no_kantong_8, kl.GOLDAR_KL8 AS PRO_DESKDAR_8,
    pd.no_kantong_9, kl.GOLDAR_KL9 AS PRO_DESKDAR_9,
    pd.no_kantong_10, kl.GOLDAR_KL10 AS PRO_DESKDAR_10,
    pd.no_kantong_11, kl.GOLDAR_KL11 AS PRO_DESKDAR_11,
    pd.no_kantong_12, kl.GOLDAR_KL12 AS PRO_DESKDAR_12,

    pd.volume_1, pd.volume_2, pd.volume_3, pd.volume_4, pd.volume_5, pd.volume_6,
    pd.volume_7, pd.volume_8, pd.volume_9, pd.volume_10, pd.volume_11, pd.volume_12,
    pd.exp_1, pd.exp_2, pd.exp_3, pd.exp_4, pd.exp_5, pd.exp_6,
    pd.exp_7, pd.exp_8, pd.exp_9, pd.exp_10, pd.exp_11, pd.exp_12,
    pd.jam_analis, pd.perawat, pd.tglkantong1, pd.tglkantong2, pd.tglkantong3,
    pd.tglkantong4, pd.tglkantong5, pd.tglkantong6, pd.tglkantong7,
    pd.tglkantong8, pd.tglkantong9, pd.tglkantong10, pd.tglkantong11, pd.tglkantong12,
    pd.analis2, pd.auto_kontrol,
    pd.myr_1, pd.myr_2, pd.myr_3, pd.myr_4, pd.myr_5, pd.myr_6,
    pd.myr_7, pd.myr_8, pd.myr_9, pd.myr_10, pd.myr_11, pd.myr_12,
    pd.mnr_1, pd.mnr_2, pd.mnr_3, pd.mnr_4, pd.mnr_5, pd.mnr_6,
    pd.mnr_7, pd.mnr_8, pd.mnr_9, pd.mnr_10, pd.mnr_11, pd.mnr_12,
    pts.PETUGAS_SERAH, pts.PETUGAS_TERIMA,
    pts.TGL_SERAH, pts.TGL_SERAH2, pts.TGL_SERAH3, pts.TGL_SERAH4, pts.TGL_SERAH5, pts.TGL_SERAH6,
    pts.TGL_SERAH7, pts.TGL_SERAH8, pts.TGL_SERAH9, pts.TGL_SERAH10, pts.TGL_SERAH11, pts.TGL_SERAH12,
    pts.PETUGAS_SERAH_2, pts.PETUGAS_TERIMA_2,
    pts.PETUGAS_SERAH_3, pts.PETUGAS_TERIMA_3,
    pts.PETUGAS_SERAH_4, pts.PETUGAS_TERIMA_4,
    pts.PETUGAS_SERAH_5, pts.PETUGAS_TERIMA_5,
    pts.PETUGAS_SERAH_6, pts.PETUGAS_TERIMA_6,
    pts.PETUGAS_SERAH_7, pts.PETUGAS_TERIMA_7,
    pts.PETUGAS_SERAH_8, pts.PETUGAS_TERIMA_8,
    pts.PETUGAS_SERAH_9, pts.PETUGAS_TERIMA_9,
    pts.PETUGAS_SERAH_10, pts.PETUGAS_TERIMA_10,
    pts.PETUGAS_SERAH_11, pts.PETUGAS_TERIMA_11,
    pts.PETUGAS_SERAH_12, pts.PETUGAS_TERIMA_12,
    IFNULL(MAX(cb1.status), 0) AS cek_billing_1,
    IFNULL(MAX(cb2.status), 0) AS cek_billing_2,
    IFNULL(MAX(cb3.status), 0) AS cek_billing_3,
    IFNULL(MAX(cb4.status), 0) AS cek_billing_4,
    IFNULL(MAX(cb5.status), 0) AS cek_billing_5,
    IFNULL(MAX(cb6.status), 0) AS cek_billing_6,
    IFNULL(MAX(cb7.status), 0) AS cek_billing_7,
    IFNULL(MAX(cb8.status), 0) AS cek_billing_8,
    IFNULL(MAX(cb9.status), 0) AS cek_billing_9,
    IFNULL(MAX(cb10.status), 0) AS cek_billing_10,
    IFNULL(MAX(cb11.status), 0) AS cek_billing_11,
    IFNULL(MAX(cb12.status), 0) AS cek_billing_12,
    NULLIF(rgd_tujuan.variabel, 'Tidak Ditentukan') AS TUJUAN, pd.tujuan AS ID_TUJUAN
FROM
    darah.pesan_darah pd
LEFT JOIN darah.referensi rgd ON pd.goldarah = rgd.ID AND rgd.JENIS = 1
LEFT JOIN darah.referensi rstatus ON pd.status = rstatus.ID AND rstatus.JENIS = 3
LEFT JOIN darah.referensi rjk ON pd.id_jenis_kelamin = rjk.ID AND rjk.JENIS = 2
LEFT JOIN darah.dokter d ON pd.dpjp = d.ID
LEFT JOIN darah.pegawai p ON p.NIP = d.NIP
LEFT JOIN darah.petugas_serah_terima pts ON pts.NO_PERMINTAAN = pd.no_permintaan
LEFT JOIN darah.kantong_luar kl ON kl.no_permintaan = pd.no_permintaan
LEFT JOIN darah.kelengkapan klp ON klp.NO_PERMINTAAN = pd.no_permintaan
LEFT JOIN darah.cek_billing cb1 ON cb1.no_permintaan = pd.no_permintaan AND cb1.no_kantong = pd.no_kantong_1
LEFT JOIN darah.cek_billing cb2 ON cb2.no_permintaan = pd.no_permintaan AND cb2.no_kantong = pd.no_kantong_2
LEFT JOIN darah.cek_billing cb3 ON cb3.no_permintaan = pd.no_permintaan AND cb3.no_kantong = pd.no_kantong_3
LEFT JOIN darah.cek_billing cb4 ON cb4.no_permintaan = pd.no_permintaan AND cb4.no_kantong = pd.no_kantong_4
LEFT JOIN darah.cek_billing cb5 ON cb5.no_permintaan = pd.no_permintaan AND cb5.no_kantong = pd.no_kantong_5
LEFT JOIN darah.cek_billing cb6 ON cb6.no_permintaan = pd.no_permintaan AND cb6.no_kantong = pd.no_kantong_6
LEFT JOIN darah.cek_billing cb7 ON cb7.no_permintaan = pd.no_permintaan AND cb7.no_kantong = pd.no_kantong_7
LEFT JOIN darah.cek_billing cb8 ON cb8.no_permintaan = pd.no_permintaan AND cb8.no_kantong = pd.no_kantong_8
LEFT JOIN darah.cek_billing cb9 ON cb9.no_permintaan = pd.no_permintaan AND cb9.no_kantong = pd.no_kantong_9
LEFT JOIN darah.cek_billing cb10 ON cb10.no_permintaan = pd.no_permintaan AND cb10.no_kantong = pd.no_kantong_10
LEFT JOIN darah.cek_billing cb11 ON cb11.no_permintaan = pd.no_permintaan AND cb11.no_kantong = pd.no_kantong_11
LEFT JOIN darah.cek_billing cb12 ON cb12.no_permintaan = pd.no_permintaan AND cb12.no_kantong = pd.no_kantong_12
LEFT JOIN darah.variabel rgd_tujuan ON pd.tujuan = rgd_tujuan.id_variabel AND rgd_tujuan.id_referensi = 1948
WHERE pd.no_permintaan = ?
GROUP BY pd.no_permintaan
SQL;

        $d = $this->db->query($sql, array($no_permintaan))->row_array();

        if (empty($d)) {
            return null;
        }

        // Override nomor kantong & golongan darah dari tabel kantong_luar
        // (migrasi logika override dari admin/edit_permintaan.php)
        $kl = $this->db->query(
            "SELECT * FROM darah.kantong_luar WHERE no_permintaan = ? LIMIT 1",
            array($no_permintaan)
        )->row_array();

        if ($kl) {
            for ($i_kl = 1; $i_kl <= 12; $i_kl++) {
                $nk_key = 'no_kantong_' . $i_kl;
                $kl_key = 'NOMOR_KL' . $i_kl;
                $gd_key = 'PRO_DESKDAR_' . $i_kl;
                $gd_kl_key = 'GOLDAR_KL' . $i_kl;
                $perlu_override_nk = false;
                if (isset($kl[$kl_key]) && trim((string)$kl[$kl_key]) !== '') {
                    if (isset($d[$nk_key]) && trim((string)$d[$nk_key]) === '') {
                        $perlu_override_nk = true;
                    } elseif (isset($d[$nk_key]) && strlen(trim((string)$d[$nk_key])) == 11) {
                        $perlu_override_nk = true;
                    } elseif (isset($d[$nk_key]) && trim((string)$d[$nk_key]) === trim((string)$kl[$kl_key])) {
                        $perlu_override_nk = true;
                    }
                }
                if ($perlu_override_nk) {
                    $d[$nk_key] = $kl[$kl_key];
                }
                if (isset($kl[$gd_kl_key]) && trim((string)$kl[$gd_kl_key]) !== '') {
                    $existing_goldar = isset($d[$gd_key]) ? trim((string)$d[$gd_key]) : '';
                    if ($existing_goldar === '' || $existing_goldar === '-') {
                        $d[$gd_key] = $kl[$gd_kl_key];
                    }
                }
            }
        }

        return $d;
    }

    /**
     * Ambil satu baris kantong_luar (dipakai untuk override 11-digit
     * dan cek existence saat update)
     * Migrasi dari admin/proses_edit_permintaan.php
     */
    public function get_kantong_luar($no_permintaan)
    {
        return $this->db->query(
            "SELECT * FROM darah.kantong_luar WHERE no_permintaan = ? LIMIT 1",
            array($no_permintaan)
        )->row_array();
    }

    /**
     * Update pesan_darah
     * Migrasi query UPDATE dari admin/proses_edit_permintaan.php
     * Audit: nm_edit (session user), tgl_edit (timestamp)
     */
    public function update_pesan_darah($no_permintaan, $data)
    {
        $this->db->where('no_permintaan', $no_permintaan);
        return $this->db->update('darah.pesan_darah', $data);
    }

    /**
     * Upsert petugas_serah_terima
     * Native: UPDATE saja (silent no-op jika baris belum ada).
     * Diubah jadi upsert (check existence) agar stabil di database lokal.
     */
    public function update_petugas_serah_terima($no_permintaan, $data)
    {
        $exists = $this->db->where('NO_PERMINTAAN', $no_permintaan)
                           ->count_all_results('darah.petugas_serah_terima');

        if ($exists > 0) {
            $this->db->where('NO_PERMINTAAN', $no_permintaan);
            return $this->db->update('darah.petugas_serah_terima', $data);
        }

        $data['NO_PERMINTAAN'] = $no_permintaan;
        return $this->db->insert('darah.petugas_serah_terima', $data);
    }

    /**
     * Upsert kantong_luar
     * Native: UPDATE lalu cek mysqli_affected_rows==0 untuk INSERT.
     * Diubah jadi check existence via SELECT untuk hindari insert duplikat
     * saat data tidak berubah (affected_rows==0 pada baris yang ada).
     */
    public function upsert_kantong_luar($no_permintaan, $nomor, $goldar)
    {
        $data = array();
        for ($i = 1; $i <= 12; $i++) {
            $data['NOMOR_KL' . $i]  = $nomor[$i];
            $data['GOLDAR_KL' . $i] = $goldar[$i];
        }

        $exists = $this->db->where('no_permintaan', $no_permintaan)
                           ->count_all_results('darah.kantong_luar');

        if ($exists > 0) {
            $this->db->where('no_permintaan', $no_permintaan);
            return $this->db->update('darah.kantong_luar', $data);
        }

        $data['no_permintaan'] = $no_permintaan;
        $data['STATUS']        = 1;

        return $this->db->insert('darah.kantong_luar', $data);
    }

    /**
     * Upsert kelengkapan
     * Migrasi dari admin/proses_edit_permintaan.php (SELECT lalu UPDATE/INSERT)
     */
    public function upsert_kelengkapan($no_permintaan, $kelengkapan)
    {
        $exists = $this->db->where('NO_PERMINTAAN', $no_permintaan)
                           ->count_all_results('darah.kelengkapan');

        if ($exists > 0) {
            $this->db->where('NO_PERMINTAAN', $no_permintaan);
            return $this->db->update('darah.kelengkapan', array(
                'kelengkapan' => $kelengkapan,
            ));
        }

        return $this->db->insert('darah.kelengkapan', array(
            'NO_PERMINTAAN' => $no_permintaan,
            'kelengkapan'   => $kelengkapan,
            'STATUS'        => 1,
        ));
    }

    /**
     * Upsert status_terima
     * Migrasi dari admin/proses_terima.php (native)
     * Native: SELECT lalu UPDATE/INSERT. Dipertahankan pola check existence.
     *
     * @param string  $no_permintaan
     * @param int     $no_mr
     * @param int     $oleh          USRID (tinyint)
     * @param string  $tanggal_terima
     * @param int     $status
     * @return bool
     */
    public function upsert_status_terima($no_permintaan, $no_mr, $oleh, $tanggal_terima, $status = 1)
    {
        $exists = $this->db->where('no_permintaan', $no_permintaan)
                           ->count_all_results('darah.status_terima');

        $data = array(
            'no_mr'         => $no_mr,
            'oleh'          => $oleh,
            'tanggal_terima'=> $tanggal_terima,
            'status'        => $status,
        );

        if ($exists > 0) {
            $this->db->where('no_permintaan', $no_permintaan);
            return $this->db->update('darah.status_terima', $data);
        }

        $data['no_permintaan'] = $no_permintaan;

        return $this->db->insert('darah.status_terima', $data);
    }

     /**
      * Update status pesan_darah
      * Migrasi dari admin/proses_terima.php (native)
      * Digunakan saat penerimaan sampel darah (status=10).
      *
      * @param string $no_permintaan
      * @param int    $status
      * @return bool
      */
    public function update_pesan_darah_status($no_permintaan, $status)
    {
        $this->db->where('no_permintaan', $no_permintaan);
        return $this->db->update('darah.pesan_darah', array('status' => $status));
    }

    /**
     * Laporan Lengkap Permintaan Darah
     * Migrasi dari native: CALL darah.LaporanDarah($P{TGLAWAL}, $P{TGLAKHIR}, $P{ANALIS}, $P{DATAENTRI}, $P{DOKTERKONSUL})
     * Database: localhost/darah (no IP RS, no SP)
     *
     * Query output 63 field sesuai LaporanInfoSimpeldar.jrxml
     */
    public function get_laporan_lengkap($params)
    {
        $tgl_awal      = $params['tgl_awal'];
        $tgl_akhir     = $params['tgl_akhir'];
        $analis        = $params['analis'];
        $data_entry    = $params['data_entry'];
        $dokter_konsul = $params['dokter_konsul'];

        $sql = "SELECT 
            pd.no_permintaan,
            DATE_FORMAT(pd.tgl_minta, '%d-%m-%Y') tgl_minta,
            pd.mr,
            pd.nama,
            rjk.DESKRIPSI jenis_kelamin,
            jd.nama_jenis jenis_darah,
            j.nama_jenis bufycoat,
            pd.volume,
            DATE_FORMAT(pd.tgl_diperlukan, '%d-%m-%Y') tgl_diperlukan,
            pd.diagnosa,
            pd.alasan,
            pd.kadar_hb,
            pd.trombosit,
            YEAR(CURDATE())-YEAR(pd.tgl_lahir) usia,
            rgd.DESKRIPSI gol_darah,
            rstatus.DESKRIPSI status_proses,
            pd.status,
            a.nama_analis,
            DATE_FORMAT(pd.tgl_lahir, '%d-%m-%Y') tgl_lahir,
            us.NAMA DE,
            CONCAT(IF(p.GELAR_DEPAN='' OR p.GELAR_DEPAN IS NULL,'',CONCAT(p.GELAR_DEPAN,'. ')),UPPER(p.NAMA),IF(p.GELAR_BELAKANG='' OR p.GELAR_BELAKANG IS NULL,'',CONCAT(', ',p.GELAR_BELAKANG))) nama_dokter,
            r.DESKRIPSI RUANGAN,
            pd.pengambil_darah pengambildarah,
            rhp.DESKRIPSI hasil,
            pd.riwayattrans,
            pd.no_kantong_1, pd.volume_1, pd.exp_1,
            pd.no_kantong_2, pd.volume_2, pd.exp_2,
            pd.no_kantong_3, pd.volume_3, pd.exp_3,
            pd.no_kantong_4, pd.volume_4, pd.exp_4,
            pd.no_kantong_5, pd.volume_5, pd.exp_5,
            pd.no_kantong_6, pd.volume_6, pd.exp_6,
            pd.no_kantong_7, pd.volume_7, pd.exp_7,
            pd.no_kantong_8, pd.volume_8, pd.exp_8,
            pd.no_kantong_9, pd.volume_9, pd.exp_9,
            pd.no_kantong_10, pd.volume_10, pd.exp_10,
            pd.no_kantong_11, pd.volume_11, pd.exp_11,
            pd.no_kantong_12, pd.volume_12, pd.exp_12,
            pd.jam_analis
            FROM darah.pesan_darah pd
            LEFT JOIN darah.referensi rgd ON pd.goldarah=rgd.ID AND rgd.JENIS=1
            LEFT JOIN darah.referensi rstatus ON pd.status=rstatus.ID AND rstatus.JENIS=3
            LEFT JOIN darah.referensi rjk ON pd.id_jenis_kelamin=rjk.ID AND rjk.JENIS=2
            LEFT JOIN darah.referensi rhp ON pd.hasil_pemeriksaan=rhp.ID AND rhp.JENIS=4
            LEFT JOIN darah.dokter d ON pd.dpjp=d.ID
            LEFT JOIN darah.pegawai p ON p.NIP=d.NIP
            LEFT JOIN darah.analis a ON a.id_analis=pd.analis
            LEFT JOIN darah.jenis_darah jd ON jd.id=pd.jenis_darah AND jd.jenis=1
            LEFT JOIN darah.jenis_darah j ON j.id=pd.buffycoat AND j.jenis=2
            LEFT JOIN darah.usrmst us ON us.USRID=pd.nm_creat
            LEFT JOIN darah.ruangan r ON r.ID=pd.ruangan AND r.JENIS=5 AND r.JENIS_KUNJUNGAN IN(1,2,3,4,5,6,13,14)
            WHERE pd.tgl_minta BETWEEN ? AND ?";

        $bind_params = array($tgl_awal, $tgl_akhir);

        if (!empty($analis)) {
            $sql .= " AND pd.analis = ?";
            $bind_params[] = $analis;
        }

        if (!empty($data_entry)) {
            $sql .= " AND pd.nm_creat = ?";
            $bind_params[] = $data_entry;
        }

        if (!empty($dokter_konsul)) {
            $sql .= " AND pd.dpjp = ?";
            $bind_params[] = $dokter_konsul;
        }

        $sql .= " ORDER BY pd.tgl_minta DESC, pd.no_permintaan DESC";

        return $this->db->query($sql, $bind_params)->result_array();
    }

    /**
     * Rekap Jumlah Pemeriksaan per Analis
     * Migrasi dari native: JRXML query correlated subquery
     * Database: localhost/darah (no IP RS, no SP)
     *
     * Hitung jumlah pemeriksaan:
     * - JUMLAH1: analis utama (pd.analis)
     * - JUMLAH2: analis pendamping (pd.analis2)
     */
    public function get_rekap_analis($params)
    {
        $tgl_awal  = $params['tgl_awal'];
        $tgl_akhir = $params['tgl_akhir'];

        $sql = "SELECT
            an.nama_analis,
            (SELECT COUNT(pd.analis)
             FROM darah.pesan_darah pd
             WHERE pd.analis = an.id_analis
               AND pd.tgl_minta BETWEEN ? AND ?
               AND pd.analis != 0) AS JUMLAH1,
            (SELECT COUNT(pd.analis2)
             FROM darah.pesan_darah pd
             WHERE pd.analis2 = an.id_analis
               AND pd.tgl_minta BETWEEN ? AND ?
               AND pd.analis2 != 0) AS JUMLAH2
            FROM darah.analis an
            ORDER BY an.id_analis ASC";

        $bind_params = array($tgl_awal, $tgl_akhir, $tgl_awal, $tgl_akhir);

        return $this->db->query($sql, $bind_params)->result_array();
    }

}
