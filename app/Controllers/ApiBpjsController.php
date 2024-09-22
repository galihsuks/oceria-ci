<?php

namespace App\Controllers;

use App\Models\PasienModel;
use App\Models\KunjunganModel;

class ApiBpjsController extends BaseController
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

    protected $pasienModel;
    protected $kunjunganModel;
    public function __construct()
    {
        $this->kodePPK = '';
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

        $this->pasienModel = new PasienModel();
        $this->kunjunganModel = new KunjunganModel();
    }

    public function getPeserta($jenisNomor, $nomor)
    {
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $variabel1 = $this->consId . "&" . $tStamp;
        $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));
        $arrCurl = $this->arrCurl;
        $arrCurl[CURLOPT_URL] = $this->baseUrl . "/peserta/" . $jenisNomor . "/" . $nomor; //nik atau noka; nomronya
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
        $key = $this->consId . $this->secretKey . $tStamp;
        $string = $hasil['response'];
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
        if (!$hasil_dekrip) {
            return $this->response->setJSON($hasil, false);
        }
        return $this->response->setJSON($hasil_dekrip, false);
    }
    public function getPoli()
    {
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $variabel1 = $this->consId . "&" . $tStamp;
        $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));
        $arrCurl = $this->arrCurl;
        $arrCurl[CURLOPT_URL] = $this->baseUrl . "/poli/fktp/0/20"; //nik atau noka; nomronya
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
        $key = $this->consId . $this->secretKey . $tStamp;
        $string = $hasil['response'];
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
        return $this->response->setJSON($hasil_dekrip, false);
    }
    public function getPoliKomponen($selected = false)
    {
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $variabel1 = $this->consId . "&" . $tStamp;
        $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));
        $arrCurl = $this->arrCurl;
        $arrCurl[CURLOPT_URL] = $this->baseUrl . "/poli/fktp/0/20"; //nik atau noka; nomronya
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
        $key = $this->consId . $this->secretKey . $tStamp;
        $string = $hasil['response'];
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
        $data = [
            'name' => 'kdPoli',
            'null' => false,
            'keyValue' => 'kdPoli',
            'keyInner' => 'nmPoli',
            'data' => json_decode($hasil_dekrip, true)['list'],
            'selected' => $selected
        ];
        return view('layout/select', $data);
    }
    public function getProvider()
    {
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $variabel1 = $this->consId . "&" . $tStamp;
        $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));
        $arrCurl = $this->arrCurl;
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
        if (substr($hasil['metaData']['code'], 0, 1) == '4') {
            return $this->response->setJSON($hasil, false);
        }
        $key = $this->consId . $this->secretKey . $tStamp;
        $string = $hasil['response'];
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
        return $this->response->setJSON($hasil_dekrip, false);
    }
    public function getPrognosa()
    {
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $variabel1 = $this->consId . "&" . $tStamp;
        $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));
        $arrCurl = $this->arrCurl;
        $arrCurl[CURLOPT_URL] = $this->baseUrl . "/prognosa";
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
        $key = $this->consId . $this->secretKey . $tStamp;
        $string = $hasil['response'];
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
        return $this->response->setJSON($hasil_dekrip, false);
    }
    public function getPrognosaKomponen($selected = false)
    {
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $variabel1 = $this->consId . "&" . $tStamp;
        $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));
        $arrCurl = $this->arrCurl;
        $arrCurl[CURLOPT_URL] = $this->baseUrl . "/prognosa";
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
        $key = $this->consId . $this->secretKey . $tStamp;
        $string = $hasil['response'];
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
        $data = [
            'name' => 'kdPrognosa',
            'null' => false,
            'keyValue' => 'kdPrognosa',
            'keyInner' => 'nmPrognosa',
            'data' => json_decode($hasil_dekrip, true)['list'],
            'selected' => $selected
        ];
        return view('layout/select', $data);
    }

    public function getPendaftaran($tanggal)
    {
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $variabel1 = $this->consId . "&" . $tStamp;
        $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));
        $arrCurl = $this->arrCurl;
        $arrCurl[CURLOPT_URL] = $this->baseUrl . "/pendaftaran/tglDaftar/" . $tanggal . "/0/15";
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
        $key = $this->consId . $this->secretKey . $tStamp;
        $string = $hasil['response'];
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
        return $this->response->setJSON($hasil_dekrip, false);
    }
    public function delPendaftaran($noKartu, $tglDaftar, $noUrut, $kdPoli)
    {
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
            return $this->response->setJSON($hasil, false);
        }

        $key = $this->consId . $this->secretKey . $tStamp;
        $string = $hasil['response'];
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
        return $this->response->setJSON($hasil_dekrip, false);
    }

    public function getRiwayatKunjungan($noKartu)
    {
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $variabel1 = $this->consId . "&" . $tStamp;
        $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));

        $arrCurl = $this->arrCurl;
        $arrCurl[CURLOPT_URL] = $this->baseUrl . "/kunjungan/peserta/" . $noKartu;
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

        $key = $this->consId . $this->secretKey . $tStamp;
        $string = $hasil['response'];
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);

        return $this->response->setJSON($hasil_dekrip, false);
    }
    public function delRiwayatKunjungan($noKunjungan)
    {
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $variabel1 = $this->consId . "&" . $tStamp;
        $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));

        $arrCurl = $this->arrCurl;
        $arrCurl[CURLOPT_URL] = $this->baseUrl . "/kunjungan/" . $noKunjungan;
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
            return $this->response->setJSON($hasil, false);
        }

        $key = $this->consId . $this->secretKey . $tStamp;
        $string = $hasil['response'];
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
        return $this->response->setJSON($hasil_dekrip, false);
    }


    public function getTindakan($kdTkp)
    {
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $variabel1 = $this->consId . "&" . $tStamp;
        $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));

        $arrCurl = $this->arrCurl;
        $arrCurl[CURLOPT_URL] = $this->baseUrl . "/tindakan/kdTkp/" . $kdTkp . "/0/15";
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

        $key = $this->consId . $this->secretKey . $tStamp;
        $string = $hasil['response'];
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
        return $this->response->setJSON($hasil_dekrip, false);
    }
    public function getTindakanKomponen($kdTkp, $selected = false)
    {
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $variabel1 = $this->consId . "&" . $tStamp;
        $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));

        $arrCurl = $this->arrCurl;
        $arrCurl[CURLOPT_URL] = $this->baseUrl . "/tindakan/kdTkp/" . $kdTkp . "/0/15";
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

        $key = $this->consId . $this->secretKey . $tStamp;
        $string = $hasil['response'];
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
        $data = [
            'name' => 'kdTindakan',
            'null' => false,
            'keyValue' => 'kdTindakan',
            'keyInner' => 'nmTindakan',
            'data' => json_decode($hasil_dekrip, true)['list'],
            'selected' => $selected
        ];
        return view('layout/select', $data);
    }
    public function addTindakan()
    {
        $bodyJson = $this->request->getBody();
        $body = json_decode($bodyJson, true);

        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $variabel1 = $this->consId . "&" . $tStamp;
        $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));

        $arrCurl = $this->arrCurl;
        $arrCurl[CURLOPT_URL] = $this->baseUrl . "/tindakan";
        $arrCurl[CURLOPT_CUSTOMREQUEST] = "POST";
        $arrCurl[CURLOPT_HTTPHEADER][4] = "X-timestamp: " . $tStamp;
        $arrCurl[CURLOPT_HTTPHEADER][5] = "X-signature: " . $signature;
        $arrCurl[CURLOPT_HTTPHEADER][6] = "content-type: text/plain";
        $arrCurl[CURLOPT_POSTFIELDS] = json_encode($body);
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

        $key = $this->consId . $this->secretKey . $tStamp;
        $string = $hasil['response'];
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
        return $this->response->setJSON($hasil_dekrip, false);
    }

    public function getDiagnosa($kdDiagnosa)
    {
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $variabel1 = $this->consId . "&" . $tStamp;
        $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));

        $arrCurl = $this->arrCurl;
        $arrCurl[CURLOPT_URL] = $this->baseUrl . "/diagnosa/" . $kdDiagnosa . "/0/15";
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

        $key = $this->consId . $this->secretKey . $tStamp;
        $string = $hasil['response'];
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
        return $this->response->setJSON($hasil_dekrip, false);
    }
    public function getKesadaran()
    {
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $variabel1 = $this->consId . "&" . $tStamp;
        $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));

        $arrCurl = $this->arrCurl;
        $arrCurl[CURLOPT_URL] = $this->baseUrl . "/kesadaran";
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

        $key = $this->consId . $this->secretKey . $tStamp;
        $string = $hasil['response'];
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
        return $this->response->setJSON($hasil_dekrip, false);
    }
    public function getKesadaranKomponen($selected = false)
    {
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $variabel1 = $this->consId . "&" . $tStamp;
        $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));

        $arrCurl = $this->arrCurl;
        $arrCurl[CURLOPT_URL] = $this->baseUrl . "/kesadaran";
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

        $key = $this->consId . $this->secretKey . $tStamp;
        $string = $hasil['response'];
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
        $data = [
            'name' => 'kdSadar',
            'null' => false,
            'keyValue' => 'kdSadar',
            'keyInner' => 'nmSadar',
            'data' => json_decode($hasil_dekrip, true)['list'],
            'selected' => $selected
        ];
        return view('layout/select', $data);
    }
    public function getAlergi($kdAlergi)
    {
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $variabel1 = $this->consId . "&" . $tStamp;
        $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));

        $arrCurl = $this->arrCurl;
        $arrCurl[CURLOPT_URL] = $this->baseUrl . "/alergi/jenis/" . $kdAlergi;
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

        $key = $this->consId . $this->secretKey . $tStamp;
        $string = $hasil['response'];
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
        return $this->response->setJSON($hasil_dekrip, false);
    }
    public function getAlergiKomponen($kdAlergi, $selected = false)
    {
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $variabel1 = $this->consId . "&" . $tStamp;
        $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));

        $arrCurl = $this->arrCurl;
        $arrCurl[CURLOPT_URL] = $this->baseUrl . "/alergi/jenis/" . $kdAlergi;
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

        $key = $this->consId . $this->secretKey . $tStamp;
        $string = $hasil['response'];
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
        switch ($kdAlergi) {
            case '01':
                $name = 'alergiMakan';
                break;
            case '02':
                $name = 'alergiUdara';
                break;
            case '03':
                $name = 'alergiObat';
                break;
        }
        $data = [
            'name' => $name,
            'null' => false,
            'keyValue' => 'kdAlergi',
            'keyInner' => 'nmAlergi',
            'data' => json_decode($hasil_dekrip, true)['list'],
            'selected' => $selected
        ];
        return view('layout/select', $data);
    }
    public function getStatusPulang($rawatInap)
    {
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $variabel1 = $this->consId . "&" . $tStamp;
        $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));

        $arrCurl = $this->arrCurl;
        $arrCurl[CURLOPT_URL] = $this->baseUrl . "/statuspulang/rawatInap/" . $rawatInap;
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

        $key = $this->consId . $this->secretKey . $tStamp;
        $string = $hasil['response'];
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
        return $this->response->setJSON($hasil_dekrip, false);
    }
    public function getStatusPulangKomponen($rawatInap, $selected = false)
    {
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $variabel1 = $this->consId . "&" . $tStamp;
        $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));

        $arrCurl = $this->arrCurl;
        $arrCurl[CURLOPT_URL] = $this->baseUrl . "/statuspulang/rawatInap/" . $rawatInap;
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
        $key = $this->consId . $this->secretKey . $tStamp;
        $string = $hasil['response'];
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
        $data = [
            'name' => 'kdStatusPulang',
            'null' => false,
            'keyValue' => 'kdStatusPulang',
            'keyInner' => 'nmStatusPulang',
            'data' => json_decode($hasil_dekrip, true)['list'],
            'selected' => $selected
        ];
        return view('layout/select', $data);
    }
    public function getDokter()
    {
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $variabel1 = $this->consId . "&" . $tStamp;
        $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));

        $arrCurl = $this->arrCurl;
        $arrCurl[CURLOPT_URL] = $this->baseUrl . "/dokter/1/15";
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

        $key = $this->consId . $this->secretKey . $tStamp;
        $string = $hasil['response'];
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
        return $this->response->setJSON($hasil_dekrip, false);
    }
    public function getDokterKomponen($selected = false)
    {
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $variabel1 = $this->consId . "&" . $tStamp;
        $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));

        $arrCurl = $this->arrCurl;
        $arrCurl[CURLOPT_URL] = $this->baseUrl . "/dokter/1/15";
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

        $key = $this->consId . $this->secretKey . $tStamp;
        $string = $hasil['response'];
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
        $data = [
            'name' => 'kdDokter',
            'null' => true,
            'keyValue' => 'kdDokter',
            'keyInner' => 'nmDokter',
            'data' => json_decode($hasil_dekrip, true)['list'],
            'selected' => $selected
        ];
        return view('layout/select', $data);
    }

    public function getRefSpesialis()
    {
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
        if (substr($hasil['metaData']['code'], 0, 1) == '4') {
            return $this->response->setJSON($hasil, false);
        }

        $key = $this->consId . $this->secretKey . $tStamp;
        $string = $hasil['response'];
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);

        if (!$hasil_dekrip) {
            return $this->response->setJSON($hasil, false);
        }
        return $this->response->setJSON($hasil_dekrip, false);
    }
    public function getRefSpesialisKomponen($selected = false)
    {
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
        if (substr($hasil['metaData']['code'], 0, 1) == '4') {
            return $this->response->setJSON($hasil, false);
        }
        $key = $this->consId . $this->secretKey . $tStamp;
        $string = $hasil['response'];
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
        $data = [
            'name' => '',
            'null' => false,
            'keyValue' => 'kdSpesialis',
            'keyInner' => 'nmSpesialis',
            'data' => json_decode($hasil_dekrip, true)['list'],
            'selected' => $selected
        ];
        return view('layout/select', $data);
    }
    public function getRefSubSpesialis($kdSpesialis)
    {
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $variabel1 = $this->consId . "&" . $tStamp;
        $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));

        $arrCurl = $this->arrCurl;
        $arrCurl[CURLOPT_URL] = $this->baseUrl . "/spesialis/" . $kdSpesialis . "/subspesialis";
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

        $key = $this->consId . $this->secretKey . $tStamp;
        $string = $hasil['response'];
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);

        if (!$hasil_dekrip) {
            return $this->response->setJSON($hasil, false);
        }
        return $this->response->setJSON($hasil_dekrip, false);
    }

    public function getRefSarana()
    {
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $variabel1 = $this->consId . "&" . $tStamp;
        $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));

        $arrCurl = $this->arrCurl;
        $arrCurl[CURLOPT_URL] = $this->baseUrl . "/spesialis/sarana";
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

        $key = $this->consId . $this->secretKey . $tStamp;
        $string = $hasil['response'];
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);

        if (!$hasil_dekrip) {
            return $this->response->setJSON($hasil, false);
        }
        return $this->response->setJSON($hasil_dekrip, false);
    }
    public function getRefSaranaKomponen($selected = false)
    {
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $variabel1 = $this->consId . "&" . $tStamp;
        $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));

        $arrCurl = $this->arrCurl;
        $arrCurl[CURLOPT_URL] = $this->baseUrl . "/spesialis/sarana";
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

        $key = $this->consId . $this->secretKey . $tStamp;
        $string = $hasil['response'];
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);

        $data = [
            'name' => 'kdSarana',
            'null' => true,
            'keyValue' => 'kdSarana',
            'keyInner' => 'nmSarana',
            'data' => json_decode($hasil_dekrip, true)['list'],
            'selected' => $selected
        ];
        return view('layout/select', $data);
    }

    public function getRefKhusus()
    {
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $variabel1 = $this->consId . "&" . $tStamp;
        $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));

        $arrCurl = $this->arrCurl;
        $arrCurl[CURLOPT_URL] = $this->baseUrl . "/spesialis/khusus";
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

        $key = $this->consId . $this->secretKey . $tStamp;
        $string = $hasil['response'];
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);

        if (!$hasil_dekrip) {
            return $this->response->setJSON($hasil, false);
        }
        return $this->response->setJSON($hasil_dekrip, false);
    }
    public function getRefKhususKomponen($selected = false)
    {
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $variabel1 = $this->consId . "&" . $tStamp;
        $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));

        $arrCurl = $this->arrCurl;
        $arrCurl[CURLOPT_URL] = $this->baseUrl . "/spesialis/khusus";
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

        $key = $this->consId . $this->secretKey . $tStamp;
        $string = $hasil['response'];
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);

        $data = [
            'name' => 'kdKhusus',
            'null' => false,
            'keyValue' => 'kdKhusus',
            'keyInner' => 'nmKhusus',
            'data' => json_decode($hasil_dekrip, true)['list'],
            'selected' => $selected
        ];
        return view('layout/select', $data);
    }

    public function getFasketRujukanSubSpesialis($kdSubSpesialis, $kdSarana, $tglEstRujuk)
    {
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $variabel1 = $this->consId . "&" . $tStamp;
        $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));

        $arrCurl = $this->arrCurl;
        $arrCurl[CURLOPT_URL] = $this->baseUrl . "/spesialis/rujuk/subspesialis/" . $kdSubSpesialis . "/sarana/" . $kdSarana . "/tglEstRujuk/" . $tglEstRujuk;
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

        $key = $this->consId . $this->secretKey . $tStamp;
        $string = $hasil['response'];
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);

        if (!$hasil_dekrip) {
            return $this->response->setJSON($hasil, false);
        }
        return $this->response->setJSON($hasil_dekrip, false);
    }

    public function getFasketRujukanKhusus($kdKhusus, $kdSubSpesialis, $noKartu, $tglEstRujuk)
    {
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $variabel1 = $this->consId . "&" . $tStamp;
        $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));

        $arrCurl = $this->arrCurl;
        $KdKhususSpesial = ["THA", "HEM"];
        if (in_array($kdKhusus, $KdKhususSpesial)) {
            $arrCurl[CURLOPT_URL] = $this->baseUrl . "/spesialis/rujuk/khusus/" . $kdKhusus . "/subspesialis/" . $kdSubSpesialis . "/noKartu/" . $noKartu . "/tglEstRujuk/" . $tglEstRujuk;
        } else {
            $arrCurl[CURLOPT_URL] = $this->baseUrl . "/spesialis/rujuk/khusus/" . $kdKhusus . "/noKartu/" . $noKartu . "/tglEstRujuk/" . $tglEstRujuk;
        }
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

        $key = $this->consId . $this->secretKey . $tStamp;
        $string = $hasil['response'];
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);

        if (!$hasil_dekrip) {
            return $this->response->setJSON($hasil, false);
        }
        return $this->response->setJSON($hasil_dekrip, false);
    }

    public function getRefTindakan($kdTkp)
    {
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $variabel1 = $this->consId . "&" . $tStamp;
        $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));
        $arrCurl = $this->arrCurl;
        $arrCurl[CURLOPT_URL] = $this->baseUrl . "/tindakan/kdTkp/" . $kdTkp . "/1/10"; //nik atau noka; nomronya
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
        $key = $this->consId . $this->secretKey . $tStamp;
        $string = $hasil['response'];
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
        return $this->response->setJSON($hasil_dekrip, false);
    }

    public function gantiTgl()
    {
        $semuaPeserta = $this->pasienModel->findAll();
        foreach ($semuaPeserta as $p) {
            if ($p['tglLahir'] != '') {
                $tglFix = str_replace("/", "-", $p['tglLahir']);
                $this->pasienModel->where(['id' => $p['id']])->set(['tglLahir' => $tglFix])->update();
            }
        }
        return $this->response->setJSON([
            'success' => true
        ], false);
    }
    public function gantiTglPraktekKunjungan()
    {
        $semuaKunjungan = $this->kunjunganModel->findAll(1000, 17000);
        $semuaTglKun = [];
        foreach ($semuaKunjungan as $k) {
            $tglPraktek = $k['tgl_praktek'];

            //hilagin hari
            // $tglPraktekBaru = explode(",", $tglPraktek)[0];

            //bulan ganti angka
            // $bulan = [
            //     ['januari', 'februari', 'maret', 'april', 'mei', 'juni', 'juli', 'agustus', 'september', 'oktober', 'nopember', 'desember'],
            //     ['january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'],
            //     ['jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'agu', 'sep', 'okt', 'nop', 'des'],
            //     ['janu', 'febr', 'marc', 'apri', 'may', 'jun', 'jul', 'aug', 'sept', 'oct', 'nov', 'dec'],
            //     ['janu', 'febr', 'marc', 'apri', 'may', 'jun', 'jul', 'ags', 'sept', 'oct', 'nov', 'dec'],
            //     ['janu', 'febr', 'marc', 'apri', 'may', 'jun', 'jul', 'augs', 'sept', 'oct', 'nope', 'dec'],
            //     ['janu', 'febr', 'marc', 'apri', 'may', 'jun', 'jul', 'agst', 'sept', 'oct', 'nove', 'dec'],
            // ];
            // $day = (int)explode("-", $tglPraktek)[0] <= 9 ? '0' . (int)explode("-", $tglPraktek)[0] : (int)explode("-", $tglPraktek)[0];
            // $month = '';
            // foreach ($bulan as $b) {
            //     if (in_array(strtolower(explode("-", $tglPraktek)[1]), $b)) {
            //         $urutanBulan = array_search(strtolower(explode("-", $tglPraktek)[1]), $b) + 1;
            //         $month = $urutanBulan <= 9 ? '0' . $urutanBulan : $urutanBulan;
            //     }
            // }
            // if ($month == '') {
            //     $month = explode("-", $tglPraktek)[1];
            // }
            // $tglPraktekBaru = $day . "-" . $month . "-" . explode("-", $tglPraktek)[2];

            // $this->kunjunganModel->where([
            //     'Nourut' => $k['NoUrut'],
            //     'tgl_praktek' => $k['tgl_praktek']
            // ])->set(['tgl_praktek' => $tglPraktekBaru])->update();
        }
        // dd($semuaTglKun);

        return $this->response->setJSON([
            'success' => true
        ], false);
    }
    public function benerinRm()
    {
        //benerin yg ada
        // $semuapasien = $this->pasienModel->findAll();
        // foreach ($semuapasien as $pasien) {
        //     $rm = json_decode($pasien['rekamMedis'], true);
        //     if (count($rm) > 0) {
        //         foreach ($rm as $ind_r => $r) {
        //             if (isset($r['0'])) {
        //                 $rm[$ind_r] = [
        //                     'tanggal' => $r['tanggal'],
        //                     'vitalSign' => $r['vitalSign'] . $r['0'],
        //                     'terapiObat/Non' => $r['terapiObat/Non']
        //                 ];
        //             }
        //         }
        //     }
        //     $this->pasienModel->where(['id' => $pasien['id']])->set(['rekamMedis' => json_encode($rm)])->update();
        // }

        //benerin terapiobat/non
        $offset = 12000;
        $semuapasien = $this->pasienModel->findAll(1000, $offset);
        $arrTerapiKolom = [
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
        foreach ($semuapasien as $pasien) {
            // $pasien = $semuapasien[0];
            $rm = json_decode($pasien['rekamMedis'], true);
            if (count($rm) > 0) {
                foreach ($rm as $ind_r => $r) {
                    if (isset($r['terapiObat/Non'])) {
                        if (str_contains($r['terapiObat/Non'], '<br>')) {
                            $getKunjungan = $this->kunjunganModel->where([
                                'tgl_praktek' => $r['tanggal'],
                                'ID_pasien' => $pasien['id']
                            ])->first();
                            $arrTerapi = explode('<br>', substr($r['terapiObat/Non'], 0, strlen($r['terapiObat/Non']) - 4));
                            $arrTerapiBaru = '';
                            $cekTerapiUdahTerisi = [];
                            foreach ($arrTerapi as $at) {
                                $nilai = explode(":", $at)[0];
                                foreach ($arrTerapiKolom as $atk) {
                                    if ($getKunjungan[$atk] == $nilai) {
                                        if (!in_array($atk, $cekTerapiUdahTerisi)) {
                                            $arrTerapiBaru = $arrTerapiBaru . str_replace('_', ' ', $atk) . ': ' . $nilai . ' | ';
                                            array_push($cekTerapiUdahTerisi, $atk);
                                        }
                                    }
                                }
                            }
                            $rm[$ind_r] = [
                                'tanggal' => $r['tanggal'],
                                'vitalSign' => $r['vitalSign'],
                                'terapiObat/Non' => $arrTerapiBaru
                            ];
                        }
                    }
                }
            }
            // dd($rm);
            $this->pasienModel->where(['id' => $pasien['id']])->set(['rekamMedis' => json_encode($rm)])->update();
        }

        return $this->response->setJSON([
            'success' => true,
            'offset' => $offset
        ], false);
    }
}
