<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- DATA KANTONG DARAH -->
<div class="native-section-title d-flex align-items-center justify-content-between">
    <span>
        DATA KANTONG DARAH
    </span>
</div>
<div class="kantong-table-wrapper">
    <table class="kantong-table">
        <thead>
            <tr>
                <th>No. Kantong</th>
                <th>Gol. Darah</th>
                <th>Exp Date</th>
                <th>Tgl. Input</th>
                <th>Volume</th>
                <th>MAYOR</th>
                <th>MINOR</th>
                <th>Tgl. Serah</th>
                <th>Petugas Serah</th>
                <th>Petugas Terima</th>
            </tr>
        </thead>
        <tbody>
            <?php for ($i = 1; $i <= 12; $i++): ?>
                <?php
                $this->load->view(
                    'darah/components/bag_item',
                    array('i' => $i)
                );
                ?>
            <?php endfor; ?>
        </tbody>
    </table>
</div>