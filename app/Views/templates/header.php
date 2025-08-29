<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Librería - Sistema</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />
  <link rel="stylesheet" href="<?= base_url('styles/header.css') ?>">
</head>
<body>
  <nav class="navbar">
    <div class="navbar-container">
      <a class="navbar-brand" href="<?= base_url() ?>">
        <div class="fav-container">
          <img src="<?= base_url('imgs/logo_mpd.png') ?>" alt="Libro" class="fav-img">
          <h1 class="fav-text">
            MOVIMIENTO<br>
            DE LA PALABRA<br>
            DE DIOS
          </h1>
        </div>
      </a>
      <div class="navbar-links">
        <?php if(session()->get('isLoggedIn')): ?>
          <!-- USUARIO LOGUEADO - Menú desplegable -->
          <div class="user-menu">
            <button class="user-btn" id="userMenuBtn">
              <i class="ti ti-user"></i>
              <span><?= esc(session()->get('name')) ?></span>
            </button>
            <div class="dropdown-menu" id="dropdownMenu">
              <div class="dropdown-header">Sesión activa</div>
              <a href="#" class="dropdown-item">
                <i class="ti ti-user-circle"></i> Cuenta
              </a>
              <div class="dropdown-divider"></div>
              <a href="<?= site_url('logout') ?>" class="dropdown-item">
                <i class="ti ti-logout"></i> Cerrar sesión
              </a>
            </div>
          </div>
          <div class="overlay" id="overlay"></div>
        <?php else: ?>
          <!-- USUARIO NO LOGUEADO - Menú desplegable -->
          <div class="user-menu">
            <button class="user-btn" id="userMenuBtn">
              <i class="ti ti-user"></i>
              <span>Usuario</span>
            </button>
            <div class="dropdown-menu" id="dropdownMenu">
              <a href="<?= site_url('login') ?>" class="dropdown-item">
                <i class="ti ti-login"></i> Iniciar sesión
              </a>
              <div class="dropdown-divider"></div>
              <a href="<?= site_url('register') ?>" class="dropdown-item">
                <i class="ti ti-user-plus"></i> Registrarse
              </a>
            </div>
          </div>
          <div class="overlay" id="overlay"></div>
        <?php endif; ?>
      </div>
    </div>
  </nav>

  <div class="container">

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const userMenuBtn = document.getElementById('userMenuBtn');
      const dropdownMenu = document.getElementById('dropdownMenu');
      const overlay = document.getElementById('overlay');
      
      if (userMenuBtn && dropdownMenu) {
        // Abrir menú
        userMenuBtn.addEventListener('click', function(e) {
          e.stopPropagation();
          dropdownMenu.classList.toggle('active');
          if (overlay) overlay.classList.toggle('active');
        });
        
        // Cerrar menú al hacer clic fuera
        document.addEventListener('click', function(e) {
          if (!userMenuBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
            dropdownMenu.classList.remove('active');
            if (overlay) overlay.classList.remove('active');
          }
        });
        
        // Cerrar menú al hacer clic en overlay
        if (overlay) {
          overlay.addEventListener('click', function() {
            dropdownMenu.classList.remove('active');
            overlay.classList.remove('active');
          });
        }
        
        // Cerrar menú con tecla Escape
        document.addEventListener('keydown', function(e) {
          if (e.key === 'Escape') {
            dropdownMenu.classList.remove('active');
            if (overlay) overlay.classList.remove('active');
          }
        });
        
        // Prevenir que el clic dentro del menú lo cierre
        dropdownMenu.addEventListener('click', function(e) {
          e.stopPropagation();
        });
      }
    });
  </script>