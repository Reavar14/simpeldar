<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();
if(!$CI->session->userdata('uname')){
    header('Location: ' . base_url('index.php/auth'));
    exit;
}
// Prevent browser caching of authenticated pages
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Content-Type: text/html; charset=UTF-8');

$userName = $CI->session->userdata('uname');
$level    = $CI->session->userdata('level');

$levelNames = array(
    '1' => 'Administrator',
    '2' => 'Petugas Rawat Inap',
    '3' => 'Petugas',
    '4' => 'Dokter'
);
$levelName = isset($levelNames[$level]) ? $levelNames[$level] : 'Pengguna';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIMPELDAR - Sistem Informasi Manajemen Pelayanan Darah</title>
    <meta name="description" content="Sistem Informasi Manajemen Pelayanan Darah">

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo base_url('assets/logo/bld.png'); ?>">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Flatpickr -->
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
    <!-- DataTables Bootstrap 5 -->
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- DataTables Responsive -->
    <link href="https://cdn.datatables.net/responsive/3.0.1/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <!-- Select2 Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css" rel="stylesheet">
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
    <nav class="app-navbar">
        <div class="navbar-inner">
            <div class="navbar-left">
                <button class="btn btn-link btn-sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar" title="Toggle Sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <a href="<?php echo base_url('index.php/dashboard'); ?>" class="navbar-brand">
                    <img src="<?php echo base_url('assets/logo/bld.png'); ?>" alt="Logo" class="navbar-logo">
                    <span class="brand-text">SIMPELDAR</span>
                </a>
            </div>

            <div class="navbar-right">
                <!-- Notifications -->
                <div class="nav-item dropdown">
                    <a class="nav-link nav-notification" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                        <i class="fas fa-bell"></i>
                        <span class="badge bg-danger notification-badge">0</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end notification-menu">
                        <div class="notification-header">
                            <h6>Notifikasi</h6>
                            <span class="badge bg-secondary">0 baru</span>
                        </div>
                        <div class="notification-body">
                            <p class="text-muted text-center py-3 mb-0">Tidak ada notifikasi</p>
                        </div>
                    </div>
                </div>

                <!-- User Dropdown -->
                <div class="nav-item dropdown">
                    <a class="nav-link nav-user" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="user-info">
                            <span class="user-name"><?php echo htmlspecialchars($userName); ?></span>
                            <span class="user-level"><?php echo htmlspecialchars($levelName); ?></span>
                        </div>
                        <i class="fas fa-chevron-down user-chevron"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end user-menu">
                        <div class="user-menu-header">
                            <div class="user-avatar-lg">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="min-w-0">
                                <h6 class="mb-0 text-truncate"><?php echo htmlspecialchars($userName); ?></h6>
                                <small class="text-muted"><?php echo htmlspecialchars($levelName); ?></small>
                            </div>
                        </div>
                        <hr class="dropdown-divider">
                        <a class="dropdown-item text-danger" href="<?php echo base_url('index.php/auth/logout'); ?>">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <?php $this->load->view('layouts/sidebar'); ?>

    <!-- Main Content Area -->
    <div class="app-container">
    <main class="content-area">
        <!-- Flash Messages -->
        <?php $this->load->view('layouts/partials/flash_message'); ?>

        <!-- Page Content -->
        <div class="page-content">
