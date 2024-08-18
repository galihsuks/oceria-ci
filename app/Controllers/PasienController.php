<?php

namespace App\Controllers;

use App\Models\PasienModel;

class PasienController extends BaseController
{
    protected $pasienModel;
    public function __construct()
    {
        $this->pasienModel = new PasienModel();
    }
    public function allPasien()
    {
        $pasien = $this->pasienModel->findAll();
        $data = [
            'title' => 'List Pasien',
            'pasien' => $pasien,
        ];
        return view('pasien/list', $data);
    }
    public function pasien($id)
    {
        $pasien = $this->pasienModel->getPasien($id);
        if (!$pasien) return redirect()->to('/pasien');
        $pasien['rekamMedis'] = json_decode($pasien['rekamMedis'], true);
        $data = [
            'title' => 'Pasien ' . $id,
            'pasien' => $pasien,
        ];
        return view('pasien/detail', $data);
    }
}
