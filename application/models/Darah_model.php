<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Darah_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get referensi data by JENIS
     * Digunakan untuk dropdown: golongan darah, status, etc
     */
    public function get_referensi_by_jenis($jenis)
    {
        $sql = "SELECT ID, DESKRIPSI FROM darah.referensi WHERE JENIS = ? ORDER BY ID ASC";
        return $this->db->query($sql, array($jenis))->result_array();
    }

    /**
     * Get golongan darah (JENIS=1, exclude ID 1-4)
     */
    public function get_golongan_darah()
    {
        $sql = "SELECT ID, DESKRIPSI FROM darah.referensi 
                WHERE JENIS = 1 AND ID NOT IN (1, 2, 3, 4) 
                ORDER BY ID ASC";
        return $this->db->query($sql)->result_array();
    }

    /**
     * Get jenis darah (darah.jenis_darah JENIS=1)
     */
    public function get_jenis_darah()
    {
        $sql = "SELECT id, nama_jenis FROM darah.jenis_darah WHERE JENIS = 1 ORDER BY id ASC";
        return $this->db->query($sql)->result_array();
    }

    /**
     * Get buffy coat (darah.jenis_darah JENIS=2)
     */
    public function get_buffycoat()
    {
        $sql = "SELECT id, nama_jenis FROM darah.jenis_darah WHERE JENIS = 2 ORDER BY id ASC";
        return $this->db->query($sql)->result_array();
    }

    /**
     * Get Data Entry (usrmst STATUS=1)
     * Migrasi dari admin/form_cetakan.php dropdown DATAENTRI
     */
    public function get_data_entry()
    {
        $sql = "SELECT USRID, NAMA FROM darah.usrmst
                WHERE STATUS = 1
                ORDER BY USRID ASC";
        return $this->db->query($sql)->result_array();
    }

    /**
     * Get dokter DPJP dari database lokal
     */
    public function get_dokter()
    {
        $sql = "SELECT d.ID, CONCAT(
                    IF(p.GELAR_DEPAN='' OR p.GELAR_DEPAN IS NULL,'',CONCAT(p.GELAR_DEPAN,'. ')),
                    UPPER(p.NAMA),
                    IF(p.GELAR_BELAKANG='' OR p.GELAR_BELAKANG IS NULL,'',CONCAT(', ',p.GELAR_BELAKANG))
                ) AS dokter
                FROM darah.dokter d
                LEFT JOIN darah.pegawai p ON p.NIP = d.NIP
                ORDER BY d.ID ASC";
        return $this->db->query($sql)->result_array();
    }

    /**
     * Get ruangan dari database lokal
     */
    public function get_ruangan()
    {
        $sql = "SELECT ID, DESKRIPSI FROM darah.ruangan 
                WHERE JENIS = 5 AND JENIS_KUNJUNGAN IN (1,2,3,4,5,6,13,14,15)
                ORDER BY DESKRIPSI ASC";
        return $this->db->query($sql)->result_array();
    }

    /**
     * Get analis
     */
    public function get_analis()
    {
        $sql = "SELECT id_analis, nama_analis FROM darah.analis ORDER BY id_analis ASC";
        return $this->db->query($sql)->result_array();
    }

    /**
     * Get perawat
     */
    public function get_perawat()
    {
        $sql = "SELECT id_perawat, nama_perawat FROM darah.perawat ORDER BY id_perawat ASC";
        return $this->db->query($sql)->result_array();
    }

    /**
     * Get tujuan dari db_master.variabel (id_referensi = 1948)
     * Sesuai native form_darah.php: $master -> db_master.variabel
     */
    public function get_tujuan()
    {
        $sql = "SELECT id_variabel, variabel FROM db_master.variabel 
                WHERE id_referensi = 1948 AND status = 1 
                ORDER BY id_variabel ASC";
        return $this->db->query($sql)->result_array();
    }

    /**
     * Get status (referensi JENIS=3)
     */
    public function get_status()
    {
        $sql = "SELECT ID, DESKRIPSI FROM darah.referensi 
                WHERE JENIS = 3 
                ORDER BY ID ASC";
        return $this->db->query($sql)->result_array();
    }

    /**
     * Get hasil pemeriksaan (referensi JENIS=4)
     */
    public function get_hasil_pemeriksaan()
    {
        $sql = "SELECT ID, DESKRIPSI FROM darah.referensi 
                WHERE JENIS = 4 
                ORDER BY ID ASC";
        return $this->db->query($sql)->result_array();
    }

    /**
     * Get MAYOR test (referensi JENIS=5)
     */
    public function get_mayor()
    {
        $sql = "SELECT ID, DESKRIPSI FROM darah.referensi 
                WHERE JENIS = 5 
                ORDER BY ID ASC";
        return $this->db->query($sql)->result_array();
    }

    /**
     * Get MINOR test (referensi JENIS=6)
     */
    public function get_minor()
    {
        $sql = "SELECT ID, DESKRIPSI FROM darah.referensi 
                WHERE JENIS = 6 
                ORDER BY ID ASC";
        return $this->db->query($sql)->result_array();
    }

    /**
     * Get Auto Kontrol (referensi JENIS=7)
     */
    public function get_auto_kontrol()
    {
        $sql = "SELECT ID, DESKRIPSI FROM darah.referensi 
                WHERE JENIS = 7 
                ORDER BY ID ASC";
        return $this->db->query($sql)->result_array();
    }

    /**
     * Get pasien by MR (nomor rekam medis)
     * Return: nama, jenis kelamin, tanggal lahir, golongan darah
     *
     * Catatan: schema lokal hanya memiliki darah.pasien2 (kolom:
     * nomr, nama, jenis_kelamin [varchar], tgl_lahir) tanpa kolom
     * ID golongan darah, sehingga id_gol_darah/gol_darah dikembalikan
     * kosong dan frontend menanganinya secara graceful.
     */
    public function get_pasien_by_mr($mr)
    {
        $sql = "SELECT 
                    p.NORM,
                    p.NAMA,
                    p.TANGGAL_LAHIR AS TGL_LAHIR,
                    DATE_FORMAT(p.TANGGAL_LAHIR, '%d-%m-%Y') AS TANGGAL_LAHIR,
                    p.JENIS_KELAMIN AS id_jenis_kelamin,
                    r.DESKRIPSI AS jenis_kelamin,
                    p.GOLONGAN_DARAH AS id_gol_darah,
                    rfg.DESKRIPSI AS gol_darah
                FROM master.pasien p
                LEFT JOIN master.referensi r ON p.JENIS_KELAMIN = r.ID AND r.JENIS = 2
                LEFT JOIN master.referensi rfg ON rfg.ID = p.GOLONGAN_DARAH AND rfg.JENIS = 6
                WHERE p.NORM = ?
                LIMIT 1";
        $row = $this->db->query($sql, array($mr))->row_array();

        if (!$row) {
            return null;
        }

        return $row;
    }

    /**
     * Get kantong darah by nomor kantong
     * Return: volume, exp date, golongan darah
     *
     * Catatan: native (dataproduksi.php) mengambil CC & TGL_EXPIRED dari
     * db_darah.tb_produksi/aftap (server remote). Lokal hanya punya schema
     * `darah` dan darah.kantong_luar tidak memiliki kolom CC/EXP_KL,
     * sehingga cc & exp dikembalikan kosong (frontend menampilkan '-').
     */
    public function get_kantong_by_nomor($nomor_kantong)
    {
        $sql = "SELECT 
                NOMOR_KL1 as nomor, GOLDAR_KL1 as goldar, STATUS
                FROM darah.kantong_luar 
                WHERE NOMOR_KL1 = ? OR NOMOR_KL2 = ? OR NOMOR_KL3 = ? OR 
                      NOMOR_KL4 = ? OR NOMOR_KL5 = ? OR NOMOR_KL6 = ? OR 
                      NOMOR_KL7 = ? OR NOMOR_KL8 = ? OR NOMOR_KL9 = ? OR 
                      NOMOR_KL10 = ? OR NOMOR_KL11 = ? OR NOMOR_KL12 = ?
                LIMIT 1";
        $params = array_fill(0, 12, $nomor_kantong);
        $row = $this->db->query($sql, $params)->row_array();

        if ($row) {
            return array(
                'cc'      => '',
                'exp'     => '',
                'deskdar' => $row['goldar'] ?? '-',
                'goldaroto' => $row['goldar'] ?? ''
            );
        }

        return array(
            'cc'      => '',
            'exp'     => '',
            'deskdar' => '-',
            'goldaroto' => ''
        );
    }

    /**
     * Get riwayat alergi transfusi pasien
     */
    public function get_riwayat_alergi_by_mr($mr)
    {
        $sql = "SELECT 
                GROUP_CONCAT(CASE WHEN riwayattrans IS NULL OR TRIM(riwayattrans) = '' THEN NULL ELSE CONCAT('- ', TRIM(riwayattrans)) END ORDER BY tgl_creat DESC, no_permintaan DESC SEPARATOR '\n') AS riwayat_alergi
                FROM darah.pesan_darah 
                WHERE mr = ? 
                LIMIT 1";
        return $this->db->query($sql, array($mr))->row_array();
    }

    /**
     * Generate nomor permintaan berikutnya
     * Format: YYYYMM + nomor urut 4 digit
     */
    public function get_next_no_permintaan()
    {
        $today = date("Ym");
        $sql = "SELECT max(no_permintaan) AS last FROM darah.pesan_darah WHERE no_permintaan LIKE ?";
        $result = $this->db->query($sql, array($today . '%'))->row_array();
        
        if (!empty($result['last'])) {
            $lastNoTransaksi = $result['last'];
            $lastNoUrut = substr($lastNoTransaksi, 6, 4);
            $nextNoUrut = $lastNoUrut + 1;
            $nextNoTransaksi = $today . sprintf('%04s', $nextNoUrut);
        } else {
            $nextNoTransaksi = $today . sprintf('%04s', 1);
        }
        
        return $nextNoTransaksi;
    }
    public function get_status_darah_terakhir($mr)
    {
        return $this->db->select('status')->from('pesan_darah')->where('mr', $mr)->order_by('tgl_creat', 'DESC')->limit(1)->get()->row('status');
    }

    /**
     * Insert pesan_darah
     */
    public function insert_pesan_darah($data)
    {
        return $this->db->insert('darah.pesan_darah', $data);
    }

    /**
     * Update golongan darah pasien
     */
    public function update_pasien_goldar($mr, $goldar_id)
    {
        $this->db->where('NORM', $mr);
        return $this->db->update('darah.pasien', array('GOLONGAN_DARAH' => $goldar_id));
    }

    /**
     * Insert kantong_luar (12 kantong)
     */
    public function insert_kantong_luar($data)
    {
        return $this->db->insert('darah.kantong_luar', $data);
    }

    /**
     * Insert petugas_serah_terima
     */
    public function insert_petugas_serah_terima($data)
    {
        return $this->db->insert('darah.petugas_serah_terima', $data);
    }

    /* ==================================================================
       RAWAT INAP - DataTables server-side
       Query native: admin/isi_view_ri.php
       ================================================================== */

    private $column_order_ri = array(
        'no_permintaan', 'mr', 'nama', 'tgl_minta', 'ruangan', 'alasan',
        'tujuan', 'goldar', 'tgl_diperlukan', 'jenis_darah', 'status_proses',
        'kelengkapan_status', 'status_terima'
    );

    /**
     * Subquery dasar dari isi_view_ri.php (native).
     * Filter: status != 0 dan tgl_minta >= 1 bulan terakhir.
     * Output kolom selaras dengan ajax_list_ri (13 kolom).
     *
     * Catatan: filter ruangan/status/tanggal HARUS ditanam di dalam
     * subquery ini (alias `pd` hanya terlihat di scope ini).
     *
     * @return array [sql, params]
     */
    private function _get_base_query_ri($ruangan = '', $status = '', $tgl_awal = '', $tgl_akhir = '')
    {
        $params = array();

        $sql = "SELECT 
            pd.no_permintaan,
            pd.mr,
            pd.nama,
            rjk.DESKRIPSI AS jenis_kelamin,
            rgd.DESKRIPSI AS goldar,

            DATE_FORMAT(pd.tgl_lahir,'%d-%m-%Y') AS tgl_lahir,
            DATE_FORMAT(pd.tgl_minta,'%d-%m-%Y') AS tgl_minta,
            DATE_FORMAT(pd.tgl_diperlukan,'%d-%m-%Y') AS tgl_diperlukan,

            pd.alasan,
            tuj.variabel AS tujuan,
            rstatus.DESKRIPSI AS status_proses,
            r.DESKRIPSI AS ruangan,
            pd.trombosit, pd.kadar_hb,
            CONCAT(jd.nama_jenis,'<br> Trombosit: ',pd.trombosit,'<br> Kadar HB: ',pd.kadar_hb) AS jenis_darah,

            CASE 
                WHEN k.kelengkapan = 1 THEN 'Sudah lengkap'
                ELSE 'Tidak lengkap'
            END AS kelengkapan_status,

            IFNULL(st.status,0) AS status_terima

            FROM darah.pesan_darah pd

            LEFT JOIN darah.referensi rgd 
                ON pd.goldarah = rgd.ID 
                AND rgd.JENIS = 1

            LEFT JOIN darah.referensi rstatus 
                ON pd.status = rstatus.ID 
                AND rstatus.JENIS = 3

            LEFT JOIN darah.referensi rjk 
                ON pd.id_jenis_kelamin = rjk.ID 
                AND rjk.JENIS = 2

            LEFT JOIN darah.ruangan r 
                ON pd.ruangan = r.ID 
                AND r.JENIS = 5

            LEFT JOIN darah.kelengkapan k 
                ON pd.no_permintaan = k.NO_PERMINTAAN

            LEFT JOIN darah.status_terima st 
                ON pd.no_permintaan = st.no_permintaan

            LEFT JOIN darah.variabel tuj 
                ON tuj.id_variabel = pd.tujuan AND tuj.id_referensi=1948

            LEFT JOIN darah.jenis_darah jd 
                ON pd.jenis_darah = jd.ID 
                AND jd.JENIS = 1

            WHERE 
            (pd.status != 0 OR pd.status IS NULL)
            AND pd.tgl_minta >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";

        if (!empty($ruangan)) {
            $sql .= ' AND pd.ruangan = ?';
            $params[] = $ruangan;
        }
        if (!empty($status)) {
            $sql .= ' AND pd.status = ?';
            $params[] = $status;
        }
        if (!empty($tgl_awal)) {
            $sql .= ' AND DATE(pd.tgl_minta) >= ?';
            $params[] = $tgl_awal;
        }
        if (!empty($tgl_akhir)) {
            $sql .= ' AND DATE(pd.tgl_minta) <= ?';
            $params[] = $tgl_akhir;
        }

        return array($sql, $params);
    }

    /**
     * Ambil data DataTables Rawat Inap
     */
    public function get_datatables_ri($search = '', $order = array(), $start = 0, $length = 10,
        $ruangan = '', $status = '', $tgl_awal = '', $tgl_akhir = '',
        $no_permintaan = '', $mr = '', $nama = '')
    {
        list($base, $baseParams) = $this->_get_base_query_ri($ruangan, $status, $tgl_awal, $tgl_akhir);

        // Filter spesifik kolom (diluar subquery, pakai alias kolom)
        $colFilters = array(
            'no_permintaan' => $no_permintaan,
            'mr'            => $mr,
            'nama'          => $nama
        );

        $whereOuter = array();
        $hasColFilter = false;
        foreach ($colFilters as $col => $val) {
            if ($val !== '' && $val !== null) {
                $hasColFilter = true;
                break;
            }
        }

        // Search global + col filter: bangun SQL string manual agar bind param aman
        $outerSql = '';
        $outerParams = array();

        if ($hasColFilter) {
            $outerSql .= ' AND (';
            $first = true;
            foreach ($colFilters as $col => $val) {
                if ($val === '' || $val === null) continue;
                if (!$first) $outerSql .= ' OR ';
                $outerSql .= '`' . $col . '` LIKE ?';
                $outerParams[] = '%' . $this->db->escape_like_str($val) . '%';
                $first = false;
            }
            $outerSql .= ')';
        }

        if (!empty($search)) {
            $outerSql .= ' AND (';
            $first = true;
            foreach ($this->column_order_ri as $col) {
                if (!$first) $outerSql .= ' OR ';
                $outerSql .= '`' . $col . '` LIKE ?';
                $outerParams[] = '%' . $this->db->escape_like_str($search) . '%';
                $first = false;
            }
            $outerSql .= ')';
        }

        // Order
        $orderSql = '';
        if (!empty($order)) {
            $col_index = $order[0]['column'];
            $dir = (strtoupper($order[0]['dir']) === 'ASC') ? 'ASC' : 'DESC';
            if (isset($this->column_order_ri[$col_index])) {
                $orderSql = ' ORDER BY `' . $this->column_order_ri[$col_index] . '` ' . $dir;
            }
        } else {
            $orderSql = ' ORDER BY `no_permintaan` DESC';
        }

        // Limit
        $limitSql = '';
        if ($length != -1) {
            $limitSql = ' LIMIT ' . (int)$start . ', ' . (int)$length;
        }

        $finalSql = 'SELECT * FROM (' . $base . ') temp WHERE 1=1'
            . $outerSql . $orderSql . $limitSql;

        $finalParams = array_merge($baseParams, $outerParams);

        return $this->db->query($finalSql, $finalParams)->result_array();
    }

    /**
     * Count data Rawat Inap setelah filter
     */
    public function count_filtered_ri($search = '',
        $ruangan = '', $status = '', $tgl_awal = '', $tgl_akhir = '',
        $no_permintaan = '', $mr = '', $nama = '')
    {
        list($base, $baseParams) = $this->_get_base_query_ri($ruangan, $status, $tgl_awal, $tgl_akhir);

        $colFilters = array(
            'no_permintaan' => $no_permintaan,
            'mr'            => $mr,
            'nama'          => $nama
        );

        $outerSql = '';
        $outerParams = array();

        $hasColFilter = false;
        foreach ($colFilters as $col => $val) {
            if ($val !== '' && $val !== null) { $hasColFilter = true; break; }
        }

        if ($hasColFilter) {
            $outerSql .= ' AND (';
            $first = true;
            foreach ($colFilters as $col => $val) {
                if ($val === '' || $val === null) continue;
                if (!$first) $outerSql .= ' OR ';
                $outerSql .= '`' . $col . '` LIKE ?';
                $outerParams[] = '%' . $this->db->escape_like_str($val) . '%';
                $first = false;
            }
            $outerSql .= ')';
        }

        if (!empty($search)) {
            $outerSql .= ' AND (';
            $first = true;
            foreach ($this->column_order_ri as $col) {
                if (!$first) $outerSql .= ' OR ';
                $outerSql .= '`' . $col . '` LIKE ?';
                $outerParams[] = '%' . $this->db->escape_like_str($search) . '%';
                $first = false;
            }
            $outerSql .= ')';
        }

        $finalSql = 'SELECT COUNT(*) AS cnt FROM (' . $base . ') temp WHERE 1=1' . $outerSql;
        $finalParams = array_merge($baseParams, $outerParams);

        $row = $this->db->query($finalSql, $finalParams)->row_array();
        return $row ? (int)$row['cnt'] : 0;
    }

    /**
     * Count total data Rawat Inap tanpa filter
     */
    public function count_all_ri()
    {
        list($base, $baseParams) = $this->_get_base_query_ri();
        $row = $this->db->query('SELECT COUNT(*) AS cnt FROM (' . $base . ') temp', $baseParams)->row_array();
        return $row ? (int)$row['cnt'] : 0;
    }

}
?>
