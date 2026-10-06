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
        $data = array();

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

        // Group per pasien (NO MR): total billing digabung
        $grouped = array();
        foreach ($list as $item) {
            $mr = $item['mr'];
            $jumlah = (int)$item['total_billing'];

            if (!isset($grouped[$mr])) {
                // TGL BILLING diambil dari record terbaru (list sudah terurut tgl DESC)
                $grouped[$mr] = array(
                    'mr'     => $item['mr'],
                    'nama'   => $item['nama'],
                    'tgl'    => $item['tgl_minta'],
                    'jumlah' => $jumlah,
                );
            } else {
                $grouped[$mr]['jumlah'] += $jumlah;
            }
        }

        foreach ($grouped as $g) {
            $data[] = array(
                $no++,
                $g['mr'],
                $g['nama'],
                $g['tgl'],
                $g['jumlah'],
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