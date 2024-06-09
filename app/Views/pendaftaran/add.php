<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<h2>Pendaftaran Antrian BPJS</h2>
<hr>
<form action="/pendaftaran/add" method="post">
    <div class="d-flex gap-4">
        <div class="w-50">
            <div class="mb-1">
                <label class="form-label">Pasien BPJS atau Umum</label>
                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input value="true" class="form-check-input" type="radio" name="bpjs" id="flexRadioDefault01" checked>
                        <label class="form-check-label" for="flexRadioDefault01">
                            BPJS
                        </label>
                    </div>
                    <div class="form-check">
                        <input value="false" class="form-check-input" type="radio" name="bpjs" id="flexRadioDefault02">
                        <label class="form-check-label" for="flexRadioDefault02">
                            Umum
                        </label>
                    </div>
                </div>
            </div>
            <hr>
            <div class="mb-1">
                <label class="form-label">Tanggal</label>
                <input type="date" class="form-control" name="tglDaftar" required value="<?= $tanggal; ?>">
            </div>
            <div class="mb-1">
                <label class="form-label">Pendaftaran</label>
                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input value="baru" class="form-check-input" type="radio" name="jenis_pendaftaran" id="flexRadioDefault1" checked>
                        <label class="form-check-label" for="flexRadioDefault1">
                            Baru
                        </label>
                    </div>
                    <div class="form-check">
                        <input value="rujukan" class="form-check-input" type="radio" name="jenis_pendaftaran" id="flexRadioDefault2">
                        <label class="form-check-label" for="flexRadioDefault2">
                            Rujukan
                        </label>
                    </div>
                </div>
            </div>
            <div class="mb-1">
                <label class="form-label">No. Pencarian</label>
                <div class="input-group">
                    <input type="text" class="form-control" aria-label="Text input with segmented dropdown button" id="input-cari" required>
                    <button type="button" class="btn btn-outline-secondary" id="btn-cari">Cari No.BPJS</button>
                    <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="visually-hidden">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" onclick="pilihJenisCari('noka')">Nomor BPJS</a></li>
                        <li><a class="dropdown-item" onclick="pilihJenisCari('nik')">NIK</a></li>
                    </ul>
                </div>
            </div>
            <div class="mb-1">
                <label class="form-label">No. BPJS</label>
                <input type="text" class="form-control" name="noKartu" required>
                <input type="text" class="d-none" name="kdProviderPeserta" required>
                <input type="text" class="d-none" name="nama" required>
                <input type="text" class="d-none" name="nik" required>
            </div>
            <div class="d-flex mt-3 gap-3">
                <div>
                    <p class="fw-bold m-0">Nama</p>
                    <p class="fw-bold m-0">Status peserta</p>
                    <p class="fw-bold m-0">Tanggal lahir</p>
                    <p class="fw-bold m-0">Kelamin</p>
                    <p class="fw-bold m-0">PPK Umum</p>
                    <p class="fw-bold m-0">PPK Gigi</p>
                    <p class="fw-bold m-0">No. Handphone</p>
                    <p class="fw-bold m-0">No. Rekam Medis</p>
                </div>
                <div id="container-detail-peserta"></div>
            </div>
        </div>
        <div class="w-50">
            <div class="mb-1">
                <label class="form-label">Jenis Kunjungan</label>
                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input onchange="changeKunjSakit(true)" value="true" class="form-check-input" type="radio" name="kunjSakit" id="flexRadioDefault3" checked>
                        <label class="form-check-label" for="flexRadioDefault3">
                            Kunjungan Sakit
                        </label>
                    </div>
                    <div class="form-check">
                        <input onchange="changeKunjSakit(false)" value="false" class="form-check-input" type="radio" name="kunjSakit" id="flexRadioDefault4">
                        <label class="form-check-label" for="flexRadioDefault4">
                            Kunjungan Sehat
                        </label>
                    </div>
                </div>
            </div>
            <div class="mb-1">
                <label class="form-label">Perawatan</label>
                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input value="10" class="form-check-input" type="radio" name="kdTkp" id="flexRadioDefault5" checked>
                        <label class="form-check-label" for="flexRadioDefault5">
                            Rawat Jalan
                        </label>
                    </div>
                    <div class="form-check">
                        <input value="20" class="form-check-input" type="radio" name="kdTkp" id="flexRadioDefault6">
                        <label class="form-check-label" for="flexRadioDefault6">
                            Rawat Inap
                        </label>
                    </div>
                    <div class="form-check">
                        <input value="50" class="form-check-input" type="radio" name="kdTkp" id="flexRadioDefault7">
                        <label class="form-check-label" for="flexRadioDefault7">
                            Promotif Preventif
                        </label>
                    </div>
                </div>
            </div>
            <div class="mb-1">
                <label class="form-label">Poli Tujuan</label>
                <select class="form-select" aria-label="Default select example" name="kdPoli">
                    <?php
                    foreach ($poli as $ind_p => $p) {
                        if ($p['poliSakit'] ==  true) {
                    ?>
                            <option value="<?= $p['kdPoli']; ?>" <?= $ind_p == 0 ? 'selected' : ''; ?>><?= $p['nmPoli']; ?></option>
                    <?php }
                    }
                    ?>
                </select>
            </div>
            <div class="mb-1">
                <label class="form-label">Keluhan</label>
                <textarea type="text" class="form-control" name="keluhan" required></textarea>
            </div>
            <hr>
            <h4>Pemeriksaan Fisik</h4>
            <div class="d-flex gap-3 w-100">
                <div class="flex-grow-1">
                    <div class="mb-1">
                        <label class="form-label">Tinggi Badan</label>
                        <div class="input-group mb-3">
                            <input type="number" class="form-control" name="tinggiBadan">
                            <span class="input-group-text">cm</span>
                        </div>
                    </div>
                    <div class="mb-1">
                        <label class="form-label">Berat Badan</label>
                        <div class="input-group mb-3">
                            <input type="number" class="form-control" name="beratBadan">
                            <span class="input-group-text">kg</span>
                        </div>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <div class="mb-1">
                        <label class="form-label">Lingkar Perut</label>
                        <div class="input-group mb-3">
                            <input type="number" class="form-control" name="lingkarPerut">
                            <span class="input-group-text">cm</span>
                        </div>
                    </div>
                    <div class="mb-1">
                        <label class="form-label">IMT</label>
                        <div class="input-group mb-3">
                            <input type="number" class="form-control" disabled>
                            <span class="input-group-text">kg/m2</span>
                        </div>
                    </div>
                </div>
            </div>
            <h4>Tekanan Darah</h4>
            <div class="d-flex gap-3">
                <div class="flex-grow-1">
                    <div class="mb-1">
                        <label class="form-label">Sistole</label>
                        <div class="input-group mb-3">
                            <input type="number" class="form-control" name="sistole">
                            <span class="input-group-text">mmHg</span>
                        </div>
                    </div>
                    <div class="mb-1">
                        <label class="form-label">Diastole</label>
                        <div class="input-group mb-3">
                            <input type="number" class="form-control" name="diastole">
                            <span class="input-group-text">mmHg</span>
                        </div>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <div class="mb-1">
                        <label class="form-label">Respiratory Rate</label>
                        <div class="input-group mb-3">
                            <input type="number" class="form-control" name="respRate">
                            <span class="input-group-text">/ minute</span>
                        </div>
                    </div>
                    <div class="mb-1">
                        <label class="form-label">Heart Rate</label>
                        <div class="input-group mb-3">
                            <input type="number" class="form-control" name="heartRate">
                            <span class="input-group-text">bpm</span>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="hijau">Simpan</button>
        </div>
    </div>
