<?php

namespace proyecto\Controller;

use proyecto\Response\Success;
use proyecto\Response\Error;
use proyecto\Models\Table;
use proyecto\Models\User;
use proyecto\Auth;
use proyecto\Router;

class LoginController
{
    public function login()

    {
        Router::headers();
        try {

            $JSONData = file_get_contents("php://input");
            $dataObject = json_decode($JSONData);

            echo"hola";
            if (!isset($dataObject->correo) || !isset   ($dataObject->contraseña)) {
                throw new \Exception("faltan datos"); //llanzar error en aso de faltar datos con PDO::Exception
               // $error = new Error('Datos incompletos');
               // return $error->send();
            }

            $correo = trim($dataObject->correo);
            $contraseña = trim($dataObject->contraseña);

            // Consultar usuario, clienteId y id_tecnico
            $query = "
                SELECT 
                    u.id_usuario, 
                    p.nombre, 
                    p.apellido_materno, 
                    p.apellido_paterno, 
                    u.contraseña, 
                    r.id_rol,
                    u.correo,
                    c.id_cliente,
                    t.id_tecnico
                FROM usuario u
                JOIN rol_usuario ru ON u.id_usuario = ru.id_usuario
                JOIN rol r ON ru.id_rol = r.id_rol
                JOIN persona p ON p.id_usuario = u.id_usuario
                LEFT JOIN clientes c ON c.id_persona = p.id_usuario
                LEFT JOIN empleado e ON p.id_persona = e.id_persona
                LEFT JOIN tecnico t ON t.id_empleado = e.id_empleado
                WHERE u.correo = :correo
            ";

            $resultados = Table::query($query, ['correo' => $correo]);

            if (empty($resultados)) {
                $error = new Error('Usuario no encontrado');
                return $error->send();
            }

            $usuario = $resultados[0];

            if (!password_verify($contraseña, $usuario->contraseña)) {
                $error = new Error('Contraseña incorrecta');
                return $error->send();
            }

            // Preparar datos para el token
            $tokenData = [
                'id' => $usuario->id_usuario,
                'nombre' => $usuario->nombre,
                'apellido_materno' => $usuario->apellido_materno,
                'apellido_paterno' => $usuario->apellido_paterno,
                'id_rol' => $usuario->id_rol,
                'correo' => $usuario->correo,
                'clienteId' => $usuario->id_cliente,
                'tecnicoId' => $usuario->id_tecnico
            ];

            // Generar token
            $token = Auth::generateToken($tokenData);

            $response = [
                'success' => true,
                'message' => 'Inicio de sesión exitoso',
                'token' => $token,
                'usuario' => $tokenData,
                'clienteId' => $usuario->id_cliente
            ];

            $success = new Success($response);
            return $success->send();

        } catch (\Exception $e) {
            $error = new Error('Error interno del servidor: ' . $e->getMessage());
            return $error->send();
        }
    }

    public function logout()
    {
        Auth::logout();
        $response = [
            'success' => true,
            'message' => 'Cierre de sesión exitoso'
        ];
        $success = new Success($response);
        return $success->send();
    }
}
