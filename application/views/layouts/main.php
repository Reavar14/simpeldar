<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIMPELDAR - Sistem Informasi Manajemen Pelayanan Darah</title>
    <meta name="description" content="Sistem Informasi Manajemen Pelayanan Darah RS Kanker Dharmais">

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo base_url('assets/logo/bld.png'); ?>">

    <!-- Bootstrap 5.3 CSS -->
    <link href="<?php echo base_url('assets/vendor/bootstrap/5.3.2/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Flatpickr -->
    <link href="<?php echo base_url('assets/vendor/flatpickr/css/flatpickr.min.css'); ?>" rel="stylesheet">
    <!-- DataTables Bootstrap 5 -->
    <link href="<?php echo base_url('assets/vendor/datatables/css/dataTables.bootstrap5.min.css'); ?>" rel="stylesheet">
    <!-- DataTables Responsive -->
    <link href="<?php echo base_url('assets/vendor/datatables/css/responsive.bootstrap5.min.css'); ?>" rel="stylesheet">
    <!-- Select2 Bootstrap 5 -->
    <link href="<?php echo base_url('assets/vendor/select2/css/select2.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/vendor/select2/css/select2-bootstrap-5-theme.min.css'); ?>" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="<?php echo base_url('assets/vendor/sweetalert/css/sweetalert2.min.css'); ?>" rel="stylesheet">
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom App CSS -->
    <link href="<?php echo base_url('assets/css/app.css'); ?>" rel="stylesheet">

    <!-- CSRF Token (for AJAX; cookie is HttpOnly, never read by JS) -->
    <meta name="csrf-token-name" content="<?= $this->security->get_csrf_token_name(); ?>">
    <meta name="csrf-token-hash" content="<?= $this->security->get_csrf_hash(); ?>">
    <meta name="csrf-refresh-url" content="<?php echo base_url('index.php/auth/csrf'); ?>">
</head>
<body>
<div class="app-wrapper">

    <!-- Navbar -->
    <?php $this->load->view('layouts/navbar'); ?>

    <!-- Sidebar -->
    <?php $this->load->view('layouts/sidebar'); ?>

    <!-- Main Content Area -->
    <div class="app-container">
        <main class="content-area">
            <!-- Flash Messages -->
            <?php $this->load->view('layouts/partials/flash_message'); ?>

            <!-- Page Content -->
            <div class="page-content">
                <?php echo $content ?? ''; ?>
            </div>
        </main>

        <!-- Footer -->
        <?php $this->load->view('layouts/footer'); ?>
    </div>

</div>

<!-- jQuery 3.7 -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap 5 Bundle (includes Popper) -->
<script src="<?php echo base_url('assets/vendor/bootstrap/5.3.2/js/bootstrap.bundle.min.js'); ?>"></script>
<!-- Flatpickr -->
<script src="<?php echo base_url('assets/vendor/flatpickr/js/flatpickr.min.js'); ?>"></script>
<!-- DataTables -->
<script src="<?php echo base_url('assets/vendor/datatables/js/dataTables.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/vendor/datatables/js/dataTables.bootstrap5.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/vendor/datatables/js/dataTables.responsive.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/vendor/datatables/js/responsive.bootstrap5.min.js'); ?>"></script>
<!-- Select2 -->
<script src="<?php echo base_url('assets/vendor/select2/js/select2.min.js'); ?>"></script>
<!-- SweetAlert2 -->
<script src="<?php echo base_url('assets/vendor/sweetalert/js/sweetalert2.all.min.js'); ?>"></script>
<!-- Custom App JS -->
<script src="<?php echo base_url('assets/js/app.js'); ?>"></script>

</body>
</html>
