<?php

namespace App\Controllers;

use App\Models\PasienModel;

class MainController extends BaseController
{
    public function home()
    {
        $data = [
            'title' => 'Oceria | Drg. Sri Umiati',
        ];
        return view('home', $data);
    }
}
