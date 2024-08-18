<?php

namespace App\Models;

use CodeIgniter\Model;

class PelayananModel extends Model
{
    protected $table = 'pelayanan';
    protected $allowedFields = [
        'id',
        'noKunjungan',
        'noKartu',
        'tglDaftar',
        'kdPoli',
        'keluhan',
        'kdSadar',
        'sistole',
        'diastole',
        'beratBadan',
        'tinggiBadan',
        'respRate',
        'heartRate',
        'lingkarPerut',
        'kdStatusPulang',
        'tglPulang',
        'kdDokter',
        'kdDiag1',
        'kdDiag2',
        'kdDiag3',
        'kdPoliRujukInternal',
        'rujukLanjut',
        'kdTacc',
        'alasanTacc',
        'anamnesa',
        'alergiMakan',
        'alergiUdara',
        'alergiObat',
        'kdPrognosa',
        'terapiObat',
        'terapiNonObat',
        'bmhp',
        'suhu',
        'noRM',
        'bpjs',
    ];
}
