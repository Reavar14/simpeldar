<?php defined('BASEPATH') OR exit('No direct script access allowed');

$i = isset($i) ? (int)$i : 1;
?>
<tr>
    <!-- NO. KANTONG -->
    <td>
        <input type="text" name="no_kantong_<?php echo $i; ?>" id="nomer<?php echo $i; ?>" class="form-control form-control-sm"
               data-kantong-lookup="<?php echo $i; ?>" placeholder="No. Kantong <?php echo $i; ?>" autocomplete="off">
    </td>
    <!-- GOL DARAH -->
    <td>
        <input type="text" id="goldaroto<?php echo $i; ?>" name="goldaroto<?php echo $i; ?>"
               class="form-control form-control-sm" readonly placeholder="Gol">
        <span id="goldarah<?php echo $i; ?>"class="d-none"></span>
    </td>
    <!-- EXPIRED DATE -->
    <td>
        <input type="text" id="exp<?php echo $i; ?>" name="exp_<?php echo $i; ?>"
               class="form-control form-control-sm datepicker" placeholder="Exp Date">
    </td>
    <!-- TANGGAL INPUT KANTONG -->
    <td>
        <input type="text" name="tglkantong<?php echo $i; ?>" id="tglkantong<?php echo $i; ?>"
               class="form-control form-control-sm datetimepicker" placeholder="Tgl. Input">
    </td>
    <!-- VOLUME -->
    <td>
        <input type="text" id="cc<?php echo $i; ?>" name="volume_<?php echo $i; ?>"
               class="form-control form-control-sm" placeholder="Volume">
    </td>
    <!-- MAYOR -->
    <td>
        <select name="myr_<?php echo $i; ?>"class="form-select form-select-sm select2" data-placeholder="MAYOR">
            <option value="">&nbsp;</option>
            <?php foreach ($mayor as $m): ?>
                <option value="<?php echo $m['ID']; ?>">
                    <?php echo htmlspecialchars($m['DESKRIPSI']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </td>
    <!-- MINOR -->
    <td>
        <select name="mnr_<?php echo $i; ?>"class="form-select form-select-sm select2"data-placeholder="MINOR">
            <option value="">&nbsp;</option>
            <?php foreach ($minor as $m): ?>
                <option value="<?php echo $m['ID']; ?>">
                    <?php echo htmlspecialchars($m['DESKRIPSI']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </td>
    <!-- TANGGAL SERAH -->
    <td>
        <input type="text"name="tgl_serah_<?php echo $i; ?>"id="tgl_serah_<?php echo $i; ?>"
               class="form-control form-control-sm datetimepicker"placeholder="Tgl. Serah">
    </td>
    <!-- PETUGAS SERAH -->
    <td>
        <input type="text" name="petugas_serah_<?php echo $i; ?>"id="petugas_serah_<?php echo $i; ?>"
               class="form-control form-control-sm" placeholder="Serah - Petugas">
    </td>
    <!-- PETUGAS TERIMA -->
    <td>
        <input type="text" name="petugas_terima_<?php echo $i; ?>"id="petugas_terima_<?php echo $i; ?>"
               class="form-control form-control-sm" placeholder="Terima - Petugas">
    </td>
</tr>