<?php defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();
$userName = $CI->session->userdata('uname');
$level = $CI->session->userdata('level');

$levelNames = array('1' => 'Administrator', '2' => 'Petugas Rawat Inap', '3' => 'Petugas', '4' => 'Dokter');
$levelName = $levelNames[$level] ?? 'Pengguna';
?>

<nav class="app-navbar">
    <div class="navbar-inner">
        <div class="navbar-left">
            <button class="btn btn-link btn-sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
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
                <a class="nav-link nav-notification" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
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
                        <div>
                            <h6 class="mb-0"><?php echo htmlspecialchars($userName); ?></h6>
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