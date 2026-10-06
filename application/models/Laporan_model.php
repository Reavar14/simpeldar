<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 1. Laporan Pelayanan
     * Migrasi query dari admin/form_laporan.php (section 1)
     *
     * Filter: tgl_awal, tgl_akhir, analis (opsional)
     * Database: localhost/darah (pesan_darah, analis, ruangan, referensi)
     */
    public function get_laporan_pelayanan($tgl_awal, $tgl_akhir, $analis = '')
    {
        $sql = "SELECT
            pd.no_permintaan,
            DATE_FORMAT(pd.tgl_minta, '%d-%m-%Y') AS tgl_minta,
            pd.mr,
            pd.nama,
            DATE_FORMAT(pd.tgl_diperlukan, '%d-%m-%Y') AS tgl_diperlukan,
            r.DESKRIPSI AS ruangan,
            a.nama_analis,
            rstatus.DESKRIPSI AS status
            FROM darah.pesan_darah pd
            LEFT JOIN darah.analis a ON pd.analis = a.id_analis
            LEFT JOIN darah.ruangan r ON r.ID = pd.ruangan AND r.JENIS = 5
            LEFT JOIN darah.referensi rstatus ON pd.status = rstatus.ID AND rstatus.JENIS = 3
            WHERE pd.tgl_minta BETWEEN ? AND ?";

        $params = array($tgl_awal, $tgl_akhir);

        if (!empty($analis)) {
            $sql .= ' AND pd.analis = ?';
            $params[] = $analis;
        }

        $sql .= ' ORDER BY pd.tgl_minta DESC, pd.no_permintaan DESC';

        return $this->db->query($sql, $params)->result_array();
    }

    public function count_pelayanan($tgl_awal, $tgl_akhir, $analis = '')
    {
        $sql = "SELECT COUNT(pd.no_permintaan) AS cnt
                FROM darah.pesan_darah pd
                WHERE pd.tgl_minta BETWEEN ? AND ?";
        $params = array($tgl_awal, $tgl_akhir);

        if (!empty($analis)) {
            $sql .= ' AND pd.analis = ?';
            $params[] = $analis;
        }

        $row = $this->db->query($sql, $params)->row_array();
        return $row ? (int)$row['cnt'] : 0;
    }

    /**
     * 2. Laporan Jumlah Permintaan Darah (group by status)
     * Migrasi query dari admin/form_laporan.php (section 2)
     *
     * Filter: tgl_awal, tgl_akhir, status (opsional)
     * Database: localhost/darah (referensi JENIS=3, pesan_darah)
     */
    public function get_jumlah_permintaan($tgl_awal, $tgl_akhir, $status = '')
    {
        $sql = "SELECT
            ref.ID AS status_id,
            ref.DESKRIPSI AS status,
            COUNT(pd.no_permintaan) AS jumlah
            FROM darah.referensi ref
            LEFT JOIN darah.pesan_darah pd
                ON pd.status = ref.ID
                AND pd.tgl_minta BETWEEN ? AND ?
            WHERE ref.JENIS = 3";

        $params = array($tgl_awal, $tgl_akhir);

        if (!empty($status)) {
            $sql .= ' AND ref.ID = ?';
            $params[] = $status;
        }

        $sql .= ' GROUP BY ref.ID, ref.DESKRIPSI ORDER BY ref.ID ASC';

        return $this->db->query($sql, $params)->result_array();
    }

    public function count_jumlah_permintaan($tgl_awal, $tgl_akhir, $status = '')
    {
        $sql = "SELECT COUNT(DISTINCT ref.ID) AS cnt
                FROM darah.referensi ref
                LEFT JOIN darah.pesan_darah pd
                    ON pd.status = ref.ID
                    AND pd.tgl_minta BETWEEN ? AND ?
                WHERE ref.JENIS = 3";
        $params = array($tgl_awal, $tgl_akhir);

        if (!empty($status)) {
            $sql .= ' AND ref.ID = ?';
            $params[] = $status;
        }

        $row = $this->db->query($sql, $params)->row_array();
        return $row ? (int)$row['cnt'] : 0;
    }

    /**
     * 3. Laporan Jumlah Jenis Permintaan (group by jenis_darah)
     * Migrasi query dari admin/form_laporan.php (section 3)
     *
     * Filter: tgl_awal, tgl_akhir
     * Database: localhost/darah (jenis_darah JENIS=1, pesan_darah)
     */
    public function get_jumlah_jenis($tgl_awal, $tgl_akhir)
    {
        $sql = "SELECT
            jd.id AS jenis_id,
            jd.nama_jenis,
            COUNT(pd.no_permintaan) AS jumlah
            FROM darah.jenis_darah jd
            LEFT JOIN darah.pesan_darah pd
                ON pd.jenis_darah = jd.id
                AND pd.tgl_minta BETWEEN ? AND ?
            WHERE jd.jenis = 1
            GROUP BY jd.id, jd.nama_jenis
            ORDER BY jd.id ASC";

        return $this->db->query($sql, array($tgl_awal, $tgl_akhir))->result_array();
    }

    public function count_jumlah_jenis($tgl_awal, $tgl_akhir)
    {
        $sql = "SELECT COUNT(DISTINCT jd.id) AS cnt
                FROM darah.jenis_darah jd
                LEFT JOIN darah.pesan_darah pd
                    ON pd.jenis_darah = jd.id
                    AND pd.tgl_minta BETWEEN ? AND ?
                WHERE jd.jenis = 1";

        $row = $this->db->query($sql, array($tgl_awal, $tgl_akhir))->row_array();
        return $row ? (int)$row['cnt'] : 0;
    }

    /**
     * 4. Laporan Billing Kantong
     * Migrasi query dari admin/form_laporan.php (section 4)
     *
     * Filter: tgl_awal, tgl_akhir
     * Database: localhost/darah (pesan_darah, cek_billing)
     *
     * Dependency audit: hanya darah.pesan_darah + darah.cek_billing.
     * Tidak ada layanan.* / pendaftaran.* / master.* / remote DB.
     */
    public function get_billing_kantong($tgl_awal, $tgl_akhir)
    {
        $sql = "SELECT
            pd.no_permintaan,
            DATE_FORMAT(pd.tgl_minta, '%d-%m-%Y') AS tgl_minta,
            pd.mr,
            pd.nama,
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
            WHERE pd.tgl_minta BETWEEN ? AND ?
            ORDER BY pd.tgl_minta DESC, pd.no_permintaan DESC";

        return $this->db->query($sql, array($tgl_awal, $tgl_akhir))->result_array();
    }

    public function count_billing_kantong($tgl_awal, $tgl_akhir)
    {
        $sql = "SELECT COUNT(pd.no_permintaan) AS cnt
                FROM darah.pesan_darah pd
                WHERE pd.tgl_minta BETWEEN ? AND ?";
        $row = $this->db->query($sql, array($tgl_awal, $tgl_akhir))->row_array();
        return $row ? (int)$row['cnt'] : 0;
    }

    /**
     * Total kantong untuk billing (SUM aggregate)
     */
    public function sum_billing_kantong($tgl_awal, $tgl_akhir)
    {
        $rows = $this->get_billing_kantong($tgl_awal, $tgl_akhir);
        $kantong = 0;
        $billed = 0;
        foreach ($rows as $row) {
            $kantong += (int)$row['total_kantong'];
            $billed  += (int)$row['total_billing'];
        }
        return array('kantong' => $kantong, 'billed' => $billed);
    }
}