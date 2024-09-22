<?php

namespace App\Models;

use CodeIgniter\Model;

class AntrianPasienModel extends Model
{
    protected $table = 'antrian_pasien';
    protected $allowedFields = [
        "id",
        "nomor_kartu",
        "nik",
        "nomor_kk",
        "nama",
        "jenis_kelamin",
        "tanggal_lahir",
        "alamat",
        "kodeprop",
        "namaprop",
        "kodedati2",
        "namadati2",
        "kodekec",
        "namakec",
        "kodekel",
        "namakel",
        "rw",
        "rt"
    ];

    public function getAntrianPasien($id = false)
    {
        if ($id) {
            return $this->where(['id' => $id])->first();
        }
        return $this->findAll();
    }
}
