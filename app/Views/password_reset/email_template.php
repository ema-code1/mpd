<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperación de Contraseña</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Arial', sans-serif; background-color: #f6f6f6;">
    <table role="presentation" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td align="center" style="padding: 40px 0;">
                <table role="presentation" style="width: 600px; max-width: 100%; border-collapse: collapse; background-color: #ffffff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="padding: 40px 40px 20px 40px; text-align: center; background: linear-gradient(135deg, #EF8D00, #ff7214); border-radius: 10px 10px 0 0;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 600;">
                                🔐 Recuperación de Contraseña
                            </h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="margin: 0 0 20px 0; font-size: 16px; color: #333333; line-height: 1.6;">
                                Hola <strong><?= htmlspecialchars($userName) ?></strong>,
                            </p>
                            
                            <p style="margin: 0 0 20px 0; font-size: 16px; color: #333333; line-height: 1.6;">
                                Recibimos una solicitud para restablecer la contraseña de tu cuenta en <strong>Movimiento de la Palabra de Dios</strong>.
                            </p>

                            <p style="margin: 0 0 30px 0; font-size: 16px; color: #333333; line-height: 1.6;">
                                Para crear una nueva contraseña, haz clic en el siguiente botón:
                            </p>

                            <!-- Botón -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td align="center" style="padding: 20px 0;">
                                        <a href="<?= $resetLink ?>" 
                                           style="display: inline-block; padding: 15px 40px; background: linear-gradient(135deg, #EF8D00, #ff7214); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 12px rgba(239, 141, 0, 0.3);">
                                            Restablecer mi contraseña
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Información adicional -->
                            <div style="margin-top: 30px; padding: 20px; background-color: #fff5e6; border-left: 4px solid #EF8D00; border-radius: 4px;">
                                <p style="margin: 0 0 10px 0; font-size: 14px; color: #666666; line-height: 1.6;">
                                    ⏱️ <strong>Este enlace expira en 12 horas</strong>
                                </p>
                                <p style="margin: 0; font-size: 14px; color: #666666; line-height: 1.6;">
                                    🔒 Si no solicitaste este cambio, ignora este email y tu contraseña permanecerá igual.
                                </p>
                            </div>

                            <!-- Enlace alternativo -->
                            <p style="margin: 30px 0 0 0; font-size: 13px; color: #999999; line-height: 1.6;">
                                Si el botón no funciona, copia y pega este enlace en tu navegador:
                            </p>
                            <p style="margin: 10px 0 0 0; font-size: 12px; color: #EF8D00; word-break: break-all;">
                                <a href="<?= $resetLink ?>" style="color: #EF8D00; text-decoration: none;">
                                    <?= $resetLink ?>
                                </a>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 30px 40px; text-align: center; background-color: #f9f9f9; border-radius: 0 0 10px 10px;">
                            <p style="margin: 0 0 10px 0; font-size: 14px; color: #666666;">
                                <strong>Movimiento de la Palabra de Dios</strong>
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #999999;">
                                Este es un correo automático, por favor no respondas a este mensaje.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>