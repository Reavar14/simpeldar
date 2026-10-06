<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    // Tab Permintaan (status = 5, belum diterima, 5 hari terakhir)
    public function get_permintaan()
    {
        $sql = "SELECT
        pd.no_permintaan,
        pd.mr,
        pd.nama,
        rjk.DESKRIPSI AS jenis_kelamin,
        rgd.DESKRIPSI AS goldar,

        DATE_FORMAT(pd.tgl_lahir,'%d-%m-%Y') AS tgl_lahir,
        DATE_FORMAT(pd.tgl_minta,'%d-%m-%Y') AS tgl_minta,
        DATE_FORMAT(pd.tgl_diperlukan,'%d-%m-%Y') AS tgl_diperlukan,

        pd.alasan, tuj.variabel AS TUJUAN,
        rstatus.DESKRIPSI AS status_proses,
        r.DESKRIPSI AS ruangan,
        pd.trombosit, pd.kadar_hb,
        CONCAT(jd.nama_jenis,'<br> Trombosit: ',pd.trombosit,'<br> Kadar HB: ',pd.kadar_hb) AS jenis_darah,

        CASE
            WHEN k.kelengkapan = 1 THEN 'Sudah lengkap'
            ELSE 'Tidak lengkap'
        END AS kelengkapan_status

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

        LEFT JOIN darah.jenis_darah jd
            ON pd.jenis_darah = jd.ID
            AND jd.jenis = 1

        LEFT JOIN darah.variabel tuj
            ON pd.tujuan = tuj.id_variabel AND tuj.id_referensi=1948

        WHERE
        pd.status = 5 AND (st.status != 1 OR st.status IS NULL)
        AND pd.tgl_minta >= DATE_SUB(NOW(),INTERVAL 5 DAY)
        ORDER BY
        pd.tgl_minta DESC, pd.mr DESC";
        return $this->db->query($sql)->result_array();
    }

    // Tab request (hidden, tidak dipakai di tab nav)
    public function get_request()
    {
        $sql = "select pd.no_permintaan, pd.mr, pd.nama, rjk.DESKRIPSI jenis_kelamin,
        DATE_FORMAT(pd.tgl_minta, '%d-%m-%Y') tgl_minta, DATE_FORMAT(pd.tgl_diperlukan, '%d-%m-%Y') tgl_diperlukan,
        pd.alasan, pd.kadar_hb, pd.trombosit, rstatus.DESKRIPSI status_proses,r.DESKRIPSI RUANGAN
        from darah.pesan_darah pd
        left join darah.referensi rgd ON pd.goldarah=rgd.ID and rgd.JENIS=1
        left join darah.referensi rstatus ON pd.`status`=rstatus.ID and rstatus.JENIS=3
        left join darah.referensi rjk ON pd.id_jenis_kelamin=rjk.ID and rjk.JENIS=2
        left join darah.ruangan r ON r.ID=pd.ruangan AND r.JENIS=5
        where pd.`status`=5 AND pd.tgl_minta >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
        order by pd.no_permintaan desc";
        return $this->db->query($sql)->result_array();
    }

    // Tab Sedang Proses (status = 1)
    public function get_sedang_proses()
    {
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
        rstatus.DESKRIPSI AS status_proses,
        r.DESKRIPSI AS ruangan,

        CASE
            WHEN k.kelengkapan = 1 THEN 'Sudah lengkap'
            ELSE 'Tidak lengkap'
        END AS kelengkapan_status

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

        WHERE
        pd.status = 1
        AND pd.tgl_minta >= DATE_SUB(CURDATE(),INTERVAL 2 MONTH)
        ORDER BY
        pd.no_permintaan DESC";
        return $this->db->query($sql)->result_array();
    }

    // Tab Darah Siap (status = 2)
    public function get_siap()
    {
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
        rstatus.DESKRIPSI AS status_proses,
        r.DESKRIPSI AS ruangan,

        CASE
            WHEN k.kelengkapan = 1 THEN 'Sudah lengkap'
            ELSE 'Tidak lengkap'
        END AS kelengkapan_status

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

        WHERE
        pd.status = 2
        AND pd.tgl_minta >= DATE_SUB(CURDATE(),INTERVAL 2 MONTH)
        ORDER BY
        pd.no_permintaan DESC";
        return $this->db->query($sql)->result_array();
    }

    // Tab Perlu Donor (status = 3)
    public function get_donor()
    {
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
        rstatus.DESKRIPSI AS status_proses,
        r.DESKRIPSI AS ruangan,

        CASE
            WHEN k.kelengkapan = 1 THEN 'Sudah lengkap'
            ELSE 'Tidak lengkap'
        END AS kelengkapan_status

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

        WHERE
        pd.status = 3
        AND pd.tgl_minta >= DATE_SUB(CURDATE(),INTERVAL 2 MONTH)
        ORDER BY
        pd.no_permintaan DESC";
        return $this->db->query($sql)->result_array();
    }

    // Tab Sampel Baru (status = 4)
    public function get_baru()
    {
        $sql = "select pd.no_permintaan, pd.mr, pd.nama, rjk.DESKRIPSI jenis_kelamin,
        DATE_FORMAT(pd.tgl_minta, '%d-%m-%Y') tgl_minta, pd.jenis_darah,pd.volume, DATE_FORMAT(pd.tgl_diperlukan, '%d-%m-%Y') tgl_diperlukan,
        pd.alasan, rgd.DESKRIPSI gol_darah, rstatus.DESKRIPSI status_proses,
        r.DESKRIPSI RUANGAN
        from darah.pesan_darah pd
        left join darah.referensi rgd ON pd.goldarah=rgd.ID and rgd.JENIS=1
        left join darah.referensi rstatus ON pd.`status`=rstatus.ID and rstatus.JENIS=3
        left join darah.referensi rjk ON pd.id_jenis_kelamin=rjk.ID and rjk.JENIS=2
        left join darah.ruangan r ON r.ID=pd.ruangan AND r.JENIS=5
        where pd.`status`=4
        AND pd.tgl_minta >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
        order by pd.no_permintaan desc";
        return $this->db->query($sql)->result_array();
    }

    // Tab Incompatible (status = 6)
    public function get_incompatible()
    {
        $sql = "select pd.no_permintaan, pd.mr, pd.nama, rjk.DESKRIPSI jenis_kelamin,
        DATE_FORMAT(pd.tgl_minta, '%d-%m-%Y') tgl_minta,DATE_FORMAT(pd.tgl_diperlukan, '%d-%m-%Y') tgl_diperlukan
        ,pd.alasan, rstatus.DESKRIPSI status_proses,r.DESKRIPSI RUANGAN
        from darah.pesan_darah pd
        left join darah.referensi rgd ON pd.goldarah=rgd.ID and rgd.JENIS=1
        left join darah.referensi rstatus ON pd.`status`=rstatus.ID and rstatus.JENIS=3
        left join darah.referensi rjk ON pd.id_jenis_kelamin=rjk.ID and rjk.JENIS=2
        left join darah.ruangan r ON r.ID=pd.ruangan AND r.JENIS=5
        where pd.`status`=6 AND pd.tgl_minta >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
        order by pd.no_permintaan desc";
        return $this->db->query($sql)->result_array();
    }

    // Tab Masa Simpan Habis (status = 7)
    public function get_masasimpan()
    {
        $sql = "select pd.no_permintaan, pd.mr, pd.nama, rjk.DESKRIPSI jenis_kelamin,
        DATE_FORMAT(pd.tgl_minta, '%d-%m-%Y') tgl_minta,DATE_FORMAT(pd.tgl_diperlukan, '%d-%m-%Y') tgl_diperlukan
        ,pd.alasan, rstatus.DESKRIPSI status_proses,r.DESKRIPSI RUANGAN
        from darah.pesan_darah pd
        left join darah.referensi rgd ON pd.goldarah=rgd.ID and rgd.JENIS=1
        left join darah.referensi rstatus ON pd.`status`=rstatus.ID and rstatus.JENIS=3
        left join darah.referensi rjk ON pd.id_jenis_kelamin=rjk.ID and rjk.JENIS=2
        left join darah.ruangan r ON r.ID=pd.ruangan AND r.JENIS=5
        where pd.`status`=7 AND pd.tgl_minta >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
        order by pd.no_permintaan desc";
        return $this->db->query($sql)->result_array();
    }

    // Tab Belum Diambil (status = 9)
    public function get_belumambil()
    {
        $sql = "select pd.no_permintaan, pd.mr, pd.nama, rjk.DESKRIPSI jenis_kelamin,
        DATE_FORMAT(pd.tgl_minta, '%d-%m-%Y') tgl_minta,DATE_FORMAT(pd.tgl_diperlukan, '%d-%m-%Y') tgl_diperlukan
        ,pd.alasan, rstatus.DESKRIPSI status_proses,r.DESKRIPSI RUANGAN
        from darah.pesan_darah pd
        left join darah.referensi rgd ON pd.goldarah=rgd.ID and rgd.JENIS=1
        left join darah.referensi rstatus ON pd.`status`=rstatus.ID and rstatus.JENIS=3
        left join darah.referensi rjk ON pd.id_jenis_kelamin=rjk.ID and rjk.JENIS=2
        left join darah.ruangan r ON r.ID=pd.ruangan AND r.JENIS=5
        where pd.`status`=9 AND pd.tgl_minta >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
        order by pd.no_permintaan desc";
        return $this->db->query($sql)->result_array();
    }

    // Tab Sudah Habis (status = 8)
    public function get_habis()
    {
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
        rstatus.DESKRIPSI AS status_proses,
        r.DESKRIPSI AS ruangan,

        CASE
            WHEN k.kelengkapan = 1 THEN 'Sudah lengkap'
            ELSE 'Tidak lengkap'
        END AS kelengkapan_status

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

        WHERE
        pd.status = 8
        AND pd.tgl_minta >= DATE_SUB(CURDATE(),INTERVAL 2 MONTH)
        ORDER BY
        pd.no_permintaan DESC";
        return $this->db->query($sql)->result_array();
    }

    // Tab Tidak Lengkap (status <> 0 dan kelengkapan = 0)
    public function get_tidaklengkap()
    {
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
        rstatus.DESKRIPSI AS status_proses,
        r.DESKRIPSI AS ruangan,

        CASE
            WHEN k.kelengkapan = 1 THEN 'Sudah lengkap'
            ELSE 'Tidak lengkap'
        END AS kelengkapan_status

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

        WHERE
        pd.status <> '0' AND k.kelengkapan = 0
        AND pd.tgl_minta >= DATE_SUB(CURDATE(),INTERVAL 2 MONTH)
        ORDER BY
        pd.no_permintaan DESC";
        return $this->db->query($sql)->result_array();
    }

    public function get_status_summary()
    {
        $sql = "SELECT
            SUM(CASE WHEN pd.status = 5 AND (st.status != 1 OR st.status IS NULL) AND pd.tgl_minta >= DATE_SUB(NOW(), INTERVAL 5 DAY) THEN 1 ELSE 0 END) AS permintaan,
            SUM(CASE WHEN pd.status = 1 AND pd.tgl_minta >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH) THEN 1 ELSE 0 END) AS sedang_proses,
            SUM(CASE WHEN pd.status = 2 AND pd.tgl_minta >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH) THEN 1 ELSE 0 END) AS darah_siap,
            SUM(CASE WHEN pd.status = 3 AND pd.tgl_minta >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH) THEN 1 ELSE 0 END) AS perlu_donor,
            SUM(CASE WHEN pd.status = 4 AND pd.tgl_minta >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH) THEN 1 ELSE 0 END) AS sampel_baru,
            SUM(CASE WHEN pd.status = 6 AND pd.tgl_minta >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH) THEN 1 ELSE 0 END) AS incompatible,
            SUM(CASE WHEN pd.status = 7 AND pd.tgl_minta >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH) THEN 1 ELSE 0 END) AS masa_simpan_habis,
            SUM(CASE WHEN pd.status = 9 AND pd.tgl_minta >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH) THEN 1 ELSE 0 END) AS belum_diambil,
            SUM(CASE WHEN pd.status = 8 AND pd.tgl_minta >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH) THEN 1 ELSE 0 END) AS sudah_habis,
            SUM(CASE WHEN pd.status <> 0 AND k.kelengkapan = 0 AND pd.tgl_minta >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH) THEN 1 ELSE 0 END) AS tidak_lengkap
        FROM darah.pesan_darah pd
        LEFT JOIN darah.status_terima st ON pd.no_permintaan = st.no_permintaan
        LEFT JOIN darah.kelengkapan k ON pd.no_permintaan = k.NO_PERMINTAAN";
        return $this->db->query($sql)->row_array();
    }
}
