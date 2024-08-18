<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div id="container-pilih-pasien" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100svh; background-color: rgba(0,0,0,0.5); z-index: 10;" class="d-none d-flex flex-column justify-content-center align-items-center">
    <div class="bg-light p-3 rounded d-flex flex-column" style="height: 80svh;">
        <div class="d-flex justify-content-between" style="height: fit-content;">
            <h5>Cari Pasien Oceria</h5>
            <button type="button" class="btn btn-light" onclick="closePilihPasien()"><i class="material-icons">close</i></button>
        </div>
        <div class="d-flex w-100 mb-2 gap-2 border-bottom">
            <div style="flex: 1;" class="text-secondary">No.</div>
            <div style="flex: 2;" class="text-secondary">Nama</div>
            <div style="flex: 1;" class="text-secondary">Tanggal Lahir</div>
            <div style="flex: 3;" class="text-secondary">Alamat</div>
            <div style="flex: 1;" class="text-secondary">Action</div>
        </div>
        <div style="flex: 1; overflow-y:auto;">
            <div class="d-flex flex-column gap-2" id="tabel-pilih-pasien">
                <div class="d-flex w-100 gap-2">
                    <div style="flex: 1;">G0123</div>
                    <div style="flex: 2;">Galih Sukmamukti hidaya</div>
                    <div style="flex: 1;">08-04-2001</div>
                    <div style="flex: 3;">Jl.majanah jonggrangan, kltean utar</div>
                    <div style="flex: 1;"><button type="button" onclick="pilihPasienOceria('G0123')" class="btn-default">Pilih</button></div>
                </div>
            </div>
        </div>
    </div>
