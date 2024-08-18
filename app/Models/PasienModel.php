<?php

namespace App\Models;

use CodeIgniter\Model;

class PasienModel extends Model
{
    protected $table = 'pasien';
    protected $allowedFields = [
        'id',
        'nama',
        'tglLahir',
        'alamat',
        'kelamin',
        'noHp',
        'rekamMedis',
        'golDarah',
        'nik',
        'noBpjs',
        'kdProviderPst',
    ];

    public function getPasien($id)
    {
        return $this->where(['id' => $id])->first();
    }
}
