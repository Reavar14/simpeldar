<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RequestController extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Request_model');
    }

    /**
     * Tampilkan halaman list permintaan darah
     */
    public function list()
    {
        $this->require_level('1');

        $this->load->view('templates/header');
        $this->load->view('request/list');
        $this->load->view('templates/footer');
    }

    /**
     * AJAX endpoint untuk DataTables server-side processing
     */
    public function ajax_list()
    {
        $this->require_level('1');

        $draw   = $this->input->post('draw');
        $start  = $this->input->post('start');
        $length = $this->input->post('length');
        $search_input = $this->input->post('search');
        $search = is_array($search_input) ? $search_input['value'] : '';

        // Ambil parameter order dari DataTables
        $order = $this->input->post('order');
        if (!is_array($order)) {
            $order = array();
        }

        // Ambil data
        $list = $this->Request_model->get_datatables($search, $order, $start, $length);
        $filtered = $this->Request_model->count_filtered($search);
        $total = $this->Request_model->count_all();

        $data = array();
        $no = $start + 1;

        foreach ($list as $item) {
            $row = array();
            $row[] = $item['no_permintaan'];
            $row[] = $item['mr'];
            $row[] = $item['nama'];
            $row[] = $item['tgl_minta'];
            $row[] = $item['ruangan'];
            $row[] = $item['alasan'];
            $row[] = !empty($item['tujuan']) ? $item['tujuan'] : '';
            $row[] = $item['goldar'];
            $row[] = $item['tgl_diperlukan'];
            $row[] = $item['jenis_darah'];
            $row[] = $item['status_proses'];
            $row[] = $item['kelengkapan_status'];
            $row[] = $item['status_terima'];

            $data[] = $row;
        }

        $output = array(
            "draw" => $draw,
            "recordsTotal" => $total,
            "recordsFiltered" => $filtered,
            "data" => $data,
        );

        echo json_encode($output);
    }

    /**
     * Tampilkan halaman detail permintaan darah
     * Migrasi dari admin/detail_view.php (native)
     */
    public function detail($no_permintaan = null)
    {
        $data['d'] = $this->Request_model->get_detail($no_permintaan);

        /* Ambil ID status master (referensi JENIS=3) untuk penentu render
           tombol Terima / Sudah Diterima berbasis status_proses (field
           source-of-truth pesan_darah.status). */
        $this->load->model('Darah_model');
        $status_ids = array();
        foreach ($this->Darah_model->get_status() as $s) {
            $status_ids[strtolower(trim((string)$s['DESKRIPSI']))] = (int)$s['ID'];
        }
        $data['status_sudah_terima_id'] = isset($status_ids['sudah terima sampel darah'])
            ? $status_ids['sudah terima sampel darah'] : null;
        $data['status_belum_terima_id'] = isset($status_ids['belum terima sampel darah'])
            ? $status_ids['belum terima sampel darah'] : null;

        $this->load->view('templates/header');
        $this->load->view('request/detail', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Cetak laporan Hasil Pemeriksaan (HTML view + window.print)
     * Migrasi dari admin/hasilpemeriksaan.php (native, PHPJasperXML -> TCPDF)
     *
     * Report dibangun dari view request/print_hasil_pemeriksaan.php.
     * Data diambil dari localhost/darah via Request_model::get_hasil_pemeriksaan().
     */
    public function print_hasil_pemeriksaan($no_permintaan = null)
    {
        // Validasi session (constructor sudah memeriksa, tetap dijaga eksplisit)
        if (!isset($_SESSION['uname'])) {
            redirect('auth');
            return;
        }

        $no_permintaan = trim((string)$no_permintaan);

        if ($no_permintaan === '') {
            show_404();
            return;
        }

        $data['d'] = $this->Request_model->get_hasil_pemeriksaan($no_permintaan);

        if (empty($data['d'])) {
            show_404();
            return;
        }

        $this->load->view('request/print_hasil_pemeriksaan', $data);
    }

    /**
     * Cetak Bon Permintaan Darah (HTML view + window.print)
     * Migrasi dari admin/bonminta.php (native, PHPJasperXML -> TCPDF)
     *
     * Report dibangun dari view request/print_bon.php.
     * Data diambil dari localhost/darah via Request_model::get_bon().
     */
    public function print_bon($no_permintaan = null)
    {
        // Validasi session (constructor sudah memeriksa, tetap dijaga eksplisit)
        if (!isset($_SESSION['uname'])) {
            redirect('auth');
            return;
        }

        $no_permintaan = trim((string)$no_permintaan);

        if ($no_permintaan === '') {
            show_404();
            return;
        }

        $data['d'] = $this->Request_model->get_bon($no_permintaan);

        if (empty($data['d'])) {
            show_404();
            return;
        }

        $this->load->view('request/print_bon', $data);
    }

    /**
     * Cetak Form Darah (HTML view + window.print)
     * Migrasi dari admin/formdarah.php (native, PHPJasperXML -> TCPDF)
     *
     * Report dibangun dari view request/print_form.php.
     * Data diambil dari localhost/darah via Request_model::get_form_darah().
     *
     * Catatan: native mengambil goldar kantong & EXP dari tabel remote
     * (db_darah.tb_prolis, produksi, bridging). Versi CI3 memakai sumber
     * lokal: pesan_darah.exp_x/volume_x dan kantong_luar.GOLDAR_KLx.
     */
    public function print_form($no_permintaan = null)
    {
        $this->require_level('1');

        $no_permintaan = trim((string)$no_permintaan);

        if ($no_permintaan === '') {
            show_404();
            return;
        }

        $data['d'] = $this->Request_model->get_form_darah($no_permintaan);

        if (empty($data['d'])) {
            show_404();
            return;
        }

        $this->load->view('request/print_form', $data);
    }

    /**
     * Cetak Detail Darah (HTML view + window.print)
     * Migrasi dari admin/detaildarah.php (native, PHPJasperXML -> TCPDF)
     *
     * Report dibangun dari view request/print_detail_darah.php.
     * Data diambil dari localhost/darah via Request_model::get_detail_darah().
     */
    public function print_detail_darah($no_permintaan = null)
    {
        $this->require_level('1');

        $no_permintaan = trim((string)$no_permintaan);

        if ($no_permintaan === '') {
            show_404();
            return;
        }

        $data['d'] = $this->Request_model->get_detail_darah($no_permintaan);

        if (empty($data['d'])) {
            show_404();
            return;
        }

        $this->load->view('request/print_detail_darah', $data);
    }

    /**
     * Tampilkan form Edit permintaan darah - MODE PENUH
     * Sumber: View Proses (requestcontroller/list)
     * Migrasi dari admin/edit_permintaan.php (native)
     */
    public function edit($no_permintaan = null)
    {
        $this->_render_edit($no_permintaan, 'full_edit');
    }

    /**
     * Tampilkan form Edit permintaan darah - MODE TERBATAS
     * Sumber: View Proses Rawat Inap (darah/view_dokter)
     * Hanya Tgl Diperlukan & Tgl Serah yang dapat diubah (dipaksa di backend).
     */
    public function edit_rawat_inap($no_permintaan = null)
    {
        $this->_render_edit($no_permintaan, 'limited_edit');
    }

    /**
     * Render form edit dengan mode tertentu.
     * @param string $no_permintaan
     * @param string $mode  full_edit | limited_edit
     */
    private function _render_edit($no_permintaan, $mode)
    {
        $this->require_level('1');

        $data['d']         = $this->Request_model->get_edit_data($no_permintaan);
        $data['edit_mode'] = $mode;

        // Normalize tglkantong1-12: replace empty/NULL/zero dates with current datetime for display
        if (!empty($data['d']) && is_array($data['d'])) {
            $now = date('Y-m-d H:i:s');
            for ($i = 1; $i <= 12; $i++) {
                $key = 'tglkantong' . $i;
                $val = $data['d'][$key] ?? '';
                if ($val === '' || $val === null || $val === '0000-00-00 00:00:00' || $val === '0000-00-00') {
                    $data['d'][$key] = $now;
                }
            }
        }

        // Load perawat_terima data for limited_edit mode
        if ($mode === 'limited_edit') {
            $perawat_terima = $this->Request_model->get_perawat_terima($no_permintaan);
            if ($perawat_terima) {
                for ($i = 1; $i <= 12; $i++) {
                    $key = ($i === 1) ? 'PERAWAT_TERIMA' : 'PERAWAT_TERIMA_' . $i;
                    $data['d'][$key] = $perawat_terima[$key] ?? '';
                }
            }
        }

        // Dropdown referensi (migrasi query dropdown dari edit_permintaan.php)
        $this->load->model('Darah_model');
        $data['goldarah']          = $this->Darah_model->get_golongan_darah();
        $data['jenis_darah']       = $this->Darah_model->get_jenis_darah();
        $data['buffycoat']         = $this->Darah_model->get_buffycoat();
        $data['dokter']            = $this->Darah_model->get_dokter();
        $data['ruangan']           = $this->Darah_model->get_ruangan();
        $data['analis']            = $this->Darah_model->get_analis();
        $data['perawat']           = $this->Darah_model->get_perawat();
        $data['status']            = $this->Darah_model->get_status();
        $data['hasil_pemeriksaan'] = $this->Darah_model->get_hasil_pemeriksaan();
        $data['mayor']             = $this->Darah_model->get_mayor();
        $data['minor']             = $this->Darah_model->get_minor();
        $data['auto_kontrol']      = $this->Darah_model->get_auto_kontrol();
        $data['tujuan']            = $this->Darah_model->get_tujuan();

        $this->load->view('templates/header');
        $this->load->view('request/edit', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Proses update permintaan darah (POST endpoint)
     *
     * Dua mode edit (dibedakan oleh POST `edit_mode`, sumber halaman):
     *   - full_edit    : View Proses  -> update seluruh field (logic lama).
     *   - limited_edit : View Proses Rawat Inap -> HANYA:
     *         darah.pesan_darah.tgl_diperlukan
     *         darah.petugas_serah_terima.TGL_SERAH[n]
     *     Field lain diabaikan walaupun dikirim via POST (proteksi backend).
     *
     * Migrasi dari admin/proses_edit_permintaan.php (native).
     */
    public function update()
    {
        $this->require_level('1');

        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            redirect('requestcontroller/list');
            return;
        }

        $no_permintaan = $this->input->post('no_permintaan');

        if (empty($no_permintaan)) {
            redirect('requestcontroller/list');
            return;
        }

        date_default_timezone_set('Asia/Jakarta');

        // Mode edit ditentukan dari POST (default: full_edit untuk kompatibilitas).
        $edit_mode = $this->input->post('edit_mode');
        if ($edit_mode !== 'limited_edit') {
            $edit_mode = 'full_edit';
        }

        $userLogin = $this->session->userdata('uname');
        $tgl_edit  = date('Y-m-d H:i:s');

        if ($edit_mode === 'limited_edit') {
            $this->_update_limited($no_permintaan, $userLogin, $tgl_edit);
        } else {
            $this->_update_full($no_permintaan, $userLogin, $tgl_edit);
        }

        if ($this->db->trans_status() === FALSE) {
            redirect('requestcontroller/list?error=1');
        } else {
            redirect('requestcontroller/list?success=1');
        }
    }

    /**
     * Update terbatas (limited_edit).
     * Hanya tgl_diperlukan, TGL_SERAH[n], PETUGAS_SERAH[n], PETUGAS_TERIMA[n], PERAWAT_TERIMA[n] yang diubah.
     */
    private function _update_limited($no_permintaan, $userLogin, $tgl_edit)
    {
        $data_pesan = array(
            'tgl_diperlukan' => $this->norm_post('tgl_diperlukan'),
            'nm_edit'        => $userLogin,
            'tgl_edit'       => $tgl_edit,
        );

        $data_pst = array();
        for ($i = 1; $i <= 12; $i++) {
            $serah_col  = ($i === 1) ? 'PETUGAS_SERAH'  : 'PETUGAS_SERAH_'  . $i;
            $terima_col = ($i === 1) ? 'PETUGAS_TERIMA' : 'PETUGAS_TERIMA_' . $i;
            $tgl_col    = ($i === 1) ? 'TGL_SERAH'      : 'TGL_SERAH' . $i;

            $data_pst[$serah_col]  = $this->input->post('petugas_serah_edit_' . $i);
            $data_pst[$terima_col] = $this->input->post('petugas_terima_edit_' . $i);
            $data_pst[$tgl_col]    = $this->norm_post('tgl_serah_edit_' . $i);
        }

        $data_perawat = array();
        for ($i = 1; $i <= 12; $i++) {
            $perawat_col = ($i === 1) ? 'PERAWAT_TERIMA' : 'PERAWAT_TERIMA_' . $i;
            $data_perawat[$perawat_col] = $this->input->post('perawat_edit_' . $i);
        }

        $this->db->trans_start();
        $this->Request_model->update_pesan_darah($no_permintaan, $data_pesan);
        $this->Request_model->update_petugas_serah_terima($no_permintaan, $data_pst);
        $this->Request_model->save_perawat_terima($no_permintaan, $data_perawat);
        $this->db->trans_complete();
    }

    /**
     * Update penuh (full_edit) - logic lama.
     * Migrasi dari admin/proses_edit_permintaan.php (native):
     *   - darah.pesan_darah          (UPDATE)
     *   - darah.petugas_serah_terima (upsert)
     *   - darah.kantong_luar         (upsert)
     *   - darah.kelengkapan          (upsert)
     */
    private function _update_full($no_permintaan, $userLogin, $tgl_edit)
    {
        // Override no_kantong 11-digit dari kantong_luar (migrasi native)
        $no_kantong = array();
        for ($i = 1; $i <= 12; $i++) {
            $no_kantong[$i] = $this->input->post('no_kantong_' . $i);
        }

        $kl_proses = $this->Request_model->get_kantong_luar($no_permintaan);
        if ($kl_proses) {
            for ($i = 1; $i <= 12; $i++) {
                $kl_col = 'NOMOR_KL' . $i;
                if (strlen(trim((string)$no_kantong[$i])) == 11
                    && isset($kl_proses[$kl_col])
                    && trim((string)$kl_proses[$kl_col]) !== '') {
                    $no_kantong[$i] = $kl_proses[$kl_col];
                }
            }
        }

        $kelengkapan = $this->input->post('kelengkapan') ? 1 : 0;

        // === UPDATE darah.pesan_darah ===
        $data_pesan = array(
            'tgl_minta'         => $this->norm_post('tgl_minta'),
            'goldarah'          => $this->norm_post('goldarah'),
            'jenis_darah'       => $this->norm_post('jenis_darah'),
            'buffycoat'         => $this->input->post('buffycoat'),
            'volume'            => $this->input->post('volume'),
            'tgl_diperlukan'    => $this->norm_post('tgl_diperlukan'),
            'diagnosa'          => $this->input->post('diagnosa'),
            'alasan'            => $this->input->post('alasan'),
            'kadar_hb'          => $this->input->post('kadar_hb'),
            'trombosit'         => $this->input->post('trombosit'),
            'status'            => $this->norm_post('status'),
            'dpjp'              => $this->norm_post('dpjp'),
            'analis'            => $this->norm_post('analis'),
            'ruangan'           => $this->input->post('ruangan'),
            'nm_edit'           => $userLogin,
            'pengambil_darah'   => $this->input->post('pengambil_darah'),
            'hasil_pemeriksaan' => $this->input->post('hasil_pemeriksaan'),
            'riwayattrans'      => $this->input->post('riwayattrans'),
            'tgl_edit'          => $tgl_edit,
            'perawat'           => $this->input->post('perawat'),
            'analis2'           => $this->norm_post('analis2'),
            'auto_kontrol'      => $this->input->post('auto_kontrol'),
            'tujuan'            => $this->norm_post('tujuan'),
        );

        for ($i = 1; $i <= 12; $i++) {
            $data_pesan['no_kantong_' . $i] = $no_kantong[$i];
            $data_pesan['volume_' . $i]     = $this->input->post('volume_' . $i);
            $data_pesan['exp_' . $i]        = $this->input->post('exp_' . $i);
            
            $tglkantong_val = $this->norm_post('tglkantong' . $i);
            if ($tglkantong_val !== null) {
                $data_pesan['tglkantong' . $i] = $tglkantong_val;
            }
            
            $data_pesan['myr_' . $i]        = $this->input->post('myr_' . $i);
            $data_pesan['mnr_' . $i]        = $this->input->post('mnr_' . $i);
        }

        // === Upsert darah.petugas_serah_terima ===
        $data_pst = array();
        for ($i = 1; $i <= 12; $i++) {
            $serah_col  = ($i === 1) ? 'PETUGAS_SERAH'  : 'PETUGAS_SERAH_' . $i;
            $terima_col = ($i === 1) ? 'PETUGAS_TERIMA' : 'PETUGAS_TERIMA_' . $i;
            $tgl_col    = ($i === 1) ? 'TGL_SERAH'      : 'TGL_SERAH' . $i;

            $data_pst[$serah_col]  = $this->input->post('petugas_serah_edit_' . $i);
            $data_pst[$terima_col] = $this->input->post('petugas_terima_edit_' . $i);
            $data_pst[$tgl_col]    = $this->norm_post('tgl_serah_edit_' . $i);
        }

        // === Upsert darah.kantong_luar ===
        $goldar_kantong = array();
        $new_goldarah_id = $this->norm_post('goldarah'); // ID baru dari form (pd.goldarah)
        for ($i = 1; $i <= 12; $i++) {
            $posted_goldar = $this->input->post('goldaroto' . $i);
            // Sinkronisasi: jika pd.goldarah berubah, pakai nilai baru untuk semua kantong
            $goldar_kantong[$i] = $new_goldarah_id ?? $posted_goldar;
        }

        // Transaction: rollback otomatis jika ada query gagal
        $this->db->trans_start();
        $this->Request_model->update_pesan_darah($no_permintaan, $data_pesan);
        $this->Request_model->update_petugas_serah_terima($no_permintaan, $data_pst);
        $this->Request_model->upsert_kantong_luar($no_permintaan, $no_kantong, $goldar_kantong);
        $this->Request_model->upsert_kelengkapan($no_permintaan, $kelengkapan);
        $this->db->trans_complete();
    }

    /**
     * Proses penerimaan sampel darah (AJAX endpoint)
     * Migrasi dari admin/proses_terima.php (native)
     *
     * Tabel terpengaruh (semua di localhost/darah):
     *   - darah.status_terima  (upsert: status=1)
     *   - darah.pesan_darah    (UPDATE: status=10 "Sudah Terima Sampel Darah")
     *
     * Response JSON sesuai native:
     *   {"success": true}  atau  {"success": false, "message": "..."}
     */
    public function mark_received()
    {
        $this->require_level('1');

        header('Content-Type: application/json; charset=utf-8');

        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            echo json_encode(array('success' => false, 'message' => 'method not allowed'));
            return;
        }

        $no_permintaan = trim((string)$this->input->post('no_permintaan'));
        $no_mr         = trim((string)$this->input->post('no_mr'));

        if ($no_permintaan === '') {
            echo json_encode(array('success' => false, 'message' => 'no_permintaan required'));
            return;
        }

        if ($no_mr === '') {
            echo json_encode(array('success' => false, 'message' => 'no_mr required'));
            return;
        }

        date_default_timezone_set('Asia/Jakarta');

        // oleh = USRID (tinyint), bukan username string
        $oleh         = $this->session->userdata('login');
        $tanggal      = date('Y-m-d H:i:s');
        $status_terima = 1;
        $status_pesan  = 10; // referensi JENIS=3: "Sudah Terima Sampel Darah"

        $this->db->trans_start();
        $this->Request_model->upsert_status_terima($no_permintaan, $no_mr, $oleh, $tanggal, $status_terima);
        $this->Request_model->update_pesan_darah_status($no_permintaan, $status_pesan);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(array('success' => false, 'message' => 'database error'));
            return;
        }

        echo json_encode(array('success' => true));
    }

/**
     * Laporan Lengkap Permintaan Darah
     * Migrasi dari native admin/cetakdata.php + LaporanInfoSimpeldar.jrxml
     * Database: localhost/darah (no IP RS, no JavaBridge, no SP)
     */
    public function print_laporan_lengkap()
    {
        $this->require_level('1');

        // Validasi session sudah di __construct

        // Load Darah_model untuk dropdown filter
        $this->load->model('Darah_model');

        // Ambil parameter GET
        $tgl_awal      = $this->input->get('tgl_awal');
        $tgl_akhir     = $this->input->get('tgl_akhir');
        $analis        = $this->input->get('ANALIS');
        $data_entry    = $this->input->get('DATAENTRI');
        $dokter_konsul = $this->input->get('DOKTERKONSUL');

        // Default tanggal: 1 bulan terakhir jika kosong
        if (empty($tgl_awal)) {
            $tgl_awal = date('Y-m-d', strtotime('-1 month'));
        }
        if (empty($tgl_akhir)) {
            $tgl_akhir = date('Y-m-d');
        }

        // Normalisasi tanggal akhir ke akhir hari
        $tgl_awal_db  = $tgl_awal . ' 00:00:00';
        $tgl_akhir_db = $tgl_akhir . ' 23:59:59';

        $params = array(
            'tgl_awal'      => $tgl_awal_db,
            'tgl_akhir'     => $tgl_akhir_db,
            'analis'        => $analis,
            'data_entry'    => $data_entry,
            'dokter_konsul' => $dokter_konsul,
        );

        // Panggil model
        $data['laporan'] = $this->Request_model->get_laporan_lengkap($params);

        // Data untuk dropdown filter
        $data['analis_list']     = $this->Darah_model->get_analis();
        $data['dokter_list']     = $this->Darah_model->get_dokter();
        $data['ruangan_list']    = $this->Darah_model->get_ruangan();

        // Pass back filter values untuk form sticky
        $data['filter'] = array(
            'tgl_awal'      => $tgl_awal,
            'tgl_akhir'     => $tgl_akhir,
            'analis'        => $analis,
            'data_entry'    => $data_entry,
            'dokter_konsul' => $dokter_konsul,
        );

        $this->load->view('templates/header');
        $this->load->view('request/print_laporan_lengkap', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Rekap Jumlah Pemeriksaan per Analis
     * Migrasi dari native admin/cetakrekap.php + LaporanCetakRekap.jrxml
     * Database: localhost/darah (no IP RS, no JavaBridge, no SP)
     */
    public function print_rekap_analis()
    {
        $this->require_level('1');

        // Validasi session sudah di __construct

        // Ambil parameter GET (nama param sesuai native)
        $tgl_awal  = $this->input->get('tgl_awal_rekap');
        $tgl_akhir = $this->input->get('tgl_akhir_rekap');
        $analis    = $this->input->get('ANALIS');

        // Default tanggal: 1 bulan terakhir jika kosong
        if (empty($tgl_awal)) {
            $tgl_awal = date('Y-m-d', strtotime('-1 month'));
        }
        if (empty($tgl_akhir)) {
            $tgl_akhir = date('Y-m-d');
        }

        // Normalisasi untuk query database
        $tgl_awal_db  = $tgl_awal . ' 00:00:00';
        $tgl_akhir_db = $tgl_akhir . ' 23:59:59';

        $params = array(
            'tgl_awal'  => $tgl_awal_db,
            'tgl_akhir' => $tgl_akhir_db,
        );

        // Panggil model
        $data['rekap'] = $this->Request_model->get_rekap_analis($params);

        // Pass back filter values untuk form sticky
        $data['filter'] = array(
            'tgl_awal'  => $tgl_awal,
            'tgl_akhir' => $tgl_akhir,
            'analis'    => $analis,
        );

        $this->load->view('templates/header');
        $this->load->view('request/print_rekap_analis', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Cetak Bon Darah - Endpoint untuk tombol Cetak Bon Darah
     * Migrasi dari native: bonminta.php (query param id)
     * URL: requestcontroller/cetak_bon_darah?id=2026100008
     */
    public function cetak_bon_darah()
    {
        $this->require_level('1');

        $id = trim($this->input->get('id', TRUE));

        if ($id === '') {
            show_404();
            return;
        }

        $data['d'] = $this->Request_model->get_bon($id);

        if (empty($data['d'])) {
            show_404();
            return;
        }

        $this->load->view('request/print_bon', $data);
    }

    /**
     * Cetak Form Darah - Endpoint untuk tombol Cetak Form Darah
     * Migrasi dari native: CetakFormDarah.php (query param id)
     * URL: requestcontroller/cetak_form_darah?id=2026100008
     */
    public function cetak_form_darah()
    {
        $this->require_level('1');

        $id = trim($this->input->get('id', TRUE));

        if ($id === '') {
            show_404();
            return;
        }

        $data['d'] = $this->Request_model->get_form_darah($id);

        if (empty($data['d'])) {
            show_404();
            return;
        }

        $this->load->view('request/print_form', $data);
    }

    /**
     * Ambil POST dan normalize nilai kosong menjadi NULL.
     *
     * MySQL lokal memakai STRICT_TRANS_TABLES + NO_ZERO_DATE, sehingga
     * string kosong ('') di kolom datetime/date/int akan menimbulkan error.
     * Native mengandalkan mode non-strict (otomatis jadi 0 / 0000-00-00).
     * Hanya dipakai untuk kolom non-string (tanggal & integer).
     */
    private function norm_post($field)
    {
        $value = $this->input->post($field);

        if ($value === null || $value === false || $value === '') {
            return null;
        }

        return $value;
    }

}
