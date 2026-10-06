<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Riwayat extends Admin_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Riwayat_model');
    }

    /**
     * Halaman Riwayat Pasien
     * Migrasi dari admin/riwayat_pasien.php (native)
     */
    public function index()
    {
        // Filter dari GET (sticky form)
        $data['filter'] = array(
            'tgl_awal'  => $this->input->get('tgl_awal'),
            'tgl_akhir' => $this->input->get('tgl_akhir'),
            'mr'        => $this->input->get('mr'),
        );

        $this->load->view('templates/header');
        $this->load->view('riwayat/index', $data);
        $this->load->view('templates/footer');
    }

    /**
     * AJAX endpoint DataTables server-side untuk Riwayat Pasien
     *
     * Filter tambahan: tgl_awal, tgl_akhir, mr
     */
    public function ajax_list()
    {
        $draw   = $this->input->post('draw');
        $start  = $this->input->post('start');
        $length = $this->input->post('length');
        $search_input = $this->input->post('search');
        $search = is_array($search_input) ? $search_input['value'] : '';

        $order = $this->input->post('order');
        if (!is_array($order)) {
            $order = array();
        }

        // Filter dari client
        $tgl_awal  = $this->input->post('tgl_awal');
        $tgl_akhir = $this->input->post('tgl_akhir');
        if (empty($tgl_awal)) {
            $tgl_awal = date('Y-m-d', strtotime('-30 days'));
        }
        if (empty($tgl_akhir)) {
            $tgl_akhir = date('Y-m-d');
        }
        $mr        = $this->input->post('mr');

        $list     = $this->Riwayat_model->get_datatables($tgl_awal, $tgl_akhir, $mr, $search, $order, $start, $length);
        $filtered = $this->Riwayat_model->count_filtered($tgl_awal, $tgl_akhir, $mr, $search);
        $total    = $this->Riwayat_model->count_all($tgl_awal, $tgl_akhir, $mr);
        $kunjungan = $this->Riwayat_model->count_kunjungan($tgl_awal, $tgl_akhir, $mr);

        $data = array();
        $no = $start + 1;

        foreach ($list as $item) {
            $row = array();
            $row[] = $no++;
            $row[] = $item['no_permintaan'];
            $row[] = $item['mr'];
            $row[] = $item['nama'];
            $row[] = $item['tgl_minta'];
            $row[] = $item['ruangan'];
            $row[] = $item['tujuan'];
            $row[] = $item['goldar'];
            $row[] = $item['jenis_darah'];
            $row[] = $item['status_proses'];
            $row[] = $item['no_permintaan']; // anchor untuk actions (kolom 11)
            $data[] = $row;
        }

        $output = array(
            "draw"            => $draw,
            "recordsTotal"    => $total,
            "recordsFiltered" => $filtered,
            "kunjungan"       => $kunjungan,
            "data"            => $data,
        );

        echo json_encode($output);
    }
}