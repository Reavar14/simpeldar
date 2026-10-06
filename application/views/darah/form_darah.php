<?php
/**
 * FORM PERMINTAAN DARAH
 *
 * View lama (monolith 64KB) telah dipecah menjadi struktur modular:
 *   darah/form.php                    -> entry point baru
 *   darah/components/*.php            -> card-card
 *   assets/js/form-darah.js           -> logic
 *
 * Controller (Darah::form) tetap me-load file ini tanpa perubahan,
 * sehingga file ini hanya mendelegasikan ke struktur baru.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->view('darah/form');
