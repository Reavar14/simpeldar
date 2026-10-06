<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cetakan extends Admin_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Darah_model');
    }

    /**
     * Halaman Cetakan
     * Migrasi dari admin/form_cetakan.php (native)
     *
     * 2 Output buttons:
     * - Laporan Lengkap → RequestController::print_laporan_lengkap
     * - Rekap Analis    → RequestController::print_rekap_analis
     *
     * Native native JavaBridge+JasperReports (remote DB) DITINGGALKAN.
     * CI3 pakai HTML view + browser print (window.print).
     */
    public function index()
    {
        // Filter values (sticky via GET)
        $data['filter'] = array(
            'tgl_awal'      => $this->input->get('tgl_awal'),
            'tgl_akhir'     => $this->input->get('tgl_akhir'),
            'analis'        => $this->input->get('analis'),
            'data_entry'    => $this->input->get('data_entry'),
            'dokter_konsul' => $this->input->get('dokter_konsul'),
            'statusp'       => $this->input->get('statusp'),
        );

        // Dropdown options
        $data['analis_list']       = $this->Darah_model->get_analis();
        $data['data_entry_list']   = $this->Darah_model->get_data_entry(); // usrmst STATUS=1
        $data['dokter_list']       = $this->Darah_model->get_dokter();
        $data['status_list']       = $this->Darah_model->get_status();

        $this->load->view('templates/header');
        $this->load->view('cetakan/index', $data);
        $this->load->view('templates/footer');
    }
}