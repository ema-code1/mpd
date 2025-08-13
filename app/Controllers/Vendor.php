<?php namespace App\Controllers;
//Controladores de dashboard 2 (protegidos)

use CodeIgniter\Controller;

class Vendor extends Controller
{
    public function index()
    {
        $session = session();
        if (! $session->get('isLoggedIn') || $session->get('role') !== 'vendedor') {
            return redirect()->to('/login')->with('error', 'Acceso denegado. Inicie sesión como vendedor.');
        }

        $data['name'] = $session->get('name');
        echo view('templates/header');
        echo view('dashboards/vendor_home', $data);
        echo view('templates/footer');
    }
}
