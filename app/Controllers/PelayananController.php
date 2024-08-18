<?php

namespace App\Controllers;

use App\Models\PendaftaranModel;
use App\Models\KunjunganModel;
use App\Models\PelayananModel;
use App\Models\PasienModel;

class PelayananController extends BaseController
{
    protected $kodePPK;
    protected $consId;
    protected $secretKey;
    protected $userkeyPCare;
    protected $username;
    protected $password;
    protected $baseUrl;
    protected $auth;
    protected $arrCurl;

    protected $kunjunganModel;
    protected $pelayananModel;
    protected $pendaftaranModel;
    protected $pasienModel;
    public function __construct()
    {
        $this->kodePPK = '095';
        $this->consId = '';
        $this->secretKey = '';
        $this->userkeyPCare = '';
        $this->username = '';
        $this->password = '';
        $this->baseUrl = "https://apijkn-dev.bpjs-kesehatan.go.id/pcare-rest-dev";
        $this->auth = base64_encode($this->username . ":" . $this->password . ":" . $this->kodePPK);
        $this->arrCurl = [
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER => [
                "Accept: application/json",
                "X-cons-id: " . $this->consId,
                "X-authorization: Basic " . $this->auth,
                "user_key: " . $this->userkeyPCare
            ],
        ];

        $this->pendaftaranModel = new PendaftaranModel();
        $this->pelayananModel = new PelayananModel();
        $this->kunjunganModel = new KunjunganModel();
        $this->pasienModel = new PasienModel();
    }
    public function getPendaftaran($tglDaftar, $noUrut)
    {
        $pendaftaran = $this->pendaftaranModel->where(['tglDaftar' => $tglDaftar, 'noUrut' => $noUrut])->first();
        return $this->response->setJSON($pendaftaran, false);
    }
    public function addPelayanan()
    {
        //get spesialis
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $variabel1 = $this->consId . "&" . $tStamp;
        $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));

        $arrCurl = $this->arrCurl;
        $arrCurl[CURLOPT_URL] = $this->baseUrl . "/spesialis";
        $arrCurl[CURLOPT_CUSTOMREQUEST] = "GET";
        $arrCurl[CURLOPT_HTTPHEADER][4] = "X-timestamp: " . $tStamp;
        $arrCurl[CURLOPT_HTTPHEADER][5] = "X-signature: " . $signature;
        $arrCurl[CURLOPT_HTTPHEADER][6] = "content-type: application/json";

        $curl = curl_init();
        curl_setopt_array($curl, $arrCurl);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return "cURL Error #:" . $err;
        }
        $hasil = json_decode($response, true);
        // dd($hasil);
        if (substr($hasil['metaData']['code'], 0, 1) == '4') {
            return $this->response->setJSON($hasil, false);
        }
        $string = $hasil['response'];
        $key = $this->consId . $this->secretKey . $tStamp;
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
        $getRefSpesialis = json_decode($hasil_dekrip, true);

        //get sub spesialis
        $arrCurl[CURLOPT_URL] = $this->baseUrl . "/spesialis/" . $getRefSpesialis['list'][0]['kdSpesialis'] . "/subspesialis";
        $curl = curl_init();
        curl_setopt_array($curl, $arrCurl);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return "cURL Error #:" . $err;
        }
        $hasil = json_decode($response, true);
        if (substr($hasil['metaData']['code'], 0, 1) == '4') {
            return $this->response->setJSON($hasil, false);
        }
        $string = $hasil['response'];
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
        $getRefSubSpesialis = json_decode($hasil_dekrip, true);

        $refTACC = [
            [
                "kdTacc" => "-1",
                "nmTacc" => "Tanpa TACC",
                "alasanTacc" => []
            ],
            [
                "kdTacc" => "1",
                "nmTacc" => "Time",
                "alasanTacc" => ["< 3 Hari", ">= 3 - 7 Hari", ">= 7 Hari"]
            ],
            [
                "kdTacc" => "2",
                "nmTacc" => "Age",
                "alasanTacc" => [
                    "< 1 Bulan",
                    ">= 1 Bulan s/d < 12 Bulan",
                    ">= 1 Tahun s/d < 5 Tahun",
                    ">= 5 Tahun s/d < 12 Tahun",
                    ">= 12 Tahun s/d < 55 Tahun",
                    ">= 55 Tahun"
                ]
            ],
            [
                "kdTacc" => "3",
                "nmTacc" => "Complication",
                "alasanTacc" => ["A09 - Diarrhoea and gastroenteritis of presumed infectious origin"]
            ],
            [
                "kdTacc" => "4",
                "nmTacc" => "Comorbidity",
                "alasanTacc" => ["< 3 Hari", ">= 3 - 7 Hari", ">= 7 Hari"]
            ]
        ];


        $d = strtotime("+7 Hours");
        $tanggal = date("Y-m-d", $d);
        $data = [
            'title' => 'Add Pelayanan',
            'tanggal' => $tanggal,
            'spesialis' => $getRefSpesialis['list'],
            'subspesialis' => $getRefSubSpesialis['list'],
            'msg' => session()->getFlashdata('msg'),
            'TACC' => $refTACC
        ];
        return view('pelayanan/add', $data);
    }
    public function actionAddPelayanan()
    {
        // dd($this->request->getVar());
        if (!$this->validate([
            'tglDaftar' => ['rules' => 'required'],
            'noUrut' => ['rules' => 'required'],
            'keluhan' => ['rules' => 'required'],
            'kdSadar' => ['rules' => 'required'],
            'kdStatusPulang' => ['rules' => 'required'],
            'tglPulang' => ['rules' => 'required'],
            // 'kdDokter' => ['rules' => 'required'],
            'kdDiag1' => ['rules' => 'required'],
            'anamnesa' => ['rules' => 'required'],
            'alergiMakan' => ['rules' => 'required'],
            'alergiUdara' => ['rules' => 'required'],
            'alergiObat' => ['rules' => 'required'],
            'kdPrognosa' => ['rules' => 'required'],
            'terapiObat' => ['rules' => 'required'],
            'terapiNonObat' => ['rules' => 'required'],
            'bmhp' => ['rules' => 'required'],
            'suhu' => ['rules' => 'required'],
        ])) {
            session()->setFlashdata('msg', 'Ada data belum terisi');
            return redirect()->to('/pelayanan/add')->withInput();
        }

        $tglDaftar = strtotime($this->request->getVar('tglDaftar'));
        $pendaftaran = $this->pendaftaranModel->where(['tglDaftar' => date("d-m-Y", $tglDaftar), 'noUrut' => $this->request->getVar('noUrut')])->first();

        $getDokter = [
            "count" => 1,
            "list" => [
                [
                    "kdDokter" => "133797",
                    "nmDokter" => "Tenaga Medis 133797"
                ]
            ]
        ];

        $datanya = [
            "noKunjungan" => null,
            "noKartu" => $pendaftaran['noKartu'],
            "tglDaftar" => $pendaftaran['tglDaftar'],
            "kdPoli" => $pendaftaran['kdPoli'],
            "sistole" => $pendaftaran['sistole'],
            "diastole" => $pendaftaran['diastole'],
            "beratBadan" => $pendaftaran['beratBadan'],
            "tinggiBadan" => $pendaftaran['tinggiBadan'],
            "respRate" => $pendaftaran['respRate'],
            "heartRate" => $pendaftaran['heartRate'],
            "lingkarPerut" => $pendaftaran['lingkarPerut'],
            "keluhan" => $this->request->getVar('keluhan'),

            "kdSadar" => $this->request->getVar('kdSadar'),
            "kdStatusPulang" => $this->request->getVar('kdStatusPulang'),
            "tglPulang" => date("d-m-Y", strtotime($this->request->getVar('tglPulang'))),
            "kdDokter" => $this->request->getVar('kdDokter') ? $this->request->getVar('kdDokter') : $getDokter['list'][0]['kdDokter'],
            "kdDiag1" => $this->request->getVar('kdDiag1'),
            "kdDiag2" => $this->request->getVar('kdDiag2') ? $this->request->getVar('kdDiag2') : null,
            "kdDiag3" => $this->request->getVar('kdDiag3') ? $this->request->getVar('kdDiag3') : null,
            "kdPoliRujukInternal" => null,
            "rujukLanjut" => null,
            "kdTacc" => $this->request->getVar('kdTacc'),
            "alasanTacc" => $this->request->getVar('alasanTacc') == 'null' ? null : $this->request->getVar('alasanTacc'),
            "anamnesa" => $this->request->getVar('anamnesa'),
            "alergiMakan" => $this->request->getVar('alergiMakan'),
            "alergiUdara" => $this->request->getVar('alergiUdara'),
            "alergiObat" => $this->request->getVar('alergiObat'),
            "kdPrognosa" => $this->request->getVar('kdPrognosa'),
            "terapiObat" => $this->request->getVar('terapiObat'),
            "terapiNonObat" => $this->request->getVar('terapiNonObat'),
            "bmhp" => $this->request->getVar('bmhp'),
            "suhu" => $this->request->getVar('suhu'),
        ];

        //Rujuk Vertikal
        if ($datanya['kdStatusPulang'] == '4') {
            $spesialis = $this->request->getVar('spesialis') == 'true' ? true : false;
            if ($spesialis) {
                $datanya['rujukLanjut'] = [
                    "tglEstRujuk" => date("d-m-Y", strtotime($this->request->getVar('tglEstRujuk'))),
                    "kdppk" => $this->request->getVar('kdppk'),
                    "subSpesialis" => [
                        "kdSubSpesialis1" => $this->request->getVar('kdSubSpesialis1'),
                        "kdSarana" => $this->request->getVar('kdSarana'),
                    ],
                    "khusus" => null
                ];
            } else {
                $datanya['rujukLanjut'] = [
                    "tglEstRujuk" => date("d-m-Y", strtotime($this->request->getVar('tglEstRujuk'))),
                    "kdppk" => $this->request->getVar('kdppk'),
                    "subSpesialis" => null,
                    "khusus" => [
                        "kdKhusus" => $this->request->getVar('kdKhusus'),
                        "kdSubSpesialis" => $this->request->getVar('kdSubSpesialis'),
                        "catatan" => $this->request->getVar('catatan'),
                    ]
                ];
            }
        }
        //Rujuk Horizontal
        if ($datanya['kdStatusPulang'] == '6') {
            $datanya['rujukLanjut'] = [
                "tglEstRujuk" => date("d-m-Y", strtotime($this->request->getVar('tglEstRujuk'))),
                "kdppk" => $this->request->getVar('kdppk'),
                "subSpesialis" => [
                    "kdSubSpesialis1" => $this->request->getVar('kdSubSpesialis1'),
                    "kdSarana" => $this->request->getVar('kdSarana'),
                ],
                "khusus" => [
                    "kdKhusus" => $this->request->getVar('kdKhusus'),
                    "kdSubSpesialis" => $this->request->getVar('kdSubSpesialis'),
                    "catatan" => $this->request->getVar('catatan'),
                ]
            ];
            $datanya['kdPoliRujukInternal'] = $this->request->getVar('kdPoliRujukInternal');
        }

        // dd([
        //     'getVar' => $this->request->getVar(),
        //     'datanya' => $datanya
        // ]);

        if ($pendaftaran['bpjs']) {
            date_default_timezone_set('UTC');
            $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
            $variabel1 = $this->consId . "&" . $tStamp;
            $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));

            $arrCurl = $this->arrCurl;
            $arrCurl[CURLOPT_URL] = $this->baseUrl . "/kunjungan"; //nik atau noka; nomronya
            $arrCurl[CURLOPT_CUSTOMREQUEST] = "POST";
            $arrCurl[CURLOPT_HTTPHEADER][4] = "X-timestamp: " . $tStamp;
            $arrCurl[CURLOPT_HTTPHEADER][5] = "X-signature: " . $signature;
            $arrCurl[CURLOPT_HTTPHEADER][6] = "content-type: text/plain";
            $arrCurl[CURLOPT_POSTFIELDS] = json_encode($datanya);

            $curl = curl_init();
            curl_setopt_array($curl, $arrCurl);
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if ($err) {
                return "cURL Error #:" . $err;
            }
            $hasil = json_decode($response, true);

            // dd($hasil);

            if (substr($hasil['metaData']['code'], 0, 1) != '2') {
                session()->setFlashdata('msg', $hasil['response'][0]['field'] . ' ' . $hasil['response'][0]['message']);
                return redirect()->to('/pelayanan/add')->withInput();
            }


            $key = $this->consId . $this->secretKey . $tStamp;
            $string = $hasil['response'];
            $encrypt_method = 'AES-256-CBC';
            $key_hash = hex2bin(hash('sha256', $key));
            $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
            $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
            $datanya['noKunjungan'] = json_decode($hasil_dekrip, true)[0]['message'];
            // dd([
            //     'hasil' => $hasil,
            //     'hasilDekrip' => json_decode($hasil_dekrip, true)[0]['message']
            // ]);

            //Add tindakan
            // $arrCurl[CURLOPT_URL] = $this->baseUrl . "/tindakan";
            // $arrCurl[CURLOPT_CUSTOMREQUEST] = "POST";
            // $arrCurl[CURLOPT_HTTPHEADER][4] = "X-timestamp: " . $tStamp;
            // $arrCurl[CURLOPT_HTTPHEADER][5] = "X-signature: " . $signature;
            // $arrCurl[CURLOPT_HTTPHEADER][6] = "content-type: text/plain";
            // $arrCurl[CURLOPT_POSTFIELDS] = json_encode([
            //     "kdTindakanSK" => 0,
            //     "noKunjungan" => $datanya['noKunjungan'],
            //     "kdTindakan" => $this->request->getVar('kdTindakan'),
            //     "biaya" => $this->request->getVar('biaya'),
            //     "keterangan" => null,
            //     "hasil" => 0
            // ]);

            // $curl = curl_init();
            // curl_setopt_array($curl, $arrCurl);
            // $response = curl_exec($curl);
            // $err = curl_error($curl);
            // curl_close($curl);
            // if ($err) {
            //     return "cURL Error #:" . $err;
            // }
            // $hasil = json_decode($response, true);
        } else {
            $datanya['noKunjungan'] = $pendaftaran['noUrut'] . str_replace("-", "", $datanya['tglPulang']);
        }

        $insertPelayanan = $datanya;
        $insertPelayanan['id'] = time();
        $insertPelayanan['noRM'] = $pendaftaran['noRM'];
        $insertPelayanan['bpjs'] = $pendaftaran['bpjs'];
        $insertPelayanan['rujukLanjut'] = json_encode($datanya['rujukLanjut']);
        $this->pelayananModel->insert($insertPelayanan);

        $this->pendaftaranModel->where(['tglDaftar' => date("d-m-Y", $tglDaftar), 'noUrut' => $this->request->getVar('noUrut')])->set([
            'status' => 'Sudah dilayani'
        ])->update();

        //masukin ke rekam medis
        $pasien = $this->pasienModel->getPasien($pendaftaran['noRM']);
        if ($pasien['rekamMedis'] == '') $rekamMedis = [];
        else $rekamMedis = json_decode($pasien['rekamMedis'], true);
        array_push($rekamMedis, [
            'tanggal' => $datanya['tglPulang'],
            'vitalSign' => 'sistole: ' . $datanya['sistole'] . ' | diastole: ' . $datanya['diastole'] . ' | beratBadan: ' . $datanya['beratBadan'] . ' | tinggiBadan: ' . $datanya['tinggiBadan'] . ' | respRate: ' . $datanya['respRate'] . ' | heartRate: ' . $datanya['heartRate'] . ' | lingkarPerut: ' . $datanya['lingkarPerut'] . ' | suhu: ' . $datanya['suhu'],
            'diagnosa' => $datanya['kdDiag1'] . ' | ' . $datanya['kdDiag2'] . ' | ' . $datanya['kdDiag3'],
            "anamnesa" => $datanya['anamnesa'],
            "kdPrognosa" => $datanya['kdPrognosa'],
            "terapiObat" => $datanya['terapiObat'],
            "terapiNonObat" => $datanya['terapiNonObat'],
        ]);
        $this->pasienModel->where(['id' => $pasien['id']])->set(['rekamMedis' => json_encode($rekamMedis)])->update();
        return redirect()->to('/pelayanan/list');
    }

    public function delPelayanan($id)
    {
        $getPelayanan = $this->pelayananModel->where(['id' => $id])->find();
        if ($getPelayanan['bpjs']) {
            date_default_timezone_set('UTC');
            $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
            $variabel1 = $this->consId . "&" . $tStamp;
            $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));

            $arrCurl = $this->arrCurl;
            $arrCurl[CURLOPT_URL] = $this->baseUrl . "/kunjungan/" . $getPelayanan['noKunjungan'];
            $arrCurl[CURLOPT_CUSTOMREQUEST] = "DELETE";
            $arrCurl[CURLOPT_HTTPHEADER][4] = "X-timestamp: " . $tStamp;
            $arrCurl[CURLOPT_HTTPHEADER][5] = "X-signature: " . $signature;
            $arrCurl[CURLOPT_HTTPHEADER][6] = "content-type: application/json";
            $curl = curl_init();
            curl_setopt_array($curl, $arrCurl);
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if ($err) {
                return "cURL Error #:" . $err;
            }
            $hasil = json_decode($response, true);
            if (substr($hasil['metaData']['code'], 0, 1) == '4') {
                // return $this->response->setJSON($hasil, false);
                session()->setFlashdata('msg', $hasil['response']['message']);
                return redirect()->to('/pelayanan/list/' . $getPelayanan['tglPulang']);
            }
        }
        //hapus didatabase
        $this->pelayananModel->where(['id' => $id])->delete();
        $this->pendaftaranModel->where(['id' => $id])->delete();
        return redirect()->to('/pelayanan/list');
    }
    public function editPelayanan($id_pelayanan)
    {
        $pelayanan = $this->pelayananModel->where(['id' => $id_pelayanan])->first();
        $pendaftaran = $this->pendaftaranModel->where([
            'tglDaftar' => $pelayanan['tglDaftar'],
            'noRM' => $pelayanan['noRM']
        ])->first();
        $pasien = $this->pasienModel->getPasien($pelayanan['noRM']);
        $pelayanan['rujukLanjut'] = $pelayanan['rujukLanjut'] ? json_decode($pelayanan['rujukLanjut'], true) : null;
        $pelayanan['tglDaftar'] = explode('-', $pelayanan['tglDaftar'])[2] . '-' . explode('-', $pelayanan['tglDaftar'])[1] . '-' . explode('-', $pelayanan['tglDaftar'])[0];
        $pelayanan['tglPulang'] = explode('-', $pelayanan['tglPulang'])[2] . '-' . explode('-', $pelayanan['tglPulang'])[1] . '-' . explode('-', $pelayanan['tglPulang'])[0];
        if ($pelayanan['rujukLanjut']) $pelayanan['rujukLanjut']['tglEstRujuk'] = explode('-', $pelayanan['rujukLanjut']['tglEstRujuk'])[2] . '-' . explode('-', $pelayanan['rujukLanjut']['tglEstRujuk'])[1] . '-' . explode('-', $pelayanan['rujukLanjut']['tglEstRujuk'])[0];

        //get spesialis
        // $arrCurl[CURLOPT_URL] = $this->baseUrl . "/spesialis";
        // $curl = curl_init();
        // curl_setopt_array($curl, $arrCurl);
        // $response = curl_exec($curl);
        // $err = curl_error($curl);
        // curl_close($curl);
        // if ($err) {
        //     return "cURL Error #:" . $err;
        // }
        // $hasil = json_decode($response, true);
        // $string = $hasil['response'];
        // $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        // $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
        // $getRefSpesialis = json_decode($hasil_dekrip, true);
        $getRefSpesialis = [
            "count" => 35,
            "list" => [
                [
                    "kdSpesialis" => "ANA",
                    "nmSpesialis" => "ANAK"
                ],
                [
                    "kdSpesialis" => "AND",
                    "nmSpesialis" => "ANDROLOGI"
                ],
                [
                    "kdSpesialis" => "BDA",
                    "nmSpesialis" => "BEDAH ANAK"
                ],
                [
                    "kdSpesialis" => "BDM",
                    "nmSpesialis" => "GIGI BEDAH MULUT"
                ],
                [
                    "kdSpesialis" => "BDP",
                    "nmSpesialis" => "BEDAH PLASTIK"
                ],
                [
                    "kdSpesialis" => "BED",
                    "nmSpesialis" => "BEDAH"
                ],
                [
                    "kdSpesialis" => "BSY",
                    "nmSpesialis" => "BEDAH SARAF"
                ],
                [
                    "kdSpesialis" => "BTK",
                    "nmSpesialis" => "BTKV (BEDAH THORAX KARDIOVASKULER)"
                ],
                [
                    "kdSpesialis" => "GIG",
                    "nmSpesialis" => "GIGI"
                ],
                [
                    "kdSpesialis" => "GIZ",
                    "nmSpesialis" => "GIZI KLINIK"
                ],
                [
                    "kdSpesialis" => "GND",
                    "nmSpesialis" => "GIGI ENDODONSI"
                ],
                [
                    "kdSpesialis" => "GOR",
                    "nmSpesialis" => "GIGI ORTHODONTI"
                ],
                [
                    "kdSpesialis" => "GPR",
                    "nmSpesialis" => "GIGI PERIODONTI"
                ],
                [
                    "kdSpesialis" => "GRD",
                    "nmSpesialis" => "GIGI RADIOLOGI"
                ],
                [
                    "kdSpesialis" => "INT",
                    "nmSpesialis" => "PENYAKIT DALAM"
                ],
                [
                    "kdSpesialis" => "IRM",
                    "nmSpesialis" => "Installasi Rehabilitasi Medik"
                ],
                [
                    "kdSpesialis" => "JAN",
                    "nmSpesialis" => "JANTUNG DAN PEMBULUH DARAH"
                ],
                [
                    "kdSpesialis" => "JIW",
                    "nmSpesialis" => "JIWA"
                ],
                [
                    "kdSpesialis" => "KDK",
                    "nmSpesialis" => "KEDOKTERAN KELAUTAN"
                ],
                [
                    "kdSpesialis" => "KDN",
                    "nmSpesialis" => "KEDOKTERAN NUKLIR"
                ],
                [
                    "kdSpesialis" => "KDO",
                    "nmSpesialis" => "KEDOKTERAN OKUPASI"
                ],
                [
                    "kdSpesialis" => "KDP",
                    "nmSpesialis" => "KEDOKTERAN PENERBANGAN"
                ],
                [
                    "kdSpesialis" => "KLT",
                    "nmSpesialis" => "KULIT KELAMIN"
                ],
                [
                    "kdSpesialis" => "KON",
                    "nmSpesialis" => "GIGI PEDODONTIS"
                ],
                [
                    "kdSpesialis" => "KOR",
                    "nmSpesialis" => "KEDOKTERAAN OLAHRAGA"
                ],
                [
                    "kdSpesialis" => "MAT",
                    "nmSpesialis" => "MATA"
                ],
                [
                    "kdSpesialis" => "OBG",
                    "nmSpesialis" => "OBGYN"
                ],
                [
                    "kdSpesialis" => "ORT",
                    "nmSpesialis" => "ORTHOPEDI"
                ],
                [
                    "kdSpesialis" => "PAR",
                    "nmSpesialis" => "PARU"
                ],
                [
                    "kdSpesialis" => "PNM",
                    "nmSpesialis" => "GIGI PENYAKIT MULUT"
                ],
                [
                    "kdSpesialis" => "PTD",
                    "nmSpesialis" => "GIGI PROSTHODONTI"
                ],
                [
                    "kdSpesialis" => "RDT",
                    "nmSpesialis" => "RADIOTERAPI"
                ],
                [
                    "kdSpesialis" => "SAR",
                    "nmSpesialis" => "SARAF"
                ],
                [
                    "kdSpesialis" => "THT",
                    "nmSpesialis" => "THT-KL"
                ],
                [
                    "kdSpesialis" => "URO",
                    "nmSpesialis" => "UROLOGI"
                ]
            ]
        ];

        //get sub spesialis
        // $arrCurl[CURLOPT_URL] = $this->baseUrl . "/spesialis/" . $getRefSpesialis['list'][0]['kdSpesialis'] . "/subspesialis";
        // $curl = curl_init();
        // curl_setopt_array($curl, $arrCurl);
        // $response = curl_exec($curl);
        // $err = curl_error($curl);
        // curl_close($curl);
        // if ($err) {
        //     return "cURL Error #:" . $err;
        // }
        // $hasil = json_decode($response, true);
        // $string = $hasil['response'];
        // $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        // $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
        // $getRefSubSpesialis = json_decode($hasil_dekrip, true);
        $getRefSubSpesialis = [
            "count" => 16,
            "list" => [
                [
                    "kdSubSpesialis" => "26",
                    "nmSubSpesialis" => "Anak",
                    "kdPoliRujuk" => "ANA"
                ],
                [
                    "kdSubSpesialis" => "27",
                    "nmSubSpesialis" => "Anak Alergi Imunologi",
                    "kdPoliRujuk" => "027"
                ],
                [
                    "kdSubSpesialis" => "28",
                    "nmSubSpesialis" => "Anak Endokrinologi",
                    "kdPoliRujuk" => "028"
                ],
                [
                    "kdSubSpesialis" => "29",
                    "nmSubSpesialis" => "Anak Gastro-Hepatologi",
                    "kdPoliRujuk" => "029"
                ],
                [
                    "kdSubSpesialis" => "30",
                    "nmSubSpesialis" => "Anak Hematologi Onkologi",
                    "kdPoliRujuk" => "030"
                ],
                [
                    "kdSubSpesialis" => "31",
                    "nmSubSpesialis" => "Anak Infeksi & Pediatri Tropis",
                    "kdPoliRujuk" => "031"
                ],
                [
                    "kdSubSpesialis" => "32",
                    "nmSubSpesialis" => "Anak Kardiologi",
                    "kdPoliRujuk" => "032"
                ],
                [
                    "kdSubSpesialis" => "33",
                    "nmSubSpesialis" => "Anak Nefrologi",
                    "kdPoliRujuk" => "033"
                ],
                [
                    "kdSubSpesialis" => "34",
                    "nmSubSpesialis" => "Anak Neurologi",
                    "kdPoliRujuk" => "034"
                ],
                [
                    "kdSubSpesialis" => "35",
                    "nmSubSpesialis" => "Anak Nutrisi & Penyakit Metabolik",
                    "kdPoliRujuk" => "035"
                ],
                [
                    "kdSubSpesialis" => "36",
                    "nmSubSpesialis" => "Pediatri Gawat Darurat",
                    "kdPoliRujuk" => "036"
                ],
                [
                    "kdSubSpesialis" => "37",
                    "nmSubSpesialis" => "Pencitraan Anak ",
                    "kdPoliRujuk" => "037"
                ],
                [
                    "kdSubSpesialis" => "38",
                    "nmSubSpesialis" => "Perinatologi",
                    "kdPoliRujuk" => "038"
                ],
                [
                    "kdSubSpesialis" => "39",
                    "nmSubSpesialis" => "Respirologi Anak ",
                    "kdPoliRujuk" => "039"
                ],
                [
                    "kdSubSpesialis" => "40",
                    "nmSubSpesialis" => "Tumbuh Kembang Ped. Sosial",
                    "kdPoliRujuk" => "040"
                ],
                [
                    "kdSubSpesialis" => "41",
                    "nmSubSpesialis" => "Kesehatan Remaja",
                    "kdPoliRujuk" => "041"
                ]
            ]
        ];

        //get spesialis
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $variabel1 = $this->consId . "&" . $tStamp;
        $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));

        $arrCurl = $this->arrCurl;
        $arrCurl[CURLOPT_URL] = $this->baseUrl . "/spesialis";
        $arrCurl[CURLOPT_CUSTOMREQUEST] = "GET";
        $arrCurl[CURLOPT_HTTPHEADER][4] = "X-timestamp: " . $tStamp;
        $arrCurl[CURLOPT_HTTPHEADER][5] = "X-signature: " . $signature;
        $arrCurl[CURLOPT_HTTPHEADER][6] = "content-type: application/json";

        $curl = curl_init();
        curl_setopt_array($curl, $arrCurl);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return "cURL Error #:" . $err;
        }
        $hasil = json_decode($response, true);
        // dd($hasil);
        if (substr($hasil['metaData']['code'], 0, 1) == '4') {
            return $this->response->setJSON($hasil, false);
        }
        $string = $hasil['response'];
        $key = $this->consId . $this->secretKey . $tStamp;
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
        $getRefSpesialis = json_decode($hasil_dekrip, true);

        //get sub spesialis
        $arrCurl[CURLOPT_URL] = $this->baseUrl . "/spesialis/" . $getRefSpesialis['list'][0]['kdSpesialis'] . "/subspesialis";
        $curl = curl_init();
        curl_setopt_array($curl, $arrCurl);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return "cURL Error #:" . $err;
        }
        $hasil = json_decode($response, true);
        if (substr($hasil['metaData']['code'], 0, 1) == '4') {
            return $this->response->setJSON($hasil, false);
        }
        $string = $hasil['response'];
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
        $getRefSubSpesialis = json_decode($hasil_dekrip, true);



        $refTACC = [
            [
                "kdTacc" => "-1",
                "nmTacc" => "Tanpa TACC",
                "alasanTacc" => []
            ],
            [
                "kdTacc" => "1",
                "nmTacc" => "Time",
                "alasanTacc" => ["< 3 Hari", ">= 3 - 7 Hari", ">= 7 Hari"]
            ],
            [
                "kdTacc" => "2",
                "nmTacc" => "Age",
                "alasanTacc" => [
                    "< 1 Bulan",
                    ">= 1 Bulan s/d < 12 Bulan",
                    ">= 1 Tahun s/d < 5 Tahun",
                    ">= 5 Tahun s/d < 12 Tahun",
                    ">= 12 Tahun s/d < 55 Tahun",
                    ">= 55 Tahun"
                ]
            ],
            [
                "kdTacc" => "3",
                "nmTacc" => "Complication",
                "alasanTacc" => ["A09 - Diarrhoea and gastroenteritis of presumed infectious origin"]
            ],
            [
                "kdTacc" => "4",
                "nmTacc" => "Comorbidity",
                "alasanTacc" => ["< 3 Hari", ">= 3 - 7 Hari", ">= 7 Hari"]
            ]
        ];

        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $variabel1 = $this->consId . "&" . $tStamp;
        $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));

        // $rawatInap = $pendaftaran['kdTkp'] == '20' ? 'true' : 'false';
        // $arrCurl = $this->arrCurl;
        // $arrCurl[CURLOPT_URL] = $this->baseUrl . "/statuspulang/rawatInap/" . $rawatInap;
        // $arrCurl[CURLOPT_CUSTOMREQUEST] = "GET";
        // $arrCurl[CURLOPT_HTTPHEADER][4] = "X-timestamp: " . $tStamp;
        // $arrCurl[CURLOPT_HTTPHEADER][5] = "X-signature: " . $signature;
        // $arrCurl[CURLOPT_HTTPHEADER][6] = "content-type: application/json";
        // $curl = curl_init();
        // curl_setopt_array($curl, $arrCurl);
        // $response = curl_exec($curl);
        // $err = curl_error($curl);
        // curl_close($curl);
        // if ($err) {
        //     return "cURL Error #:" . $err;
        // }
        // $hasil = json_decode($response, true);
        // $key = $this->consId . $this->secretKey . $tStamp;
        // $string = $hasil['response'];
        // $encrypt_method = 'AES-256-CBC';
        // $key_hash = hex2bin(hash('sha256', $key));
        // $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        // $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        // $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
        // $getStatusPulang = json_decode($hasil_dekrip, true);

        //dapetin faskes
        $faskes = false;
        if ($pelayanan['rujukLanjut']) {
            $arrCurl[CURLOPT_URL] = $this->baseUrl . "/provider/0/20"; //nik atau noka; nomronya
            $arrCurl[CURLOPT_CUSTOMREQUEST] = "GET";
            $arrCurl[CURLOPT_HTTPHEADER][4] = "X-timestamp: " . $tStamp;
            $arrCurl[CURLOPT_HTTPHEADER][5] = "X-signature: " . $signature;
            $arrCurl[CURLOPT_HTTPHEADER][6] = "content-type: application/json";
            $curl = curl_init();
            curl_setopt_array($curl, $arrCurl);
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if ($err) {
                return "cURL Error #:" . $err;
            }
            $hasil = json_decode($response, true);
            $key = $this->consId . $this->secretKey . $tStamp;
            $string = $hasil['response'];
            $encrypt_method = 'AES-256-CBC';
            $key_hash = hex2bin(hash('sha256', $key));
            $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
            $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
            foreach (json_decode($hasil_dekrip, true)['list'] as $f) {
                if ($f['kdProvider'] == $pelayanan['rujukLanjut']['kdppk']) {
                    $faskes = $f;
                }
            }
        }

        $d = strtotime("+7 Hours");
        $tanggal = date("Y-m-d", $d);
        $data = [
            'title' => 'Edit Pelayanan',
            'tanggal' => $tanggal,
            // 'dokter' => $getDokter['list'],
            // 'kesadaran' => $getKesadaran['list'],
            // 'prognosa' => $getPrognosa['list'],
            // 'alergi' => [
            //     'makanan' => $getAlergiMakanan['list'],
            //     'udara' => $getAlergiUdara['list'],
            //     'obat' => $getAlergiObat['list'],
            // ],
            // 'khusus' => $getRefKhusus['list'],
            'spesialis' => $getRefSpesialis['list'],
            'subspesialis' => $getRefSubSpesialis['list'],
            // 'sarana' => $getRefSarana['list'],
            'pelayanan' => $pelayanan,
            'pasien' => $pasien,
            // 'statusPulang' => $getStatusPulang['list'],
            'msg' => session()->getFlashdata('msg'),
            'pelayanan' => $pelayanan,
            'pendaftaran' => $pendaftaran,
            'faskes' => $faskes,
            'TACC' => $refTACC
        ];
        return view('pelayanan/edit', $data);
    }

    public function actionEditPelayanan($id)
    {
        $pelayananCur = $this->pelayananModel->where(['id' => $id])->first();
        if (!$this->validate([
            'keluhan' => ['rules' => 'required'],
            'kdSadar' => ['rules' => 'required'],
            'kdStatusPulang' => ['rules' => 'required'],
            'tglPulang' => ['rules' => 'required'],
            // 'kdDokter' => ['rules' => 'required'],
            'kdDiag1' => ['rules' => 'required'],
            'anamnesa' => ['rules' => 'required'],
            'alergiMakan' => ['rules' => 'required'],
            'alergiUdara' => ['rules' => 'required'],
            'alergiObat' => ['rules' => 'required'],
            'kdPrognosa' => ['rules' => 'required'],
            'terapiObat' => ['rules' => 'required'],
            'terapiNonObat' => ['rules' => 'required'],
            'bmhp' => ['rules' => 'required'],
            'suhu' => ['rules' => 'required'],
        ])) {
            session()->setFlashdata('msg', 'Ada data belum terisi');
            return redirect()->to('/pelayanan/add')->withInput();
        }

        $pendaftaran = $this->pendaftaranModel->where(['tglDaftar' => $pelayananCur['tglDaftar'], 'noRM' => $pelayananCur['noRM']])->first();

        $getDokter = [
            "count" => 1,
            "list" => [
                [
                    "kdDokter" => "133797",
                    "nmDokter" => "Tenaga Medis 133797"
                ]
            ]
        ];

        $datanya = [
            "noKunjungan" => $pelayananCur['noKunjungan'],
            "noKartu" => $pendaftaran['noKartu'],
            "tglDaftar" => $pendaftaran['tglDaftar'],
            "kdPoli" => $pendaftaran['kdPoli'],
            "sistole" => $pendaftaran['sistole'],
            "diastole" => $pendaftaran['diastole'],
            "beratBadan" => $pendaftaran['beratBadan'],
            "tinggiBadan" => $pendaftaran['tinggiBadan'],
            "respRate" => $pendaftaran['respRate'],
            "heartRate" => $pendaftaran['heartRate'],
            "lingkarPerut" => $pendaftaran['lingkarPerut'],
            "keluhan" => $this->request->getVar('keluhan'),

            "kdSadar" => $this->request->getVar('kdSadar'),
            "kdStatusPulang" => $this->request->getVar('kdStatusPulang'),
            "tglPulang" => date("d-m-Y", strtotime($this->request->getVar('tglPulang'))),
            "kdDokter" => $this->request->getVar('kdDokter') ? $this->request->getVar('kdDokter') : $getDokter['list'][0]['kdDokter'],
            "kdDiag1" => $this->request->getVar('kdDiag1'),
            "kdDiag2" => $this->request->getVar('kdDiag2') ? $this->request->getVar('kdDiag2') : null,
            "kdDiag3" => $this->request->getVar('kdDiag3') ? $this->request->getVar('kdDiag3') : null,
            "kdPoliRujukInternal" => null,
            "rujukLanjut" => null,
            "kdTacc" => -1, //ibuk biasanya tanpaTACC
            "alasanTacc" => null, //ibuk biasanya tanpaTACC
            "anamnesa" => $this->request->getVar('anamnesa'),
            "alergiMakan" => $this->request->getVar('alergiMakan'),
            "alergiUdara" => $this->request->getVar('alergiUdara'),
            "alergiObat" => $this->request->getVar('alergiObat'),
            "kdPrognosa" => $this->request->getVar('kdPrognosa'),
            "terapiObat" => $this->request->getVar('terapiObat'),
            "terapiNonObat" => $this->request->getVar('terapiNonObat'),
            "bmhp" => $this->request->getVar('bmhp'),
            "suhu" => $this->request->getVar('suhu'),
        ];

        //Rujuk Vertikal
        if ($datanya['kdStatusPulang'] == '4') {
            $spesialis = $this->request->getVar('spesialis') == 'true' ? true : false;
            if ($spesialis) {
                $datanya['rujukLanjut'] = [
                    "tglEstRujuk" => date("d-m-Y", strtotime($this->request->getVar('tglEstRujuk'))),
                    "kdppk" => $this->request->getVar('kdppk'),
                    "subSpesialis" => [
                        "kdSubSpesialis1" => $this->request->getVar('kdSubSpesialis1'),
                        "kdSarana" => $this->request->getVar('kdSarana'),
                    ],
                    "khusus" => null
                ];
            } else {
                $datanya['rujukLanjut'] = [
                    "tglEstRujuk" => date("d-m-Y", strtotime($this->request->getVar('tglEstRujuk'))),
                    "kdppk" => $this->request->getVar('kdppk'),
                    "subSpesialis" => null,
                    "khusus" => [
                        "kdKhusus" => $this->request->getVar('kdKhusus'),
                        "kdSubSpesialis" => $this->request->getVar('kdSubSpesialis'),
                        "catatan" => $this->request->getVar('catatan'),
                    ]
                ];
            }
        }
        //Rujuk Horizontal
        if ($datanya['kdStatusPulang'] == '6') {
            $datanya['rujukLanjut'] = [
                "tglEstRujuk" => date("d-m-Y", strtotime($this->request->getVar('tglEstRujuk'))),
                "kdppk" => $this->request->getVar('kdppk'),
                "subSpesialis" => [
                    "kdSubSpesialis1" => $this->request->getVar('kdSubSpesialis1'),
                    "kdSarana" => $this->request->getVar('kdSarana'),
                ],
                "khusus" => [
                    "kdKhusus" => $this->request->getVar('kdKhusus'),
                    "kdSubSpesialis" => $this->request->getVar('kdSubSpesialis'),
                    "catatan" => $this->request->getVar('catatan'),
                ]
            ];
            $datanya['kdPoliRujukInternal'] = $this->request->getVar('kdPoliRujukInternal');
        }

        if ($pendaftaran['bpjs']) {
            date_default_timezone_set('UTC');
            $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
            $variabel1 = $this->consId . "&" . $tStamp;
            $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));

            $arrCurl = $this->arrCurl;
            $arrCurl[CURLOPT_URL] = $this->baseUrl . "/kunjungan"; //nik atau noka; nomronya
            $arrCurl[CURLOPT_CUSTOMREQUEST] = "PUT";
            $arrCurl[CURLOPT_HTTPHEADER][4] = "X-timestamp: " . $tStamp;
            $arrCurl[CURLOPT_HTTPHEADER][5] = "X-signature: " . $signature;
            $arrCurl[CURLOPT_HTTPHEADER][6] = "content-type: text/plain";
            $arrCurl[CURLOPT_POSTFIELDS] = json_encode($datanya);

            $curl = curl_init();
            curl_setopt_array($curl, $arrCurl);
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if ($err) {
                return "cURL Error #:" . $err;
            }
            $hasil = json_decode($response, true);

            if (substr($hasil['metaData']['code'], 0, 1) != '2') {
                session()->setFlashdata('msg', $hasil['response'][0]['field'] . ' ' . $hasil['response'][0]['message']);
                return redirect()->to('/pelayanan/edit/' . $id)->withInput();
            }
        }

        $insertPelayanan = $datanya;
        $insertPelayanan['id'] = time();
        $insertPelayanan['noRM'] = $pendaftaran['noRM'];
        $insertPelayanan['bpjs'] = $pendaftaran['bpjs'];
        $insertPelayanan['rujukLanjut'] = json_encode($datanya['rujukLanjut']);
        $this->pelayananModel->where(['id' => $id])->set($insertPelayanan)->update();

        //edit rekam medis
        $pasien = $this->pasienModel->getPasien($pendaftaran['noRM']);
        $rekamMedis = json_decode($pasien['rekamMedis'], true);
        foreach ($rekamMedis as $ind_rm => $rm) {
            if ($rm['tanggal'] == $datanya['tglPulang']) {
                $rekamMedis[$ind_rm] = [
                    'tanggal' => $datanya['tglPulang'],
                    'vitalSign' => 'sistole: ' . $datanya['sistole'] . ' | diastole: ' . $datanya['diastole'] . ' | beratBadan: ' . $datanya['beratBadan'] . ' | tinggiBadan: ' . $datanya['tinggiBadan'] . ' | respRate: ' . $datanya['respRate'] . ' | heartRate: ' . $datanya['heartRate'] . ' | lingkarPerut: ' . $datanya['lingkarPerut'] . ' | suhu: ' . $datanya['suhu'],
                    'diagnosa' => $datanya['kdDiag1'] . ' | ' . $datanya['kdDiag2'] . ' | ' . $datanya['kdDiag3'],
                    "anamnesa" => $datanya['anamnesa'],
                    "kdPrognosa" => $datanya['kdPrognosa'],
                    "terapiObat" => $datanya['terapiObat'],
                    "terapiNonObat" => $datanya['terapiNonObat'],
                ];
            }
        }
        $this->pasienModel->where(['id' => $pasien['id']])->set(['rekamMedis' => json_encode($rekamMedis)])->update();
        return redirect()->to('/pelayanan/list/' . $datanya['tglPulang']);
    }

    public function moveFromKunjungan()
    {
        $kunjungan = $this->kunjunganModel->findAll(1000, 17000);
        // dd($kunjungan);
        $arrTerapi = [
            'Exo_Perm',
            'Exo_Susu',
            'LC',
            'Fuji',
            'RawatSyaraf',
            'Scalling',
            'Antibiotik',
            'Analgetik',
            'AntiRadang',
        ];
        $urutanBpjs = 1;
        $urutanUmum = 1;
        $urutanTglB = '';
        $urutanTglU = '';
        $pembedaTime = 1;
        $timeCur = '';
        foreach ($kunjungan as $k) {
            $pasien = $this->pasienModel->getPasien($k['ID_pasien']);
            if ($pasien) {
                $arrTerapiFilter = '';
                foreach ($arrTerapi as $at) {
                    if ($k[$at] > 0) {
                        $arrTerapiFilter = $arrTerapiFilter . str_replace("_", " ", $at) . ': ' . $k[$at] . ' | ';
                    }
                }
                if ($k['Lain_Lain'] != '') {
                    $arrTerapiFilter = $arrTerapiFilter . 'Lain-lain: ' . $k['Lain_Lain'];
                }

                if ($pasien['rekamMedis'] == '') $rekamMedis = [];
                else $rekamMedis = json_decode($pasien['rekamMedis'], true);
                array_push($rekamMedis, [
                    'tanggal' => $k['tgl_praktek'],
                    'vitalSign' => 'Tensi: ' . $k['tensi'] . ' | Berat: ' . $k['berat'] . ' | Tinggi: ' . $k['tinggi'] . ' | Suhu: ' . $k['suhu'],
                    'terapiObat/Non' => $arrTerapiFilter
                ]);
                $this->pasienModel->where(['id' => $k['ID_pasien']])->set(['rekamMedis' => json_encode($rekamMedis)])->update();

                if (strtolower($k['BPJS']) == 'true') {
                    if ($urutanTglB != $k['tgl_praktek']) {
                        $urutanBpjs = 1;
                        $urutanTglB = $k['tgl_praktek'];
                    } else {
                        $urutanBpjs = $urutanBpjs + 1;
                    }
                    $generateNoKun = 'B' . $urutanBpjs . str_replace("-", "", $k['tgl_praktek']);
                } else {
                    if ($urutanTglU != $k['tgl_praktek']) {
                        $urutanUmum = 1;
                        $urutanTglU = $k['tgl_praktek'];
                    } else {
                        $urutanUmum = $urutanUmum + 1;
                    }
                    $generateNoKun = 'U' . $urutanUmum . str_replace("-", "", $k['tgl_praktek']);
                }
                if ($timeCur != $k['tgl_praktek']) {
                    $timeCur = $k['tgl_praktek'];
                    $pembedaTime = 1;
                } else {
                    $pembedaTime = $pembedaTime + 1;
                }
                $cekPelayananExist = $this->pelayananModel->where(['id' => explode("-", $k['tgl_praktek'])[2] . explode("-", $k['tgl_praktek'])[1] . explode("-", $k['tgl_praktek'])[0] . $pembedaTime])->first();
                if ($cekPelayananExist) {
                    $pelayananSeTgl = $this->pelayananModel->where(['tglDaftar' => $k['tgl_praktek']])->findAll();
                    $timeCur = $k['tgl_praktek'];
                    $pembedaTime = count($pelayananSeTgl) + 4;
                }
                $this->pelayananModel->insert([
                    'id' => explode("-", $k['tgl_praktek'])[2] . explode("-", $k['tgl_praktek'])[1] . explode("-", $k['tgl_praktek'])[0] . $pembedaTime,
                    'noKunjungan' => $generateNoKun,
                    'noKartu' => $pasien['noBpjs'],
                    'tglDaftar' => $k['tgl_praktek'],
                    'tglPulang' => $k['tgl_praktek'],
                    'terapiObat' => $arrTerapiFilter,
                    'noRM' => $k['ID_pasien'],
                    'bpjs' => $k['BPJS'],
                    'beratBadan' => $k['berat'],
                    'tinggiBadan' => $k['tinggi'],
                    'suhu' => $k['suhu'],
                ]);
            }
        }
        return $this->response->setJSON([
            'success' => true
        ], false);
    }
    public function gantiIdLama()
    {
        $offset = 17000;
        $pelayanan = $this->pelayananModel->findAll(1000, $offset);
        foreach ($pelayanan as $p) {
            $idBaru = '11' . $p['id'];
            $this->pelayananModel->where(['id' => $p['id']])->set(['id' => $idBaru])->update();
        }
        return $this->response->setJSON([
            'success' => true,
            'offset' => $offset
        ], false);
    }

    public function listPelayanan($tanggal = false)
    {
        if (!$tanggal) $tanggal = date("d-m-Y", strtotime("+7 Hours"));
        $pelayanan = $this->pelayananModel->where(['tglPulang' => $tanggal])->findAll();
        foreach ($pelayanan as $ind_p => $p) {
            $pelayanan[$ind_p]['detail_pasien'] = $this->pasienModel->getPasien($p['noRM']);
        }
        $data = [
            'title' => 'List Pelayanan',
            'pelayanan' => $pelayanan,
            'tanggal' => explode("-", $tanggal)[2] . "-" . explode("-", $tanggal)[1] . "-" . explode("-", $tanggal)[0],
            'msg' => session()->getFlashdata('msg') ? session()->getFlashdata('msg') : false
        ];
        return view('pelayanan/list', $data);
    }
}
