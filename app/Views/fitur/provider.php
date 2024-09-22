<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<h2 class="m-0">Referensi Provider Rayonisasi</h2>
<p class="mb-3">Jumlah : <?= $provider['count']; ?></p>
<hr>
<div class="d-flex flex-column gap-2 w-100">
    <div class="d-flex w-100 mb-2">
        <div style="flex: 1;" class="fw-bold">Nomor</div>
        <div style="flex: 4;" class="fw-bold">Kode Provider</div>
        <div style="flex: 4;" class="fw-bold">Nama Provider</div>
    </div>
    <?php foreach ($provider['list'] as $ind_p => $p) { ?>
        <div class="d-flex w-100">
            <div style="flex: 1;"><?= $ind_p + 1; ?></div>
            <div style="flex: 4;"><?= $p['kdProvider']; ?></div>
            <div style="flex: 4;"><?= $p['nmProvider']; ?></div>
        </div>
    <?php } ?>
</div>
<?= $this->endSection(); ?>