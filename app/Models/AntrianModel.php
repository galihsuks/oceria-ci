<?php

namespace App\Models;

use CodeIgniter\Model;

class AntrianModel extends Model
{
    protected $table = 'antrian';
    protected $allowedFields = [
        "id",
        "nomor_kartu",
        "nik",
        "kode_poli",
        "tanggal_periksa",
        "keluhan",

        "kode_dokter",
        "nama_dokter",
        "jam_praktek",
        "norm",
        "nohp",

        "nomor_antrean",
        "angka_antrean",
        "nama_poli",
        "keterangan",
        "dipanggil",
        'batal'
        // "sisa_antrean",
        // "antrean_panggil",
        // "keterangan",
    ];

    public function getStatus($kdPoli, $tglPeriksa)
    {
        $antrian = $this->where(['kode_poli' => $kdPoli, 'tanggal_periksa' => $tglPeriksa, 'batal' => false])->findAll();
        if (count($antrian) == 0) {
            return null;
        }
        $namaPoli = $antrian[0]['nama_poli'];
        $kodeDokter = $antrian[0]['kode_dokter'];
        $namaDokter = $antrian[0]['nama_dokter'];
        $jamPraktek = $antrian[0]['jam_praktek'];
        $totalAntrean = count($antrian);
        $sisaAntrean = count($antrian);
        $antreanPanggil = '';
        $keterangan = '';
        foreach ($antrian as $a) {
            if ($a['dipanggil']) {
                $antreanPanggil = $a['nomor_antrean'];
                $keterangan = $a['keterangan'];
                $sisaAntrean = (int)$sisaAntrean - 1;
            }
        }
        return [
            'namapoli' => $namaPoli,
            'totalantrean' => $totalAntrean,
            'sisaantrean' => $sisaAntrean,
            'antreanpanggil' => $antreanPanggil,
            'keterangan' => $keterangan,
            'kodedokter' => $kodeDokter,
            'namadokter' => $namaDokter,
            'jampraktek' => $jamPraktek
        ];
    }
}
