<link rel="stylesheet" href="<?= base_url('styles/checkout.css') ?>">

<?php
    // Para no repetir lógica y poder usar el estado en clases CSS
    $estadoActual = $ventaMaestro['estado'] ?? 'pendiente';
?>

<div class="content fade-in">

    <div class="card <?= $estadoActual === 'cancelado' ? 'card-cancelado' : '' ?>">

        <h1>Resumen de tu compra</h1>
        <p class="subtitle">Gracias por tu compra. Acá podés ver todos los detalles del pedido.</p>

        <!-- ESTADO DEL PEDIDO -->
        <div class="estado-container">
            <h2 class="estado-titulo">Estado de la compra</h2>

            <div class="timeline">
                <!-- Paso 1: Pedido realizado -->
                <div class="timeline-step <?= $estadoPedido['paso1']['completado'] ? 'active' : '' ?>">
                    <div class="circle"></div>
                    <span><?= $estadoPedido['paso1']['titulo'] ?></span>
                </div>

                <!-- Paso 2: En revisión -->
                <div class="timeline-step <?= $estadoPedido['paso2']['completado'] ? 'active' : '' ?>">
                    <div class="circle"></div>
                    <span><?= $estadoPedido['paso2']['titulo'] ?></span>
                </div>

                <!-- Paso 3: Preparando pedido -->
                <div class="timeline-step <?= $estadoPedido['paso3']['completado'] ? 'active' : '' ?>">
                    <div class="circle"></div>
                    <span><?= $estadoPedido['paso3']['titulo'] ?></span>
                </div>

                <!-- Paso 4: Entregado -->
                <div class="timeline-step <?= $estadoPedido['paso4']['completado'] ? 'active' : '' ?>">
                    <div class="circle"></div>
                    <span><?= $estadoPedido['paso4']['titulo'] ?></span>
                </div>
            </div>
        </div>

        <!-- RESUMEN GENERAL -->
        <div class="summary-box">

            <h2 class="section-title">Datos del pedido</h2>

            <div class="summary-row">
                <span>Fecha:</span>
                <strong><?= date("d/m/Y", strtotime($ventaMaestro['fecha_de_pago'])) ?></strong>
            </div>

            <div class="summary-row">
                <span>Método de pago:</span>
                <strong><?= ucfirst($ventaMaestro['met_pago']) ?></strong>
            </div>

            <div class="summary-row">
                <span>Total:</span>
                <strong class="total-color">$<?= number_format($ventaMaestro['total_venta'], 2, ',', '.') ?></strong>
            </div>

            <div class="summary-row">
                <span>Estado actual:</span>
                <?php
                    $estadoClass = 'estado-' . $estadoActual;
                ?>
                <strong class="estado-text <?= $estadoClass ?>">
                    <?= ucfirst($estadoActual) ?>
                </strong>
            </div>

            <?php if ($ventaMaestro['comprobante']): ?>
                <div class="summary-comprobante">
                    <span>Comprobante cargado:</span>
                    <img src="<?= base_url($ventaMaestro['comprobante']) ?>" class="comprobante-img" alt="Comprobante de pago">
                </div>
            <?php endif; ?>

            <?php if ($estadoActual === 'cancelado'): ?>
                <div class="alert-cancelado">
                    <strong>Pedido cancelado por un administrador.</strong><br>
                    Si ya realizaste el pago, ponete en contacto con soporte para revisar tu caso.
                </div>
            <?php endif; ?>

        </div>

        <!-- LISTA DE PRODUCTOS -->
        <h2 class="section-title">Productos comprados</h2>

        <div class="productos-lista">
            <?php foreach ($detalleVenta as $item): ?>
                <div class="producto-item slide-in">
                    <img src="<?= base_url($item['foto1']) ?>" class="producto-img" alt="Portada del libro">

                    <div class="producto-datos">
                        <h3 class="producto-titulo"><?= $item['titulo'] ?></h3>

                        <p class="producto-info">
                            Cantidad: <strong><?= $item['cantidad'] ?></strong><br>
                            Precio unitario: <strong>$<?= number_format($item['precio_unitario'], 2, ',', '.') ?></strong><br>
                            Subtotal: <strong class="subtotal-color">$<?= number_format($item['subtotal'], 2, ',', '.') ?></strong>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- ACCIONES -->
        <div class="checkout-actions">
            <a href="<?= base_url('/') ?>" class="btn-volver">
                ⬅ Volver al inicio
            </a>
        </div>

    </div>
</div>

<?php include(APPPATH . 'Views/templates/footer.php'); ?>
