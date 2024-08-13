<?php

namespace proyecto\Controller;

use proyecto\Response\Success;
use proyecto\Response\Error;
use proyecto\Auth;

class AuthController
{
    public function checkAuth()
    {
        // Obtener el token de las cabeceras de la solicitud
        $headers = apache_request_headers();
        if (!isset($headers['Authorization'])) {
            $error = new Error('No se proporcionó token');
            return $error->send();
        }

        // Extraer el token de la cabecera
        $token = str_replace('Bearer ', '', $headers['Authorization']);

        try {
            // Verificar y decodificar el token
            $decodedToken = Auth::verifyToken($token);
            
            // Verificar que el token sea válido y que los datos estén presentes
            if (!$decodedToken) {
                $error = new Error('Token inválido');
                return $error->send();
            }

            // Preparar la respuesta con los datos del usuario
            $response = [
                'success' => true,
                'usuario' => $decodedToken->data // Asegúrate de que los datos estén en 'data'
            ];

            $success = new Success($response);
            return $success->send();
        } catch (\Exception $e) {
            $error = new Error('Error en la verificación del token');
            return $error->send();
        }
    }
}
