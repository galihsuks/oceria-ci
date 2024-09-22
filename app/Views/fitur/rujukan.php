<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<h2>Cari Rujukan</h2>
<hr>
<?php if ($msg) { ?>
    <div class="alert alert-danger" role="alert">
        <?= $msg; ?>
    </div>
<?php } ?>
<form action="/rujukan" method="post">
    <div class="d-flex gap-2 align-items-center">
        <p class="m-0" style="width: fit-content;">No. Kunjungan</p>
        <input type="text" class="form-control" name="nokun" value="<?= $nokun; ?>" style="flex: 1;">
        <button type="submit" class="btn-default" style="width: fit-content;">Cari</button>
    </div>
</form>
<hr>
<?php if ($rujukan) { ?>
    <div class="d-flex gap-2">
        <div>
            <p class="m-0">No Rujuk</p>
            <p class="m-0">Nama PPK</p>
            <p class="m-0">Alamat PPK</p>
            <p class="m-0">Tanggal Kunjungan</p>
            <p class="m-0">Poli</p>
            <p class="m-0">Peserta</p>
            <p class="m-0">Diagnosa</p>
            <?= $rujukan['diag2'] ? '<p class="m-0">Diagnosa 2</p>' : ''; ?>
            <?= $rujukan['diag3'] ? '<p class="m-0">Diagnosa 3</p>' : ''; ?>
        </div>
        <div>
            <p class="m-0"><?= $rujukan['noRujukan']; ?></p>
            <p class="m-0"><?= $rujukan['ppk']['nmPPK']; ?></p>
            <p class="m-0"><?= $rujukan['ppk']['alamat']; ?></p>
            <p class="m-0"><?= $rujukan['tglKunjungan']; ?></p>
            <p class="m-0"><?= $rujukan['poli']['nmPoli']; ?></p>
            <p class="m-0"><?= $rujukan['nmPst']; ?></p>
            <p class="m-0"><?= $rujukan['diag1']['nmDiag']; ?></p>
            <?= $rujukan['diag2'] ? '<p class="m-0">' . $rujukan['diag2']['nmDiag'] . '</p>' : ''; ?>
            <?= $rujukan['diag3'] ? '<p class="m-0">' . $rujukan['diag3']['nmDiag'] . '</p>' : ''; ?>
        </div>
    </div>
    <hr>
    <h3>PPK Rujukan</h3>
    <div class="d-flex gap-2">
        <div>
            <p class="m-0">Kode</p>
            <p class="m-0">Nama</p>
            <p class="m-0">Alamat</p>
            <p class="m-0">Telp</p>
        </div>
        <div>
            <p class="m-0"><?= $rujukan['ppkRujuk']['kdPPK']; ?></p>
            <p class="m-0"><?= $rujukan['ppkRujuk']['nmPPK']; ?></p>
            <p class="m-0"><?= $rujukan['ppkRujuk']['alamat']; ?></p>
            <p class="m-0"><?= $rujukan['ppkRujuk']['telp']; ?></p>
        </div>
    </div>
<?php } ?>
<?= $this->endSection(); ?>