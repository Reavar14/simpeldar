<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Dashboard_model');
    }

    public function index()
    {
        if ($this->session->userdata('level') === '2') {
            redirect('darah/view_dokter');
        }

        $this->require_level('1');

        $data['permintaan']     = $this->Dashboard_model->get_permintaan();
        $data['request']        = $this->Dashboard_model->get_request();
        $data['sedang_proses']  = $this->Dashboard_model->get_sedang_proses();
        $data['siap']           = $this->Dashboard_model->get_siap();
        $data['donor']          = $this->Dashboard_model->get_donor();
        $data['baru']           = $this->Dashboard_model->get_baru();
        $data['incompatible']   = $this->Dashboard_model->get_incompatible();
        $data['masasimpan']     = $this->Dashboard_model->get_masasimpan();
        $data['belumambil']     = $this->Dashboard_model->get_belumambil();
        $data['habis']          = $this->Dashboard_model->get_habis();
        $data['tidaklengkap']   = $this->Dashboard_model->get_tidaklengkap();
        $data['summary']        = $this->Dashboard_model->get_status_summary();

        $this->load->view('templates/header');
        $this->load->view('dashboard/index', $data);
        $this->load->view('templates/footer');
    }
}
