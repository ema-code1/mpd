<?php namespace App\Controllers;
//Controlador de autenticación

use App\Models\UserModel;
use App\Models\AdminKeyModel; // Corregido: VendorKeyModel → AdminKeyModel
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

        // Si elige administrador, validar la clave adicional
        if ($role === 'administrador') {
            $admin_input = $this->request->getPost('admin_key');
            if (empty($admin_input)) {
                return redirect()->back()->withInput()->with('error', 'La clave de administrador es obligatoria.');
            }
            $adminModel = new AdminKeyModel();
            $currentKey = $adminModel->getCurrentKey();
            if (!$currentKey || !password_verify($admin_input, $currentKey['admin_key_hash'])) {
                return redirect()->back()->withInput()->with('error', 'Clave de administrador incorrecta.');
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
        $role = $this->request->getPost('role');
        $admin_input = $this->request->getPost('admin_key');

        if (empty($email) || empty($password) || empty($role)) {
            return redirect()->back()->withInput()->with('error', 'Complete todos los campos requeridos.');
        }

        $userModel = new UserModel();
        $user = $userModel->findByEmail($email);

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Usuario/Email no encontrado.');
        }

        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Contraseña incorrecta.');
        }

        if ($user['role'] !== $role) {
            return redirect()->back()->with('error', 'El rol seleccionado no coincide.');
        }

        // Validar clave de administrador si elige ese rol
        if ($role === 'administrador') {
            if (empty($admin_input)) {
                return redirect()->back()->withInput()->with('error', 'Debe ingresar la clave de administrador.');
            }
            $adminModel = new AdminKeyModel();
            $currentKey = $adminModel->getCurrentKey();
            if (!$currentKey || !password_verify($admin_input, $currentKey['admin_key_hash'])) {
                return redirect()->back()->withInput()->with('error', 'Clave de administrador incorrecta.');
            }
        }

        // Todo OK - REDIRECCIÓN ÚNICA A /panel
        $session->set([
            'isLoggedIn' => true,
            'userId'     => $user['id'],
            'name'       => $user['name'],
            'email'      => $user['email'],
            'role'       => $user['role']
        ]);

        return redirect()->to('/panel');
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/login')->with('success','Cierre de sesión correcto.');
    }
}