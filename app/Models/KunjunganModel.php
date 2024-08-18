<?php

namespace App\Models;

use CodeIgniter\Model;

class KunjunganModel extends Model
{
    protected $table = 'kunjungan';
    protected $allowedFields = [
        'tgl_praktek',
        'ID_pasien',
        'BPJS',
        'Exo_Perm',
        'Exo_Susu',
        'LC',
        'Fuji',
        'RawatSyaraf',
        'Scalling',
        'Antibiotik',
        'Analgetik',
        'AntiRadang',
        'Lain_Lain',
        'tensi',
        'berat',
        'tinggi',
        'suhu',
    ];
}
