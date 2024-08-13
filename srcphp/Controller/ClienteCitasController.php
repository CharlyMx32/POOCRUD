<?php

namespace proyecto\Controller;

use proyecto\Response\Success;
use proyecto\Response\Error;
use proyecto\Models\Table;
use proyecto\Auth;

class ClienteCitasController
{
    // Función existente para obtener todas las citas del cliente
    public function obtenerCitasCliente()
    {
        try {
            $clienteId = $this->getClientIdFromToken();

            if (!$clienteId) {
                $error = new Error('Usuario no encontrado en sesión');
                return $error->send();
            }

            $query = "
SELECT 
    oc.producto,
    oc.problema,
    CONCAT(p.nombre, ' ', p.apellido_paterno, ' ', p.apellido_materno) AS nombre_tecnico,
    dal.costo_chequeo,
    dal.costo_reparacion,
    SUM(dal.costo_chequeo + dal.costo_reparacion) AS total,
    dal.seguimiento,
    dal.entregado,
    dal.tiempo_garantia,
    dal.diagnostico_linea,
    dal.uso_garantia,
    dal.estado_del_pago,
    dal.id_detalle_linea
FROM 
    orden_cita oc
JOIN 
    asignacion_linea al ON oc.id_orden_cita = al.id_orden_cita
JOIN 
    detalle_asignacion_linea dal ON al.id_asignacion_linea = dal.id_asignacion_linea
JOIN 
    tecnico t ON al.id_tecnico = t.id_tecnico
JOIN 
    empleado e ON t.id_empleado = e.id_empleado
JOIN 
    persona p ON e.id_persona = p.id_persona
WHERE
    oc.id_cliente = :id_cliente
AND dal.estado_del_pago = 'Pendiente'
GROUP BY 
    oc.producto,
    oc.problema,
    nombre_tecnico,
    dal.costo_chequeo,
    dal.costo_reparacion,
    dal.seguimiento,
    dal.entregado,
    dal.tiempo_garantia,
    dal.diagnostico_linea,
    dal.uso_garantia,
    dal.estado_del_pago,
    dal.id_detalle_linea
LIMIT 0, 1000;

            ";

            $citas = Table::query($query, ['id_cliente' => $clienteId]);

            if (empty($citas)) {
                $error = new Error('No se encontraron citas para este usuario');
                return $error->send();
            }

            $success = new Success(['citas' => $citas]);
            return $success->send();
        } catch (\Exception $e) {
            $error = new Error('Error al obtener citas del cliente: ' . $e->getMessage());
            return $error->send();
        }
    }

    // Función para obtener citas con seguimiento distinto de 'Completado'
    public function obtenerCitasEnProceso()
    {
        try {
            $clienteId = $this->getClientIdFromToken();

            if (!$clienteId) {
                $error = new Error('Usuario no encontrado en sesión');
                return $error->send();
            }

            $query = "
                SELECT 
                    oc.producto,
                    oc.problema,
                    CONCAT(p.nombre, ' ', p.apellido_paterno, ' ', p.apellido_materno) AS nombre_tecnico,
                    dal.costo_chequeo,
                    dal.costo_reparacion,
                    (dal.costo_chequeo + dal.costo_reparacion) AS total,
                    dal.seguimiento,
                    dal.entregado,
                    dal.tiempo_garantia,
                    dal.diagnostico_linea,
                    dal.uso_garantia,
                    dal.estado_del_pago
                FROM 
                    orden_cita oc
                JOIN 
                    asignacion_linea al ON oc.id_orden_cita = al.id_orden_cita
                JOIN 
                    detalle_asignacion_linea dal ON al.id_asignacion_linea = dal.id_asignacion_linea
                JOIN 
                    tecnico t ON al.id_tecnico = t.id_tecnico
                JOIN 
                    empleado e ON t.id_empleado = e.id_empleado
                JOIN 
                    persona p ON e.id_persona = p.id_persona
                WHERE
                    oc.id_cliente = :id_cliente
                    AND dal.seguimiento <> 'Completado' 
                    AND dal.estado_del_pago <> 'Pendiente';
            ";

            $citas = Table::query($query, ['id_cliente' => $clienteId]);

            if (empty($citas)) {
                $error = new Error('No se encontraron citas en proceso para este usuario');
                return $error->send();
            }

            $success = new Success(['citas' => $citas]);
            return $success->send();
        } catch (\Exception $e) {
            $error = new Error('Error al obtener citas en proceso del cliente: ' . $e->getMessage());
            return $error->send();
        }
    }

