<?php

namespace App\Controllers;

use App\Models\PasienModel;

class MainController extends BaseController
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
    }
    public function home()
    {
        $data = [
            'title' => 'Oceria | Drg. Sri Umiati',
        ];
        return view('home', $data);
    }
    public function refProvider()
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
        $provider = json_decode($hasil_dekrip, true);
        $data = [
            'title' => 'Provider Rayonisasi',
            'provider' => $provider
        ];
        return view('fitur/provider', $data);
    }

    public function rujukan()
    {
        $rujukan =  session()->getFlashdata('rujukan') ? session()->getFlashdata('rujukan') : false;
        $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        if ($rujukan) {
            $rujukan['tglKunjungan'] = explode('-', $rujukan['tglKunjungan'])[0] . " " . $bulan[(int)explode('-', $rujukan['tglKunjungan'])[1] - 1] . " " . explode('-', $rujukan['tglKunjungan'])[2];
        }
        $data = [
            'title' => 'Cari Rujukan',
            'rujukan' => $rujukan,
            'msg' => session()->getFlashdata('msg') ? session()->getFlashdata('msg') : false,
            'nokun' => session()->getFlashdata('nokun') ? session()->getFlashdata('nokun') : ''
        ];
        return view('fitur/rujukan', $data);
    }
    public function getRujukan()
    {
        $nokun = $this->request->getVar('nokun');
        // dd($nokun);
        if ($nokun == '') {
            session()->setFlashdata('msg', 'No Kunjungan harus diisi');
            return redirect()->to('/rujukan');
        }
        if (strlen($nokun) < 19) {
            session()->setFlashdata('msg', 'No kunjungan harus berjumlah 19 digit');
            return redirect()->to('/rujukan');
        }
        //get spesialis
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $variabel1 = $this->consId . "&" . $tStamp;
        $signature = base64_encode(hash_hmac('sha256', $variabel1, $this->secretKey, true));

        $arrCurl = $this->arrCurl;
        $arrCurl[CURLOPT_URL] = $this->baseUrl . "/kunjungan/rujukan/" . $nokun;
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
        $getRujukan = json_decode($hasil_dekrip, true);

        session()->setFlashdata('rujukan', $getRujukan);
        session()->setFlashdata('nokun', $nokun);
        return redirect()->to('/rujukan');
    }
}