</div>
<h2>Pendaftaran Pasien</h2>
<hr>
<?php if ($msg) { ?>
    <div class="alert alert-danger" role="alert">
        <?= $msg; ?>
    </div>
<?php } ?>
<form action="/pendaftaran/add" method="post">
    <div class="d-flex gap-4 mb-3">
        <div class="w-50">
            <div class="d-flex mb-2">
                <div class="w-50">
                    <label class="form-label">Pasien BPJS atau Umum</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input onchange="changeBPJS(true)" value="true" class="form-check-input" type="radio" name="bpjs" id="flexRadioDefault01" checked>
                            <label class="form-check-label" for="flexRadioDefault01">
                                BPJS
                            </label>
                        </div>
                        <div class="form-check">
                            <input onchange="changeBPJS(false)" value="false" class="form-check-input" type="radio" name="bpjs" id="flexRadioDefault02">
                            <label class="form-check-label" for="flexRadioDefault02">
                                Umum
                            </label>
                        </div>
                    </div>
                </div>
                <div class="w-50">
                    <label class="form-label">Sudah pernah berkunjung?</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input value="false" onchange="changePasienLama(false)" class="form-check-input" type="radio" name="pasienLama" id="radioPasienLama1" checked>
                            <label class="form-check-label" for="radioPasienLama1">
                                Belum
                            </label>
                        </div>
                        <div class="form-check">
                            <input value="true" onchange="changePasienLama(true)" class="form-check-input" type="radio" name="pasienLama" id="radioPasienLama2">
                            <label class="form-check-label" for="radioPasienLama2">
                                Sudah
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div id="container-input-pasien-lama" class="d-none">
                <div class="mb-2">
                    <label class=" form-label">Masukan nama pasien</label>
                    <div class="input-group has-validation">
                        <input type="text" class="form-control" id="input-cari-pasien">
                        <button type="button" class="btn btn-outline-secondary" onclick="cariPasien()">Cari</button>
                        <div class="invalid-feedback">
                            Pasien tidak ditemukan
                        </div>
                    </div>
                </div>
                <div class="d-flex mb-2 gap-3">
                    <div>
                        <p class="fw-bold m-0">No. Rekam Medis</p>
                        <p class="fw-bold m-0">Nama</p>
                        <p class="fw-bold m-0">Tanggal lahir</p>
                        <p class="fw-bold m-0">Kelamin</p>
                        <p class="fw-bold m-0">Alamat</p>
                        <p class="fw-bold m-0">No.BPJS</p>
                    </div>
                    <div id="container-detail-cari-pasien"></div>
                </div>
            </div>
            <div id="bpjs-section">
                <!-- <div class="mb-2">
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
                </div> -->
                <div class="mb-2">
                    <label class="form-label">No. Pencarian</label>
                    <div class="input-group has-validation">
                        <input type="number" class="form-control" aria-label="Text input with segmented dropdown button" id="input-cari">
                        <button type="button" class="btn btn-outline-secondary" id="btn-cari">Cari No.BPJS</button>
                        <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" onclick="pilihJenisCari('noka')">Nomor BPJS</a></li>
                            <li><a class="dropdown-item" onclick="pilihJenisCari('nik')">NIK</a></li>
                        </ul>
                        <div class="invalid-feedback">
                            Pasien sudah pernah berkunjung
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label">No. BPJS</label>
                    <input type="text" class="form-control" name="noKartu">
                    <input type="text" class="d-none" name="kdProviderPeserta">
                    <!-- <input type="text" class="d-none" name="nama">
                    <input type="text" class="d-none" name="nik"> -->
                </div>
                <div class="d-flex mb-2 gap-3">
                    <div>
                        <p class="fw-bold m-0">Nama</p>
                        <p class="fw-bold m-0">Status peserta</p>
                        <p class="fw-bold m-0">Tanggal lahir</p>
                        <p class="fw-bold m-0">Kelamin</p>
                        <p class="fw-bold m-0">PPK Umum</p>
                        <p class="fw-bold m-0">PPK Gigi</p>
                        <p class="fw-bold m-0">No. Handphone</p>
                    </div>
                    <div id="container-detail-peserta"></div>
                </div>
            </div>
            <div id="container-input-pasien-baru" class="d-none">
                <input type="text" class="d-none" name="noRM" value="kosong">
                <div class="mb-2">
                    <label class="form-label">Nama</label>
                    <input type="text" class="form-control" name="nama">
                    <div class="invalid-feedback">
                        Pasien sudah pernah berkunjung
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" class="form-control" name="tglLahir">
                </div>
                <div class="mb-2">
                    <label class="form-label">Kelamin</label>
                    <select type="text" class="form-select" name="kelamin">
                        <option value="L" selected>Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label">No.HP</label>
                    <input type="number" class="form-control" name="noHp">
                </div>
                <div class="mb-2">
                    <label class="form-label">Golongan Darah</label>
                    <select name="golDarah" class="form-select">
                        <option value="0">tidak ada</option>
                        <option value="1" selected>1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="O">O</option>
                        <option value="AB">AB</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label">NIK</label>
                    <input type="number" class="form-control" name="nik">
                </div>
            </div>
            <div class="mb-1" id="container-input-alamat">
                <label class="form-label">Alamat pasien</label>
                <input type="text" class="form-control" name="alamat">
            </div>
            <hr>
        </div>
        <div class="w-50">
            <div class="mb-2">
                <label class="form-label">Tanggal</label>
                <input type="date" class="form-control" name="tglDaftar" min="<?= $tanggal; ?>" value="<?= $tanggal; ?>">
            </div>
            <div class="mb-2">
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
            <div class="mb-2">
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
            <div class="mb-2" id="select-poli">
                <label class="form-label">Poli Tujuan</label>
                <select class="form-select" name="kdPoli">
                    <?php foreach ($poli as $ind_d => $d) {
                        if ($d['poliSakit']) { ?>
                            <option value="<?= $d['kdPoli']; ?>"><?= $d['nmPoli']; ?></option>
                    <?php }
                    } ?>
                </select>
            </div>
            <!-- <script>
                async function getPoli() {
                    const fetchDokter = await fetch('/com/poli');
                    const res = await fetchDokter.text();
                    document.getElementById('select-poli').innerHTML += res
                }
                getPoli()
            </script> -->
            <div class="mb-2">
                <label class="form-label">Keluhan</label>
                <textarea type="text" class="form-control" name="keluhan"></textarea>
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
            <button type="submit" class="btn-default">Simpan</button>
        </div>
    </div>
