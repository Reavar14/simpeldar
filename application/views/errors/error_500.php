<?php defined('BASEPATH') OR exit('No direct script access allowed');
$heading = isset($heading) ? $heading : '500 - Terjadi Kesalahan Sistem';
$message = isset($message) ? $message : 'Maaf, terjadi kesalahan internal. Tim teknis telah diberitahu.';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars((string)$heading); ?> - SIMPELDAR</title>
    <link rel="shortcut icon" href="assets/logo/bld.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 60vh">
        <div class="col-12 col-md-6 col-lg-5">
            <div class="card text-center border-danger shadow-sm">
                <div class="card-body py-5">
                    <div class="mb-4">
                        <i class="fas fa-exclamation-triangle text-danger" style="font-size: 3.5rem"></i>
                    </div>
                    <h3 class="card-title mb-2">500 - Terjadi Kesalahan Sistem</h3>
                    <p class="text-muted mb-1">Maaf, terjadi kesalahan internal. Silakan coba beberapa saat lagi.</p>
                    <a href="index.php/dashboard" class="btn btn-primary mt-3">
                        <i class="fas fa-home me-1"></i> Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>