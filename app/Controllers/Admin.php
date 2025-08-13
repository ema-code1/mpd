<?php namespace App\Controllers;
//Controladores de dashboard 1 (protegidos)

use CodeIgniter\Controller;

class Admin extends Controller
{
    public function index()
    {
        $session = session();
        if (! $session->get('isLoggedIn') || $session->get('role') !== 'administrador') {
            return redirect()->to('/login')->with('error', 'Acceso denegado. Inicie sesión como administrador.');
        }

        $data['name'] = $session->get('name');
        echo view('templates/header');
        echo view('dashboards/admin_home', $data);
        echo view('templates/footer');
    }
}
