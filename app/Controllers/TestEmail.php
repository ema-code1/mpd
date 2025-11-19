<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class TestEmail extends Controller
{
    public function send()
    {
        // Configurar email
        $email = \Config\Services::email();
        
        // IMPORTANTE: Limpiar y configurar como texto plano primero
        $email->clear();
        
        // Configurar como texto plano (más simple para testing)
        $email->setMailType('text');
        
        // Configurar destinatario
        $email->setTo('emarissopatron@gmail.com');
        
        // Configurar asunto
        $email->setSubject('Test de Email - MPD');
        
        // Mensaje simple en texto plano
        $message = "Hola!\n\n";
        $message .= "Si estas viendo este mensaje, el sistema de email funciona correctamente.\n\n";
        $message .= "Saludos,\n";
        $message .= "Movimiento de la Palabra de Dios";
        
        $email->setMessage($message);
        
        // Intentar enviar
        if ($email->send()) {
            echo "<h2 style='color: green;'>✅ Email enviado correctamente!</h2>";
            echo "<p>Revisa tu bandeja de entrada: <strong>emarissopatron@gmail.com</strong></p>";
            echo "<p style='color: orange;'>También revisa Spam/Promociones por si acaso.</p>";
        } else {
            echo "<h2 style='color: red;'>❌ Error al enviar email</h2>";
            echo "<div style='background: #f5f5f5; padding: 15px; border-radius: 5px;'>";
            echo "<h3>Debug Info:</h3>";
            echo "<pre style='background: white; padding: 10px; overflow: auto;'>";
            echo htmlspecialchars($email->printDebugger(['headers', 'subject', 'body']));
            echo "</pre>";
            echo "</div>";
        }
    }
}