    // Nueva función para obtener citas con seguimiento 'Completado'
    public function obtenerCitasCompletadas()
    {
        try {
            $clienteId = $this->getClientIdFromToken();

            if (!$clienteId) {
                $error = new Error('Usuario no encontrado en sesión');
                return $error->send();
            }

            $query = "
                SELECT 
                    oc.producto,
                    oc.problema,
                    CONCAT(p.nombre, ' ', p.apellido_paterno, ' ', p.apellido_materno) AS nombre_tecnico,
                    dal.costo_chequeo,
                    dal.costo_reparacion,
                    (dal.costo_chequeo + dal.costo_reparacion) AS total,
                    dal.seguimiento,
                    dal.entregado,
                    dal.tiempo_garantia,
                    dal.diagnostico_linea,
                    dal.uso_garantia,
                    dal.estado_del_pago
                FROM 
                    orden_cita oc
                JOIN 
                    asignacion_linea al ON oc.id_orden_cita = al.id_orden_cita
                JOIN 
                    detalle_asignacion_linea dal ON al.id_asignacion_linea = dal.id_asignacion_linea
                JOIN 
                    tecnico t ON al.id_tecnico = t.id_tecnico
                JOIN 
                    empleado e ON t.id_empleado = e.id_empleado
                JOIN 
                    persona p ON e.id_persona = p.id_persona
                WHERE
                    oc.id_cliente = :id_cliente
                    AND dal.seguimiento = 'Completado'
            ";

            $citas = Table::query($query, ['id_cliente' => $clienteId]);

            if (empty($citas)) {
                $error = new Error('No se encontraron citas completadas para este usuario');
                return $error->send();
            }

            $success = new Success(['citas' => $citas]);
            return $success->send();
        } catch (\Exception $e) {
            $error = new Error('Error al obtener citas completadas del cliente: ' . $e->getMessage());
            return $error->send();
        }
    }

    // Método para obtener el ID del cliente a partir del token
    private function getClientIdFromToken()
    {
        $headers = getallheaders();
        $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : null;

        if ($authHeader) {
            $token = str_replace('Bearer ', '', $authHeader);
            $decodedToken = Auth::verifyToken($token);

            if ($decodedToken && isset($decodedToken->data->clienteId)) {
                return $decodedToken->data->clienteId;
            } else {
                throw new \Exception('Token inválido o expirado');
            }
        } else {
            throw new \Exception('Encabezado de autorización no encontrado');
        }
    }
    public function actualizarPago()
{
    try {
        // Obtener el ID del cliente desde el token de la sesión
        $clienteId = $this->getClientIdFromToken();

        if (!$clienteId) {
            $error = new Error('Usuario no encontrado en sesión');
            return $error->send();
        }

        // Obtener los datos enviados en la solicitud
        $data = json_decode(file_get_contents('php://input'), true);

        // Validar que el id_detalle_linea esté presente
        $id_detalle_Linea = $data['id_detalle_linea'] ?? null;

        if (!$id_detalle_Linea) {
            $error = new Error('Datos inválidos para actualizar el pago');
            return $error->send();
        }

        // Actualizar el estado del pago a "Aceptado"
        $query = "
            UPDATE detalle_asignacion_linea
            SET estado_del_pago = 'Aceptado'
            WHERE id_detalle_linea = :id_detalle_linea
        ";

        $params = [
            'id_detalle_linea' => $id_detalle_Linea
        ];

        $result = Table::query($query, $params);

        if ($result) {
            $success = new Success(['message' => 'Estado de pago actualizado correctamente']);
            return $success->send();
        } else {
            $error = new Error('No se pudo actualizar el estado del pago');
            return $error->send();
        }
    } catch (\Exception $e) {
        $error = new Error('Error al actualizar el estado del pago: ' . $e->getMessage());
        return $error->send();
    }
}


    
}
