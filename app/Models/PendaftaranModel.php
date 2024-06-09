<?php

namespace App\Models;

use CodeIgniter\Model;

class PendaftaranModel extends Model
{
    protected $table = 'pendaftaran';
    protected $allowedFields = [
        'id',
        'kdProviderPeserta',
        'tglDaftar',
        'noKartu',
        'nik',
        'nama',
        'kdPoli',
        'keluhan',
        'kunjSakit',
        'sistole',
        'diastole',
        'beratBadan',
        'tinggiBadan',
        'respRate',
        'lingkarPerut',
        'heartRate',
        'rujukBalik',
        'kdTkp',
        'bpjs',
    ];

    public function getPendaftaranTgl($tglDaftar)
    {
        return $this->where(['tglDaftar' => $tglDaftar])->findAll();
    }
}
