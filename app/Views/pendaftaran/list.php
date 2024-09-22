<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<h2>Riwayat Pendaftaran</h2>
<hr>
<?php if ($msg) { ?>
    <div class="alert alert-danger" role="alert">
        <?= $msg; ?>
    </div>
<?php } ?>
<div class="d-flex gap-5">
    <div class="d-flex gap-2 align-items-center">
        <p class="m-0">Tanggal</p>
        <input type="date" class="form-control" value="<?= $tanggal; ?>" onchange="gantiTanggal(event)">
    </div>
    <div class="d-flex gap-2 align-items-center">
        <p class="m-0">BPJS/Umum</p>
        <select class="form-select" onchange="gantiJenis(event)">
            <option value="all" <?= $jenis == 'all' ? 'selected' : ''; ?>>Semua</option>
            <option value="bpjs" <?= $jenis == 'bpjs' ? 'selected' : ''; ?>>BPJS</option>
            <option value="non" <?= $jenis == 'non' ? 'selected' : ''; ?>>Umum</option>
        </select>
    </div>
</div>
<hr>
<div class="d-flex flex-column gap-2">
    <div class="d-flex w-100 mb-2">
        <div style="flex: 1;">No</div>
        <div style="flex: 3;">Nama Peserta</div>
        <div style="flex: 2;">No.Kartu</div>
        <div style="flex: 2;">NIK</div>
        <div style="flex: 2;">Status</div>
        <div style="flex: 2;">Action</div>
    </div>
    <?php foreach ($pendaftaran as $p) { ?>
        <div class="d-flex w-100">
            <div style="flex: 1;"><?= $p['noUrut']; ?></div>
            <div style="flex: 3;"><?= $p['nama']; ?></div>
            <div style="flex: 2;"><?= $p['noKartu']; ?></div>
            <div style="flex: 2;"><?= $p['nik']; ?></div>
            <div style="flex: 2;"><?= $p['status']; ?></div>
            <div style="flex: 2;"><a onclick="openConfirm('NoUrut <?= $p['noUrut']; ?> pada tanggal <?= $p['tglDaftar']; ?> akan dihapus?','/pendaftaran/del/<?= $p['id']; ?>')" class="btn-default"><i class="material-icons">delete_forever</i> Hapus</a></div>
        </div>
    <?php }
    if (count($pendaftaran) <= 0) { ?>
        <div>Data tidak ditemukan</div>
    <?php } ?>
</div>
<script>
    let tanggalValue = '<?= date("d-m-Y", strtotime($tanggal)); ?>';
    let jenisValue = 'all';

    function gantiTanggal(e) {
        const tglValue = e.target.value.split("-");
        const tgl = tglValue[2] + "-" + tglValue[1] + "-" + tglValue[0];
        tanggalValue = tgl
        window.location.href = '/pendaftaran/list/' + tanggalValue + "/" + jenisValue
    }

    function gantiJenis(e) {
        const jenis = e.target.value;
        jenisValue = jenis
        window.location.href = '/pendaftaran/list/' + tanggalValue + "/" + jenisValue
    }
</script>
<?= $this->endSection(); ?>