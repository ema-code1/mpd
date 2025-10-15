<?php
namespace App\Controllers;

class StockController extends BaseController
{
    public function index()
    {
        // Podés traer datos de la DB si querés
        $libroModel = new \App\Models\LibroModel();
        $data['libros'] = $libroModel->findAll();

        echo view('templates/header');
        return view('dashboards/stock', $data); // ↪ stock.php dentro de dashboards/
    }
}

?>