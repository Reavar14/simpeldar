<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Billing extends Admin_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Billing_model');
    }

    public function index()
    {
        $data['filter'] = array(
            'tgl_awal'      => $this->input->get('tgl_awal'),
            'tgl_akhir'     => $this->input->get('tgl_akhir'),
            'ruangan'       => $this->input->get('ruangan'),
            'status_billing'=> $this->input->get('status_billing'),
        );

        $data['ruangan_list']       = $this->Billing_model->get_ruangan();
        $data['status_list']        = $this->Billing_model->get_status_billing();

        $this->load->view('templates/header');
        $this->load->view('billing/index', $data);
        $this->load->view('templates/footer');
    }

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

        $tgl_awal       = $this->input->post('tgl_awal');
        $tgl_akhir      = $this->input->post('tgl_akhir');
        if (empty($tgl_awal)) {
            $tgl_awal = date('Y-m-d', strtotime('-30 days'));
        }
        if (empty($tgl_akhir)) {
            $tgl_akhir = date('Y-m-d');
        }
        $ruangan        = $this->input->post('ruangan');
        $status_billing = $this->input->post('status_billing');

        $list     = $this->Billing_model->get_datatables($tgl_awal, $tgl_akhir, $ruangan, $status_billing, $search, $order, $start, $length);
        $filtered = $this->Billing_model->count_filtered($tgl_awal, $tgl_akhir, $ruangan, $status_billing, $search);
        $total    = $this->Billing_model->count_all($tgl_awal, $tgl_akhir, $ruangan);

        $data = array();
        $no = $start + 1;

        foreach ($list as $item) {
            $data[] = array(
                $no++,
                $item['no_permintaan'],
                $item['mr'],
                $item['nama'],
                $item['tgl_minta'],
                $item['ruangan'],
                $item['jenis_darah'],
                (int)$item['total_kantong'],
                $item['status_billing'],
                $item['no_permintaan'],
            );
        }

        $output = array(
            "draw" => $draw,
            "recordsTotal" => $total,
            "recordsFiltered" => $filtered,
            "data" => $data,
        );

        echo json_encode($output);
    }
}