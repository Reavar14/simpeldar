<?php defined('BASEPATH') OR exit('No direct script access allowed');
$is_limited = isset($is_limited) ? (bool)$is_limited : false;
?>

<!-- ============================================================
     CARD 4 - DATA KANTONG DARAH (1-12)
     ============================================================ -->
<div class="card mb-4">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
        <h5 class="card-title mb-0">
            <i class="fas fa-prescription-bottle-medical text-primary me-2"></i> Data Kantong Darah
        </h5>
        <div class="ms-auto">
            <span class="badge bg-primary fs-6" id="kantongFilledCount">0 / 12 terisi</span>
        </div>
    </div>
    <div class="card-body">
        <!-- Kantong 1-3 always visible -->
        <div class="row g-3" id="kantongUtama">
            <?php for ($i = 1; $i <= 3; $i++) { $this->load->view('request/components/edit_bag_item', array('i' => $i, 'is_limited' => $is_limited)); } ?>
        </div>

        <!-- Kantong 4-12 collapsible -->
        <div class="collapse" id="kantongLainnya">
            <div class="row g-3">
                <?php for ($i = 4; $i <= 12; $i++) { $this->load->view('request/components/edit_bag_item', array('i' => $i, 'is_limited' => $is_limited)); } ?>
            </div>
        </div>

        <div class="text-center mt-3">
            <button type="button"
                    class="btn btn-outline-primary"
                    data-bs-toggle="collapse"
                    data-bs-target="#kantongLainnya"
                    id="btnToggleKantong"
                    aria-expanded="false"
                    aria-controls="kantongLainnya">
                <i class="fas fa-chevron-down me-1"></i> Tampilkan Kantong 4–12
            </button>
        </div>
    </div>
</div>