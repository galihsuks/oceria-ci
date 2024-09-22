<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<h2>Riwayat Pelayanan BPJS</h2>
<hr>
<?php if ($msg) { ?>
    <div class="alert alert-danger" role="alert">
        <?= $msg; ?>
    </div>
<?php } ?>
<form action="/pelayanan/caririwayatkunbpjs" method="post">
    <div class="d-flex gap-2 align-items-center">
        <p class="m-0" style="width: fit-content;">No. Kartu BPJS</p>
        <input type="number" class="form-control" name="noka" value="<?= $noka; ?>" style="flex: 1;">
        <button type="submit" class="btn-default" style="width: fit-content;">Cari</button>
    </div>
</form>
<hr>
<?php if (count($pelayanan) > 0) { ?>
    <div class="d-flex gap-2">
        <div>
            <p class="m-0">Nama</p>
            <p class="m-0">Tanggal Lahir</p>
            <p class="m-0">NIK</p>
        </div>
        <div>
            <p class="m-0"><?= $pelayanan[0]['peserta']['nama']; ?></p>
            <p class="m-0"><?= $pelayanan[0]['peserta']['tglLahir']; ?></p>
            <p class="m-0"><?= $pelayanan[0]['peserta']['noKTP']; ?></p>
        </div>
    </div>
    <hr>
<?php } ?>
<div class="d-flex flex-column gap-2">
    <div class="d-flex w-100 mb-2">
        <div style="flex: 1;">No. Kunjungan</div>
        <div style="flex: 1;">Tanggal Kunjungan</div>
        <div style="flex: 1;">Provider</div>
        <div style="flex: 1;">Poli</div>
        <div style="flex: 1;">Status Pulang</div>
        <div style="flex: 1;">Tanggal Pulang</div>
    </div>
    <?php foreach ($pelayanan as $p) { ?>
        <div class="d-flex w-100">
            <div style="flex: 1;"><?= $p['noKunjungan']; ?></div>
            <div style="flex: 1;"><?= $p['tglKunjungan']; ?></div>
            <div style="flex: 1;"><?= $p['providerPelayanan']['nmProvider']; ?></div>
            <div style="flex: 1;"><?= $p['poli']['nmPoli']; ?></div>
            <div style="flex: 1;"><?= $p['statusPulang']['nmStatusPulang']; ?></div>
            <div style="flex: 1;"><?= $p['tglPulang']; ?></div>
        </div>
    <?php } ?>
</div>
<?= $this->endSection(); ?>