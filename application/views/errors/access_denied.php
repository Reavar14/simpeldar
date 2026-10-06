<?php defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();
$uname = $CI->session->userdata('uname');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Akses Ditolak - SIMPELDAR</title>
    <link rel="shortcut icon" href="<?php echo base_url('assets/logo/bld.png'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/app.css'); ?>" rel="stylesheet">
</head>
<body>
<div class="app-wrapper">
    <main class="content-area">
        <div class="page-content">
            <div class="row justify-content-center align-items-center" style="min-height: 60vh">
                <div class="col-12 col-md-6 col-lg-5">
                    <div class="card text-center border-danger">
                        <div class="card-body py-5">
                            <div class="mb-4">
                                <i class="fas fa-lock text-danger" style="font-size: 3.5rem"></i>
                            </div>
                            <h3 class="card-title mb-2">Akses Ditolak</h3>
                            <p class="text-muted mb-1">Anda tidak memiliki izin untuk membuka halaman ini.</p>
                            <p class="text-muted small">
                                Pengguna: <strong><?php echo htmlspecialchars($uname ? $uname : '-'); ?></strong>
                            </p>
                            <a href="<?php echo base_url('index.php/dashboard'); ?>" class="btn btn-primary mt-3">
                                <i class="fas fa-home me-1"></i> Kembali ke Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>