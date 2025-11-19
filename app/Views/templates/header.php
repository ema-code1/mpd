<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
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
  
  
  <!-- DEBUG: Mostrar información de sesión -->
  <?php 
    $session = session();
    echo "<!-- Sesión: ";
    print_r($session->get());
    echo " -->";
    echo "<!-- Current URL: " . current_url() . " -->";
    echo "<!-- Base URL: " . base_url() . " -->";
  ?>
  
  <?php if(session()->get('isLoggedIn')): ?>
  <!-- USUARIO LOGUEADO - Menú desplegable -->
  <div class="user-menu">
    <button class="user-btn" id="userMenuBtn">
      <i class="ti ti-user"></i>
      <span><?= esc(session()->get('name')) ?></span>
    </button>
    <div class="dropdown-menu" id="dropdownMenu">
      <div class="dropdown-header">Sesión activa</div>
      <a href="<?= base_url('perfil') ?>" class="dropdown-item">
        <i class="ti ti-user-circle"></i> Cuenta
      </a>
      <button onclick="window.location.href='<?= base_url('checkout') ?>'" class="dropdown-item">
          <i class="fas fa-receipt"></i> Pedidos
      </button>

      <div class="dropdown-divider"></div>

      <div class="dropdown-divider"></div>
      <a href="<?= site_url('logout') ?>" class="dropdown-item" id="logoutBtn">
        <i class="ti ti-logout"></i> Cerrar sesión
      </a>
    </div>
  </div>

  <!-- ✅ Botón de Admin: Solo si el rol es 'administrador' -->
  <?php if (session()->get('role') === 'administrador'): ?>
    <a href="<?= site_url('panel') ?>" class="admin-panel-btn">
      <i class="ti ti-dashboard"></i>
      <span>Admin.</span>
    </a>
    
<?php endif ?>

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

    <a href="<?= site_url('cart') ?>" class="cartbtn">
      <i class="cart-icon fas fa-shopping-cart"></i>
    </a>

</div>
    </div>
  </nav>

<!-- Popup genérico reutilizable -->
<div id="confirmPopupOverlay" class="popup-overlay">
    <div class="popup warning"> <!-- Podés cambiar la clase 'warning' por 'success' o 'error' -->
        <div class="popup-icon">⚠️</div>
        <h3 id="confirmPopupTitle" class="popup-title">¿Cerrar sesión?</h3> <!-- Cambiá el título -->
        <p id="confirmPopupMessage" class="popup-message">
            Estás por salir de tu cuenta. ¿Seguro que quieres hacerlo?
        </p> <!-- Cambiá el mensaje -->
        <div class="popup-buttons">
            <button id="confirmPopupConfirm" class="popup-btn confirm">Sí, cerrar sesión</button> <!-- Botón que ejecuta la acción -->
            <button id="confirmPopupCancel" class="popup-btn cancel">Cancelar</button>
        </div>
    </div>
</div>


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

// =============================
// CONFIGURACIÓN DEL POPUP
// =============================

// El botón original (con el href de base_url)
const logoutBtn = document.getElementById('logoutBtn');

// Elementos del popup
const popupOverlay = document.getElementById('confirmPopupOverlay');
const popupConfirm = document.getElementById('confirmPopupConfirm');
const popupCancel = document.getElementById('confirmPopupCancel');

// =============================
// FUNCIONALIDAD
// =============================

// Abrir popup cuando se clickea el botón original
logoutBtn.addEventListener('click', function (e) {
    e.preventDefault(); // Evita que se ejecute la ruta automáticamente
    popupOverlay.classList.add('active'); // Muestra el popup
});

// Confirmar acción: usar el href original del botón
popupConfirm.addEventListener('click', function () {
    // Obtiene la ruta original del botón
    const logoutUrl = logoutBtn.getAttribute('href');

    // Redirige a esa ruta (usa la función original del botón)
    window.location.href = logoutUrl;
});

// Cancelar: cierra el popup
popupCancel.addEventListener('click', function () {
    popupOverlay.classList.remove('active');
});

// Cerrar si clickea fuera
popupOverlay.addEventListener('click', function (e) {
    if (e.target === popupOverlay) popupOverlay.classList.remove('active');
});

  </script>