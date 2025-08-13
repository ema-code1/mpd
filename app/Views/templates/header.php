<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Librería - Sistema</title>
  <!-- Puedes usar bootstrap CDN para facilitar -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand bg-dark navbar-dark mb-4">
  <div class="container">
    <a class="navbar-brand" href="<?= base_url() ?>">Librería</a>
    <div>
      <?php if(session()->get('isLoggedIn')): ?>
        <span class="text-white me-3">Hola, <?= esc(session()->get('name')) ?></span>
        <a class="btn btn-sm btn-outline-light" href="<?= site_url('logout') ?>">Cerrar sesión</a>
      <?php else: ?>
        <a class="btn btn-sm btn-outline-light" href="<?= site_url('login') ?>">Iniciar sesión</a>
        <a class="btn btn-sm btn-outline-light ms-2" href="<?= site_url('register') ?>">Registrarse</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
<div class="container">
