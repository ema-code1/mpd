<?php namespace App\Controllers;
//(área pública / comprador)

use CodeIgniter\Controller;

class Home extends Controller
{
    public function index()
    {
        echo view('templates/header');
        echo view('home/index');
        echo view('templates/footer');
    }
}