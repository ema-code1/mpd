<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />
  <link rel="stylesheet" href="<?= base_url('styles/admin_home.css') ?>">
</head>
<body>
  <!-- BOTON MENU -->
<i class="ti ti-menu-2 menu-btn" id="menu-btn"></i>
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
