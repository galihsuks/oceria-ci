<?php

namespace App\Models;

use CodeIgniter\Model;

class PendaftaranModel extends Model
{
    protected $table = 'pendaftaran';
    protected $allowedFields = [
        'id',
        'noUrut',
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
        'noRM',
        'status',
    ];

    public function getPendaftaran($tglDaftar, $jenis = 'all')
    {
        switch ($jenis) {
            case 'all':
                return $this->where(['tglDaftar' => $tglDaftar])->findAll();
                break;
            case 'bpjs':
                return $this->where(['tglDaftar' => $tglDaftar, 'bpjs' => true])->findAll();
                break;
            case 'non':
                return $this->where(['tglDaftar' => $tglDaftar, 'bpjs' => false])->findAll();
                break;
        }
    }
}
