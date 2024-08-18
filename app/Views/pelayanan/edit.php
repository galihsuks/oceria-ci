<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<form action="/pelayanan/edit/<?= $pelayanan['id']; ?>" method="post">
    <div id="container-cari-rujukan" style="z-index: 5; position: fixed; top: 0; left: 0; width: 100vw; height: 100svh; background-color: rgba(0,0,0,0.5)" class="d-none justify-content-center align-items-center">
        <div class="p-4" style="background-color: white; width: 90%; max-height: 90svh; overflow-y: auto; border-radius: 1em;">
            <h5>Cari Faskes</h5>
            <hr>
            <div class="mb-1">
                <label class="form-label">Kondisi Khusus atau Spesialis</label>
                <div class="d-flex gap-3 w-100">
                    <div class="form-check flex-grow-1">
                        <input onchange="changeJenisSpesialis(false)" value="false" class="form-check-input" type="radio" name="spesialis" id="flexRadioDefault01" <?= $pelayanan['rujukLanjut'] ? ($pelayanan['rujukLanjut']['khusus'] ? 'checked' : '') : ''; ?>>
                        <label class="form-check-label" for="flexRadioDefault01">
                            Kondisi Khusus
                        </label>
                    </div>
                    <div class="form-check flex-grow-1">
                        <input onchange="changeJenisSpesialis(true)" value="true" class="form-check-input" type="radio" name="spesialis" id="flexRadioDefault02" <?= $pelayanan['rujukLanjut'] ? ($pelayanan['rujukLanjut']['subSpesialis'] ? 'checked' : '') : ''; ?>>
                        <label class="form-check-label" for="flexRadioDefault02">
                            Spesialis
                        </label>
                    </div>
                </div>
            </div>
            <hr>
            <div id="cari-faskes-khusus" class="<?= $pelayanan['rujukLanjut'] ? ($pelayanan['rujukLanjut']['khusus'] ? '' : 'd-none') : ''; ?>">
                <div class="mb-1" id="select-khusus">
                    <label for="">Referensi Khusus</label>
                    <select class="form-select" disabled>
                        <option>Loading..</option>
                    </select>
                </div>
                <script>
                    async function getKhusus() {
                        const fetchDokter = await fetch('/com/khusus<?= $pelayanan['rujukLanjut'] ? ($pelayanan['rujukLanjut']['khusus'] ? "/" . $pelayanan['rujukLanjut']['khusus']['kdKhusus'] : '') : ''; ?>');
                        const res = await fetchDokter.text();
                        document.getElementById('select-khusus').innerHTML = '<label for="">Referensi Khusus</label>'
                        document.getElementById('select-khusus').innerHTML += res
                    }
                    getKhusus()
                </script>
                <div class="mb-1">
                    <label for="">Spesialis</label>
                    <select type="text" class="form-select" onchange="changeSpesialis(event)">
                        <?php foreach ($spesialis as $s) { ?>
                            <option value="<?= $s['kdSpesialis']; ?>"><?= $s['nmSpesialis']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-1">
                    <label for="">Sub Spesialis</label>
                    <select type="text" class="form-select" name="kdSubSpesialis">
                        <option value="<?= $pelayanan['rujukLanjut'] ? ($pelayanan['rujukLanjut']['khusus'] ? $pelayanan['rujukLanjut']['khusus']['kdKhusus'] : '') : ''; ?>" selected>-- Sesuai pilihan sebelumnya --</option>
                    </select>
                </div>
                <div>
                    <label for="">Catatan</label>
                    <textarea class="form-control" name="catatan"><?= $pelayanan['rujukLanjut'] ? ($pelayanan['rujukLanjut']['khusus'] ? $pelayanan['rujukLanjut']['khusus']['catatan'] : '') : ''; ?></textarea>
                </div>
            </div>
            <div id="cari-faskes-spesialis" class="<?= $pelayanan['rujukLanjut'] ? ($pelayanan['rujukLanjut']['subSpesialis'] ? '' : 'd-none') : 'd-none'; ?>">
                <div class="mb-1">
                    <label for="">Spesialis</label>
                    <select type="text" class="form-select" onchange="changeSpesialis1(event)">
                        <?php foreach ($spesialis as $s) { ?>
                            <option value="<?= $s['kdSpesialis']; ?>"><?= $s['nmSpesialis']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-1">
                    <label for="">Sub Spesialis</label>
                    <select type="text" class="form-select" name="kdSubSpesialis1">
                        <option value="<?= $pelayanan['rujukLanjut'] ? ($pelayanan['rujukLanjut']['subSpesialis'] ? $pelayanan['rujukLanjut']['subSpesialis']['kdSubSpesialis1'] : '') : ''; ?>" selected>-- Sesuai pilihan sebelumnya --</option>
                    </select>
                </div>
                <div class="mb-1" id="select-sarana">
                    <label for="">Sarana</label>
                    <select class="form-select" disabled>
                        <option>Loading..</option>
                    </select>
                </div>
                <script>
                    async function getSarana() {
                        const fetchDokter = await fetch('/com/sarana<?= $pelayanan['rujukLanjut'] ? ($pelayanan['rujukLanjut']['subSpesialis'] ? "/" . $pelayanan['rujukLanjut']['subSpesialis']['kdSarana'] : '') : ''; ?>');
                        const res = await fetchDokter.text();
                        document.getElementById('select-sarana').innerHTML = '<label for="">Sarana</label>'
                        document.getElementById('select-sarana').innerHTML += res
                    }
                    getSarana()
                </script>
            </div>
            <hr>
            <div class="d-flex gap-1 align-items-center mb-2">
                <p class="m-0" style="width: fit-content;">Tanggal Estimasi Rujuk</p>
                <input type="date" name="tglEstRujuk" class="form-control" value="<?= $pelayanan['rujukLanjut'] ? $pelayanan['rujukLanjut']['tglEstRujuk'] : ''; ?>">
                <button type="button" class="btn-default" onclick="cariFaskes(event)">Cari</button>
            </div>
            <div class="alert alert-danger d-none" role="alert" id="alert-cari-faskes"></div>
            <div id="container-list-faskes" class="mb-2">
                <?php if ($faskes) { ?>
                    <input type="radio" name="kdppk" value="<?= $faskes['kdProvider']; ?>" checked id="kdppk1" class="d-none">
                    <label for="kdppk1">
                        <div class="list-faskes">
                            <h5><?= $faskes['nmProvider']; ?></h5>
                            <p>Kode PPK : <?= $faskes['kdProvider']; ?></p>
                        </div>
                    </label>
                <?php } ?>
            </div>
            <button type="button" class="btn-default" onclick="closeCariRujukan()">Ok</button>
        </div>
    </div>

    <h2>Edit Pelayanan</h2>
    <hr>
    <?php if ($msg) { ?>
        <div class="alert alert-danger" role="alert">
            <?= $msg; ?>
        </div>
    <?php } ?>

    <div class="baris-ke-kolom mb-3">
        <div class="limapuluh-ke-seratus">
            <div class="d-flex mt-3 gap-3">
                <div>
                    <p class="fw-bold m-0">Nama</p>
                    <p class="fw-bold m-0">No.Kartu BPJS</p>
                    <p class="fw-bold m-0">NIK</p>
                    <p class="fw-bold m-0">No. Rekam Medis</p>
                </div>
                <div id="container-detail-peserta">
                    <p class="fw-bold m-0"><?= $pasien['nama']; ?></p>
                    <p class="fw-bold m-0"><?= $pasien['noBpjs']; ?></p>
                    <p class="fw-bold m-0"><?= $pasien['nik']; ?></p>
                    <p class="fw-bold m-0"><?= $pasien['id']; ?></p>
                </div>
            </div>
            <hr>
            <div class="d-flex gap-1 mb-2">
                <div style="flex: 1;">
                    <label class="form-label">Tanggal Pulang</label>
                    <input type="date" class="form-control" name="tglPulang" value="<?= $pelayanan['tglPulang']; ?>">
                </div>
                <div id="select-dokter" style="flex: 1">
                    <label class="form-label">Dokter</label>
                    <select class="form-select" disabled>
                        <option>Loading..</option>
                    </select>
                </div>
                <script>
                    async function getDokter() {
                        const fetchDokter = await fetch('/com/dokter/<?= $pelayanan['kdDokter']; ?>');
                        const res = await fetchDokter.text();
                        document.getElementById('select-dokter').innerHTML = '<label class="form-label">Dokter</label>'
                        document.getElementById('select-dokter').innerHTML += res
                    }
                    getDokter()
                </script>
            </div>
            <div class="mb-1">
                <label class="form-label">Keluhan</label>
                <textarea class="form-control" name="keluhan" maxlength="100"><?= $pelayanan['keluhan']; ?></textarea>
            </div>
            <div class="mb-2">
                <label class="form-label">Anamnesa</label>
                <textarea class="form-control" name="anamnesa" maxlength="100"><?= $pelayanan['anamnesa']; ?></textarea>
            </div>
            <div class="mb-2">
                <label class="form-label">Riwayat Alergi</label>
                <div class="d-flex gap-1">
                    <div class="flex-grow-1" id="select-alergimakan">
                        <select class="form-select" disabled>
                            <option>Loading..</option>
                        </select>
                    </div>
                    <script>
                        async function getAlergiMakan() {
                            const fetchDokter = await fetch('/com/alergi/01/<?= $pelayanan['alergiMakan']; ?>');
                            const res = await fetchDokter.text();
                            document.getElementById('select-alergimakan').innerHTML = res
                        }
                        getAlergiMakan()
                    </script>
                    <div class="flex-grow-1" id="select-alergiudara">
                        <select class="form-select" disabled>
                            <option>Loading..</option>
                        </select>
                    </div>
                    <script>
                        async function getAlergiUdara() {
                            const fetchDokter = await fetch('/com/alergi/02/<?= $pelayanan['alergiUdara']; ?>');
                            const res = await fetchDokter.text();
                            document.getElementById('select-alergiudara').innerHTML = res
                        }
                        getAlergiUdara()
                    </script>
                    <div class="flex-grow-1" id="select-alergiobat">
                        <select class="form-select" disabled>
                            <option>Loading..</option>
                        </select>
                    </div>
                    <script>
                        async function getAlergiObat() {
                            const fetchDokter = await fetch('/com/alergi/03/<?= $pelayanan['alergiObat']; ?>');
                            const res = await fetchDokter.text();
                            document.getElementById('select-alergiobat').innerHTML = res
                        }
                        getAlergiObat()
                    </script>
                </div>
            </div>
            <div class="mb-2" id="select-prognosa">
                <label class="form-label">Prognosa</label>
                <select class="form-select" disabled>
                    <option>Loading..</option>
                </select>
            </div>
            <script>
                async function getPrognosa() {
                    const fetchDokter = await fetch('/com/prognosa/<?= $pelayanan['kdPrognosa']; ?>');
                    const res = await fetchDokter.text();
                    document.getElementById('select-prognosa').innerHTML = '<label class="form-label">Prognosa</label>'
                    document.getElementById('select-prognosa').innerHTML += res
                }
                getPrognosa()
            </script>
            <div class="mb-2">
                <label class="form-label">TACC</label>
                <div class="d-flex gap-1">
                    <select class="form-select" style="flex: 1;" name="kdTacc" onchange="changeTacc(event)">
                        <?php
                        $indTaccSelected = 0;
                        foreach ($TACC as $ind_t => $t) { ?>
                            <option value="<?= $t['kdTacc']; ?>" <?= $pelayanan['kdTacc'] == $t['kdTacc'] ? 'selected' : ''; ?>><?= $t['nmTacc']; ?></option>
                        <?php
                            if ($pelayanan['kdTacc'] == $t['kdTacc']) $indTaccSelected = $ind_t;
                        } ?>
                    </select>
                    <select class="form-select" style="flex: 1;" name="alasanTacc">
                        <?php
                        if (count($TACC[$indTaccSelected]['alasanTacc']) == 0) { ?>
                            <option value="null" selected>Tidak ada</option>
                            <?php } else {
                            foreach ($TACC[$indTaccSelected]['alasanTacc'] as $at) { ?>
                                <option value="<?= $at; ?>" <?= $at == $pelayanan['alasanTacc'] ? 'selected' : ''; ?>><?= $at; ?></option>
                        <?php }
                        } ?>
                    </select>
                    <script>
                        const alasanTaccElm = document.querySelector('select[name="alasanTacc"]');
                        let refTACC = [{
                                kdTacc: "-1",
                                nmTacc: "Tanpa TACC",
                                alasanTacc: []
                            },
                            {
                                kdTacc: "1",
                                nmTacc: "Time",
                                alasanTacc: ["< 3 Hari", ">= 3 - 7 Hari", ">= 7 Hari"]
                            },
                            {
                                kdTacc: "2",
                                nmTacc: "Age",
                                alasanTacc: [
                                    "< 1 Bulan",
                                    ">= 1 Bulan s/d < 12 Bulan",
                                    ">= 1 Tahun s/d < 5 Tahun",
                                    ">= 5 Tahun s/d < 12 Tahun",
                                    ">= 12 Tahun s/d < 55 Tahun",
                                    ">= 55 Tahun"
                                ]
                            },
                            {
                                kdTacc: "3",
                                nmTacc: "Complication",
                                alasanTacc: ["Belum mengisi diagnosa"]
                            },
                            {
                                kdTacc: "4",
                                nmTacc: "Comorbidity",
                                alasanTacc: ["< 3 Hari", ">= 3 - 7 Hari", ">= 7 Hari"]
                            }
                        ]

                        function changeTacc(e) {
                            console.log(e.target.value)
                            alasanTaccElm.innerHTML = '';
                            switch (e.target.value) {
                                case '-1':
                                    alasanTaccElm.innerHTML = '<option value="null">Tidak ada</option>'
                                    break;
                                case '1':
                                    refTACC[1].alasanTacc.forEach(alasan => {
                                        alasanTaccElm.innerHTML += '<option value="' + alasan + '">' + alasan + '</option>'
                                    });
                                    break;
                                case '2':
                                    refTACC[2].alasanTacc.forEach(alasan => {
                                        alasanTaccElm.innerHTML += '<option value="' + alasan + '">' + alasan + '</option>'
                                    });
                                    break;
                                case '3':
                                    refTACC[3].alasanTacc.forEach(alasan => {
                                        alasanTaccElm.innerHTML += '<option value="' + alasan + '">' + alasan + '</option>'
                                    });
                                    break;
                                case '4':
                                    refTACC[4].alasanTacc.forEach(alasan => {
                                        alasanTaccElm.innerHTML += '<option value="' + alasan + '">' + alasan + '</option>'
                                    });
                                    break;
                                default:
                                    break;
                            }
                        }
                    </script>
                </div>
            </div>
        </div>
        <div class="limapuluh-ke-seratus">
            <div class="mb-1">
                <label class="form-label">Terapi Obat</label>
                <textarea class="form-control" name="terapiObat" maxlength="100"><?= $pelayanan['terapiObat']; ?></textarea>
            </div>
            <div class="mb-1">
                <label class="form-label">Terapi Non Obat</label>
                <textarea class="form-control" name="terapiNonObat" maxlength="100"><?= $pelayanan['terapiNonObat']; ?></textarea>
            </div>
            <div class="mb-2">
                <label class="form-label">BMHP</label>
                <textarea class="form-control" name="bmhp" maxlength="100"><?= $pelayanan['bmhp']; ?></textarea>
            </div>
            <div class="mb-2">
                <label class="form-label">Diagnosa</label>
                <div class="d-flex gap-1 mb-1">
                    <input style="flex: 1" class="form-control input-diagnosa" type="text" name="kdDiag1" value="<?= $pelayanan['kdDiag1'] ? $pelayanan['kdDiag1'] : ''; ?>">
                    <input style="flex: 2;" class="form-control display-diagnosa" disabled type="text">
                </div>
                <div class="d-flex gap-1 mb-1">
                    <input style="flex: 1" class="form-control input-diagnosa" type="text" name="kdDiag2" value="<?= $pelayanan['kdDiag2'] ? $pelayanan['kdDiag2'] : ''; ?>">
                    <input style="flex: 2;" class="form-control display-diagnosa" disabled type="text">
                </div>
                <div class="d-flex gap-1">
                    <input style="flex: 1" class="form-control input-diagnosa" type="text" name="kdDiag3" value="<?= $pelayanan['kdDiag3'] ? $pelayanan['kdDiag3'] : ''; ?>">
                    <input style="flex: 2;" class="form-control display-diagnosa" disabled type="text">
                </div>
            </div>
            <div class="d-flex gap-1 mb-2">
                <div style="flex: 1;" id="select-sadar">
                    <label class="form-label">Kesadaran</label>
                    <select class="form-select" disabled>
                        <option>Loading..</option>
                    </select>
                </div>
                <script>
                    async function getSadar() {
                        const fetchDokter = await fetch('/com/sadar/<?= $pelayanan['kdSadar']; ?>');
                        const res = await fetchDokter.text();
                        document.getElementById('select-sadar').innerHTML = '<label class="form-label">Kesadaran</label>'
                        document.getElementById('select-sadar').innerHTML += res
                    }
                    getSadar()
                </script>
                <div style="flex: 1;">
                    <label class="form-label">Suhu</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="suhu" value="<?= $pelayanan['suhu']; ?>">
                        <span class="input-group-text" id="basic-addon2">°C</span>
                    </div>
                </div>
            </div>
            <div class="d-flex mb-2 gap-1">
                <div style="flex: 1;" id="select-status-pulang">
                    <label class="form-label">Status Pulang</label>
                    <select class="form-select" disabled>
                        <option>Loading..</option>
                    </select>
                </div>
                <script>
                    async function getStatusPulang() {
                        const fetchDokter = await fetch('/com/statuspulang/<?= $pendaftaran['kdTkp'] == '20' ? 'true' : 'false'; ?>/<?= $pelayanan['kdStatusPulang']; ?>');
                        const res = await fetchDokter.text();
                        document.getElementById('select-status-pulang').innerHTML = '<label class="form-label">Status Pulang</label>'
                        document.getElementById('select-status-pulang').innerHTML += res
                    }
                    getStatusPulang()
                </script>
                <div class="align-items-end display-rujukan-vertikal <?= $pelayanan['kdStatusPulang'] == '4' ? 'd-flex' : 'd-none'; ?>" style="width: fit-content;">
                    <button type="button" class="btn-default" onclick="openCariRujukan()">Ubah Faskes Rujukan</button>
                </div>
            </div>
            <div class="display-rujukan-vertikal <?= $pelayanan['kdStatusPulang'] == '4' ? '' : 'd-none'; ?>">
                <div class="d-flex gap-3">
                    <div>
                        <p class="fw-bold m-0">Tgl. Est Rujuk</p>
                        <p class="fw-bold m-0">Nama PPK</p>
                        <p class="fw-bold m-0">Sub Spesialis</p>
                        <p class="fw-bold m-0">Kondisi Khusus</p>
                    </div>
                    <div id="display-rujukan-vertikal">
                        <p class="fw-bold m-0"><?= $pelayanan['kdStatusPulang'] == '4' ? $pelayanan['rujukLanjut']['tglEstRujuk'] : ''; ?></p>
                        <p class="fw-bold m-0"><?= $pelayanan['kdStatusPulang'] == '4' ? $faskes['nmProvider'] : ''; ?></p>
                        <p class="fw-bold m-0"><?= $pelayanan['kdStatusPulang'] == '4' ? ($pelayanan['rujukLanjut']['subSpesialis'] ? $pelayanan['rujukLanjut']['subSpesialis']['kdSubSpesialis1'] : '') : ''; ?></p>
                        <p class="fw-bold m-0"><?= $pelayanan['kdStatusPulang'] == '4' ? ($pelayanan['rujukLanjut']['khusus'] ? $pelayanan['rujukLanjut']['khusus']['kdKhusus'] : '') : ''; ?></p>
                    </div>
                </div>
            </div>
            <hr>
            <button type="submit" class="btn-default">Simpan</button>
        </div>
    </div>
</form>
<script>
    const alertCariFaskesElm = document.getElementById('alert-cari-faskes');
    // const alertCariPasienElm = document.getElementById('alert-cari-pasien');
    // const btnCariPasienElm = document.getElementById('btn-cari-pasien');
    const inputTglDaftarElm = document.querySelector('input[name="tglDaftar"]')
    const inputNoUrutElm = document.querySelector('input[name="noUrut"]')
    const containerDetailPesertaElm = document.getElementById('container-detail-peserta');
    const kdStatusPulangElm = document.querySelector('select[name="kdStatusPulang"]');
    const inputDiagElm = document.querySelectorAll('.input-diagnosa');
    const displayDiagElm = document.querySelectorAll('.display-diagnosa');
    const displayRujukanVertikalElm = document.querySelectorAll('.display-rujukan-vertikal');
    const displayRujukanVertikalTeksElm = document.querySelector('#display-rujukan-vertikal');
    const cariFaskesKhususElm = document.getElementById('cari-faskes-khusus')
    const cariFaskesSpesialisElm = document.getElementById('cari-faskes-spesialis')
    const containerCariRujukanElm = document.getElementById('container-cari-rujukan')
    const kdSubSpesialisElm = document.querySelector('select[name="kdSubSpesialis"]')
    const kdSubSpesialis1Elm = document.querySelector('select[name="kdSubSpesialis1"]')
    const containerListFaskesElm = document.getElementById('container-list-faskes');
    const inputKeluhanElm = document.querySelector('textarea[name="keluhan"]');
    let jenisSpesialisSelected = 'khusus';
    let noKartuBpjs = '<?= $pelayanan['noKunjungan']; ?>';

    function kosongkanListFaskes() {
        containerListFaskesElm.innerHTML = '';
        displayRujukanVertikalTeksElm.children[0].innerHTML = ''
        displayRujukanVertikalTeksElm.children[1].innerHTML = ''
        displayRujukanVertikalTeksElm.children[2].innerHTML = ''
        displayRujukanVertikalTeksElm.children[3].innerHTML = ''
    }

    function changeSpesialis(e) {
        console.log(e.target.value)
        async function fetchSubspesialis() {
            const res = await fetch('/bpjs/getrefsubspesialis/' + e.target.value);
            const resJson = await res.json();
            console.log(resJson)
            const hasil = resJson.list;
            kdSubSpesialisElm.innerHTML = '';
            hasil.forEach((element, ind) => {
                kdSubSpesialisElm.innerHTML += '<option value="' + element.kdSubSpesialis + '" ' + (ind == 0 ? 'selected' : '') + '>' + element.nmSubSpesialis + '</option>'
            });
        }
        fetchSubspesialis()
        kosongkanListFaskes()
    }

    function changeSpesialis1(e) {
        console.log(e.target.value)
        async function fetchSubspesialis() {
            const res = await fetch('/bpjs/getrefsubspesialis/' + e.target.value);
            const resJson = await res.json();
            console.log(resJson)
            const hasil = resJson.list;
            kdSubSpesialis1Elm.innerHTML = '';
            hasil.forEach(element => {
                kdSubSpesialis1Elm.innerHTML += '<option value="' + element.kdSubSpesialis + '">' + element.nmSubSpesialis + '</option>'
            });
        }
        fetchSubspesialis()
        kosongkanListFaskes()
    }

    function changeJenisSpesialis(value) {
        console.log(value)
        if (value) {
            jenisSpesialisSelected = 'spesialis';
            cariFaskesKhususElm.classList.add('d-none')
            cariFaskesSpesialisElm.classList.remove('d-none')
        } else {
            jenisSpesialisSelected = 'khusus';
            cariFaskesKhususElm.classList.remove('d-none')
            cariFaskesSpesialisElm.classList.add('d-none')
        }
        kosongkanListFaskes()
    }

    // btnCariPasienElm.addEventListener('click', () => {
    //     alertCariPasienElm.classList.add('d-none');
    //     btnCariPasienElm.innerHTML = 'Loading'
    //     const tanggal = inputTglDaftarElm.value.split("-")[2] + "-" + inputTglDaftarElm.value.split("-")[1] + "-" + inputTglDaftarElm.value.split("-")[0]
    //     async function fetchPasien() {
    //         const res = await fetch('/pelayanan/getpendaftaran/' + tanggal + '/' + inputNoUrutElm.value)
    //         const resJson = await res.json()
    //         console.log(resJson)
    //         if (resJson) {
    //             if (resJson.status == "Sudah dilayani") {
    //                 alertCariPasienElm.innerHTML = 'Pasien sudah dilayani'
    //                 alertCariPasienElm.classList.remove('d-none');
    //             } else {
    //                 containerDetailPesertaElm.innerHTML = '<p class="fw-bold m-0">' + resJson.nama + '</p>' +
    //                     '<p class="fw-bold m-0">' + (resJson.noKartu ? resJson.noKartu : 'Belum ada') + '</p>' +
    //                     '<p class="fw-bold m-0">' + (resJson.nik ? resJson.nik : 'Belum ada') + '</p>' +
    //                     '<p class="fw-bold m-0">' + resJson.noRM + '</p>'

    //                 if (resJson.bpjs == '1') {
    //                     const rawatInap = resJson.kdTkp == '20' ? 'true' : 'false';
    //                     const resGetStatutPulang = await fetch('/bpjs/getstatuspulang/' + rawatInap);
    //                     const resGetStatutPulangJson = await resGetStatutPulang.json();
    //                     console.log(resGetStatutPulangJson);
    //                     kdStatusPulangElm.innerHTML = '';
    //                     resGetStatutPulangJson.list.forEach((element, ind_e) => {
    //                         kdStatusPulangElm.innerHTML += '<option value="' + element.kdStatusPulang + '" ' + (ind_e == 0 ? 'selected' : '') + '>' + element.nmStatusPulang + '</option>'
    //                     });
    //                     kdStatusPulangElm.removeAttribute('disabled')

    //                     noKartuBpjs = (resJson.noKartu ? resJson.noKartu : false);
    //                 } else {
    //                     kdStatusPulangElm.innerHTML = '<option value="1" selected="">Meninggal</option><option value="3">Berobat Jalan</option>'
    //                     kdStatusPulangElm.removeAttribute('disabled')
    //                 }
    //                 inputKeluhanElm.innerHTML = resJson.keluhan
    //             }
    //         } else {
    //             alertCariPasienElm.innerHTML = 'Pasien tidak ditemukan'
    //             alertCariPasienElm.classList.remove('d-none');
    //             containerDetailPesertaElm.innerHTML = ''
    //         }
    //         btnCariPasienElm.innerHTML = 'Cari'
    //     }
    //     fetchPasien()
    //     kosongkanListFaskes()
    // })

    inputDiagElm.forEach((element, ind_e) => {
        element.addEventListener('change', (e) => {
            console.log(e.target.value)
            async function fetchDiag() {
                const res = await fetch('/bpjs/getdiagnosa/' + e.target.value);
                const resJson = await res.json();
                console.log(resJson);
                if (resJson.list) {
                    displayDiagElm[ind_e].value = resJson.list[0].nmDiag
                } else {
                    displayDiagElm[ind_e].value = 'Tidak ditemukan'
                }
            }
            fetchDiag()
        })
    });

    kdStatusPulangElm.addEventListener('change', (e) => {
        const kdStatusPulang = e.target.value;
        if (kdStatusPulang == '4') {
            displayRujukanVertikalElm.forEach(element => {
                element.classList.remove('d-none')
                element.classList.add('d-flex')
            });
            cariFaskesKhususElm.classList.remove('d-none')
        } else {
            displayRujukanVertikalElm.forEach(element => {
                element.classList.add('d-none')
                element.classList.remove('d-flex')
            });
        }
    })

    function openCariRujukan() {
        containerCariRujukanElm.classList.remove('d-none')
        containerCariRujukanElm.classList.add('d-flex')
    }

    function closeCariRujukan() {
        containerCariRujukanElm.classList.add('d-none')
        containerCariRujukanElm.classList.remove('d-flex')
    }

    function cariFaskes(e) {
        e.target.innerHTML = 'Loading'
        alertCariFaskesElm.classList.add('d-none')
        const tglEstRujukElmValue = document.querySelector('input[name="tglEstRujuk"]').value;
        const tglEstRujuk = tglEstRujukElmValue.split("-")[2] + "-" + tglEstRujukElmValue.split("-")[1] + "-" + tglEstRujukElmValue.split("-")[0];
        console.log('sedang proses cari faskes')
        containerListFaskesElm.innerHTML = ''
        if (jenisSpesialisSelected == 'khusus') {
            const kdKhususElm = document.querySelector('select[name="kdKhusus"]');
            console.log('/bpjs/getfasketrujukankhusus/' + kdKhususElm.value + "/" + kdSubSpesialisElm.value + "/" + noKartuBpjs + '/' + tglEstRujuk)
            async function fetchFaskesKhusus() {
                const res = await fetch('/bpjs/getfasketrujukankhusus/' + kdKhususElm.value + "/" + kdSubSpesialisElm.value + "/" + noKartuBpjs + '/' + tglEstRujuk)
                const resJson = await res.json();
                console.log(resJson)
                if (resJson.metaData != undefined) {
                    alertCariFaskesElm.classList.remove('d-none')
                    alertCariFaskesElm.innerHTML = resJson.metaData.message
                } else {
                    const listFaskes = resJson.list;
                    listFaskes.forEach((faskes, indFaskes) => {
                        containerListFaskesElm.innerHTML += '<input type="radio" name="kdppk" value="' + faskes.kdppk + '" id="kdppk' + (indFaskes + 1) + `" class="d-none" onchange="changeKdPpk('` + faskes.nmppk + `')">` +
                            '<label for="kdppk' + (indFaskes + 1) + '">' +
                            '<div class="list-faskes"><h5>' + faskes.nmppk + '</h5><div class="baris-ke-kolom"><div class="limapuluh-ke-seratus">' +
                            '<p>' + faskes.alamatPpk + '</p><p>' + faskes.jadwal + '</p><p>' + faskes.telpPpk + '</p></div><div class="limapuluh-ke-seratus">' +
                            '<p>Kode PPK : ' + faskes.kdppk + '</p><p>Kelas : ' + faskes.kelas + '</p><p>Persentase : ' + faskes.persentase + '</p><p>Jumlah Rujuk : ' + faskes.jmlRujuk + '</p><p>Kapasitas : ' + faskes.kapasitas + '</p></div></div></div></label>'
                    });

                    displayRujukanVertikalTeksElm.children[0].innerHTML = tglEstRujuk
                    displayRujukanVertikalTeksElm.children[1].innerHTML = 'Belum dipilih'
                    displayRujukanVertikalTeksElm.children[2].innerHTML = kdSubSpesialisElm.value
                    displayRujukanVertikalTeksElm.children[3].innerHTML = kdKhususElm.value
                }
                e.target.innerHTML = 'Cari'
            }
            fetchFaskesKhusus()
        } else if (jenisSpesialisSelected == 'spesialis') {
            const kdSaranaElm = document.querySelector('select[name="kdSarana"]');
            console.log('/bpjs/getfasketrujukansubspesialis/' + kdSubSpesialis1Elm.value + "/" + kdSaranaElm.value + '/' + tglEstRujuk)
            async function fetchFaskesSpesialis() {
                const res = await fetch('/bpjs/getfasketrujukansubspesialis/' + kdSubSpesialis1Elm.value + "/" + kdSaranaElm.value + '/' + tglEstRujuk)
                const resJson = await res.json();
                console.log(resJson)
                if (resJson.metaData != undefined) {
                    alertCariFaskesElm.classList.remove('d-none')
                    alertCariFaskesElm.innerHTML = resJson.metaData.message
                } else {
                    const listFaskes = resJson.list;
                    listFaskes.forEach((faskes, indFaskes) => {
                        containerListFaskesElm.innerHTML += '<input type="radio" name="kdppk" value="' + faskes.kdppk + '" id="kdppk' + (indFaskes + 1) + `" class="d-none" onchange="changeKdPpk('` + faskes.nmppk + `')">` +
                            '<label for="kdppk' + (indFaskes + 1) + '">' +
                            '<div class="list-faskes"><h5>' + faskes.nmppk + '</h5><div class="baris-ke-kolom"><div class="limapuluh-ke-seratus">' +
                            '<p>' + faskes.alamatPpk + '</p><p>' + faskes.jadwal + '</p><p>' + faskes.telpPpk + '</p></div><div class="limapuluh-ke-seratus">' +
                            '<p>Kode PPK : ' + faskes.kdppk + '</p><p>Kelas : ' + faskes.kelas + '</p><p>Persentase : ' + faskes.persentase + '</p><p>Jumlah Rujuk : ' + faskes.jmlRujuk + '</p><p>Kapasitas : ' + faskes.kapasitas + '</p></div></div></div></label>'
                    });

                    displayRujukanVertikalTeksElm.children[0].innerHTML = tglEstRujuk
                    displayRujukanVertikalTeksElm.children[1].innerHTML = 'Belum dipilih'
                    displayRujukanVertikalTeksElm.children[2].innerHTML = kdSubSpesialisElm.value
                    displayRujukanVertikalTeksElm.children[3].innerHTML = 'Tidak dipilih'
                }
                e.target.innerHTML = 'Cari'
            }
            fetchFaskesSpesialis()
        }
    }

    function changeKdPpk(namaPpk) {
        displayRujukanVertikalTeksElm.children[1].innerHTML = namaPpk
    }
</script>
<?= $this->endSection(); ?>