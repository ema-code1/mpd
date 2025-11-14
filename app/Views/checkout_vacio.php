<?php ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Sin Pedidos</title>
    <link rel="stylesheet" href="<?= base_url('public/css/checkout.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.0/bootstrap-icons.min.css">
</head>
<body>
    <div class="checkout-container">
        <div class="empty-state">
            <div class="empty-icon">
                <i class="bi bi-inbox"></i>
            </div>
            <h1>No hay Pedidos</h1>
            <p>Parece que aún no has realizado ninguna compra o el pedido no existe.</p>
            
            <div class="empty-illustration">
                <svg viewBox="0 0 200 150" xmlns="http://www.w3.org/2000/svg">
                    <!-- Caja vacía -->
                    <rect x="50" y="40" width="100" height="80" fill="none" stroke="#0f172a" stroke-width="2" rx="5"/>
                    <path d="M 50 50 L 150 50" stroke="#0f172a" stroke-width="2"/>
                    <path d="M 75 50 L 75 120" stroke="#0f172a" stroke-width="1.5" opacity="0.3"/>
                    <path d="M 100 50 L 100 120" stroke="#0f172a" stroke-width="1.5" opacity="0.3"/>
                    <path d="M 125 50 L 125 120" stroke="#0f172a" stroke-width="1.5" opacity="0.3"/>
                    
                    <!-- X grande -->
                    <path d="M 70 65 L 130 105" stroke="#dc2626" stroke-width="3" stroke-linecap="round"/>
                    <path d="M 130 65 L 70 105" stroke="#dc2626" stroke-width="3" stroke-linecap="round"/>
                </svg>
            </div>

            <div class="empty-actions">
                <a href="<?= site_url('') ?>" class="btn btn-primary btn-large">
                    <i class="bi bi-house-fill"></i> Ir a la Tienda
                </a>
                <a href="<?= site_url('cart') ?>" class="btn btn-secondary btn-large">
                    <i class="bi bi-cart3"></i> Ver Carrito
                </a>
            </div>

            <p class="empty-hint">Comienza a explorar nuestros productos y realiza tu primera compra</p>
        </div>
    </div>

    <style>
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 80vh;
            text-align: center;
            padding: 2rem;
        }

        .empty-icon {
            font-size: 5rem;
            color: #94a3b8;
            margin-bottom: 1.5rem;
            animation: float 3s ease-in-out infinite;
        }

        .empty-state h1 {
            font-size: 2rem;
            color: #0f172a;
            margin-bottom: 0.5rem;
            font-weight: 700;
        }

        .empty-state > p {
            font-size: 1rem;
            color: #64748b;
            margin-bottom: 2rem;
            max-width: 400px;
        }

        .empty-illustration {
            margin: 2rem 0;
            width: 200px;
            height: 150px;
        }

        .empty-actions {
            display: flex;
            gap: 1rem;
            margin: 2rem 0;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn-large {
            padding: 0.875rem 2rem;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .empty-hint {
            color: #94a3b8;
            font-size: 0.875rem;
            margin-top: 1.5rem;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        @media (max-width: 640px) {
            .empty-state {
                min-height: 60vh;
            }

            .empty-state h1 {
                font-size: 1.5rem;
            }

            .empty-actions {
                flex-direction: column;
                width: 100%;
            }

            .btn-large {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</body>
</html>