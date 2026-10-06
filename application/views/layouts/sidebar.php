<?php defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();
$level = $CI->session->userdata('level');

function nav_active_new($targets) {
    $CI =& get_instance();
    $c = $CI->uri->segment(1);
    $m = $CI->uri->segment(2);
    $current = $m ? ($c.'/'.$m) : $c;
    $targets = (array)$targets;
    return in_array($current, $targets, true) ? ' active' : '';
}

$base = 'index.php/';
?>



<aside class="app-sidebar" id="appSidebar">
    <div class="sidebar-scroll">
        <nav class="sidebar-nav">
            <!-- DASHBOARD (Level 1 only) -->
            <?php if ($level == '1') { ?>
            <div class="nav-section-title">DASHBOARD</div>
            <div class="nav-section">
                <a href="<?php echo base_url($base.'dashboard'); ?>" class="nav-item<?php echo nav_active_new('dashboard'); ?>">
                    <i class="fas fa-home nav-icon"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </div>
            <?php } ?>

            <?php if ($level == '1') { ?>
            <!-- TRANSAKSI -->
            <div class="nav-section-title">TRANSAKSI</div>
            <div class="nav-section">
                <a href="<?php echo base_url($base.'darah/form'); ?>" class="nav-item<?php echo nav_active_new('darah/form'); ?>">
                    <i class="fas fa-tint nav-icon"></i>
                    <span class="nav-text">Form Darah</span>
                </a>
                <a href="<?php echo base_url($base.'requestcontroller/list'); ?>" class="nav-item<?php echo nav_active_new(array('requestcontroller/list', 'requestcontroller/detail', 'requestcontroller/edit')); ?>">
                    <i class="fas fa-clipboard-list nav-icon"></i>
                    <span class="nav-text">View Proses</span>
                </a>
                <a href="<?php echo base_url($base.'darah/view_dokter'); ?>" class="nav-item<?php echo nav_active_new(array('darah/view_dokter')); ?>">
                    <i class="fas fa-hospital nav-icon"></i>
                    <span class="nav-text">View Proses Rawat Inap</span>
                </a>
            </div>

            <!-- LAPORAN -->
            <div class="nav-section-title">LAPORAN</div>
            <div class="nav-section">
                <a href="<?php echo base_url($base.'laporan'); ?>" class="nav-item<?php echo nav_active_new('laporan'); ?>">
                    <i class="fas fa-file-medical nav-icon"></i>
                    <span class="nav-text">Laporan</span>
                </a>
                <a href="<?php echo base_url($base.'cetakan'); ?>" class="nav-item<?php echo nav_active_new('cetakan'); ?>">
                    <i class="fas fa-print nav-icon"></i>
                    <span class="nav-text">Cetakan</span>
                </a>
            </div>

            <!-- DATA -->
            <div class="nav-section-title">DATA</div>
            <div class="nav-section">
                <a href="<?php echo base_url($base.'riwayat'); ?>" class="nav-item<?php echo nav_active_new('riwayat'); ?>">
                    <i class="fas fa-folder-open nav-icon"></i>
                    <span class="nav-text">Riwayat Pasien</span>
                </a>
                <a href="<?php echo base_url($base.'billing'); ?>" class="nav-item<?php echo nav_active_new('billing'); ?>">
                    <i class="fas fa-file-invoice-dollar nav-icon"></i>
                    <span class="nav-text">Billing</span>
                </a>
            </div>
            <?php } elseif ($level == '2') { ?>
            <!-- TRANSAKSI -->
            <div class="nav-section-title">TRANSAKSI</div>
            <div class="nav-section">
                <a href="<?php echo base_url($base.'darah/view_dokter'); ?>" class="nav-item<?php echo nav_active_new('darah/view_dokter'); ?>">
                    <i class="fas fa-hospital nav-icon"></i>
                    <span class="nav-text">View Proses Rawat Inap</span>
                </a>
            </div>
            <?php } elseif ($level == '3') { ?>
            <div class="nav-section-title">TRANSAKSI</div>
            <div class="nav-section">
                <a href="<?php echo base_url($base.'dashboard'); ?>" class="nav-item<?php echo nav_active_new('dashboard'); ?>">
                    <i class="fas fa-home nav-icon"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
                <a href="<?php echo base_url($base.'darah/view_dokter'); ?>" class="nav-item<?php echo nav_active_new('darah/view_dokter'); ?>">
                    <i class="fas fa-hospital nav-icon"></i>
                    <span class="nav-text">View Proses Rawat Inap</span>
                </a>
            </div>
            <?php } elseif ($level == '4') { ?>
            <div class="nav-section-title">TRANSAKSI</div>
            <div class="nav-section">
                <a href="<?php echo base_url($base.'dashboard'); ?>" class="nav-item<?php echo nav_active_new('dashboard'); ?>">
                    <i class="fas fa-home nav-icon"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
                <a href="<?php echo base_url($base.'darah/view_dokter'); ?>" class="nav-item<?php echo nav_active_new('darah/view_dokter'); ?>">
                    <i class="fas fa-hospital nav-icon"></i>
                    <span class="nav-text">View Proses Rawat Inap</span>
                </a>
            </div>
            <?php } ?>

            <!-- ADMIN -->
            <div class="nav-section-title">ADMIN</div>
            <div class="nav-section">
                <a href="<?php echo base_url($base.'auth/logout'); ?>" class="nav-item nav-logout">
                    <i class="fas fa-sign-out-alt nav-icon"></i>
                    <span class="nav-text">Logout</span>
                </a>
            </div>
        </nav>
    </div>
</aside>

<div class="sidebar-overlay" id="sidebarOverlay"></div>