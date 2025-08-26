<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Librería - Sistema</title>
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
          <span class="navbar-user">Hola, <?= esc(session()->get('name')) ?></span>
          <a class="btn" href="<?= site_url('logout') ?>">Cerrar sesión</a>
        <?php else: ?>
          <a class="btn" href="<?= site_url('login') ?>">Iniciar sesión</a>
          <a class="btn ms" href="<?= site_url('register') ?>">Registrarse</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>

  <div class="container">
