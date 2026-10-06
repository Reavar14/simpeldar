<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan extends Admin_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Laporan_model');
        $this->load->model('Darah_model'); // untuk dropdown analis, status
    }

    /**
     * Halaman Laporan
     * Tab: Pelayanan | Jumlah Permintaan | Jumlah Jenis | Billing
     */
    public function index()
    {
        // Filter values (sticky via GET)
        $data['filter'] = array(
            // Tab 1
            'pelayanan_tgl_awal'  => $this->input->get('pelayanan_tgl_awal'),
            'pelayanan_tgl_akhir' => $this->input->get('pelayanan_tgl_akhir'),
            'pelayanan_analis'    => $this->input->get('pelayanan_analis'),
            // Tab 2
            'permintaan_tgl_awal'  => $this->input->get('permintaan_tgl_awal'),
            'permintaan_tgl_akhir' => $this->input->get('permintaan_tgl_akhir'),
            'permintaan_status'    => $this->input->get('permintaan_status'),
            // Tab 3
            'jenis_tgl_awal'  => $this->input->get('jenis_tgl_awal'),
            'jenis_tgl_akhir' => $this->input->get('jenis_tgl_akhir'),
            // Tab 4
            'billing_tgl_awal'  => $this->input->get('billing_tgl_awal'),
            'billing_tgl_akhir' => $this->input->get('billing_tgl_akhir'),
        );

        // Dropdown options
        $data['analis_list']  = $this->Darah_model->get_analis();
        $data['status_list']  = $this->Darah_model->get_status();

        $this->load->view('templates/header');
        $this->load->view('laporan/index', $data);
        $this->load->view('templates/footer');
    }

    /**
     * AJAX: Laporan Pelayanan (Tab 1)
     */
    public function ajax_pelayanan()
    {
        $tgl_awal  = $this->input->post('tgl_awal');
        $tgl_akhir = $this->input->post('tgl_akhir');
        $analis    = $this->input->post('analis');

        if (empty($tgl_awal) || empty($tgl_akhir)) {
            echo json_encode(array('data' => array(), 'recordsTotal' => 0, 'recordsFiltered' => 0, 'jml_tindakan' => 0));
            return;
        }

        $data = $this->Laporan_model->get_laporan_pelayanan($tgl_awal, $tgl_akhir, $analis);
        $total = $this->Laporan_model->count_pelayanan($tgl_awal, $tgl_akhir, $analis);

        $output = array();
        $no = 1;
        foreach ($data as $row) {
            $output[] = array(
                $no++,
                $row['no_permintaan'],
                $row['tgl_minta'],
                $row['mr'],
                $row['nama'],
                $row['tgl_diperlukan'],
                $row['ruangan'],
                $row['nama_analis'],
                $row['status']
            );
        }

        echo json_encode(array(
            'data' => $output,
            'recordsTotal'    => $total,
            'recordsFiltered' => $total,
            'jml_tindakan'    => $total
        ));
    }

    /**
     * AJAX: Laporan Jumlah Permintaan Darah (Tab 2)
     */
    public function ajax_jumlah_permintaan()
    {
        $tgl_awal  = $this->input->post('tgl_awal');
        $tgl_akhir = $this->input->post('tgl_akhir');
        $status    = $this->input->post('status');

        if (empty($tgl_awal) || empty($tgl_akhir)) {
            echo json_encode(array('data' => array(), 'recordsTotal' => 0, 'recordsFiltered' => 0));
            return;
        }

        $data = $this->Laporan_model->get_jumlah_permintaan($tgl_awal, $tgl_akhir, $status);
        $total = $this->Laporan_model->count_jumlah_permintaan($tgl_awal, $tgl_akhir, $status);

        $output = array();
        $no = 1;
        foreach ($data as $row) {
            $output[] = array($no++, $row['status'], $row['jumlah']);
        }

        echo json_encode(array(
            'data' => $output,
            'recordsTotal'    => $total,
            'recordsFiltered' => $total
        ));
    }

    /**
     * AJAX: Laporan Jumlah Jenis Permintaan (Tab 3)
     */
    public function ajax_jumlah_jenis()
    {
        $tgl_awal  = $this->input->post('tgl_awal');
        $tgl_akhir = $this->input->post('tgl_akhir');

        if (empty($tgl_awal) || empty($tgl_akhir)) {
            echo json_encode(array('data' => array(), 'recordsTotal' => 0, 'recordsFiltered' => 0));
            return;
        }

        $data = $this->Laporan_model->get_jumlah_jenis($tgl_awal, $tgl_akhir);
        $total = $this->Laporan_model->count_jumlah_jenis($tgl_awal, $tgl_akhir);

        $output = array();
        $no = 1;
        foreach ($data as $row) {
            $output[] = array($no++, $row['nama_jenis'], $row['jumlah']);
        }

        echo json_encode(array(
            'data' => $output,
            'recordsTotal'    => $total,
            'recordsFiltered' => $total
        ));
    }

    /**
     * AJAX: Laporan Billing Kantong (Tab 4)
     */
    public function ajax_billing()
    {
        $tgl_awal  = $this->input->post('tgl_awal');
        $tgl_akhir = $this->input->post('tgl_akhir');

        if (empty($tgl_awal) || empty($tgl_akhir)) {
            echo json_encode(array('data' => array(), 'recordsTotal' => 0, 'recordsFiltered' => 0, 'sum_kantong' => 0, 'sum_billed' => 0));
            return;
        }

        $data = $this->Laporan_model->get_billing_kantong($tgl_awal, $tgl_akhir);
        $total = $this->Laporan_model->count_billing_kantong($tgl_awal, $tgl_akhir);
        $sum = $this->Laporan_model->sum_billing_kantong($tgl_awal, $tgl_akhir);

        $output = array();
        $no = 1;
        foreach ($data as $row) {
            $output[] = array(
                $no++,
                $row['no_permintaan'],
                $row['tgl_minta'],
                $row['mr'],
                $row['nama'],
                $row['total_kantong'],
                $row['total_billing']
            );
        }

        echo json_encode(array(
            'data' => $output,
            'recordsTotal'    => $total,
            'recordsFiltered' => $total,
            'sum_kantong'     => $sum['kantong'],
            'sum_billed'      => $sum['billed']
        ));
    }
}