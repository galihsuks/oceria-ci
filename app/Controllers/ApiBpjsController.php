<?php

namespace App\Controllers;


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
    public function __construct()
    {
        $this->kodePPK = '095';
        $this->consId = '1384';
        $this->secretKey = '8kQABD0F66';
        $this->userkeyPCare = '002f4d85a8df265f8a4a1f46df3aec15';
        $this->username = '0150G017-develop';
        $this->password = 'Pcare123#';
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

        $key = $this->consId . $this->secretKey . $tStamp;
        $string = $hasil['response'];
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        $hasil_dekrip = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);

        return $this->response->setJSON($hasil_dekrip, false);
    }
}
