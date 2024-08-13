<?php

namespace proyecto\Controller;

use proyecto\Response\Success;
use proyecto\Response\Error;
use proyecto\Models\Table;
use proyecto\Auth;

class TareasController
{
    public function obtenerTareasAsignadas()
    {
        try {
            // Obtener el token y decodificarlo para extraer id_usuario
            $headers = getallheaders();
            $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : null;

            // Inicializar id_usuario en null
            $tecnicoId = null;
            
            if ($authHeader) {
                // Extraer el token del encabezado
                $token = str_replace('Bearer ', '', $authHeader);
                
                // Verificar y decodificar el token
                $decodedToken = Auth::verifyToken($token);
                
                if ($decodedToken && isset($decodedToken->data->tecnicoId)) {
                    $tecnicoId = $decodedToken->data->tecnicoId;
                } else {
                    throw new \Exception('Token inválido o expirado ', );
                }
            } else {
                throw new \Exception('Encabezado de autorización no encontrado');
            }

            if (!$tecnicoId) {
                $error = new Error('Usuario no encontrado en sesión');
                return $error->send();
            }

            // Consultar las tareas asignadas al técnico basado en id_usuario
            $query = "
    SELECT
        al.id_asignacion_linea,
        CONCAT(p.nombre, ' ', p.apellido_paterno) AS nombre_cliente,
        oc.producto,
        oc.problema,
        'Orden de Cita' AS tipo_orden
    FROM
        orden_cita oc
        INNER JOIN asignacion_linea al ON oc.id_orden_cita = al.id_orden_cita
        INNER JOIN tecnico t ON al.id_tecnico = t.id_tecnico
        INNER JOIN empleado e ON t.id_empleado = e.id_empleado
        INNER JOIN persona p ON e.id_persona = p.id_persona
    WHERE
        t.id_tecnico = :id_tecnico
        AND al.estado = 'activo'
";

            // Ejecutar la consulta
            $tareas = Table::query($query, ['id_tecnico' => $tecnicoId]);

            // Depuración: Verificar el resultado de la consulta
            error_log('Consulta ejecutada: ' . print_r($query, true));
            error_log('Parámetros: ' . print_r(['id_tecnico' => $tecnicoId], true));
            error_log('Resultado de la consulta: ' . print_r($tareas, true));

            // Si no hay tareas asignadas
            if (empty($tareas)) {
                $error = new Error('No se encontraron tareas asignadas para este usuario');
                return $error->send();
            }

            // Enviar respuesta de éxito con las tareas
            $success = new Success($tareas);
            return $success->send();

        } catch (\Exception $e) {
            // Capturar cualquier excepción y devolver un error genérico
            $error = new Error('Error al obtener tareas asignadas: ' . $e->getMessage());
            return $error->send();
        }
    }
}