</form>
<script>
    const formElm = document.querySelector('form');
    const kdPoliElm = document.querySelector('select[name="kdPoli"]');
    const inputCariElm = document.getElementById("input-cari")
    const btnCariElm = document.getElementById("btn-cari")
    const containerDetailPesertaElm = document.getElementById("container-detail-peserta");
    const inputNoBPJSElm = document.querySelector('input[name="noKartu"]');
    const inputkdProviderPesertaElm = document.querySelector('input[name="kdProviderPeserta"]');
    const inputnamaElm = document.querySelector('input[name="nama"]');
    const inputnikElm = document.querySelector('input[name="nik"]');

    const poliArr = JSON.parse(<?= json_encode($poliJson); ?>);

    function changeKunjSakit(value) {
        console.log(value)
        kdPoliElm.innerHTML = '';
        poliArr.forEach((poli, ind) => {
            if (poli.poliSakit == value) {
                if (ind == 0)
                    kdPoliElm.innerHTML += '<option value="' + poli.kdPoli + '" selected>' + poli.nmPoli + '</option>'
                else
                    kdPoliElm.innerHTML += '<option value="' + poli.kdPoli + '">' + poli.nmPoli + '</option>'
            }
        })
    }

    let cariPesertaBerdasarkan = 'noka';

    function pilihJenisCari(value) {
        console.log(value)
        switch (value) {
            case 'noka':
                cariPesertaBerdasarkan = 'noka'
                btnCariElm.innerHTML = 'Cari No.BPJS'
                break;
            case 'nik':
                cariPesertaBerdasarkan = 'nik'
                btnCariElm.innerHTML = 'Cari NIK'
                break;
        }
    }
    btnCariElm.addEventListener('click', () => {
        async function fetchPeserta() {
            const res = await fetch('/bpjs/getpeserta/' + cariPesertaBerdasarkan + '/' + inputCariElm.value);
            const resJson = await res.json()
            console.log(resJson)
            let kelamin = 'Laki-laki'
            let nohp = 'Tidak ada'
            if (resJson.sex == 'P') kelamin = 'Perempuan'
            if (resJson.noHP != '') nohp = resJson.noHP
            inputNoBPJSElm.value = resJson.noKartu
            inputkdProviderPesertaElm.value = resJson.kdProviderPst.kdProvider
            inputnamaElm.value = resJson.nama
            inputnikElm.value = resJson.noKTP
            containerDetailPesertaElm.innerHTML = ''
            containerDetailPesertaElm.innerHTML += '<p class="fw-bold m-0">' + resJson.nama + '</p>'
            containerDetailPesertaElm.innerHTML += '<p class="fw-bold m-0">' + resJson.hubunganKeluarga + '</p>'
            containerDetailPesertaElm.innerHTML += '<p class="fw-bold m-0">' + resJson.tglLahir + '</p>'
            containerDetailPesertaElm.innerHTML += '<p class="fw-bold m-0">' + kelamin + '</p>'
            containerDetailPesertaElm.innerHTML += '<p class="fw-bold m-0">' + resJson.kdProviderPst.kdProvider + ' - ' + resJson.kdProviderPst.nmProvider + '</p>'
            containerDetailPesertaElm.innerHTML += '<p class="fw-bold m-0">' + resJson.kdProviderGigi.kdProvider + ' - ' + resJson.kdProviderGigi.nmProvider + '</p>'
            containerDetailPesertaElm.innerHTML += '<p class="fw-bold m-0">' + nohp + '</p>'
            containerDetailPesertaElm.innerHTML += '<p class="fw-bold m-0 text-danger">Ini belum tau dimana</p>'
        }
        fetchPeserta();
    })
</script>
<?= $this->endSection(); ?>