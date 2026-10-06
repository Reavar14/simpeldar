<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base Controller with authentication check.
 * All application controllers should extend this (or Admin_Controller).
 */
class MY_Controller extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('uname')) {
            redirect('auth');
        }
    }

    /**
     * Deny access: 403 page for web, JSON 403 for AJAX.
     *
     * @return void
     */
    protected function deny_access()
    {
        if ($this->input->is_ajax_request()) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(403);
            echo json_encode(array('success' => false, 'message' => 'Akses ditolak'));
            exit;
        }

        $this->output->set_status_header(403);
        echo $this->load->view('errors/access_denied', null, true);
        exit;
    }

    /**
     * Check if current user has required level.
     * Show 403 access denied if not authorized.
     *
     * @param array|string $allowed_levels
     * @return void
     */
    protected function require_level($allowed_levels)
    {
        $current = $this->session->userdata('level');
        $levels = is_array($allowed_levels) ? $allowed_levels : array($allowed_levels);

        if (!in_array($current, $levels, true)) {
            $this->deny_access();
        }
    }

    /**
     * Check if current user is admin (level 1).
     *
     * @return bool
     */
    protected function is_admin()
    {
        return $this->session->userdata('level') === '1';
    }
}

/**
 * Admin-only Controller.
 * Only level 1 (Administrator) can access controllers extending this.
 * Shows 403 access denied for non-admin users.
 */
class Admin_Controller extends MY_Controller {

    public function __construct()
    {
        parent::__construct();

        if (!$this->is_admin()) {
            $this->deny_access();
        }
    }
}