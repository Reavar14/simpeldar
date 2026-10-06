<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
    }

    public function index()
    {
        $this->load->view('auth/login');
    }

    public function login()
    {
        $uname    = isset($_POST['uname']) ? $_POST['uname'] : '';
        $password = isset($_POST['pass']) ? $_POST['pass'] : '';

        $data = $this->User_model->check_login($uname, $password);

        if ($data && $password == $data['PASSWORD'])
        {
            // menyimpan username dan level ke dalam session
            $_SESSION['level'] = $data['LEVEL'];
            $_SESSION['uname'] = $data['USRNM'];
            $_SESSION['login'] = $data['USRID'];

            if ($_SESSION['level'] === '2')
            {
                header('Location: ' . base_url('index.php/darah/view_dokter'));
            }
            else
            {
                header('Location: ' . base_url('index.php/dashboard'));
            }
            exit();
        }
        else
        {
            echo "<script>
                alert('User atau Password salah!');
                window.location.href = '" . base_url('index.php/auth') . "';
            </script>";
        }
    }

    public function logout()
    {
        session_start();
        session_destroy();
        header('Location: ' . base_url('index.php/auth'));
        exit();
    }

    /**
     * AJAX: Provide fresh CSRF token name + hash.
     * GET /auth/csrf -> { csrf_token_name, csrf_hash }
     * Used by app.js refreshCsrfToken() when an AJAX call receives HTTP 403.
     * GET requests do not regenerate the token, so the returned hash always
     * matches the current csrf_cookie.
     */
    public function csrf()
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array(
            'csrf_token_name' => $this->security->get_csrf_token_name(),
            'csrf_hash'       => $this->security->get_csrf_hash()
        ));
    }
}
