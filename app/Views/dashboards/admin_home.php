<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <style>
    :root { --header-h: 140px; } /* ajustá si tu header mide más/menos */
header { min-height: var(--header-h); }

* {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: Arial, sans-serif;
    }

body {
      background: #eaeaea;
      transition: margin-left 0.3s ease;
      width: 100vw;
      background: linear-gradient(to top, #CBCBCB, #CBCBCB, #FFFFFF);
    }

    body {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    grid-template-rows: repeat(5, 20vh);
    }

header {
    grid-column: span 5 / span 5;
}

.sidebar {
    grid-row: span 4 / span 4;
    grid-row-start: 2;
}

.content {
    grid-column: span 5 / span 5;
    grid-row: span 3 / span 3;
    grid-column-start: 1;
    grid-row-start: 2;
}

.menu-btn {
    grid-column-start: 1;
    grid-row-start: 2;
}


    /* --- HEADER --- */
    header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 10px 20px;
      position: static;
      top: 0;
      width: 100%;
      max-height: 100px;
    }

    header .logo img {
      height: 90px;
      cursor: pointer;
    }

    header h2 {
      font-weight: normal;
    }

    header .user {
      display: flex;
      align-items: center;
    }

    header .user a {
      text-decoration: none;
      color: rgb(19, 19, 19);
      font-weight: 500;
    }

    /* --- MENU BUTTON --- */
.menu-btn {
  position: fixed;
   justify-self: start;        /* o start si lo querés más a la izq */
   font-size: 24px;
   left: -10px;
   cursor: pointer;
   background: #f28c00;
   color: white;
   padding: 6px 12px;
   border-radius: 10px;
   border: none;
   z-index: 1;
   width: 60px;
   height: 90px;
   top: var(--header-h);
   padding: 6px 6px 6px 12px;
}

    /* --- SIDEBAR --- */
   .sidebar {
   position: fixed;
   top: var(--header-h);
   left: -270px;        /* empieza justo debajo del header */
   width: 260px;
   height: auto;
   background: #f28c00;
   border-radius: 10px;
   padding: 20px 20px 20px 40px;
   transition: 0.6s;
   z-index: 2;
   box-shadow: 2px 0 5px rgba(0,0,0,0.2);
}

    .sidebar.active {
      left: -10px;
      transition: 0.6s ease;
    }

    .sidebar .close-btn {
      font-size: 24px;
      cursor: pointer;
      color: white;
      margin-bottom: 20px;
      display: block;
    }

    .sidebar h3 {
      color: white;
      font-size: 20px;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .sidebar a {
      display: block;
      margin: 15px 0;
      text-decoration: none;
      color: white;
      font-size: 18px;
      transition: 0.3s;
      gap: 8px;
      padding-left: 7px;
    }

    .sidebar a:hover {
      padding: 5px;
      background-color: #ffaf3e;
      border-radius: 5px;
    }

    /* --- CONTENIDO PRINCIPAL --- */
    .content {
      position: fixed;
      top: var(--header-h);
      margin-top: 0;
      margin-left: 51px;
      transition: 0.6s ease;
      padding: 10px;
      padding-top: 0;
      width: 97vw;
    }

    .content.active {
      margin-left: 250px; /* se corre cuando aparece el menú */
      width: calc(100% - 252px);
    }

    .card {
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      width: 100%;
      margin: auto;
      height: 80vh;
    }

    .card h3 {
      margin-bottom: 15px;
    }

    /* --- RESPONSIVE --- */
    @media (max-width: 768px) {
      .content.active {
        margin-left: 0; /* en mobile que tape */
      }
      .sidebar {
        width: 200px;
      }
    }
  </style>
</head>
<body>
  <!-- HEADER -->
  <header>
    <div class="logo">
      <a href="#"><img src="public/imgs/LogoMPD.png" alt="Logo MPD"></a>
    </div>
    <h2>Dashboard</h2>
    <div class="user">
      <span>👤</span>
      <a href="#"><?= esc($name) ?></a>
    </div>
  </header>

  <!-- BOTON MENU -->
  <button class="menu-btn" id="menu-btn">☰</button>

  <!-- SIDEBAR -->
  <div class="sidebar" id="sidebar">
    <span class="close-btn" id="close-btn">X</span>
    <h3>Menu</h3>
    <a href="#">Dashboard</a>
    <a href="#">Stock</a>
    <a href="#">Cargar nuevo libro</a>
    <a href="#">Actividad de compras</a>
    <a href="#">Movimientos</a>
  </div>

  <!-- CONTENIDO -->
  <div class="content" id="content">
    <div class="card">
      <h3>Ventas</h3>
      <p>Aquí iría el contenido principal de las ventas...</p>
    </div>
  </div>

  <!-- SCRIPT -->
  <script>
    const menuBtn = document.getElementById("menu-btn");
    const sidebar = document.getElementById("sidebar");
    const closeBtn = document.getElementById("close-btn");
    const content = document.getElementById("content");

    menuBtn.addEventListener("click", () => {
      sidebar.classList.add("active");
      content.classList.add("active");
    });

    closeBtn.addEventListener("click", () => {
      sidebar.classList.remove("active");
      content.classList.remove("active");
    });

    document.addEventListener("click", (e) => {
      if (!sidebar.contains(e.target) && !menuBtn.contains(e.target)) {
        sidebar.classList.remove("active");
        content.classList.remove("active");
      }
    });


    const headerEl = document.querySelector('header');
function syncHeaderH(){ 
  document.documentElement.style.setProperty('--header-h', headerEl.offsetHeight + 'px');
}
syncHeaderH();
window.addEventListener('resize', syncHeaderH);

  </script>

</body>
</html>
