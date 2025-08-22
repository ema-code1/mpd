<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Librería - Sistema</title>

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: Arial, sans-serif;
    }

    body {
      background: linear-gradient(to top, #CBCBCB, #FFFFFF);
    }

    /* --- HEADER estilo referencia --- */
    header {
      background: #fff;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 10px 20px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .logo {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .logo img {
      height: 40px;
    }

    .title {
      flex: 1;
      text-align: center;
      font-weight: bold;
      font-size: 18px;
      color: #333;
    }

    /* --- AREA USUARIO / BOTONES --- */
    .user-area {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .username {
      font-size: 14px;
      color: #444;
    }

    .btn {
      padding: 6px 12px;
      border-radius: 6px;
      border: none;
      background: #f28c00;
      color: white;
      font-size: 14px;
      cursor: pointer;
      transition: 0.3s;
    }

    .btn:hover {
      background: #d97b00;
    }

    /* --- CONTENEDOR PRINCIPAL --- */
    .container {
      max-width: 1200px;
      margin: 20px auto;
      padding: 0 20px;
    }
  </style>
</head>
<body>

<header>
  <div class="logo">
    <img src="<?= base_url('uploads/logo.png') ?>" alt="Logo">
    <span>MOVIMIENTO DE LA PALABRA DE DIOS</span>
  </div>
  
  <div class="title">Dashboard</div>
  
  <div class="user-area">
    <?php if(session()->get('isLoggedIn')): ?>
      <span class="username">Hola, <?= esc(session()->get('name')) ?></span>
      <a class="btn" href="<?= site_url('logout') ?>">Cerrar sesión</a>
    <?php else: ?>
      <a class="btn" href="<?= site_url('login') ?>">Iniciar sesión</a>
      <a class="btn" href="<?= site_url('register') ?>">Registrarse</a>
    <?php endif; ?>
  </div>
</header>

<div class="container">
