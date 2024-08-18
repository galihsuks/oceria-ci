<?php if (!$selected) { ?>
    <select class="form-select" name="<?= $name; ?>">
        <?php if ($null) { ?>
            <option value="0" selected>Tidak ada</option>
        <?php }
        foreach ($data as $ind_d => $d) { ?>
            <option value="<?= $d[$keyValue]; ?>" <?= $null ? '' : ($ind_d == 0 ? 'selected' : ''); ?>><?= $d[$keyInner]; ?></option>
        <?php } ?>
    </select>
<?php } else { ?>
    <select class="form-select" name="<?= $name; ?>">
        <?php if ($null) { ?>
            <option value="0" <?= $selected == '0' ? 'selected' : ''; ?>>Tidak ada</option>
        <?php }
        foreach ($data as $ind_d => $d) { ?>
            <option value="<?= $d[$keyValue]; ?>" <?= $d[$keyValue] == $selected ? 'selected' : ''; ?>><?= $d[$keyInner]; ?></option>
        <?php } ?>
    </select>
<?php } ?>