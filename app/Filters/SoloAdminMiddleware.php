<?php

namespace App\Filters;

use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class SoloAdminMiddleware implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null): ?ResponseInterface
    {
        $session = session();

        // 1. Si no está logueado → a login
        if (! $session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión para acceder.');
        }

        // 2. Si está logueado pero no es administrador → al panel (vista de usuario)
        if ($session->get('role') !== 'administrador') {
            return redirect()->to('/panel')->with('error', 'Acceso denegado. Requiere permisos de administrador.');
        }

        // 3. Si es admin → continúa
        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No hacemos nada después
    }
}