</form>
<script>
    const formElm = document.querySelector('form');
    const inputCariElm = document.getElementById("input-cari")
    const btnCariElm = document.getElementById("btn-cari")
    const containerDetailPesertaElm = document.getElementById("container-detail-peserta");
    const inputNoBPJSElm = document.querySelector('input[name="noKartu"]');
    const inputkdProviderPesertaElm = document.querySelector('input[name="kdProviderPeserta"]');
    const inputnamaElm = document.querySelector('input[name="nama"]');
    const inputnikElm = document.querySelector('input[name="nik"]');
    const inputtglLahirElm = document.querySelector('input[name="tglLahir"]');
    const inputkelaminElm = document.querySelector('select[name="kelamin"]');
    const inputnoHpElm = document.querySelector('input[name="noHp"]');
    const inputgolDarahElm = document.querySelector('select[name="golDarah"]');
    const inputalamatElm = document.querySelector('input[name="alamat"]');
    const inputNoRMElm = document.querySelector('input[name="noRM"]');


    const containerInputPasienLamaElm = document.getElementById('container-input-pasien-lama');
    const inputCariPasienElm = document.getElementById('input-cari-pasien');
    const containerDetailCariPasienElm = document.getElementById('container-detail-cari-pasien');

    const containerPilihPasienElm = document.getElementById('container-pilih-pasien');
    const tabelPilihPasienElm = document.getElementById('tabel-pilih-pasien');
    const bpjsSectionElm = document.getElementById('bpjs-section');
    const containerInputAlamatElm = document.getElementById('container-input-alamat');
    const containerInputPasienBaruElm = document.getElementById('container-input-pasien-baru');
    const poliArr = JSON.parse(<?= json_encode($poliJson); ?>);
    let bpjsDanPasienLama = [true, false];
    let hasilCariPasien = [];

    function changeKunjSakit(value) {
        const kdPoliElm = document.querySelector('select[name="kdPoli"]');
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
        inputCariElm.classList.remove('is-invalid');
        const btnCariCurr = btnCariElm.innerHTML;
        btnCariElm.innerHTML = 'Loading'
        async function fetchPeserta() {
            const invalidElm = document.querySelector('#input-cari ~ .invalid-feedback');
            if (inputCariElm.value == '') {
                inputCariElm.classList.add('is-invalid')
                invalidElm.innerHTML = 'Harus diisi terlebih dahulu'
                btnCariElm.innerHTML = btnCariCurr;
                return;
            }
            const res = await fetch('/bpjs/getpeserta/' + cariPesertaBerdasarkan + '/' + inputCariElm.value);
            const resJson = await res.json()
            console.log(resJson)
            if (resJson.metaData) {
                if (resJson.metaData.code == 412) {
                    inputCariElm.classList.add('is-invalid')
                    invalidElm.innerHTML = resJson.response.message
                    btnCariElm.innerHTML = btnCariCurr;
                    return;
                } else if (resJson.metaData.code == 204) {
                    inputCariElm.classList.add('is-invalid')
                    invalidElm.innerHTML = 'Nomor tidak ditemukan'
                    btnCariElm.innerHTML = btnCariCurr;
                    return;
                }
            }
            //cek pasien itu udah ada di db pasien apa blm
            const resGetPasien = await fetch('../pendaftaran/getpasien/' + resJson.nama);
            const resGetPasienJson = await resGetPasien.json();
            console.log(resGetPasienJson);
            console.log(resGetPasienJson.length);
            if (resGetPasienJson.length > 0) {
                inputCariElm.classList.add('is-invalid')
                invalidElm.innerHTML = 'Nama pasien sudah pernah berkunjung'
                btnCariElm.innerHTML = btnCariCurr;
                // return;
            }
            let kelamin = 'Laki-laki'
            let nohp = 'Tidak ada'
            if (resJson.sex == 'P') kelamin = 'Perempuan'
            if (resJson.noHP != '') nohp = resJson.noHP
            inputNoBPJSElm.value = resJson.noKartu
            inputkdProviderPesertaElm.value = resJson.kdProviderPst.kdProvider
            inputnamaElm.value = resJson.nama
            inputnikElm.value = resJson.noKTP
            inputtglLahirElm.value = resJson.tglLahir.split("-")[2] + "-" + resJson.tglLahir.split("-")[1] + "-" + resJson.tglLahir.split("-")[0]
            inputkelaminElm.value = resJson.sex;
            inputnoHpElm.value = resJson.noHP;
            inputgolDarahElm.value = resJson.golDarah;
            containerDetailPesertaElm.innerHTML = ''
            containerDetailPesertaElm.innerHTML += '<p class="fw-bold m-0">' + resJson.nama + '</p>'
            containerDetailPesertaElm.innerHTML += '<p class="fw-bold m-0">' + resJson.hubunganKeluarga + '</p>'
            containerDetailPesertaElm.innerHTML += '<p class="fw-bold m-0">' + resJson.tglLahir + '</p>'
            containerDetailPesertaElm.innerHTML += '<p class="fw-bold m-0">' + kelamin + '</p>'
            containerDetailPesertaElm.innerHTML += '<p class="fw-bold m-0">' + resJson.kdProviderPst.kdProvider + ' - ' + resJson.kdProviderPst.nmProvider + '</p>'
            containerDetailPesertaElm.innerHTML += '<p class="fw-bold m-0">' + resJson.kdProviderGigi.kdProvider + ' - ' + resJson.kdProviderGigi.nmProvider + '</p>'
            containerDetailPesertaElm.innerHTML += '<p class="fw-bold m-0">' + nohp + '</p>'
            btnCariElm.innerHTML = btnCariCurr;
        }
        fetchPeserta();
    })

    function changePasienLama(value) {
        if (value) {
            bpjsDanPasienLama[1] = true;
            containerInputPasienLamaElm.classList.remove('d-none')
            containerInputAlamatElm.classList.add('d-none')
            bpjsSectionElm.classList.add('d-none');
            containerInputPasienBaruElm.classList.add('d-none');
        } else {
            inputNoRMElm.value = 'kosong';
            bpjsDanPasienLama[1] = false;
            containerInputAlamatElm.classList.remove('d-none')
            containerInputPasienLamaElm.classList.add('d-none')
            if (bpjsDanPasienLama[0]) {
                bpjsSectionElm.classList.remove('d-none');
                containerInputPasienBaruElm.classList.add('d-none');
            } else {
                containerInputPasienBaruElm.classList.remove('d-none');
            }
        }
    }

    inputnamaElm.addEventListener('change', (e) => {
        inputnamaElm.classList.remove('is-invalid')
        //cek pasien itu udah ada di db pasien apa blm
        async function getPasienOceria() {
            const resGetPasien = await fetch('../pendaftaran/getpasien/' + e.target.value);
            const resGetPasienJson = await resGetPasien.json();
            console.log(resGetPasienJson);
            console.log(resGetPasienJson.length);
            if (resGetPasienJson.length > 0) {
                inputnamaElm.classList.add('is-invalid')
            }
        }
        getPasienOceria()
    })

    function cariPasien() {
        inputCariPasienElm.classList.remove("is-invalid");
        const valuenya = inputCariPasienElm.value
        async function fetchPasien() {
            const res = await fetch('../pendaftaran/getpasien/' + valuenya);
            const resjson = await res.json();
            console.log(resjson)
            hasilCariPasien = resjson;

            if (resjson.length > 0) {
                // tabelPilihPasienElm.innerHTML = '<div class="d-flex w-100 mb-2 gap-2 border-bottom"><div style="flex: 1;" class="text-secondary">No.</div><div style="flex: 2;" class="text-secondary">Nama</div><div style="flex: 3;" class="text-secondary">Alamat</div><div style="flex: 1;" class="text-secondary">Action</div></div>'
                tabelPilihPasienElm.innerHTML = '';
                resjson.forEach((pasien, ind_p) => {
                    tabelPilihPasienElm.innerHTML += '<div class="d-flex w-100 gap-2">' +
                        '<div style="flex: 1;">' + pasien.id + '</div>' +
                        '<div style="flex: 2;">' + pasien.nama + '</div>' +
                        '<div style="flex: 1;">' + pasien.tglLahir + '</div>' +
                        '<div style="flex: 3;">' + pasien.alamat + '</div>' +
                        '<div style="flex: 1;"><button type="button" onclick="pilihPasienOceria(' + ind_p + ')" class="btn-default">Pilih</button></div>' +
                        '</div>'
                });
                containerPilihPasienElm.classList.remove("d-none");
            } else {
                inputCariPasienElm.classList.add("is-invalid");
            }
        }
        fetchPasien();
    }

    function closePilihPasien() {
        containerPilihPasienElm.classList.add("d-none");
    }

    function pilihPasienOceria(ind_pasien) {
        const hasilCariPasienCurr = hasilCariPasien[ind_pasien];
        console.log(hasilCariPasienCurr)
        containerDetailCariPasienElm.innerHTML =
            '<p class="fw-bold m-0">' + hasilCariPasienCurr.id + '</p>' +
            '<p class="fw-bold m-0">' + hasilCariPasienCurr.nama + '</p>' +
            '<p class="fw-bold m-0">' + (hasilCariPasienCurr.tglLahir ? hasilCariPasienCurr.tglLahir : 'Belum ada') + '</p>' +
            '<p class="fw-bold m-0">' + hasilCariPasienCurr.kelamin + '</p>' +
            '<p class="fw-bold m-0">' + hasilCariPasienCurr.alamat + '</p>';
        if (hasilCariPasienCurr.noBpjs == '0' || hasilCariPasienCurr.noBpjs == '' || hasilCariPasienCurr.noBpjs == null || hasilCariPasienCurr.noBpjs == undefined || hasilCariPasienCurr.noBpjs == false) {
            if (bpjsDanPasienLama[0]) {
                bpjsSectionElm.classList.remove('d-none');
            }
            containerDetailCariPasienElm.innerHTML += '<p class="fw-bold text-danger m-0">Belum diisi</p>'
        } else {
            containerDetailCariPasienElm.innerHTML += '<p class="fw-bold m-0">' + hasilCariPasienCurr.noBpjs + '</p>'
        }
        containerPilihPasienElm.classList.add("d-none");

        inputNoBPJSElm.value = hasilCariPasienCurr.noBpjs
        inputkdProviderPesertaElm.value = hasilCariPasienCurr.kdProviderPst
        inputnamaElm.value = hasilCariPasienCurr.nama
        inputtglLahirElm.value = hasilCariPasienCurr.tglLahir.split("-")[2] + "-" + hasilCariPasienCurr.tglLahir.split("-")[1] + "-" + hasilCariPasienCurr.tglLahir.split("-")[0]
        inputkelaminElm.value = hasilCariPasienCurr.kelamin;
        inputnoHpElm.value = hasilCariPasienCurr.noHp;
        inputgolDarahElm.value = hasilCariPasienCurr.golDarah != '' ? hasilCariPasienCurr.golDarah : '0';
        inputnikElm.value = hasilCariPasienCurr.nik;
        inputalamatElm.value = hasilCariPasienCurr.alamat;
        inputNoRMElm.value = hasilCariPasienCurr.id;
    }

    function changeBPJS(value) {
        if (value) {
            bpjsDanPasienLama[0] = true;
            if (bpjsDanPasienLama[1]) {
                bpjsSectionElm.classList.add('d-none');
                containerInputAlamatElm.classList.add('d-none')
                containerInputPasienLamaElm.classList.remove('d-none')
                containerInputPasienBaruElm.classList.add('d-none');
            } else {
                bpjsSectionElm.classList.remove('d-none');
                containerInputAlamatElm.classList.remove('d-none')
                containerInputPasienLamaElm.classList.add('d-none')
                containerInputPasienBaruElm.classList.add('d-none');
            }
        } else {
            bpjsDanPasienLama[0] = false;
            bpjsSectionElm.classList.add('d-none');
            if (bpjsDanPasienLama[1]) {
                containerInputAlamatElm.classList.add('d-none')
                containerInputPasienLamaElm.classList.remove('d-none')
                containerInputPasienBaruElm.classList.add('d-none');
            } else {
                containerInputAlamatElm.classList.remove('d-none')
                containerInputPasienLamaElm.classList.add('d-none')
                containerInputPasienBaruElm.classList.remove('d-none');
            }
        }
    }
</script>
<?= $this->endSection(); ?>