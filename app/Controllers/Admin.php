<?php namespace App\Controllers;
//Controladores de dashboard 1 (protegidos) prueba

use CodeIgniter\Controller;

class Admin extends Controller
{
    public function index() //el controlador verifica la sesión… ¡y no existe! 
    {
        $session = session();
        if (! $session->get('isLoggedIn') || $session->get('role') !== 'administrador') { //$session->get('isLoggedIn') → devuelve null o false, porque la sesión fue destruida.
            return redirect()->to('/login')->with('error', 'Acceso denegado. Inicie sesión como administrador.');
        }
        // ... mostrar dashboard

        $data['name'] = $session->get('name');
        echo view('templates/header');
        echo view('dashboards/admin_home', $data);
    }

    public function admincasa(){
        
    }
}
