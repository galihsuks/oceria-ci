<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<h2>Pasien <?= $pasien['id']; ?></h2>
<hr>
<div class="baris-ke-kolom">
    <div>
        <input type="text" class="d-none" name="noRM" value="kosong">
        <div class="mb-2">
            <label class="form-label">Nama</label>
            <input type="text" class="form-control" name="nama" value="<?= $pasien['nama']; ?>">
            <div class="invalid-feedback">
                Pasien sudah pernah berkunjung
            </div>
        </div>
        <div class="mb-2">
            <label class="form-label">Tanggal Lahir</label>
            <input type="date" class="form-control" name="tglLahir" value="<?= $pasien['tglLahir'] ? explode('-', $pasien['tglLahir'])[2] . '-' . explode('-', $pasien['tglLahir'])[1] . '-' . explode('-', $pasien['tglLahir'])[0] : ''; ?>">
        </div>
        <div class="mb-2">
            <label class="form-label">Kelamin</label>
            <select type="text" class="form-select" name="kelamin">
                <option value="L" <?= $pasien['kelamin'] == 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                <option value="P" <?= $pasien['kelamin'] == 'P' ? 'selected' : ''; ?>>Perempuan</option>
            </select>
        </div>
        <div class="mb-2">
            <label class="form-label">No.HP</label>
            <input type="number" class="form-control" name="noHp" value="<?= $pasien['noHp']; ?>">
        </div>
        <div class="mb-2">
            <label class="form-label">Golongan Darah</label>
            <select name="golDarah" class="form-select">
                <option value="0" <?= $pasien['golDarah'] == '0' ? 'selected' : ''; ?>>tidak ada</option>
                <option value="1" <?= $pasien['golDarah'] == '1' ? 'selected' : ''; ?>>1</option>
                <option value="2" <?= $pasien['golDarah'] == '2' ? 'selected' : ''; ?>>2</option>
                <option value="3" <?= $pasien['golDarah'] == '3' ? 'selected' : ''; ?>>3</option>
                <option value="4" <?= $pasien['golDarah'] == '4' ? 'selected' : ''; ?>>4</option>
                <option value="A" <?= $pasien['golDarah'] == 'A' ? 'selected' : ''; ?>>A</option>
                <option value="B" <?= $pasien['golDarah'] == 'B' ? 'selected' : ''; ?>>B</option>
                <option value="O" <?= $pasien['golDarah'] == 'O' ? 'selected' : ''; ?>>O</option>
                <option value="AB" <?= $pasien['golDarah'] == 'AB' ? 'selected' : ''; ?>>AB</option>
            </select>
        </div>
        <div class="mb-2">
            <label class="form-label">NIK</label>
            <input type="number" class="form-control" name="nik" value="<?= $pasien['nik']; ?>">
        </div>
        <div class="mb-2">
            <label class="form-label">No Kartu BPJS</label>
            <input type="text" class="form-control" disabled value="<?= $pasien['noBpjs'] ? $pasien['noBpjs'] : 'Belum pernah daftar'; ?>">
        </div>
    </div>
    <div style="flex: 1;">
        <h5>Rekam Medis</h5>
        <div style="max-height: calc(100svh - 300px); overflow:auto;">
            <div class="container-rm w-100">
                <?php foreach ($pasien['rekamMedis'] as $rm) { ?>
                    <div class="item-rm">
                        <div style="width: 100px;">
                            <p class="m-0"><?= $rm['tanggal']; ?></p>
                        </div>
                        <div>
                            <?php foreach ($rm as $k_rm => $v_rm) {
                                if ($k_rm != 'tanggal') { ?>
                                    <p class="m-0"><?= $k_rm; ?> : <?= $v_rm; ?></p>
                            <?php }
                            } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>