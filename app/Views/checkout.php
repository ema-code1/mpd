<?php
// Asegurar que $ventaMaestro esté disponible
if (!isset($ventaMaestro) || empty($ventaMaestro)) {
    return redirect()->to('/');
}

$ventaId = $ventaMaestro['venta_id'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Pedido #<?= htmlspecialchars($ventaId) ?></title>
    <link rel="stylesheet" href="<?= site_url('public/css/checkout.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.0/bootstrap-icons.min.css">
</head>
<body>
    <div class="checkout-container">
        <!-- Header -->
        <div class="checkout-header">
            <h1>Seguimiento de Pedido #<?= htmlspecialchars($ventaId) ?></h1>
            <p class="fecha-pedido">Realizado el <?= date('d/m/Y', strtotime($ventaMaestro['created_at'])) ?></p>
        </div>

        <div class="checkout-content">
            <!-- Stepper de progreso -->
            <div class="stepper-section">
                <h2>Estado de tu Pedido</h2>
                <div class="stepper-box">
                    <!-- Paso 1: Pedido Realizado -->
                    <div class="stepper-step <?= $estadoPedido['paso1']['completado'] ? 'stepper-completed' : 'stepper-pending' ?>">
                        <div class="stepper-circle">
                            <?php if ($estadoPedido['paso1']['completado']): ?>
                                <i class="bi bi-check-lg"></i>
                            <?php else: ?>
                                1
                            <?php endif; ?>
                        </div>
                        <div class="stepper-line"></div>
                        <div class="stepper-content">
                            <div class="stepper-title"><?= htmlspecialchars($estadoPedido['paso1']['titulo']) ?></div>
                            <div class="stepper-status <?= strtolower($estadoPedido['paso1']['status']) ?>">
                                <?= htmlspecialchars($estadoPedido['paso1']['status']) ?>
                            </div>
                            <div class="stepper-time"><?= date('d/m/Y H:i', strtotime($ventaMaestro['created_at'])) ?></div>
                        </div>
                    </div>

                    <!-- Paso 2: En Revisión -->
                    <div class="stepper-step <?= $estadoPedido['paso2']['completado'] ? 'stepper-completed' : ($ventaMaestro['estado'] === 'revisado' ? 'stepper-active' : 'stepper-pending') ?>">
                        <div class="stepper-circle">
                            <?php if ($estadoPedido['paso2']['completado']): ?>
                                <i class="bi bi-check-lg"></i>
                            <?php else: ?>
                                2
                            <?php endif; ?>
                        </div>
                        <div class="stepper-line"></div>
                        <div class="stepper-content">
                            <div class="stepper-title"><?= htmlspecialchars($estadoPedido['paso2']['titulo']) ?></div>
                            <div class="stepper-status <?= strtolower($estadoPedido['paso2']['status']) ?>">
                                <?= htmlspecialchars($estadoPedido['paso2']['status']) ?>
                            </div>
                            <div class="stepper-time">
                                <?= $estadoPedido['paso2']['completado'] ? date('d/m/Y H:i') : 'Pendiente' ?>
                            </div>
                        </div>
                    </div>

                    <!-- Paso 3: Envío -->
                    <div class="stepper-step <?= $estadoPedido['paso3']['completado'] ? 'stepper-completed' : ($ventaMaestro['estado'] !== 'cancelado' && $estadoPedido['paso2']['completado'] ? 'stepper-active' : 'stepper-pending') ?>">
                        <div class="stepper-circle">
                            <?php if ($estadoPedido['paso3']['completado']): ?>
                                <i class="bi bi-check-lg"></i>
                            <?php else: ?>
                                3
                            <?php endif; ?>
                        </div>
                        <div class="stepper-content">
                            <div class="stepper-title"><?= htmlspecialchars($estadoPedido['paso3']['titulo']) ?></div>
                            <div class="stepper-status <?= strtolower($estadoPedido['paso3']['status']) ?>">
                                <?= htmlspecialchars($estadoPedido['paso3']['status']) ?>
                            </div>
                            <div class="stepper-time">
                                <?= $estadoPedido['paso3']['completado'] ? 'Completado' : 'Estimado próximamente' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comprobante -->
            <div class="comprobante-section">
                <div class="comprobante-card">
                    <!-- Header del Comprobante -->
                    <div class="comprobante-header">
                        <div class="admin-info">
                            <div class="admin-foto">
                                <img src="<?= htmlspecialchars($administrador['foto']) ?>" alt="Administrador" onerror="this.src='<?= site_url('public/img/default-avatar.png') ?>'">
                            </div>
                            <div class="admin-datos">
                                <h3><?= htmlspecialchars($administrador['nombre']) ?></h3>
                                <p><strong>DNI:</strong> <?= htmlspecialchars($administrador['dni']) ?></p>
                                <p><strong>Nacimiento:</strong> <?= htmlspecialchars($administrador['fechaNacimiento']) ?></p>
                            </div>
                        </div>
                        <div class="comprobante-numero">
                            <p class="label">Comprobante Nº</p>
                            <p class="numero"><?= str_pad($ventaId, 8, '0', STR_PAD_LEFT) ?></p>
                        </div>
                    </div>

                    <!-- Datos del Comprador -->
                    <div class="comprobante-comprador">
                        <h4>Cliente</h4>
                        <p><strong><?= htmlspecialchars($comprador['name']) ?></strong></p>
                        <p><?= htmlspecialchars($comprador['email']) ?></p>
                    </div>

                    <!-- Detalles de la Venta -->
                    <div class="comprobante-detalle">
                        <table class="tabla-detalle">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unitario</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($detalleVenta as $detalle): ?>
                                    <tr>
                                        <td>
                                            <div class="producto-info">
                                                <img src="<?= site_url(htmlspecialchars($detalle['foto1'])) ?>" alt="<?= htmlspecialchars($detalle['titulo']) ?>">
                                                <div>
                                                    <p class="titulo"><?= htmlspecialchars($detalle['titulo']) ?></p>
                                                    <p class="autor"><?= htmlspecialchars($detalle['autor']) ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="cantidad"><?= intval($detalle['cantidad']) ?></td>
                                        <td class="precio">$<?= number_format($detalle['precio_unitario'], 2, ',', '.') ?></td>
                                        <td class="subtotal">$<?= number_format($detalle['subtotal'], 2, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Resumen Financiero -->
                    <div class="comprobante-resumen">
                        <div class="resumen-row">
                            <span>Método de Pago:</span>
                            <strong><?= htmlspecialchars(ucfirst($ventaMaestro['met_pago'])) ?></strong>
                        </div>
                        <div class="resumen-row">
                            <span>Fecha de Pago:</span>
                            <strong><?= date('d/m/Y', strtotime($ventaMaestro['fecha_de_pago'])) ?></strong>
                        </div>
                        <div class="resumen-row total">
                            <span>Total:</span>
                            <strong>$<?= number_format($ventaMaestro['total_venta'], 2, ',', '.') ?></strong>
                        </div>
                    </div>

                    <!-- Comprobante Adjunto -->
                    <div class="comprobante-archivo">
                        <h4>Comprobante</h4>
                        <?php if ($ventaMaestro['comprobante']): ?>
                            <div class="archivo-presente">
                                <i class="bi bi-file-pdf"></i>
                                <p>Comprobante adjuntado</p>
                                <a href="<?= site_url(htmlspecialchars($ventaMaestro['comprobante'])) ?>" download class="btn-descargar">
                                    <i class="bi bi-download"></i> Descargar Comprobante
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="archivo-ausente">
                                <i class="bi bi-exclamation-circle"></i>
                                <p>Comprobante pendiente de carga</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Footer del Comprobante -->
                    <div class="comprobante-footer">
                        <p>Documento válido como comprobante de compra</p>
                        <p class="fecha-emision">Emitido el <?= date('d/m/Y H:i:s') ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="checkout-actions">
            <a href="<?= site_url('') ?>" class="btn btn-primary">
                <i class="bi bi-house"></i> Ir a Inicio
            </a>
            <a href="<?= site_url('cart') ?>" class="btn btn-secondary">
                <i class="bi bi-cart"></i> Ver Carrito
            </a>
        </div>
    </div>
</body>
</html>