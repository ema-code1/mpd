<?php namespace App\Controllers;
//(área pública / comprador)
use CodeIgniter\Controller;
use App\Models\LibroModel; // Añadimos el modelo de libros

class Home extends Controller
{
    public function index()
    {
        // Creamos instancia del modelo de libros
        $libroModel = new LibroModel();
        
        // Obtenemos todos los libros de la base de datos
        $data['libros'] = $libroModel->findAll();
        
        // Pasamos los datos a la vista
        echo view('templates/header');
        echo view('home/index', $data); // Aquí pasamos los libros a la vista
        echo view('templates/footer');
    }
}