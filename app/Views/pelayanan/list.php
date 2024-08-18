<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<h2>Riwayat Pelayanan</h2>
<hr>
<?php if ($msg) { ?>
    <div class="alert alert-danger" role="alert">
        <?= $msg; ?>
    </div>
<?php } ?>
<div class="d-flex gap-2 align-items-center">
    <p class="m-0">Tanggal</p>
    <input type="date" class="form-control" value="<?= $tanggal; ?>" onchange="gantiTanggal(event)">
</div>
<hr>
<div class="d-flex flex-column gap-2">
    <div class="d-flex w-100 mb-2">
        <div style="flex: 3;">No. Kunjungan</div>
        <div style="flex: 3;">Nama Peserta</div>
        <div style="flex: 2;">BPJS/Umum</div>
        <div style="flex: 1;">Action</div>
    </div>
    <?php foreach ($pelayanan as $p) { ?>
        <div class="d-flex w-100">
            <div style="flex: 3;"><?= $p['noKunjungan']; ?></div>
            <div style="flex: 3;"><?= $p['detail_pasien']['nama']; ?></div>
            <div style="flex: 2;"><?= $p['bpjs'] ? 'BPJS' : 'Umum'; ?></div>
            <div style="flex: 1;" class="d-flex gap-1">
                <a href="/pelayanan/edit/<?= $p['id']; ?>" class="btn-default"><i class="material-icons">edit</i></a>
                <a onclick="openConfirm('Kunjungan <?= $p['noKunjungan']; ?> akan menghapus?','/pelayanan/del/<?= $p['id']; ?>')" class="btn-default"><i class="material-icons">delete_forever</i></a>
            </div>
        </div>
    <?php } ?>
</div>
<script>
    let tanggalValue = '<?= date("d-m-Y", strtotime($tanggal)); ?>';

    function gantiTanggal(e) {
        const tglValue = e.target.value.split("-");
        const tgl = tglValue[2] + "-" + tglValue[1] + "-" + tglValue[0];
        tanggalValue = tgl
        window.location.href = '/pelayanan/list/' + tanggalValue
    }
</script>
<?= $this->endSection(); ?>