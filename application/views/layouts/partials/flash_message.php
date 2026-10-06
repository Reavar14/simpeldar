<?php defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();
$success = $CI->session->flashdata('success');
$error = $CI->session->flashdata('error');
$info = $CI->session->flashdata('info');
?>

<?php if ($success): ?>
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
    <i class="fas fa-check-circle me-2"></i>
    <div><?php echo htmlspecialchars($success); ?></div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i>
    <div><?php echo htmlspecialchars($error); ?></div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<?php if ($info): ?>
<div class="alert alert-info alert-dismissible fade show d-flex align-items-center" role="alert">
    <i class="fas fa-info-circle me-2"></i>
    <div><?php echo htmlspecialchars($info); ?></div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>