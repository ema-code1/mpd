<?php namespace App\Controllers;
//Controlador de autenticación

use App\Models\UserModel;
use App\Models\VendorKeyModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    protected $helpers = ['form','url','session'];

    public function register()
    {
        echo view('templates/header');
        echo view('auth/register');
        echo view('templates/footer');
    }

    public function registerPost()
    {
        $session = session();
        $rules = [
            'name'     => 'required|min_length[2]|max_length[150]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'role'     => 'required'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $role = $this->request->getPost('role');

        // Si elige vendedor, validar la clave secundaria
        if ($role === 'vendedor') {
            $vendor_input = $this->request->getPost('vendor_key');
            if (empty($vendor_input)) {
                return redirect()->back()->withInput()->with('error', 'La clave de vendedor es obligatoria para el rol vendedor.');
            }
            $vendorModel = new VendorKeyModel();
            $currentKey = $vendorModel->getCurrentKey();
            if (!$currentKey || !password_verify($vendor_input, $currentKey['vendor_key_hash'])) {
                return redirect()->back()->withInput()->with('error', 'Clave de vendedor incorrecta.');
            }
        }

        $userModel = new UserModel();
        $userModel->insert([
           'name' => $name,
           'email' => $email,
           'password' => password_hash($password, PASSWORD_DEFAULT),
           'role' => $role
        ]);

        return redirect()->to('/login')->with('success', 'Registro correcto. Puede iniciar sesión.');
    }

    public function login()
    {
        echo view('templates/header');
        echo view('auth/login');
        echo view('templates/footer');
    }

    public function loginPost()
    {
        $session = session();
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $role = $this->request->getPost('role'); // administrador / vendedor / comprador
        $vendor_input = $this->request->getPost('vendor_key');

        // Validaciones básicas
        if (empty($email) || empty($password) || empty($role)) {
            return redirect()->back()->withInput()->with('error', 'Complete todos los campos requeridos.');
        }

        $userModel = new UserModel();
        $user = $userModel->findByEmail($email);

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Usuario/Email no encontrado.');
        }

        // Validar contraseña principal
        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Contraseña incorrecta.');
        }

        // Validar que el rol elegido coincida con el rol del usuario
        if ($user['role'] !== $role) {
            return redirect()->back()->with('error', 'El rol seleccionado no coincide con el rol del usuario.');
        }

        // Si el rol es vendedor: validar clave secundaria
        if ($role === 'vendedor') {
            if (empty($vendor_input)) {
                return redirect()->back()->withInput()->with('error', 'Debe ingresar la clave de vendedor.');
            }
            $vendorModel = new VendorKeyModel();
            $currentKey = $vendorModel->getCurrentKey();
            if (!$currentKey || !password_verify($vendor_input, $currentKey['vendor_key_hash'])) {
                return redirect()->back()->withInput()->with('error', 'Clave de vendedor incorrecta.');
            }
        }

        // Todo OK: setear sesión
        $session->set([
            'isLoggedIn' => true,
            'userId'     => $user['id'],
            'name'       => $user['name'],
            'email'      => $user['email'],
            'role'       => $user['role']
        ]);

        // Redirigir según rol
        if ($user['role'] === 'administrador') {
            return redirect()->to('/admin');
        } elseif ($user['role'] === 'vendedor') {
            return redirect()->to('/vendor');
        } else {
            return redirect()->to('/home');
        }
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/login')->with('success','Cierre de sesión correcto.');
    }
}
