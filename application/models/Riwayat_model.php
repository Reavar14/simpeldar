<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Riwayat_model extends CI_Model {

    /* Mapping index DataTables (JS) -> nama kolom.
     * Index 0 = "No" (row number, bukan kolom DB) -> null.
     * Index terakhir = "Opsi" (actions) -> null. */
    private $column_order = array(
        null,               // 0: No
        'no_permintaan',    // 1
        'mr',               // 2
        'nama',             // 3
        'tgl_minta',        // 4
        'ruangan',          // 5
        'tujuan',           // 6
        'goldar',           // 7
        'jenis_darah',      // 8
        'status_proses',    // 9
        null                // 10: Opsi
    );

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Subquery dasar untuk riwayat pasien.
     * Mirip dengan native riwayat_pasien.php tapi diperluas kolom
     * agar sesuai target UI: tujuan, golongan darah, jenis permintaan, status.
     *
     * @param string $tgl_awal
     * @param string $tgl_akhir
     * @param string $mr
     * @return array [sql, params]
     */
    private function _get_base_query($tgl_awal = '', $tgl_akhir = '', $mr = '')
    {
        $params = array();

        $sql = "SELECT
            pd.no_permintaan,
            pd.mr,
            pd.nama,
            DATE_FORMAT(pd.tgl_minta, '%d-%m-%Y') AS tgl_minta,
            r.DESKRIPSI AS ruangan,
            tuj.variabel AS tujuan,
            rgd.DESKRIPSI AS goldar,
            jd.nama_jenis AS jenis_darah,
            rstatus.DESKRIPSI AS status_proses
            FROM darah.pesan_darah pd

            LEFT JOIN darah.ruangan r
                ON pd.ruangan = r.ID
                AND r.JENIS = 5

            LEFT JOIN darah.variabel tuj
                ON pd.tujuan = tuj.id_variabel
                AND tuj.id_referensi = 1948

            LEFT JOIN darah.referensi rgd
                ON pd.goldarah = rgd.ID
                AND rgd.JENIS = 1

            LEFT JOIN darah.jenis_darah jd
                ON pd.jenis_darah = jd.ID
                AND jd.JENIS = 1

            LEFT JOIN darah.referensi rstatus
                ON pd.status = rstatus.ID
                AND rstatus.JENIS = 3

            WHERE
            (pd.status != 0 OR pd.status IS NULL)";

        if (!empty($tgl_awal)) {
            $sql .= ' AND DATE(pd.tgl_minta) >= ?';
            $params[] = $tgl_awal;
        }
        if (!empty($tgl_akhir)) {
            $sql .= ' AND DATE(pd.tgl_minta) <= ?';
            $params[] = $tgl_akhir;
        }
        if (!empty($mr)) {
            $sql .= ' AND pd.mr = ?';
            $params[] = $mr;
        }

        return array($sql, $params);
    }

    /**
     * Bangun klausa WHERE untuk global search (skip placeholder null).
     *
     * @return array [sql, params]
     */
    private function _build_search($search)
    {
        $sql = '';
        $params = array();

        if (!empty($search)) {
            $sql .= ' AND (';
            $first = true;
            foreach ($this->column_order as $col) {
                if ($col === null) {
                    continue;
                }
                if (!$first) $sql .= ' OR ';
                $sql .= '`' . $col . '` LIKE ?';
                $params[] = '%' . $this->db->escape_like_str($search) . '%';
                $first = false;
            }
            $sql .= ')';
        }

        return array($sql, $params);
    }

    /**
     * Ambil data DataTables server-side.
     */
    public function get_datatables($tgl_awal = '', $tgl_akhir = '', $mr = '',
                                   $search = '', $order = array(), $start = 0, $length = 10)
    {
        list($base, $baseParams) = $this->_get_base_query($tgl_awal, $tgl_akhir, $mr);
        list($searchSql, $searchParams) = $this->_build_search($search);

        $orderSql = '';
        if (!empty($order)) {
            $col_index = $order[0]['column'];
            $dir = (strtoupper($order[0]['dir']) === 'ASC') ? 'ASC' : 'DESC';
            if (isset($this->column_order[$col_index]) && $this->column_order[$col_index] !== null) {
                $orderSql = ' ORDER BY `' . $this->column_order[$col_index] . '` ' . $dir;
            }
        } else {
            $orderSql = ' ORDER BY `no_permintaan` DESC';
        }

        $limitSql = '';
        if ($length != -1) {
            $limitSql = ' LIMIT ' . (int)$start . ', ' . (int)$length;
        }

        $finalSql = 'SELECT * FROM (' . $base . ') temp WHERE 1=1'
            . $searchSql . $orderSql . $limitSql;

        $finalParams = array_merge($baseParams, $searchParams);

        return $this->db->query($finalSql, $finalParams)->result_array();
    }

    /**
     * Count data setelah filter search.
     */
    public function count_filtered($tgl_awal = '', $tgl_akhir = '', $mr = '', $search = '')
    {
        list($base, $baseParams) = $this->_get_base_query($tgl_awal, $tgl_akhir, $mr);
        list($searchSql, $searchParams) = $this->_build_search($search);

        $finalSql = 'SELECT COUNT(*) AS cnt FROM (' . $base . ') temp WHERE 1=1' . $searchSql;
        $finalParams = array_merge($baseParams, $searchParams);

        $row = $this->db->query($finalSql, $finalParams)->row_array();
        return $row ? (int)$row['cnt'] : 0;
    }

    /**
     * Count total tanpa filter search (hanya filter tanggal/MR).
     */
    public function count_all($tgl_awal = '', $tgl_akhir = '', $mr = '')
    {
        list($base, $baseParams) = $this->_get_base_query($tgl_awal, $tgl_akhir, $mr);
        $row = $this->db->query('SELECT COUNT(*) AS cnt FROM (' . $base . ') temp', $baseParams)->row_array();
        return $row ? (int)$row['cnt'] : 0;
    }

    /**
     * Hitung jumlah kunjungan untuk info tambahan (native: jml_kunjungan).
     */
    public function count_kunjungan($tgl_awal = '', $tgl_akhir = '', $mr = '')
    {
        $params = array();
        $sql = "SELECT COUNT(*) AS cnt FROM darah.pesan_darah pd
                WHERE (pd.status != 0 OR pd.status IS NULL)";

        if (!empty($tgl_awal)) {
            $sql .= ' AND DATE(pd.tgl_minta) >= ?';
            $params[] = $tgl_awal;
        }
        if (!empty($tgl_akhir)) {
            $sql .= ' AND DATE(pd.tgl_minta) <= ?';
            $params[] = $tgl_akhir;
        }
        if (!empty($mr)) {
            $sql .= ' AND pd.mr = ?';
            $params[] = $mr;
        }

        $row = $this->db->query($sql, $params)->row_array();
        return $row ? (int)$row['cnt'] : 0;
    }
}