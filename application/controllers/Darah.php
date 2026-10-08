<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Darah extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Darah_model');
    }

    /**
     * Form input permintaan darah
     */
    public function form()
    {
        $this->require_level('1');

        // Generate nomor permintaan
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

        // Load semua data dropdown
        $data['no_permintaan']      = $nextNoTransaksi;
        $data['goldarah']           = $this->Darah_model->get_golongan_darah();
        $data['jenis_darah']        = $this->Darah_model->get_jenis_darah();
        $data['buffycoat']          = $this->Darah_model->get_buffycoat();
        $data['dokter']             = $this->Darah_model->get_dokter();
        $data['ruangan']            = $this->Darah_model->get_ruangan();
        $data['analis']             = $this->Darah_model->get_analis();
        $data['perawat']            = $this->Darah_model->get_perawat();
        $data['status']             = $this->Darah_model->get_status();
        $data['hasil_pemeriksaan']  = $this->Darah_model->get_hasil_pemeriksaan();
        $data['mayor']              = $this->Darah_model->get_mayor();
        $data['minor']              = $this->Darah_model->get_minor();
        $data['auto_kontrol']       = $this->Darah_model->get_auto_kontrol();
        $data['tujuan']             = $this->Darah_model->get_tujuan();

        $this->load->view('templates/header');
        $this->load->view('darah/form_darah', $data);
        $this->load->view('templates/footer');
    }

    /**
     * AJAX: Autofill pasien berdasarkan MR
     */
    public function autofill_pasien()
    {
        $this->require_level('1');

        $mr = $this->input->post('nomr');
        
        if (empty($mr)) {
            echo json_encode(['error' => 'MR tidak valid']);
            return;
        }

        $pasien = $this->Darah_model->get_pasien_by_mr($mr);
        
        if (!$pasien) {
            echo json_encode(['error' => 'Pasien tidak ditemukan']);
            return;
        }

        // Format tanggal lahir
        $tgl_lahir = $pasien['TGL_LAHIR'] ? date('d-m-Y', strtotime($pasien['TGL_LAHIR'])) : '';
        
        // Get riwayat alergi
        $riwayat = $this->Darah_model->get_riwayat_alergi_by_mr($mr);
        $status_darah = $this->Darah_model->get_status_darah_terakhir($mr);
        
        $response = array(
            'nama' => $pasien['NAMA'],
            'jk' => $pasien['jenis_kelamin'],
            'id_jenis_kelamin' => $pasien['id_jenis_kelamin'],
            'tgl_lahir' => $pasien['TGL_LAHIR'],
            'TANGGAL_LAHIR' => $tgl_lahir,
            'gol_darah' => $pasien['gol_darah'],
            'id_gol_darah' => $pasien['id_gol_darah'],
            'riwayattrans1' => $riwayat ? $riwayat['riwayat_alergi'] : '',
            'statusdarah' => $status_darah
        );

        echo json_encode($response);
    }

    /**
     * AJAX: Lookup nomor kantong darah
     */
    public function lookup_kantong()
    {
        $this->require_level('1');

        $nomer = $this->input->post('nomer');
        
        if (empty($nomer)) {
            echo json_encode(['error' => 'Nomor kantong tidak valid']);
            return;
        }

        $kantong = $this->Darah_model->get_kantong_by_nomor($nomer);
        
        if (!$kantong) {
            // Jika kantong tidak ditemukan, return dash untuk deskripsi golongan darah
            $response = array(
                'cc' => '',
                'exp' => '',
                'deskdar' => '-',
                'goldaroto' => ''
            );
        } else {
            $response = array(
                'cc' => $kantong['cc'] ?? '',
                'exp' => $kantong['exp'] ?? '',
                'deskdar' => $kantong['deskdar'] ?? '-',
                'goldaroto' => $kantong['goldaroto'] ?? ''
            );
        }

        echo json_encode($response);
    }

    /**
     * Submit form permintaan darah
     */
    public function submit()
    {
        $this->require_level('1');

        if ($this->input->server('REQUEST_METHOD') != 'POST') {
            redirect('darah/form');
        }

        // Sanitasi input
        $tgl_minta           = $this->input->post('tgl_minta');
        $mr                  = $this->input->post('mr');
        $nama                = htmlspecialchars(trim($this->input->post('nama')));
        $id_jenis_kelamin    = $this->input->post('id_jenis_kelamin');
        $tgl_lahir           = $this->input->post('tgl_lahir');
        $goldarah            = $this->input->post('selectgoldarah');
        $jenis_darah         = $this->input->post('jenis_darah');
        $buffycoat           = $this->input->post('buffycoat');
        $volume              = $this->input->post('volume');
        $tgl_diperlukan       = $this->input->post('tgl_diperlukan');
        $diagnosa            = $this->input->post('diagnosa');
        $alasan              = $this->input->post('alasan');
        $kadar_hb            = $this->input->post('kadar_hb');
        $trombosit           = $this->input->post('trombosit');
        $status              = $this->input->post('status');
        $dpjp                = $this->input->post('dpjp');
        $analis              = $this->input->post('analis');
        $ruangan             = $this->input->post('ruangan');
        $pengambil_darah     = htmlspecialchars(trim($this->input->post('pengambil_darah')));
        $hasil_pemeriksaan   = htmlspecialchars(trim($this->input->post('hasil_pemeriksaan')));
        $riwayattrans        = htmlspecialchars(trim($this->input->post('riwayattrans')));
        $perawat             = $this->input->post('perawat');
        $analis2             = $this->input->post('analis2');
        $auto_kontrol        = $this->input->post('auto_kontrol');
        $tujuan              = $this->input->post('tujuan');

        // Kantong 1-12
        $no_kantong = array();
        $volume_kantong = array();
        $exp_kantong = array();
        $tglkantong = array();
        $myr_kantong = array();
        $mnr_kantong = array();
        $tgl_serah = array();
        $petugas_serah = array();
        $petugas_terima = array();
        $goldaroto = array();

        for ($i = 1; $i <= 12; $i++) {
            $no_kantong[$i]      = $this->input->post('no_kantong_' . $i);
            $volume_kantong[$i]  = $this->input->post('volume_' . $i);
            $exp_kantong[$i]     = $this->input->post('exp_' . $i);
            
            $no_kantong_val = trim((string)$no_kantong[$i]);
            if ($no_kantong_val !== '') {
                $tglkantong_posted = $this->input->post('tglkantong' . $i);
                $tglkantong[$i] = ($tglkantong_posted !== null && $tglkantong_posted !== '' && $tglkantong_posted !== false)
                    ? $tglkantong_posted
                    : date('Y-m-d H:i:s');
            } else {
                $tglkantong[$i] = null;
            }
            
            $myr_kantong[$i]     = $this->input->post('myr_' . $i);
            $mnr_kantong[$i]     = $this->input->post('mnr_' . $i);
            $tgl_serah[$i]       = $this->input->post('tgl_serah_' . $i);
            $petugas_serah[$i]   = $this->input->post('petugas_serah_' . $i);
            $petugas_terima[$i]  = $this->input->post('petugas_terima_' . $i);
            $goldaroto[$i]       = $this->input->post('goldaroto' . $i);
        }

        // Generate nomor permintaan
        $nextNoTransaksi = $this->Darah_model->get_next_no_permintaan();
        $userLogin       = $_SESSION['login'];
        $tgl_creat       = date('Y-m-d H:i:s');

        // Mulai transaction
        $this->db->trans_start();

        // 1. INSERT pesan_darah
        $data_pesan = array(
            'no_permintaan'      => $nextNoTransaksi,
            'tgl_minta'          => $tgl_minta,
            'mr'                 => $mr,
            'nama'               => $nama,
            'id_jenis_kelamin'   => $id_jenis_kelamin,
            'tgl_lahir'          => $tgl_lahir,
            'goldarah'           => $goldarah,
            'jenis_darah'        => $jenis_darah,
            'buffycoat'          => $buffycoat,
            'volume'             => $volume,
            'tgl_diperlukan'     => $tgl_diperlukan,
            'diagnosa'           => $diagnosa,
            'alasan'             => $alasan,
            'kadar_hb'           => $kadar_hb,
            'trombosit'          => $trombosit,
            'status'             => $status,
            'dpjp'               => $dpjp,
            'analis'             => $analis,
            'ruangan'            => $ruangan,
            'no_kantong_1'       => $no_kantong[1],
            'no_kantong_2'       => $no_kantong[2],
            'no_kantong_3'       => $no_kantong[3],
            'no_kantong_4'       => $no_kantong[4],
            'no_kantong_5'       => $no_kantong[5],
            'no_kantong_6'       => $no_kantong[6],
            'no_kantong_7'       => $no_kantong[7],
            'no_kantong_8'       => $no_kantong[8],
            'no_kantong_9'       => $no_kantong[9],
            'no_kantong_10'      => $no_kantong[10],
            'no_kantong_11'      => $no_kantong[11],
            'no_kantong_12'      => $no_kantong[12],
            'nm_creat'           => $userLogin,
            'tgl_creat'          => $tgl_creat,
            'pengambil_darah'    => $pengambil_darah,
            'hasil_pemeriksaan'  => $hasil_pemeriksaan,
            'riwayattrans'       => $riwayattrans,
            'volume_1'           => $volume_kantong[1],
            'volume_2'           => $volume_kantong[2],
            'volume_3'           => $volume_kantong[3],
            'volume_4'           => $volume_kantong[4],
            'volume_5'           => $volume_kantong[5],
            'volume_6'           => $volume_kantong[6],
            'volume_7'           => $volume_kantong[7],
            'volume_8'           => $volume_kantong[8],
            'volume_9'           => $volume_kantong[9],
            'volume_10'          => $volume_kantong[10],
            'volume_11'          => $volume_kantong[11],
            'volume_12'          => $volume_kantong[12],
            'exp_1'              => $exp_kantong[1],
            'exp_2'              => $exp_kantong[2],
            'exp_3'              => $exp_kantong[3],
            'exp_4'              => $exp_kantong[4],
            'exp_5'              => $exp_kantong[5],
            'exp_6'              => $exp_kantong[6],
            'exp_7'              => $exp_kantong[7],
            'exp_8'              => $exp_kantong[8],
            'exp_9'              => $exp_kantong[9],
            'exp_10'             => $exp_kantong[10],
            'exp_11'             => $exp_kantong[11],
            'exp_12'             => $exp_kantong[12],
            'perawat'            => $perawat,
            'tglkantong1'        => $tglkantong[1],
            'tglkantong2'        => $tglkantong[2],
            'tglkantong3'        => $tglkantong[3],
            'tglkantong4'        => $tglkantong[4],
            'tglkantong5'        => $tglkantong[5],
            'tglkantong6'        => $tglkantong[6],
            'tglkantong7'        => $tglkantong[7],
            'tglkantong8'        => $tglkantong[8],
            'tglkantong9'        => $tglkantong[9],
            'tglkantong10'       => $tglkantong[10],
            'tglkantong11'       => $tglkantong[11],
            'tglkantong12'       => $tglkantong[12],
            'analis2'            => $analis2,
            'myr_1'              => $myr_kantong[1],
            'myr_2'              => $myr_kantong[2],
            'myr_3'              => $myr_kantong[3],
            'myr_4'              => $myr_kantong[4],
            'myr_5'              => $myr_kantong[5],
            'myr_6'              => $myr_kantong[6],
            'myr_7'              => $myr_kantong[7],
            'myr_8'              => $myr_kantong[8],
            'myr_9'              => $myr_kantong[9],
            'myr_10'             => $myr_kantong[10],
            'myr_11'             => $myr_kantong[11],
            'myr_12'             => $myr_kantong[12],
            'mnr_1'              => $mnr_kantong[1],
            'mnr_2'              => $mnr_kantong[2],
            'mnr_3'              => $mnr_kantong[3],
            'mnr_4'              => $mnr_kantong[4],
            'mnr_5'              => $mnr_kantong[5],
            'mnr_6'              => $mnr_kantong[6],
            'mnr_7'              => $mnr_kantong[7],
            'mnr_8'              => $mnr_kantong[8],
            'mnr_9'              => $mnr_kantong[9],
            'mnr_10'             => $mnr_kantong[10],
            'mnr_11'             => $mnr_kantong[11],
            'mnr_12'             => $mnr_kantong[12],
            'auto_kontrol'       => $auto_kontrol,
            'tujuan'             => $tujuan
        );

        $this->Darah_model->insert_pesan_darah($data_pesan);

        // 2. UPDATE pasien goldar (SKIPPED - pasien2 table doesn't have golongan_darah column)
        // $this->Darah_model->update_pasien_goldar($mr, $goldarah);

        // 3. INSERT kantong_luar
        // Get goldar description untuk masing-masing kantong
        $goldar_pasien = $this->Darah_model->get_pasien_by_mr($mr);
        $goldar_pasien_deskripsi = $goldar_pasien ? $goldar_pasien['gol_darah'] : '';

        $data_kantong = array(
            'no_permintaan'  => $nextNoTransaksi,
            'NOMOR_KL1'      => $no_kantong[1],
            'GOLDAR_KL1'     => !empty($goldaroto[1]) ? $goldaroto[1] : $goldar_pasien_deskripsi,
            'NOMOR_KL2'      => $no_kantong[2],
            'GOLDAR_KL2'     => !empty($goldaroto[2]) ? $goldaroto[2] : $goldar_pasien_deskripsi,
            'NOMOR_KL3'      => $no_kantong[3],
            'GOLDAR_KL3'     => !empty($goldaroto[3]) ? $goldaroto[3] : $goldar_pasien_deskripsi,
            'NOMOR_KL4'      => $no_kantong[4],
            'GOLDAR_KL4'     => !empty($goldaroto[4]) ? $goldaroto[4] : $goldar_pasien_deskripsi,
            'NOMOR_KL5'      => $no_kantong[5],
            'GOLDAR_KL5'     => !empty($goldaroto[5]) ? $goldaroto[5] : $goldar_pasien_deskripsi,
            'NOMOR_KL6'      => $no_kantong[6],
            'GOLDAR_KL6'     => !empty($goldaroto[6]) ? $goldaroto[6] : $goldar_pasien_deskripsi,
            'NOMOR_KL7'      => $no_kantong[7],
            'GOLDAR_KL7'     => !empty($goldaroto[7]) ? $goldaroto[7] : $goldar_pasien_deskripsi,
            'NOMOR_KL8'      => $no_kantong[8],
            'GOLDAR_KL8'     => !empty($goldaroto[8]) ? $goldaroto[8] : $goldar_pasien_deskripsi,
            'NOMOR_KL9'      => $no_kantong[9],
            'GOLDAR_KL9'     => !empty($goldaroto[9]) ? $goldaroto[9] : $goldar_pasien_deskripsi,
            'NOMOR_KL10'     => $no_kantong[10],
            'GOLDAR_KL10'    => !empty($goldaroto[10]) ? $goldaroto[10] : $goldar_pasien_deskripsi,
            'NOMOR_KL11'     => $no_kantong[11],
            'GOLDAR_KL11'    => !empty($goldaroto[11]) ? $goldaroto[11] : $goldar_pasien_deskripsi,
            'NOMOR_KL12'     => $no_kantong[12],
            'GOLDAR_KL12'    => !empty($goldaroto[12]) ? $goldaroto[12] : $goldar_pasien_deskripsi,
            'STATUS'         => 1
        );

        $this->Darah_model->insert_kantong_luar($data_kantong);

        // 4. INSERT petugas_serah_terima
        $data_serah_terima = array(
            'NO_PERMINTAAN'      => $nextNoTransaksi,
            'PETUGAS_SERAH'      => $petugas_serah[1],
            'PETUGAS_TERIMA'     => $petugas_terima[1],
            'TGL_SERAH'          => $tgl_serah[1],
            'PETUGAS_SERAH_2'    => $petugas_serah[2],
            'PETUGAS_TERIMA_2'   => $petugas_terima[2],
            'TGL_SERAH2'         => $tgl_serah[2],
            'PETUGAS_SERAH_3'    => $petugas_serah[3],
            'PETUGAS_TERIMA_3'   => $petugas_terima[3],
            'TGL_SERAH3'         => $tgl_serah[3],
            'PETUGAS_SERAH_4'    => $petugas_serah[4],
            'PETUGAS_TERIMA_4'   => $petugas_terima[4],
            'TGL_SERAH4'         => $tgl_serah[4],
            'PETUGAS_SERAH_5'    => $petugas_serah[5],
            'PETUGAS_TERIMA_5'   => $petugas_terima[5],
            'TGL_SERAH5'         => $tgl_serah[5],
            'PETUGAS_SERAH_6'    => $petugas_serah[6],
            'PETUGAS_TERIMA_6'   => $petugas_terima[6],
            'TGL_SERAH6'         => $tgl_serah[6],
            'PETUGAS_SERAH_7'    => $petugas_serah[7],
            'PETUGAS_TERIMA_7'   => $petugas_terima[7],
            'TGL_SERAH7'         => $tgl_serah[7],
            'PETUGAS_SERAH_8'    => $petugas_serah[8],
            'PETUGAS_TERIMA_8'   => $petugas_terima[8],
            'TGL_SERAH8'         => $tgl_serah[8],
            'PETUGAS_SERAH_9'    => $petugas_serah[9],
            'PETUGAS_TERIMA_9'   => $petugas_terima[9],
            'TGL_SERAH9'         => $tgl_serah[9],
            'PETUGAS_SERAH_10'   => $petugas_serah[10],
            'PETUGAS_TERIMA_10'  => $petugas_terima[10],
            'TGL_SERAH10'        => $tgl_serah[10],
            'PETUGAS_SERAH_11'   => $petugas_serah[11],
            'PETUGAS_TERIMA_11'  => $petugas_terima[11],
            'TGL_SERAH11'        => $tgl_serah[11],
            'PETUGAS_SERAH_12'   => $petugas_serah[12],
            'PETUGAS_TERIMA_12'  => $petugas_terima[12],
            'TGL_SERAH12'        => $tgl_serah[12]
        );

        $this->Darah_model->insert_petugas_serah_terima($data_serah_terima);

        // Complete transaction
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            // Rollback otomatis
            redirect('darah/form?error=1');
        } else {
            // Success
            redirect('darah/form?success=1');
        }
    }

    /**
     * Halaman Rawat Inap
     * Menggantikan placeholder view_dokter.
     * Data filter: ruangan & status (dipakai dropdown filter client-side).
     */
    public function view_dokter()
    {
        $this->require_level(array('1', '2'));
        $data['ruangan'] = $this->Darah_model->get_ruangan();
        $data['status']  = $this->Darah_model->get_status();

        $this->load->view('templates/header');
        $this->load->view('darah/rawat_inap', $data);
        $this->load->view('templates/footer', array('rawat_inap_js' => true));
    }

    /**
     * AJAX endpoint DataTables server-side untuk Rawat Inap
     * Query native: isi_view_ri.php (tgl_minta >= last 1 month)
     * Filter tambahan: ruangan, status, tgl_awal, tgl_akhir, no_permintaan, mr, nama
     */
    public function ajax_list_ri()
    {
        $this->require_level(array('1', '2'));
        $draw   = $this->input->post('draw');
        $start  = $this->input->post('start');
        $length = $this->input->post('length');
        $search_input = $this->input->post('search');
        $search = is_array($search_input) ? $search_input['value'] : '';

        // Filter tambahan dari client
        $filter_ruangan     = $this->input->post('ruangan');
        $filter_status      = $this->input->post('status');
        $filter_tgl_awal    = $this->input->post('tgl_awal');
        $filter_tgl_akhir   = $this->input->post('tgl_akhir');
        $filter_no_permintaan = $this->input->post('no_permintaan');
        $filter_mr          = $this->input->post('mr');
        $filter_nama        = $this->input->post('nama');

        // Order
        $order = $this->input->post('order');
        if (!is_array($order)) { $order = array(); }

        $list = $this->Darah_model->get_datatables_ri(
            $search, $order, $start, $length,
            $filter_ruangan, $filter_status,
            $filter_tgl_awal, $filter_tgl_akhir,
            $filter_no_permintaan, $filter_mr, $filter_nama
        );
        $filtered = $this->Darah_model->count_filtered_ri(
            $search,
            $filter_ruangan, $filter_status,
            $filter_tgl_awal, $filter_tgl_akhir,
            $filter_no_permintaan, $filter_mr, $filter_nama
        );
        $total = $this->Darah_model->count_all_ri();

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
            "draw"            => $draw,
            "recordsTotal"    => $total,
            "recordsFiltered" => $filtered,
            "data"            => $data,
        );

        echo json_encode($output);
    }
}
