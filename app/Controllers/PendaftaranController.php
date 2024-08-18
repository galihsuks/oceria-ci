<?php

namespace App\Controllers;

use App\Models\PendaftaranModel;
use App\Models\PasienModel;

class PendaftaranController extends BaseController
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
        $this->pasienModel = new PasienModel();
    }
    public function addPendaftaran()
    {
        // date_default_timezone_set('UTC');
        // $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        // $variabel1 = $this->consId . "&" . $tStamp;
        // $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));
        // $curl = curl_init();
        // curl_setopt_array($curl, array(
        //     CURLOPT_URL => base_url('bpjs/getpoli'),
        //     CURLOPT_SSL_VERIFYHOST => 0,
        //     CURLOPT_SSL_VERIFYPEER => 0,
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => "",
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 30,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => "GET",
        // ));
        // $response = curl_exec($curl);
        // $err = curl_error($curl);
        // curl_close($curl);
        // if ($err) {
        //     return "cURL Error #:" . $err;
        // }
        // $poli = json_decode($response, true)['list'];

        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $variabel1 = $this->consId . "&" . $tStamp;
        $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));
        $arrCurl = $this->arrCurl;
        $arrCurl[CURLOPT_URL] = $this->baseUrl . "/poli/fktp/0/20";
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
        $poli = json_decode($hasil_dekrip, true)['list'];

        $d = strtotime("+7 Hours");
        $tanggal = date("Y-m-d", $d);
        $data = [
            'title' => 'Add Pendaftaran',
            'section' => 'pendaftaran',
            'poli' => $poli,
            'tanggal' => $tanggal,
            'poliJson' => json_encode($poli),
            'msg' => session()->getFlashdata('msg') ? session()->getFlashdata('msg') : false
            // 'msg' => 'Ada yang beli disii'
        ];
        return view('pendaftaran/add', $data);
    }
    public function getPasien($nama)
    {
        $pasien = $this->pasienModel->like('nama', $nama, 'both')->findAll();
        return $this->response->setJSON($pasien, false);
    }
    public function actionAddPendaftaran()
    {
        $pasienLama = $this->request->getVar('pasienLama') == 'true' ? true : false;
        $bpjs = $this->request->getVar('bpjs') == 'true' ? true : false;
        // dd($this->request->getVar());

        if ($bpjs) {
            if (!$pasienLama) {
                if (!$this->validate([
                    'bpjs' => [
                        'rules' => 'required',
                    ],
                    'pasienLama' => [
                        'rules' => 'required',
                    ],
                    'noKartu' => [
                        'rules' => 'required',
                    ],
                    'kdProviderPeserta' => [
                        'rules' => 'required',
                    ],
                    'nama' => [
                        'rules' => 'required',
                    ],
                    'nik' => [
                        'rules' => 'required',
                    ],
                    'tglLahir' => [
                        'rules' => 'required',
                    ],
                    'kelamin' => [
                        'rules' => 'required',
                    ],
                    'noHp' => [
                        'rules' => 'required',
                    ],
                    'golDarah' => [
                        'rules' => 'required',
                    ],
                    'alamat' => [
                        'rules' => 'required',
                    ],
                    'tglDaftar' => [
                        'rules' => 'required',
                    ],
                    'kunjSakit' => [
                        'rules' => 'required',
                    ],
                    'kdTkp' => [
                        'rules' => 'required',
                    ],
                    'kdPoli' => [
                        'rules' => 'required',
                    ],
                    'keluhan' => [
                        'rules' => 'required',
                    ],
                    'tinggiBadan' => [
                        'rules' => 'required',
                    ],
                    'beratBadan' => [
                        'rules' => 'required',
                    ],
                    'lingkarPerut' => [
                        'rules' => 'required',
                    ],
                    'sistole' => [
                        'rules' => 'required',
                    ],
                    'diastole' => [
                        'rules' => 'required',
                    ],
                    'respRate' => [
                        'rules' => 'required',
                    ],
                    'heartRate' => [
                        'rules' => 'required',
                    ]
                ])) {
                    session()->setFlashdata('msg', 'Ada data belum terisi');
                    return redirect()->to('/pendaftaran/add')->withInput();
                }
                $pesertaSeVocal = $this->pasienModel->like('id', substr($this->request->getVar('nama'), 0, 1), 'after')->findAll();
                $angkaIdTerbesar = 0;
                foreach ($pesertaSeVocal as $p) {
                    $angkaId = (int)substr($p['id'], 1);
                    if ($angkaId > $angkaIdTerbesar) $angkaIdTerbesar = $angkaId;
                }
                $noRM = strtoupper(substr($this->request->getVar('nama'), 0, 1)) . ($angkaIdTerbesar + 1);
                $dataBpjs = [
                    "kdProviderPeserta" => $this->request->getVar('kdProviderPeserta'),
                    "tglDaftar" => date("d-m-Y", strtotime($this->request->getVar('tglDaftar'))),
                    "noKartu" => $this->request->getVar('noKartu'),
                    "kdPoli" => $this->request->getVar('kdPoli'),
                    "keluhan" => $this->request->getVar('keluhan'),
                    "kunjSakit" => $this->request->getVar('kunjSakit') == 'true' ? true : false,
                    "sistole" => $this->request->getVar('sistole'),
                    "diastole" => $this->request->getVar('diastole'),
                    "beratBadan" => $this->request->getVar('beratBadan'),
                    "tinggiBadan" => $this->request->getVar('tinggiBadan'),
                    "respRate" => $this->request->getVar('respRate'),
                    "lingkarPerut" => $this->request->getVar('lingkarPerut'),
                    "heartRate" => $this->request->getVar('heartRate'),
                    "rujukBalik" => '0',
                    "kdTkp" => $this->request->getVar('kdTkp'),

                    "id" => time(),
                    "nama" => $this->request->getVar('nama'),
                    "nik" => $this->request->getVar('nik'),
                    "bpjs" => $this->request->getVar('bpjs') == 'true' ? true : false,
                    "pasienLama" => $this->request->getVar('pasienLama') == 'true' ? true : false,
                    "noRM" => $noRM,
                    "tglLahir" => date("d-m-Y", strtotime($this->request->getVar('tglLahir'))),
                    "kelamin" => $this->request->getVar('kelamin'),
                    "noHp" => $this->request->getVar('noHp'),
                    "golDarah" => $this->request->getVar('golDarah'),
                    "alamat" => $this->request->getVar('alamat'),
                ];

                date_default_timezone_set('UTC');
                $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
                $variabel1 = $this->consId . "&" . $tStamp;
                $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));
                $arrCurl = $this->arrCurl;
                $arrCurl[CURLOPT_URL] = $this->baseUrl . "/pendaftaran"; //nik atau noka; nomronya
                $arrCurl[CURLOPT_CUSTOMREQUEST] = "POST";
                $arrCurl[CURLOPT_HTTPHEADER][4] = "X-timestamp: " . $tStamp;
                $arrCurl[CURLOPT_HTTPHEADER][5] = "X-signature: " . $signature;
                $arrCurl[CURLOPT_HTTPHEADER][6] = "content-type: text/plain";
                $arrCurl[CURLOPT_POSTFIELDS] = json_encode([
                    "kdProviderPeserta" => $dataBpjs['kdProviderPeserta'],
                    "tglDaftar" => $dataBpjs['tglDaftar'],
                    "noKartu" => $dataBpjs['noKartu'],
                    "kdPoli" => $dataBpjs['kdPoli'],
                    "keluhan" => $dataBpjs['keluhan'],
                    "kunjSakit" => $dataBpjs['kunjSakit'],
                    "sistole" => $dataBpjs['sistole'],
                    "diastole" => $dataBpjs['diastole'],
                    "beratBadan" => $dataBpjs['beratBadan'],
                    "tinggiBadan" => $dataBpjs['tinggiBadan'],
                    "respRate" => $dataBpjs['respRate'],
                    "lingkarPerut" => $dataBpjs['lingkarPerut'],
                    "heartRate" => $dataBpjs['heartRate'],
                    "rujukBalik" => '0', //Belum tau apa itu rujuk balik
                    "kdTkp" => $dataBpjs['kdTkp']
                ]);
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
                $dataBpjs['noUrut'] = json_decode($hasil_dekrip, true)['message'];

                if (substr($hasil['metaData']['code'], 0, 1) == '4') {
                    session()->setFlashdata('msg', $hasil['metaData']['message']);
                    return redirect()->to('/pendaftaran/add')->withInput();
                }

                // dd([
                //     'hasil' => $hasil,
                //     'hasil_dekrip' => $hasil_dekrip,
                //     'dataBPJS' => $dataBpjs
                // ]);

                $this->pasienModel->insert([
                    'id' => $dataBpjs['noRM'],
                    'nama' => $dataBpjs['nama'],
                    'tglLahir' => $dataBpjs['tglLahir'],
                    'alamat' => $dataBpjs['alamat'],
                    'kelamin' => $dataBpjs['kelamin'],
                    'noHp' => $dataBpjs['noHp'],
                    'rekamMedis' => 'Kunjungan ' . $dataBpjs['tglDaftar'],
                    'golDarah' => $dataBpjs['golDarah'],
                    'nik' => $dataBpjs['nik'],
                    'noBpjs' => $dataBpjs['noKartu'],
                    'kdProviderPst' => $dataBpjs['kdProviderPeserta'],
                ]);
                $this->pendaftaranModel->insert([
                    'id' => $dataBpjs['id'],
                    'noUrut' => $dataBpjs['noUrut'],
                    'kdProviderPeserta' => $dataBpjs['kdProviderPeserta'],
                    'tglDaftar' => $dataBpjs['tglDaftar'],
                    'noKartu' => $dataBpjs['noKartu'],
                    'nik' => $dataBpjs['nik'],
                    'nama' => $dataBpjs['nama'],
                    'kdPoli' => $dataBpjs['kdPoli'],
                    'keluhan' => $dataBpjs['keluhan'],
                    'kunjSakit' => $dataBpjs['kunjSakit'],
                    'sistole' => $dataBpjs['sistole'],
                    'diastole' => $dataBpjs['diastole'],
                    'beratBadan' => $dataBpjs['beratBadan'],
                    'tinggiBadan' => $dataBpjs['tinggiBadan'],
                    'respRate' => $dataBpjs['respRate'],
                    'lingkarPerut' => $dataBpjs['lingkarPerut'],
                    'heartRate' => $dataBpjs['heartRate'],
                    'rujukBalik' => $dataBpjs['rujukBalik'],
                    'kdTkp' => $dataBpjs['kdTkp'],
                    'bpjs' => $dataBpjs['bpjs'],
                    'noRM' => $dataBpjs['noRM'],
                    'status' => 'Baru',
                ]);
            } else {
                if (!$this->validate([
                    'bpjs' => [
                        'rules' => 'required',
                    ],
                    'pasienLama' => [
                        'rules' => 'required',
                    ],
                    'noKartu' => [
                        'rules' => 'required',
                    ],
                    'kdProviderPeserta' => [
                        'rules' => 'required',
                    ],
                    'nama' => [
                        'rules' => 'required',
                    ],
                    'nik' => [
                        'rules' => 'required',
                    ],
                    'tglLahir' => [
                        'rules' => 'required',
                    ],
                    'kelamin' => [
                        'rules' => 'required',
                    ],
                    // 'noHp' => [
                    //     'rules' => 'required',
                    // ],
                    // 'golDarah' => [
                    //     'rules' => 'required',
                    // ],
                    'alamat' => [
                        'rules' => 'required',
                    ],
                    'tglDaftar' => [
                        'rules' => 'required',
                    ],
                    'kunjSakit' => [
                        'rules' => 'required',
                    ],
                    'kdTkp' => [
                        'rules' => 'required',
                    ],
                    'kdPoli' => [
                        'rules' => 'required',
                    ],
                    'keluhan' => [
                        'rules' => 'required',
                    ],
                    'tinggiBadan' => [
                        'rules' => 'required',
                    ],
                    'beratBadan' => [
                        'rules' => 'required',
                    ],
                    'lingkarPerut' => [
                        'rules' => 'required',
                    ],
                    'sistole' => [
                        'rules' => 'required',
                    ],
                    'diastole' => [
                        'rules' => 'required',
                    ],
                    'respRate' => [
                        'rules' => 'required',
                    ],
                    'heartRate' => [
                        'rules' => 'required',
                    ]
                ])) {
                    session()->setFlashdata('msg', 'Ada data belum terisi');
                    return redirect()->to('/pendaftaran/add')->withInput();
                }

                $dataBpjs = [
                    "kdProviderPeserta" => $this->request->getVar('kdProviderPeserta'),
                    "tglDaftar" => date("d-m-Y", strtotime($this->request->getVar('tglDaftar'))),
                    "noKartu" => $this->request->getVar('noKartu'),
                    "kdPoli" => $this->request->getVar('kdPoli'),
                    "keluhan" => $this->request->getVar('keluhan'),
                    "kunjSakit" => $this->request->getVar('kunjSakit') == 'true' ? true : false,
                    "sistole" => $this->request->getVar('sistole'),
                    "diastole" => $this->request->getVar('diastole'),
                    "beratBadan" => $this->request->getVar('beratBadan'),
                    "tinggiBadan" => $this->request->getVar('tinggiBadan'),
                    "respRate" => $this->request->getVar('respRate'),
                    "lingkarPerut" => $this->request->getVar('lingkarPerut'),
                    "heartRate" => $this->request->getVar('heartRate'),
                    "rujukBalik" => '0',
                    "kdTkp" => $this->request->getVar('kdTkp'),

                    "id" => time(),
                    "nama" => $this->request->getVar('nama'),
                    "nik" => $this->request->getVar('nik'),
                    "bpjs" => $this->request->getVar('bpjs') == 'true' ? true : false,
                    "pasienLama" => $this->request->getVar('pasienLama') == 'true' ? true : false,
                    "noRM" => $this->request->getVar('noRM'),
                    "tglLahir" => date("d-m-Y", strtotime($this->request->getVar('tglLahir'))),
                    "kelamin" => $this->request->getVar('kelamin'),
                    "noHp" => $this->request->getVar('noHp'),
                    "golDarah" => $this->request->getVar('golDarah'),
                    "alamat" => $this->request->getVar('alamat'),
                ];

                date_default_timezone_set('UTC');
                $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
                $variabel1 = $this->consId . "&" . $tStamp;
                $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));
                $arrCurl = $this->arrCurl;
                $arrCurl[CURLOPT_URL] = $this->baseUrl . "/pendaftaran"; //nik atau noka; nomronya
                $arrCurl[CURLOPT_CUSTOMREQUEST] = "POST";
                $arrCurl[CURLOPT_HTTPHEADER][4] = "X-timestamp: " . $tStamp;
                $arrCurl[CURLOPT_HTTPHEADER][5] = "X-signature: " . $signature;
                $arrCurl[CURLOPT_HTTPHEADER][6] = "content-type: text/plain";
                $arrCurl[CURLOPT_POSTFIELDS] = json_encode([
                    "kdProviderPeserta" => $dataBpjs['kdProviderPeserta'],
                    "tglDaftar" => $dataBpjs['tglDaftar'],
                    "noKartu" => $dataBpjs['noKartu'],
                    "kdPoli" => $dataBpjs['kdPoli'],
                    "keluhan" => $dataBpjs['keluhan'],
                    "kunjSakit" => $dataBpjs['kunjSakit'],
                    "sistole" => $dataBpjs['sistole'],
                    "diastole" => $dataBpjs['diastole'],
                    "beratBadan" => $dataBpjs['beratBadan'],
                    "tinggiBadan" => $dataBpjs['tinggiBadan'],
                    "respRate" => $dataBpjs['respRate'],
                    "lingkarPerut" => $dataBpjs['lingkarPerut'],
                    "heartRate" => $dataBpjs['heartRate'],
                    "rujukBalik" => '0', //Belum tau apa itu rujuk balik
                    "kdTkp" => $dataBpjs['kdTkp']
                ]);
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

                $key = $this->consId . $this->secretKey . $tStamp;
                $string = $hasil['response'];
                $encrypt_method = 'AES-256-CBC';
                $key_hash = hex2bin(hash('sha256', $key));
                $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
                $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
                $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
                $dataBpjs['noUrut'] = json_decode($hasil_dekrip, true)['message'];

                if (substr($hasil['metaData']['code'], 0, 1) == '4') {
                    session()->setFlashdata('msg', $hasil['metaData']['message']);
                    return redirect()->to('/pendaftaran/add')->withInput();
                }

                $this->pasienModel->where(['id' => $this->request->getVar('noRM')])->set([
                    'nama' => $dataBpjs['nama'],
                    'tglLahir' => $dataBpjs['tglLahir'],
                    'noHp' => $dataBpjs['noHp'],
                    'golDarah' => $dataBpjs['golDarah'],
                    'nik' => $dataBpjs['nik'],
                    'noBpjs' => $dataBpjs['noKartu'],
                    'kdProviderPst' => $dataBpjs['kdProviderPeserta'],
                ])->update();

                $this->pendaftaranModel->insert([
                    'id' => $dataBpjs['id'],
                    'noUrut' => $dataBpjs['noUrut'],
                    'kdProviderPeserta' => $dataBpjs['kdProviderPeserta'],
                    'tglDaftar' => $dataBpjs['tglDaftar'],
                    'noKartu' => $dataBpjs['noKartu'],
                    'nik' => $dataBpjs['nik'],
                    'nama' => $dataBpjs['nama'],
                    'kdPoli' => $dataBpjs['kdPoli'],
                    'keluhan' => $dataBpjs['keluhan'],
                    'kunjSakit' => $dataBpjs['kunjSakit'],
                    'sistole' => $dataBpjs['sistole'],
                    'diastole' => $dataBpjs['diastole'],
                    'beratBadan' => $dataBpjs['beratBadan'],
                    'tinggiBadan' => $dataBpjs['tinggiBadan'],
                    'respRate' => $dataBpjs['respRate'],
                    'lingkarPerut' => $dataBpjs['lingkarPerut'],
                    'heartRate' => $dataBpjs['heartRate'],
                    'rujukBalik' => $dataBpjs['rujukBalik'],
                    'kdTkp' => $dataBpjs['kdTkp'],
                    'bpjs' => $dataBpjs['bpjs'],
                    'noRM' => $dataBpjs['noRM'],
                    'status' => 'Baru',
                ]);
            }
        } else if (!$bpjs) {
            if (!$pasienLama) {
                if (!$this->validate([
                    'bpjs' => [
                        'rules' => 'required',
                    ],
                    'pasienLama' => [
                        'rules' => 'required',
                    ],
                    // 'noKartu' => [
                    //     'rules' => 'required',
                    // ],
                    // 'kdProviderPeserta' => [
                    //     'rules' => 'required',
                    // ],
                    'nama' => [
                        'rules' => 'required',
                    ],
                    'nik' => [
                        'rules' => 'required',
                    ],
                    'tglLahir' => [
                        'rules' => 'required',
                    ],
                    'kelamin' => [
                        'rules' => 'required',
                    ],
                    'noHp' => [
                        'rules' => 'required',
                    ],
                    'golDarah' => [
                        'rules' => 'required',
                    ],
                    'alamat' => [
                        'rules' => 'required',
                    ],
                    'tglDaftar' => [
                        'rules' => 'required',
                    ],
                    'kunjSakit' => [
                        'rules' => 'required',
                    ],
                    'kdTkp' => [
                        'rules' => 'required',
                    ],
                    'kdPoli' => [
                        'rules' => 'required',
                    ],
                    'keluhan' => [
                        'rules' => 'required',
                    ],
                    'tinggiBadan' => [
                        'rules' => 'required',
                    ],
                    'beratBadan' => [
                        'rules' => 'required',
                    ],
                    'lingkarPerut' => [
                        'rules' => 'required',
                    ],
                    'sistole' => [
                        'rules' => 'required',
                    ],
                    'diastole' => [
                        'rules' => 'required',
                    ],
                    'respRate' => [
                        'rules' => 'required',
                    ],
                    'heartRate' => [
                        'rules' => 'required',
                    ]
                ])) {
                    session()->setFlashdata('msg', 'Ada data belum terisi');
                    return redirect()->to('/pendaftaran/add')->withInput();
                }
                $pesertaSeVocal = $this->pasienModel->like('id', substr($this->request->getVar('nama'), 0, 1), 'after')->findAll();
                $angkaIdTerbesar = 0;
                foreach ($pesertaSeVocal as $p) {
                    $angkaId = (int)substr($p['id'], 1);
                    if ($angkaId > $angkaIdTerbesar) $angkaIdTerbesar = $angkaId;
                }
                $noRM = strtoupper(substr($this->request->getVar('nama'), 0, 1)) . ($angkaIdTerbesar + 1);
                $dataBpjs = [
                    "kdProviderPeserta" => $this->request->getVar('kdProviderPeserta'),
                    "tglDaftar" => date("d-m-Y", strtotime($this->request->getVar('tglDaftar'))),
                    "noKartu" => $this->request->getVar('noKartu'),
                    "kdPoli" => $this->request->getVar('kdPoli'),
                    "keluhan" => $this->request->getVar('keluhan'),
                    "kunjSakit" => $this->request->getVar('kunjSakit') == 'true' ? true : false,
                    "sistole" => $this->request->getVar('sistole'),
                    "diastole" => $this->request->getVar('diastole'),
                    "beratBadan" => $this->request->getVar('beratBadan'),
                    "tinggiBadan" => $this->request->getVar('tinggiBadan'),
                    "respRate" => $this->request->getVar('respRate'),
                    "lingkarPerut" => $this->request->getVar('lingkarPerut'),
                    "heartRate" => $this->request->getVar('heartRate'),
                    "rujukBalik" => '0',
                    "kdTkp" => $this->request->getVar('kdTkp'),

                    "id" => time(),
                    "nama" => $this->request->getVar('nama'),
                    "nik" => $this->request->getVar('nik'),
                    "bpjs" => $this->request->getVar('bpjs') == 'true' ? true : false,
                    "pasienLama" => $this->request->getVar('pasienLama') == 'true' ? true : false,
                    "noRM" => $noRM,
                    "tglLahir" => date("d-m-Y", strtotime($this->request->getVar('tglLahir'))),
                    "kelamin" => $this->request->getVar('kelamin'),
                    "noHp" => $this->request->getVar('noHp'),
                    "golDarah" => $this->request->getVar('golDarah'),
                    "alamat" => $this->request->getVar('alamat'),
                ];

                $getDataTerbaru = $this->pendaftaranModel->orderBy('id', 'desc')->where([
                    'tglDaftar' => $dataBpjs['tglDaftar'],
                ])->like('noUrut', 'U', 'both')->first();
                if ($getDataTerbaru) {
                    $dataBpjs['noUrut'] = 'U' . ((int)substr($getDataTerbaru['noUrut'], 1) + 1);
                } else {
                    $dataBpjs['noUrut'] = 'U1';
                }

                $this->pasienModel->insert([
                    'id' => $dataBpjs['noRM'],
                    'nama' => $dataBpjs['nama'],
                    'tglLahir' => $dataBpjs['tglLahir'],
                    'alamat' => $dataBpjs['alamat'],
                    'kelamin' => $dataBpjs['kelamin'],
                    'noHp' => $dataBpjs['noHp'],
                    'rekamMedis' => 'Kunjungan ' . $dataBpjs['tglDaftar'],
                    'golDarah' => $dataBpjs['golDarah'],
                    'nik' => $dataBpjs['nik'],
                    'noBpjs' => $dataBpjs['noKartu'],
                    'kdProviderPst' => $dataBpjs['kdProviderPeserta'],
                ]);

                //cek pasien ini udah masuk daftar pendaftaran apa blm utk hari ini
                $getPendaftaran = $this->pendaftaranModel->where([
                    'tglDaftar' => $dataBpjs['tglDaftar'],
                    'noRM' => $dataBpjs['noRM'],
                ])->first();
                if ($getPendaftaran) {
                    session()->setFlashdata('msg', 'Pasien sudah masuk ke pendaftaran');
                    return redirect()->to('/pendaftaran/add')->withInput();
                }

                $this->pendaftaranModel->insert([
                    'id' => $dataBpjs['id'],
                    'noUrut' => $dataBpjs['noUrut'],
                    'kdProviderPeserta' => $dataBpjs['kdProviderPeserta'],
                    'tglDaftar' => $dataBpjs['tglDaftar'],
                    'noKartu' => $dataBpjs['noKartu'],
                    'nik' => $dataBpjs['nik'],
                    'nama' => $dataBpjs['nama'],
                    'kdPoli' => $dataBpjs['kdPoli'],
                    'keluhan' => $dataBpjs['keluhan'],
                    'kunjSakit' => $dataBpjs['kunjSakit'],
                    'sistole' => $dataBpjs['sistole'],
                    'diastole' => $dataBpjs['diastole'],
                    'beratBadan' => $dataBpjs['beratBadan'],
                    'tinggiBadan' => $dataBpjs['tinggiBadan'],
                    'respRate' => $dataBpjs['respRate'],
                    'lingkarPerut' => $dataBpjs['lingkarPerut'],
                    'heartRate' => $dataBpjs['heartRate'],
                    'rujukBalik' => $dataBpjs['rujukBalik'],
                    'kdTkp' => $dataBpjs['kdTkp'],
                    'bpjs' => $dataBpjs['bpjs'],
                    'noRM' => $dataBpjs['noRM'],
                    'status' => 'Baru',
                ]);
            } else {
                if (!$this->validate([
                    'bpjs' => [
                        'rules' => 'required',
                    ],
                    'pasienLama' => [
                        'rules' => 'required',
                    ],
                    // 'noKartu' => [
                    //     'rules' => 'required',
                    // ],
                    // 'kdProviderPeserta' => [
                    //     'rules' => 'required',
                    // ],
                    'nama' => [
                        'rules' => 'required',
                    ],
                    // 'nik' => [
                    //     'rules' => 'required',
                    // ],
                    // 'tglLahir' => [
                    //     'rules' => 'required',
                    // ],
                    'kelamin' => [
                        'rules' => 'required',
                    ],
                    // 'noHp' => [
                    //     'rules' => 'required',
                    // ],
                    // 'golDarah' => [
                    //     'rules' => 'required',
                    // ],
                    'alamat' => [
                        'rules' => 'required',
                    ],
                    'tglDaftar' => [
                        'rules' => 'required',
                    ],
                    'kunjSakit' => [
                        'rules' => 'required',
                    ],
                    'kdTkp' => [
                        'rules' => 'required',
                    ],
                    'kdPoli' => [
                        'rules' => 'required',
                    ],
                    'keluhan' => [
                        'rules' => 'required',
                    ],
                    'tinggiBadan' => [
                        'rules' => 'required',
                    ],
                    'beratBadan' => [
                        'rules' => 'required',
                    ],
                    'lingkarPerut' => [
                        'rules' => 'required',
                    ],
                    'sistole' => [
                        'rules' => 'required',
                    ],
                    'diastole' => [
                        'rules' => 'required',
                    ],
                    'respRate' => [
                        'rules' => 'required',
                    ],
                    'heartRate' => [
                        'rules' => 'required',
                    ]
                ])) {
                    session()->setFlashdata('msg', 'Ada data belum terisi');
                    return redirect()->to('/pendaftaran/add')->withInput();
                }
                $dataBpjs = [
                    // "kdProviderPeserta" => $this->request->getVar('kdProviderPeserta'),
                    "tglDaftar" => date("d-m-Y", strtotime($this->request->getVar('tglDaftar'))),
                    // "noKartu" => $this->request->getVar('noKartu'),
                    "kdPoli" => $this->request->getVar('kdPoli'),
                    "keluhan" => $this->request->getVar('keluhan'),
                    "kunjSakit" => $this->request->getVar('kunjSakit') == 'true' ? true : false,
                    "sistole" => $this->request->getVar('sistole'),
                    "diastole" => $this->request->getVar('diastole'),
                    "beratBadan" => $this->request->getVar('beratBadan'),
                    "tinggiBadan" => $this->request->getVar('tinggiBadan'),
                    "respRate" => $this->request->getVar('respRate'),
                    "lingkarPerut" => $this->request->getVar('lingkarPerut'),
                    "heartRate" => $this->request->getVar('heartRate'),
                    "rujukBalik" => '0',
                    "kdTkp" => $this->request->getVar('kdTkp'),

                    "id" => time(),
                    "nama" => $this->request->getVar('nama'),
                    // "nik" => $this->request->getVar('nik'),
                    "bpjs" => $this->request->getVar('bpjs') == 'true' ? true : false,
                    "pasienLama" => $this->request->getVar('pasienLama') == 'true' ? true : false,
                    "noRM" => $this->request->getVar('noRM'),
                    // "tglLahir" => date("d-m-Y", strtotime($this->request->getVar('tglLahir'))),
                    "kelamin" => $this->request->getVar('kelamin'),
                    // "noHp" => $this->request->getVar('noHp'),
                    // "golDarah" => $this->request->getVar('golDarah'),
                    "alamat" => $this->request->getVar('alamat'),
                ];

                $getDataTerbaru = $this->pendaftaranModel->orderBy('id', 'desc')->where([
                    'tglDaftar' => $dataBpjs['tglDaftar'],
                ])->like('noUrut', 'U', 'both')->first();
                if ($getDataTerbaru) {
                    $dataBpjs['noUrut'] = 'U' . ((int)substr($getDataTerbaru['noUrut'], 1) + 1);
                } else {
                    $dataBpjs['noUrut'] = 'U1';
                }

                //cek pasien ini udah masuk daftar pendaftaran apa blm utk hari ini
                $getPendaftaran = $this->pendaftaranModel->where([
                    'tglDaftar' => $dataBpjs['tglDaftar'],
                    'noRM' => $dataBpjs['noRM'],
                ])->first();
                if ($getPendaftaran) {
                    session()->setFlashdata('msg', 'Pasien sudah masuk ke pendaftaran');
                    return redirect()->to('/pendaftaran/add')->withInput();
                }

                $pesertanya = $this->pasienModel->where(['id' => $this->request->getVar('noRM')])->first();
                $this->pendaftaranModel->insert([
                    'id' => $dataBpjs['id'],
                    'noUrut' => $dataBpjs['noUrut'],
                    'kdProviderPeserta' => $pesertanya['kdProviderPst'],
                    'tglDaftar' => $dataBpjs['tglDaftar'],
                    'noKartu' => $pesertanya['noBpjs'],
                    'nik' => $pesertanya['nik'],
                    'nama' => $dataBpjs['nama'],
                    'kdPoli' => $dataBpjs['kdPoli'],
                    'keluhan' => $dataBpjs['keluhan'],
                    'kunjSakit' => $dataBpjs['kunjSakit'],
                    'sistole' => $dataBpjs['sistole'],
                    'diastole' => $dataBpjs['diastole'],
                    'beratBadan' => $dataBpjs['beratBadan'],
                    'tinggiBadan' => $dataBpjs['tinggiBadan'],
                    'respRate' => $dataBpjs['respRate'],
                    'lingkarPerut' => $dataBpjs['lingkarPerut'],
                    'heartRate' => $dataBpjs['heartRate'],
                    'rujukBalik' => $dataBpjs['rujukBalik'],
                    'kdTkp' => $dataBpjs['kdTkp'],
                    'bpjs' => $dataBpjs['bpjs'],
                    'noRM' => $dataBpjs['noRM'],
                    'status' => 'Baru',
                ]);
            }
        }
        return redirect()->to('/pendaftaran/list');
    }

    public function listPendaftaran($tanggal = false, $jenis = 'all')
    {
        if (!$tanggal) $tanggal = date("d-m-Y", strtotime("+7 Hours"));
        $pendaftaran = $this->pendaftaranModel->getPendaftaran($tanggal, $jenis);
        // dd([
        //     'tanggal' => $tanggal,
        //     'jenis' => $jenis,
        //     'pendaftar' => $pendaftaran,
        // ]);
        $data = [
            'title' => 'List Pendaftaran',
            'pendaftaran' => $pendaftaran,
            'jenis' => $jenis,
            'tanggal' => explode("-", $tanggal)[2] . "-" . explode("-", $tanggal)[1] . "-" . explode("-", $tanggal)[0],
            'msg' => session()->getFlashdata('msg') ? session()->getFlashdata('msg') : false
        ];
        return view('pendaftaran/list', $data);
    }
    public function delPendaftaran($id)
    {
        $getPendaftaran = $this->pendaftaranModel->where(['id' => $id])->first();
        $noKartu = $getPendaftaran['noKartu'];
        $tglDaftar = $getPendaftaran['tglDaftar'];
        $noUrut = $getPendaftaran['noUrut'];
        $kdPoli = $getPendaftaran['kdPoli'];
        $bpjs = $getPendaftaran['bpjs'];
        // dd($getPendaftaran);
        if ($bpjs) {
            date_default_timezone_set('UTC');
            $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
            $variabel1 = $this->consId . "&" . $tStamp;
            $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));

            $arrCurl = $this->arrCurl;
            $arrCurl[CURLOPT_URL] = $this->baseUrl . "/pendaftaran/peserta/" . $noKartu . "/tglDaftar/" . $tglDaftar . "/noUrut/" . $noUrut . "/kdPoli/" . $kdPoli;
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
                session()->setFlashdata('msg', $hasil['response']['message']);
                return redirect()->to('/pendaftaran/list/' . $tglDaftar . '/all');
            }
        }

        //hapus didatabase
        $this->pendaftaranModel->where(['id' => $id])->delete();
        return redirect()->to('/pendaftaran/list');
    }
}
