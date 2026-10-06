<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Billing_model extends CI_Model {

    private $column_order = array(
        null,
        'no_permintaan',
        'mr',
        'nama',
        'tgl_sort',
        'ruangan',
        'jenis_darah',
        'total_kantong',
        'status_billing',
        'no_permintaan'
    );

    public function __construct()
    {
        parent::__construct();
    }

    private function _get_base_query($tgl_awal = '', $tgl_akhir = '', $ruangan = '')
    {
        $params = array();

        $sql = "SELECT
            pd.no_permintaan,
            pd.mr,
            pd.nama,
            DATE_FORMAT(pd.tgl_minta, '%d-%m-%Y') AS tgl_minta,
            pd.tgl_minta AS tgl_sort,
            r.DESKRIPSI AS ruangan,
            jd.nama_jenis AS jenis_darah,
            (
                (CASE WHEN pd.no_kantong_1 IS NOT NULL AND pd.no_kantong_1 <> '' THEN 1 ELSE 0 END) +
                (CASE WHEN pd.no_kantong_2 IS NOT NULL AND pd.no_kantong_2 <> '' THEN 1 ELSE 0 END) +
                (CASE WHEN pd.no_kantong_3 IS NOT NULL AND pd.no_kantong_3 <> '' THEN 1 ELSE 0 END) +
                (CASE WHEN pd.no_kantong_4 IS NOT NULL AND pd.no_kantong_4 <> '' THEN 1 ELSE 0 END) +
                (CASE WHEN pd.no_kantong_5 IS NOT NULL AND pd.no_kantong_5 <> '' THEN 1 ELSE 0 END) +
                (CASE WHEN pd.no_kantong_6 IS NOT NULL AND pd.no_kantong_6 <> '' THEN 1 ELSE 0 END) +
                (CASE WHEN pd.no_kantong_7 IS NOT NULL AND pd.no_kantong_7 <> '' THEN 1 ELSE 0 END) +
                (CASE WHEN pd.no_kantong_8 IS NOT NULL AND pd.no_kantong_8 <> '' THEN 1 ELSE 0 END) +
                (CASE WHEN pd.no_kantong_9 IS NOT NULL AND pd.no_kantong_9 <> '' THEN 1 ELSE 0 END) +
                (CASE WHEN pd.no_kantong_10 IS NOT NULL AND pd.no_kantong_10 <> '' THEN 1 ELSE 0 END) +
                (CASE WHEN pd.no_kantong_11 IS NOT NULL AND pd.no_kantong_11 <> '' THEN 1 ELSE 0 END) +
                (CASE WHEN pd.no_kantong_12 IS NOT NULL AND pd.no_kantong_12 <> '' THEN 1 ELSE 0 END)
            ) AS total_kantong,
            (
                SELECT COUNT(*) FROM darah.cek_billing cb
                WHERE cb.no_permintaan = pd.no_permintaan AND cb.status = 1
            ) AS total_billing
            FROM darah.pesan_darah pd
            LEFT JOIN darah.ruangan r
                ON pd.ruangan = r.ID
                AND r.JENIS = 5
            LEFT JOIN darah.jenis_darah jd
                ON pd.jenis_darah = jd.ID
                AND jd.JENIS = 1
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
        if (!empty($ruangan)) {
            $sql .= ' AND pd.ruangan = ?';
            $params[] = $ruangan;
        }

        return array($sql, $params);
    }

    private function _wrap_status($base)
    {
        return "SELECT *, CASE WHEN total_billing > 0 THEN 'Sudah Billing' ELSE 'Belum Billing' END AS status_billing FROM (" . $base . ") temp";
    }

    private function _build_where($search = '', $status_billing = '')
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

        if (!empty($status_billing)) {
            if ($status_billing === 'sudah') {
                $sql .= ' AND total_billing > 0';
            } elseif ($status_billing === 'belum') {
                $sql .= ' AND total_billing = 0';
            }
        }

        return array($sql, $params);
    }

    public function get_datatables($tgl_awal = '', $tgl_akhir = '', $ruangan = '', $status_billing = '',
                                   $search = '', $order = array(), $start = 0, $length = 10)
    {
        list($base, $baseParams) = $this->_get_base_query($tgl_awal, $tgl_akhir, $ruangan);
        $wrapped = $this->_wrap_status($base);
        list($whereSql, $whereParams) = $this->_build_where($search, $status_billing);

        $orderSql = '';
        if (!empty($order)) {
            $col_index = $order[0]['column'];
            $dir = (strtoupper($order[0]['dir']) === 'ASC') ? 'ASC' : 'DESC';
            if (isset($this->column_order[$col_index]) && $this->column_order[$col_index] !== null) {
                $orderSql = ' ORDER BY `' . $this->column_order[$col_index] . '` ' . $dir;
            }
        } else {
            $orderSql = ' ORDER BY `tgl_sort` DESC';
        }

        $limitSql = '';
        if ($length != -1) {
            $limitSql = ' LIMIT ' . (int)$start . ', ' . (int)$length;
        }

        $finalSql = 'SELECT * FROM (' . $wrapped . ') t2 WHERE 1=1' . $whereSql . $orderSql . $limitSql;
        $finalParams = array_merge($baseParams, $whereParams);

        return $this->db->query($finalSql, $finalParams)->result_array();
    }

    public function count_filtered($tgl_awal = '', $tgl_akhir = '', $ruangan = '', $status_billing = '', $search = '')
    {
        list($base, $baseParams) = $this->_get_base_query($tgl_awal, $tgl_akhir, $ruangan);
        $wrapped = $this->_wrap_status($base);
        list($whereSql, $whereParams) = $this->_build_where($search, $status_billing);

        $finalSql = 'SELECT COUNT(*) AS cnt FROM (' . $wrapped . ') t2 WHERE 1=1' . $whereSql;
        $finalParams = array_merge($baseParams, $whereParams);

        $row = $this->db->query($finalSql, $finalParams)->row_array();
        return $row ? (int)$row['cnt'] : 0;
    }

    public function count_all($tgl_awal = '', $tgl_akhir = '', $ruangan = '')
    {
        list($base, $baseParams) = $this->_get_base_query($tgl_awal, $tgl_akhir, $ruangan);
        $wrapped = $this->_wrap_status($base);
        $row = $this->db->query('SELECT COUNT(*) AS cnt FROM (' . $wrapped . ') t2', $baseParams)->row_array();
        return $row ? (int)$row['cnt'] : 0;
    }

    public function get_ruangan()
    {
        $sql = "SELECT ID, DESKRIPSI FROM darah.ruangan
                WHERE JENIS = 5 AND JENIS_KUNJUNGAN IN (1,2,3,4,5,6,13,14,15)
                ORDER BY DESKRIPSI ASC";
        return $this->db->query($sql)->result_array();
    }

    public function get_status_billing()
    {
        return array(
            array('id' => 'sudah', 'nama' => 'Sudah Billing'),
            array('id' => 'belum', 'nama' => 'Belum Billing'),
        );
    }
}