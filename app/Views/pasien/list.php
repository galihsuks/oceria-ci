<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<?php
$id = isset($_GET['id']) ? $_GET['id'] : false;
$nama = isset($_GET['nama']) ? $_GET['nama'] : false;
$pag = isset($_GET['pag']) ? $_GET['pag'] : 1;
$pasienSemua = $pasien;
if ($id) {
    $pasienLama = $pasienSemua;
    $pasienSemua = [];
    foreach ($pasienLama as $p) {
        if (stripos($p['id'], $id) !== false) {
            array_push($pasienSemua, $p);
        }
    }
}
if ($nama) {
    $pasienLama = $pasienSemua;
    $pasienSemua = [];
    foreach ($pasienLama as $p) {
        if (stripos($p['nama'], $nama) !== false) {
            array_push($pasienSemua, $p);
        }
    }
}

$pasienLama = $pasienSemua;
$pasienSemua = [];
for ($i = ($pag - 1) * 20; $i < ($pag - 1) * 20 + 20; $i++) {
    if (isset($pasienLama[$i])) {
        array_push($pasienSemua, $pasienLama[$i]);
    }
}
$hitungPag = ceil(count($pasienLama) / 20);
?>
<h2>List Pasien Oceria</h2>
<form action="">
    <div class="d-flex gap-4">
        <div class="d-flex gap-2 align-items-center">
            <p class="m-0">ID</p>
            <input type="text" class="form-control" name="id" value="<?= $id ? $id : ''; ?>">
        </div>
        <div class="d-flex gap-2 align-items-center">
            <p class="m-0">Nama</p>
            <input type="text" class="form-control" name="nama" value="<?= $nama ? $nama : ''; ?>">
        </div>
        <button type="submit" class="btn-default">Terapkan</button>
    </div>
</form>
<hr>
<div class="d-flex flex-column gap-2 w-100">
    <div class="d-flex w-100 mb-2">
        <div style="flex: 1;" class="fw-bold">ID</div>
        <div style="flex: 3;" class="fw-bold">Nama</div>
        <div style="flex: 3;" class="fw-bold">Alamat</div>
        <div style="flex: 1;" class="fw-bold">Kelamin</div>
        <div style="flex: 2;" class="fw-bold">Action</div>
    </div>
    <?php foreach ($pasienSemua as $p) { ?>
        <div class="d-flex w-100">
            <div style="flex: 1;"><?= $p['id']; ?></div>
            <div style="flex: 3;"><?= $p['nama']; ?></div>
            <div style="flex: 3;"><?= $p['alamat']; ?></div>
            <div style="flex: 1;"><?= $p['kelamin']; ?></div>
            <div style="flex: 2;" class="d-flex gap-1">
                <a class="btn btn-light" href="/pasien/<?= $p['id']; ?>">
                    <p class="m-0">Detail</p>
                </a>
                <a class="btn-default"><i class="material-icons">delete_forever</i></a>
            </div>
        </div>
    <?php } ?>
</div>
<hr>
<div class="d-flex justify-content-center mb-3">
    <?php if ($hitungPag < 10) { ?>
        <div class="pagination">
            <a class="pag-item <?= $pag == 1 ? 'disable' : ''; ?>" href="/pasien?<?= $id ? 'id=' . $id : ''; ?><?= $nama ? '&nama=' . $nama : ''; ?><?= '&pag=1'; ?>"><i class="material-icons">first_page</i></a>
            <a class="pag-item <?= $pag == 1 ? 'disable' : ''; ?>" href="/pasien?<?= $id ? 'id=' . $id : ''; ?><?= $nama ? '&nama=' . $nama : ''; ?><?= '&pag=' . $pag - 1; ?>"><i class="material-icons">chevron_left</i></a>
            <span class="d-block mx-3" style="width: 2px; height: 1.5em; background-color: whitesmoke"></span>
            <?php for ($i = 1; $i <= $hitungPag; $i++) { ?>
                <a class="pag-item <?= $pag == $i ? 'aktif' : ''; ?>" href="/pasien?<?= $id ? 'id=' . $id : ''; ?><?= $nama ? '&nama=' . $nama : ''; ?><?= '&pag=' . $i; ?>"><?= $i; ?></a>
            <?php } ?>
            <span class="d-block mx-3" style="width: 2px; height: 1.5em; background-color: whitesmoke"></span>
            <a class="pag-item <?= $pag == $hitungPag ? 'disable' : ''; ?>" href="/pasien?<?= $id ? 'id=' . $id : ''; ?><?= $nama ? '&nama=' . $nama : ''; ?><?= '&pag=' . $pag + 1; ?>"><i class="material-icons">chevron_right</i></a>
            <a class="pag-item <?= $pag == $hitungPag ? 'disable' : ''; ?>" href="/pasien?<?= $id ? 'id=' . $id : ''; ?><?= $nama ? '&nama=' . $nama : ''; ?><?= '&pag=' . $hitungPag; ?>"><i class="material-icons">last_page</i></a>
        </div>
    <?php } else { ?>
        <div class="pagination">
            <a class="pag-item <?= $pag == 1 ? 'disable' : ''; ?>" href="/pasien?<?= $id ? 'id=' . $id : ''; ?><?= $nama ? '&nama=' . $nama : ''; ?><?= '&pag=1'; ?>"><i class="material-icons">first_page</i></a>
            <a class="pag-item <?= $pag == 1 ? 'disable' : ''; ?>" href="/pasien?<?= $id ? 'id=' . $id : ''; ?><?= $nama ? '&nama=' . $nama : ''; ?><?= '&pag=' . $pag - 1; ?>"><i class="material-icons">chevron_left</i></a>
            <span class="d-block mx-3" style="width: 2px; height: 1.5em; background-color: whitesmoke"></span>
            <?php for ($i = $pag; $i <= $pag + 9; $i++) {
                if ($i < $hitungPag) { ?>
                    <a class="pag-item <?= $pag == $i ? 'aktif' : ''; ?>" href="/pasien?<?= $id ? 'id=' . $id : ''; ?><?= $nama ? '&nama=' . $nama : ''; ?><?= '&pag=' . $i; ?>"><?= $i; ?></a>
            <?php }
            } ?>

            <span class="d-block mx-3" style="width: 2px; height: 1.5em; background-color: whitesmoke"></span>
            <a class="pag-item <?= $pag == $hitungPag ? 'disable' : ''; ?>" href="/pasien?<?= $id ? 'id=' . $id : ''; ?><?= $nama ? '&nama=' . $nama : ''; ?><?= '&pag=' . $pag + 1; ?>"><i class="material-icons">chevron_right</i></a>
            <a class="pag-item <?= $pag == $hitungPag ? 'disable' : ''; ?>" href="/pasien?<?= $id ? 'id=' . $id : ''; ?><?= $nama ? '&nama=' . $nama : ''; ?><?= '&pag=' . $hitungPag; ?>"><i class="material-icons">last_page</i></a>
        </div>
    <?php } ?>
</div>
<?= $this->endSection(); ?>