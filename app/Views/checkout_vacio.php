<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos</title>
    <link rel="stylesheet" href="<?= base_url('styles/checkout.css') ?>">
</head>
<body>
    <div class="content">
        <div class="inner-container">

            <div class="checkout-vacio fade-in">
                <img src="<?= base_url('images/empty.png') ?>" alt="Sin compras">

                <h2>No tenés compras todavía</h2>
                <p>Cuando compres un libro, vas a ver acá el seguimiento completo de tu pedido.</p>

                <a href="<?= base_url('/') ?>" class="btn-volver">Volver al inicio</a>
            </div>

        </div>
    </div>
</body>
</html>
