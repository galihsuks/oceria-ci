<?php

namespace App\Controllers;

use App\Models\PendaftaranModel;

class PendaftaranController extends BaseController
{
    protected $pendaftaranModel;
    public function __construct()
    {
        $this->pendaftaranModel = new PendaftaranModel();
    }
    public function addPendaftaran()
    {
        $poli = [
            [
                "kdPoli" => "001",
                "nmPoli" => "Umum",
                "poliSakit" => true
            ],
            [
                "kdPoli" => "003",
                "nmPoli" => "K I A",
                "poliSakit" => true
            ]
        ];
        $d = strtotime("+7 Hours");
        $tanggal = date("Y-m-d", $d);
        $data = [
            'title' => 'Add Pendaftaran',
            'section' => 'pendaftaran',
            'poli' => $poli,
            'tanggal' => $tanggal,
            'poliJson' => json_encode($poli),
        ];
        return view('pendaftaran/add', $data);
    }
    public function actionAddPendaftaran()
    {
        $datanya = [
            "kdProviderPeserta" => $this->request->getVar('kdProviderPeserta'),
            "tglDaftar" => $this->request->getVar('tglDaftar'),
            "noKartu" => $this->request->getVar('noKartu'),
            "kdPoli" => $this->request->getVar('kdPoli'),
            "keluhan" => $this->request->getVar('keluhan'),
            "kunjSakit" => $this->request->getVar('kunjSakit'),
            "sistole" => $this->request->getVar('sistole'),
            "diastole" => $this->request->getVar('diastole'),
            "beratBadan" => $this->request->getVar('beratBadan'),
            "tinggiBadan" => $this->request->getVar('tinggiBadan'),
            "respRate" => $this->request->getVar('respRate'),
            "lingkarPerut" => $this->request->getVar('lingkarPerut'),
            "heartRate" => $this->request->getVar('heartRate'),
            "rujukBalik" => '0', //Belum tau apa itu rujuk balik
            "kdTkp" => $this->request->getVar('kdTkp'),

            "id" => time(),
            "nama" => $this->request->getVar('nama'),
            "nik" => $this->request->getVar('nik'),
            "bpjs" => $this->request->getVar('bpjs'),
        ];
        dd($datanya);
        // $this->pendaftaranModel->insert();
    }
}
