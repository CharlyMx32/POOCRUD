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
            // Obtener los encabezados
            $headers = getallheaders();
            error_log('Encabezados recibidos: ' . print_r($headers, true)); 
    
            $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : null;
            if (!$authHeader) {
                throw new \Exception('Encabezado de autorización no encontrado');
            }
    
            // Extraer el token del encabezado
            $token = str_replace('Bearer ', '', $authHeader);
    
            // Verificar y decodificar el token
            $decodedToken = Auth::verifyToken($token);
            if (!$decodedToken || !isset($decodedToken->data->tecnicoId)) {
                throw new \Exception('Token inválido o expirado');
            }
    
            $tecnicoId = $decodedToken->data->tecnicoId;
    
            // Consultar las tareas asignadas al técnico basado en id_tecnico
            $query = "
              SELECT 
    id_asignacion, 
    nombre_cliente,
    producto,
    problema,
    tipo_orden
FROM (
    SELECT
        al.id_asignacion_linea AS id_asignacion,
        CONCAT(p.nombre, ' ', p.apellido_paterno) AS nombre_cliente,
        oc.producto,
        oc.problema,
        'Orden de Cita' AS tipo_orden
    FROM
        orden_cita oc
        INNER JOIN asignacion_linea al ON oc.id_orden_cita = al.id_orden_cita
        INNER JOIN clientes c ON oc.id_cliente = c.id_cliente
        INNER JOIN persona p ON c.id_persona = p.id_persona
		INNER JOIN tecnico t ON al.id_tecnico = t.id_tecnico
    WHERE
        t.id_tecnico = :id_tecnico
        AND al.estado = 'activo'
        AND NOT EXISTS (
            SELECT 1 
            FROM detalle_asignacion_linea dal 
            WHERE dal.id_asignacion_linea = al.id_asignacion_linea
        )
    
    UNION ALL
    
    SELECT
        af.id_asignacion_fisica AS id_asignacion,
        CONCAT(ofi.nombre, ' ', ofi.apellido_paterno) AS nombre_cliente,
        ofi.producto,
        ofi.problema,
        'Orden Física' AS tipo_orden
    FROM
        orden_fisica ofi
        INNER JOIN asignacion_fisica af ON ofi.id_orden_fisica = af.id_orden_fisica
		INNER JOIN tecnico t ON af.id_tecnico = t.id_tecnico
    WHERE
        t.id_tecnico = :id_tecnico
        AND NOT EXISTS (
            SELECT 1 
            FROM detalle_asignacion_fisica daf 
            WHERE daf.id_asignacion_fisica = af.id_asignacion_fisica
        )
) AS combined;


            ";
    
            // Ejecutar la consulta
            $detalles = Table::query($query, ['id_tecnico' => $tecnicoId]);
    
            // Log para depuración
            error_log('Resultado de la consulta: ' . print_r($detalles, true));
    
            if (empty($detalles)) {
                $error = new Error('No se encontraron tareas asignadas para este usuario');
                return $error->send();
            }
    
            $success = new Success(['tareas' => $detalles]);
            return $success->send();
        } catch (\Exception $e) {
            // Log de error y devolución de respuesta
            error_log('Error: ' . $e->getMessage());
            $error = new Error('Error al obtener tareas asignadas: ' . $e->getMessage());
            return $error->send();
        }
    }    
}
