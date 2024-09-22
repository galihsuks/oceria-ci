<?php

namespace App\Controllers;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\PasienModel;
use App\Models\AntrianModel;
use App\Models\AntrianPasienModel;

class WsfktpController extends BaseController
{
    protected $secretKey;
    protected $pasienModel;
    protected $antrianModel;
    protected $antrianPasienModel;
    public function __construct()
    {
        $this->secretKey = 'SDFTb5GNwQdDXGxPlBnqimQGdd8PjNZJOjXqofbjPjXVzI7m';
        $this->pasienModel = new PasienModel();
        $this->antrianModel = new AntrianModel();
        $this->antrianPasienModel = new AntrianPasienModel();
    }
    public function token()
    {
        if (!$this->request->hasHeader('x-username') || !$this->request->hasHeader('x-password')) {
            return $this->response->setJSON([
                "metadata" => [
                    "message" => "Precondition Failed",
                    "code" => 412
                ]
            ], false);
        }
        $username = $this->request->header('x-username')->getValue();
        $password = $this->request->header('x-password')->getValue();
        if ($username != 'user1' || $password != 'pass1') {
            return $this->response->setJSON([
                "metadata" => [
                    "message" => "Unauthorized",
                    "code" => 401
                ]
            ], false);
        }
        $payload = [
            'username' => $username,
        ];
        $tokenJwt = JWT::encode($payload, $this->secretKey, 'HS256');
        return $this->response->setJSON([
            "response" => [
                'token' => $tokenJwt
            ],
            "metadata" => [
                "message" => "Ok",
                "code" => 200
            ]
        ], false);
    }
    public function statusAntrean($kdPoli, $tglPeriksa)
    {
        $jwt = $this->request->header('x-token')->getValue();
        $username = $this->request->header('x-username')->getValue();
        $payload = json_encode(JWT::decode($jwt, new Key($this->secretKey, 'HS256')));
        if (!$payload) {
            return $this->response->setJSON([
                "metadata" => [
                    "message" => "Unauthorized",
                    "code" => 401
                ]
            ], false);
        }
        if ($username != 'user1') {
            return $this->response->setJSON([
                "metadata" => [
                    "message" => "Unauthorized",
                    "code" => 401
                ]
            ], false);
        }

        $antrian = $this->antrianModel->getStatus($kdPoli, $tglPeriksa);
        return $this->response->setJSON([
            "response" => $antrian,
            "metadata" => [
                "message" => "Ok",
                "code" => 200
            ]
        ], false);
    }
    public function ambilAntrean()
    {
        $jwt = $this->request->header('x-token')->getValue();
        $username = $this->request->header('x-username')->getValue();
        $payload = json_encode(JWT::decode($jwt, new Key($this->secretKey, 'HS256')));
        if (!$payload) {
            return $this->response->setJSON([
                "metadata" => [
                    "message" => "Unauthorized",
                    "code" => 401
                ]
            ], false);
        }
        if ($username != 'user1') {
            return $this->response->setJSON([
                "metadata" => [
                    "message" => "Unauthorized",
                    "code" => 401
                ]
            ], false);
        }

        $bodyJson = $this->request->getBody();
        $body = json_decode($bodyJson, true);
        if (!isset($body['nomorkartu']) || !isset($body["nik"]) || !isset($body["kodepoli"]) || !isset($body["tanggalperiksa"]) || !isset($body["keluhan"])) {
            return $this->response->setJSON([
                "metadata" => [
                    "message" => "Precondition Failed",
                    "code" => 412
                ]
            ], false);
        }
        //cek apakah pakai V2
        $versi2 = false;
        if (isset($body['kodedokter']) || isset($body['jampraktek']) || isset($body['norm']) || isset($body['nohp'])) $versi2 = true;
        if ($versi2) {
            if (!isset($body['kodedokter']) || !isset($body['jampraktek']) || !isset($body['norm']) || !isset($body['nohp'])) {
                return $this->response->setJSON([
                    "metadata" => [
                        "message" => "Precondition Failed",
                        "code" => 412
                    ]
                ], false);
            }
        }
        $pasien = $this->pasienModel->where(['noBpjs' => $body['nomorkartu']])->first();
        if (!$pasien) {
            return $this->response->setJSON([
                "metadata" => [
                    "message" => "Kunjungan pertama wajib berkunjung ke FKTP secara langsung",
                    "code" => 201
                ]
            ], false);
        }
        $cekUdahAntri = $this->antrianModel->where(['tanggal_periksa' => $body["tanggalperiksa"], 'kode_poli' => $body["kodepoli"], 'nomor_kartu' => $body["nomorkartu"], 'batal' => false])->first();
        if ($cekUdahAntri) {
            return $this->response->setJSON([
                "metadata" => [
                    "message" => "Pasien sudah mendaftar",
                    "code" => 201
                ]
            ], false);
        }
        if (date("D", strtotime($body["tanggalperiksa"])) == 'Sun') {
            return $this->response->setJSON([
                "metadata" => [
                    "message" => "FKTP di hari minggu tidak beroprasi",
                    "code" => 201
                ]
            ], false);
        }

        $data = [
            "nomor_kartu" => $body['nomorkartu'],
            "nik" => $body["nik"],
            "kode_poli" => $body["kodepoli"],
            "tanggal_periksa" => $body["tanggalperiksa"],
            "keluhan" => $body["keluhan"],
            "dipanggil" => false,
        ];
        if ($versi2) {
            $data['kode_dokter'] = $body['kodedokter'];
            $data['nama_dokter'] = 'Nama dokter ' . $body['kodedokter'];
            $data['jam_praktek'] = $body['jampraktek'];
            $data['norm'] = $body['norm'];
            $data['nohp'] = $body['nohp'];
        }

        $antrian = $this->antrianModel->where(['kode_poli' => $body["kodepoli"], 'tanggal_periksa' => $body["tanggalperiksa"]])->findAll();
        $data['nomor_antrean'] = 'A' . (count($antrian) + 1);
        $data['angka_antrean'] = count($antrian) + 1;
        if (count($antrian) == 0) {
            $data['dipanggil'] = true;
        }

        $data['keterangan'] = 'Apabila antrean terlewat harap mengambil antrean kembali';
        $data['nama_poli'] = 'Nama dari poli ' . $body["kodepoli"];
        $data['id'] = str_replace("-", '', $body["tanggalperiksa"]) . $body["kodepoli"] . sprintf("%02d", $data['angka_antrean']);
        $this->antrianModel->insert($data);

        $antrianTidakBatal = $this->antrianModel->where(['kode_poli' => $body["kodepoli"], 'tanggal_periksa' => $body["tanggalperiksa"], 'batal' => false])->findAll();
        $antreanPanggil = 'A1';
        if (count($antrianTidakBatal) == 0) {
            $sisaAntrean = 0;
        } else {
            $sisaAntrean = count($antrianTidakBatal);
            foreach ($antrianTidakBatal as $a) {
                if ($a['dipanggil']) {
                    $antreanPanggil = $a['nomor_antrean'];
                    $sisaAntrean = (int)$sisaAntrean - 1;
                }
            }
        }
        return $this->response->setJSON([
            "response" => [
                'nomorantrean' => $data['nomor_antrean'],
                'angkaantrean' => $data['angka_antrean'],
                'namapoli' => $data['nama_poli'],
                'sisaantrean' => $sisaAntrean,
                'antreanpanggil' => $antreanPanggil,
                'keterangan' => $data['keterangan']
            ],
            "metadata" => [
                "message" => "Ok",
                "code" => 200
            ]
        ], false);
    }
    public function sisaAntrean($noKartu, $kdPoli, $tglPeriksa)
    {
        $antrianList = $this->antrianModel->where(['kode_poli' => $kdPoli, 'tanggal_periksa' => $tglPeriksa, 'batal' => false])->findAll();
        $antrian = $this->antrianModel->where(['kode_poli' => $kdPoli, 'tanggal_periksa' => $tglPeriksa, 'nomor_kartu' => $noKartu, 'batal' => false])->first();
        if (!$antrian) {
            return $this->response->setJSON([
                "response" => null,
                "metadata" => [
                    "message" => "Ok",
                    "code" => 200
                ]
            ], false);
        }
        // return $this->response->setJSON([
        //     'antrianList' => $antrianList,
        //     'antrian' => $antrian
        // ], false);
        $nomorAntrean = $antrian['nomor_antrean'];
        $namaPoli = $antrian['nama_poli'];
        $sisaAntrean = $antrian['angka_antrean'];
        $keterangan = $antrian['keterangan'];
        $antreanPanggil = '';
        foreach ($antrianList as $a) {
            if ($a['dipanggil']) {
                $antreanPanggil = $a['nomor_antrean'];
                $sisaAntrean = (int)$sisaAntrean - 1;
            }
        }
        return $this->response->setJSON([
            "response" => [
                'nomorantrean' => $nomorAntrean,
                'namapoli' => $namaPoli,
                'sisaantrean' => $sisaAntrean,
                'antreanpanggil' => $antreanPanggil,
                'keterangan' => $keterangan
            ],
            "metadata" => [
                "message" => "Ok",
                "code" => 200
            ]
        ], false);
    }
    public function pasienBaru()
    {
        $jwt = $this->request->header('x-token')->getValue();
        $username = $this->request->header('x-username')->getValue();
        $payload = json_encode(JWT::decode($jwt, new Key($this->secretKey, 'HS256')));
        if (!$payload) {
            return $this->response->setJSON([
                "metadata" => [
                    "message" => "Unauthorized",
                    "code" => 401
                ]
            ], false);
        }
        if ($username != 'user1') {
            return $this->response->setJSON([
                "metadata" => [
                    "message" => "Unauthorized",
                    "code" => 401
                ]
            ], false);
        }

        $bodyJson = $this->request->getBody();
        $body = json_decode($bodyJson, true);
        if (
            !isset($body['nomorkartu']) || !isset($body["nik"]) || !isset($body["nomorkk"]) ||
            !isset($body["nama"]) || !isset($body["jeniskelamin"]) || !isset($body["tanggallahir"]) ||
            !isset($body["alamat"]) || !isset($body["kodeprop"]) || !isset($body["kodeprop"]) ||
            !isset($body["kodedati2"]) || !isset($body["namadati2"]) || !isset($body["kodekec"]) ||
            !isset($body["namakec"]) || !isset($body["kodekel"]) || !isset($body["namakel"]) ||
            !isset($body["rw"]) || !isset($body["rt"])
        ) {
            return $this->response->setJSON([
                "metadata" => [
                    "message" => "Precondition Failed",
                    "code" => 412
                ]
            ], false);
        }
        $this->antrianPasienModel->insert([
            "nomor_kartu" => $body['nomorkartu'],
            "nik" => $body['nik'],
            "nomor_kk" => $body['nomorkk'],
            "nama" => $body['nama'],
            "jenis_kelamin" => $body['jeniskelamin'],
            "tanggal_lahir" => $body['tanggallahir'],
            "alamat" => $body['alamat'],
            "kodeprop" => $body['kodeprop'],
            "namaprop" => $body['namaprop'],
            "kodedati2" => $body['kodedati2'],
            "namadati2" => $body['namadati2'],
            "kodekec" => $body['kodekec'],
            "namakec" => $body['namakec'],
            "kodekel" => $body['kodekel'],
            "namakel" => $body['namakel'],
            "rw" => $body['rw'],
            "rt" => $body['rt']
        ]);
        return $this->response->setJSON([
            "metadata" => [
                "message" => "Ok",
                "code" => 200
            ]
        ], false);
    }
    public function batalAntrean()
    {
        $jwt = $this->request->header('x-token')->getValue();
        $username = $this->request->header('x-username')->getValue();
        $payload = json_encode(JWT::decode($jwt, new Key($this->secretKey, 'HS256')));
        if (!$payload) {
            return $this->response->setJSON([
                "metadata" => [
                    "message" => "Unauthorized",
                    "code" => 401
                ]
            ], false);
        }
        if ($username != 'user1') {
            return $this->response->setJSON([
                "metadata" => [
                    "message" => "Unauthorized",
                    "code" => 401
                ]
            ], false);
        }

        $bodyJson = $this->request->getBody();
        $body = json_decode($bodyJson, true);
        if (!isset($body['nomorkartu']) || !isset($body["kodepoli"]) || !isset($body["tanggalperiksa"])) {
            return $this->response->setJSON([
                "metadata" => [
                    "message" => "Precondition Failed",
                    "code" => 412
                ]
            ], false);
        }

        $cekAdaDatanyaNggk = $this->antrianModel->where([
            'nomor_kartu' => $body['nomorkartu'],
            'kode_poli' => $body['kodepoli'],
            'tanggal_periksa' => $body['tanggalperiksa'],
            'batal' => false
        ])->first();
        if (!$cekAdaDatanyaNggk) {
            return $this->response->setJSON([
                "metadata" => [
                    "message" => "Data tidak ditemukan",
                    "code" => 201
                ]
            ], false);
        }

        $dataInsert = [
            'nomor_kartu' => $body['nomorkartu'],
            'kode_poli' => $body['kodepoli'],
            'tanggal_periksa' => $body['tanggalperiksa'],
            'batal' => false
        ];
        if (isset($body['keterangan'])) $dataInsert['keterangan'] = $body['keterangan'];
        $dataUpdate = ['batal' => true];
        if (isset($body['keterangan'])) $dataUpdate['keterangan'] = $body['keterangan'];
        $this->antrianModel->where($dataInsert)->set($dataUpdate)->update();

        return $this->response->setJSON([
            "metadata" => [
                "message" => "Ok",
                "code" => 200
            ]
        ], false);
    }
}
