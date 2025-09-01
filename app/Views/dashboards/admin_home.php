<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Administración</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />
  <link rel="stylesheet" href="<?= base_url('styles/admin_home.css') ?>">
</head>
<body>
  <!-- El header es un template que se incluye automáticamente -->
  
  <!-- BOTON MENU -->
  <i class="ti ti-menu-2 menu-btn" id="menu-btn"></i>
  
  <!-- SIDEBAR -->
  <div class="sidebar" id="sidebar">
    <button class="close-btn" id="close-btn">
      <i class="ti ti-x"></i>
    </button>
    <h3><i class="ti ti-dashboard"></i> Dashboard</h3>
    <a href="#"><i class="ti ti-chart-bar"></i> Dashboard</a>
    <a href="#"><i class="ti ti-books"></i> Stock</a>
    <a href="<?= site_url('upload_book') ?>"><i class="ti ti-book-upload"></i> Cargar nuevo libro</a>
    <a href="#"><i class="ti ti-shopping-cart"></i> Actividad de compras</a>
    <a href="#"><i class="ti ti-transfer"></i> Movimientos</a>
  </div>

  <!-- CONTENIDO -->
  <div class="content" id="content">
    <div class="card fade-in">
      <h3>Ventas</h3>
      <p>Resumen de las ventas recientes y estadísticas.</p>
      
      <div class="dashboard-grid">
        <div class="dashboard-card">
          <h4><i class="ti ti-trending-up"></i> Ventas Hoy</h4>
          <p><strong>$2,450</strong> - 15 transacciones</p>
        </div>
        
        <div class="dashboard-card">
          <h4><i class="ti ti-calendar-stats"></i> Ventas Semanales</h4>
          <p><strong>$12,800</strong> - 84 transacciones</p>
        </div>
        
        <div class="dashboard-card">
          <h4><i class="ti ti-chart-pie"></i> Productos Populares</h4>
          <p>Los 5 libros más vendidos esta semana</p>
        </div>
        
        <div class="dashboard-card">
          <h4><i class="ti ti-discount"></i> Descuentos Aplicados</h4>
          <p><strong>$320</strong> en descuentos esta semana</p>
        </div>
      </div>
      
      <!-- Contenido adicional para demostrar el scroll -->
      <div style="margin-top: 2.5rem;">
        <h4>Historial de Ventas</h4>
        <div style="height: 800px; background: linear-gradient(to bottom, #f9f9f9, #eee); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #777;">
          Área de historial de ventas (contenido largo para demostrar el scroll)
        </div>
      </div>
    </div>
  </div>

  <!-- SCRIPT -->
  <script>
    const menuBtn = document.getElementById("menu-btn");
    const sidebar = document.getElementById("sidebar");
    const closeBtn = document.getElementById("close-btn");
    const content = document.getElementById("content");

    // Abrir sidebar
    menuBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      sidebar.classList.toggle("active");
      content.classList.toggle("active");
    });

    // Cerrar sidebar
    closeBtn.addEventListener("click", () => {
      sidebar.classList.remove("active");
      content.classList.remove("active");
    });

    // Cerrar al hacer clic fuera
    document.addEventListener("click", (e) => {
      if (!sidebar.contains(e.target) && e.target !== menuBtn && !menuBtn.contains(e.target)) {
        sidebar.classList.remove("active");
        content.classList.remove("active");
      }
    });

    // Prevenir que los clicks dentro del sidebar cierren el sidebar
    sidebar.addEventListener("click", (e) => {
      e.stopPropagation();
    });

    // Ajustar altura del header
    const headerEl = document.querySelector('header');
    function syncHeaderH() { 
      if (headerEl) {
        const headerHeight = headerEl.offsetHeight;
        document.documentElement.style.setProperty('--header-h', headerHeight + 'px');
        
        // Ajustar posiciones dinámicamente
        menuBtn.style.top = `calc(${headerHeight}px + 1rem)`;
        sidebar.style.top = `calc(${headerHeight}px + 1rem)`;
        sidebar.style.height = `calc(100vh - ${headerHeight}px - 2rem)`;
        content.style.marginTop = `${headerHeight}px`;
        document.querySelector('.card').style.minHeight = `calc(100vh - ${headerHeight}px - 2rem)`;
      } else {
        // Valores por defecto
        document.documentElement.style.setProperty('--header-h', '140px');
      }
    }
    
    // Sincronizar inicialmente y en resize
    syncHeaderH();
    window.addEventListener('resize', syncHeaderH);
    window.addEventListener('load', syncHeaderH);

    // Cerrar sidebar con tecla Escape
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        sidebar.classList.remove("active");
        content.classList.remove("active");
      }
    });
  </script>
</body>
</html>