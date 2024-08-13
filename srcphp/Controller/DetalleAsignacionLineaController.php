<?php

namespace proyecto\Controller;

use proyecto\Response\Success;
use proyecto\Response\Error;
use proyecto\Models\Table;
use proyecto\Auth;

class DetalleAsignacionLineaController
{
    public function obtenerDetallesAsignacionLinea()
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
                    throw new \Exception('Token inválido o expirado ');
                }
            } else {
                throw new \Exception('Encabezado de autorización no encontrado');
            }

            if (!$tecnicoId) {
                $error = new Error('Usuario no encontrado en sesión');
                return $error->send();
            }

            // Consultar los detalles de asignación de línea del técnico
            $query = "
                 SELECT
                    dal.id_detalle_linea,
                    concat( p.nombre, ' ',
                    p.apellido_paterno, ' ', 
                    p.apellido_materno) as Nombre_Cliente,
                    oc.producto,
					dal.diagnostico_linea,
                    dal.cambios,
                    dal.costo_chequeo,
                    dal.costo_reparacion,
                    dal.estado_del_pago,
                    dal.seguimiento
                FROM
                    detalle_asignacion_linea dal
                    INNER JOIN asignacion_linea al ON dal.id_asignacion_linea = al.id_asignacion_linea
                    INNER JOIN tecnico t ON al.id_tecnico = t.id_tecnico
                    INNER JOIN orden_cita oc ON oc.id_orden_cita = al.id_orden_cita
                    INNER JOIN clientes c ON c.id_cliente = oc.id_cliente
                    INNER JOIN persona p ON p.id_persona = c.id_persona
                WHERE
                    t.id_tecnico = :id_tecnico
                    AND dal.seguimiento != 'Completado'
            ";

            // Ejecutar la consulta
            $detalles = Table::query($query, ['id_tecnico' => $tecnicoId]);


            // Si no hay detalles de asignación de línea
            if (empty($detalles)) {
                $error = new Error('No se encontraron detalles de asignación de línea para este usuario');
                return $error->send();
            }

            // Enviar respuesta de éxito con los detalles
            $success = new Success(['tareas' => $detalles]);
            return $success->send();
        } catch (\Exception $e) {
            // Capturar cualquier excepción y devolver un error genérico
            $error = new Error('Error al obtener detalles de asignación de línea: ' . $e->getMessage());
            return $error->send();
        }
    }

    public function actualizarSeguimiento()
    {
        try {
            // Obtener el token y decodificarlo para extraer id_usuario
            $headers = getallheaders();
            $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : null;

            // Obtener los datos del cuerpo de la solicitud
            $data = json_decode(file_get_contents('php://input'), true);
            $idDetalleLinea = $data['idDetalleLinea'] ?? null;
            $seguimiento = $data['seguimiento'] ?? null;

            if (!$idDetalleLinea || !$seguimiento) {
                throw new \Exception('Faltan parámetros');
            }

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
                    throw new \Exception('Token inválido o expirado ');
                }
            } else {
                throw new \Exception('Encabezado de autorización no encontrado');
            }

            if (!$tecnicoId) {
                $error = new Error('Usuario no encontrado en sesión');
                return $error->send();
            }

            // Actualizar el seguimiento del detalle de asignación de línea
            $query = "
            UPDATE
                detalle_asignacion_linea
            SET
                seguimiento = :seguimiento
            WHERE
                id_detalle_linea = :id_detalle_linea
        ";

            // Ejecutar la consulta
            // ...

            $result = Table::query($query, [
                'id_detalle_linea' => $idDetalleLinea,
                'seguimiento' => $seguimiento
            ]);
    
            // Depuración: Verificar el resultado de la consulta
            error_log('Consulta ejecutada: ' . print_r($query, true));
            error_log('Parámetros: ' . print_r([
                'id_detalle_linea' => $idDetalleLinea,
                'seguimiento' => $seguimiento
            ], true));
            error_log('Resultado de la consulta: ' . print_r($result, true));
            error_log('Affected rows: ' . (isset($result['rowCount']) ? $result['rowCount'] : 'Unknown'));
    
 // Enviar una respuesta al cliente
$respuesta = [
    'mensaje' => 'Seguimiento actualizado correctamente',
    'resultado' => $result
];
echo json_encode($respuesta);
        } catch (\Exception $e) {
            // Capturar cualquier excepción y devolver un error genérico
            error_log('Excepción: ' . $e->getMessage());
            $error = new Error('Error al actualizar seguimiento: ' . $e->getMessage());
            return $error->send();
        }
    }

    public function obtenerDetallesAsignacionLineaCompletados()
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
                    throw new \Exception('Token inválido o expirado ');
                }
            } else {
                throw new \Exception('Encabezado de autorización no encontrado');
            }

            if (!$tecnicoId) {
                $error = new Error('Usuario no encontrado en sesión');
                return $error->send();
            }

            // Consultar los detalles de asignación de línea del técnico con seguimiento completado
            $query = "
            SELECT
                dal.id_detalle_linea,
                concat( p.nombre, ' ',
                p.apellido_paterno, ' ', 
                p.apellido_materno) as Nombre_Cliente,
                oc.producto,
                dal.diagnostico_linea,
                dal.cambios,
                dal.costo_chequeo,
                dal.costo_reparacion,
                dal.estado_del_pago,
                dal.seguimiento
            FROM
                detalle_asignacion_linea dal
                INNER JOIN asignacion_linea al ON dal.id_asignacion_linea = al.id_asignacion_linea
                INNER JOIN tecnico t ON al.id_tecnico = t.id_tecnico
                INNER JOIN orden_cita oc ON oc.id_orden_cita = al.id_orden_cita
                INNER JOIN clientes c ON c.id_cliente = oc.id_cliente
                INNER JOIN persona p ON p.id_persona = c.id_persona
            WHERE
                t.id_tecnico = :id_tecnico
                AND dal.seguimiento = 'Completado'
        ";

            // Ejecutar la consulta
            $detalles = Table::query($query, ['id_tecnico' => $tecnicoId]);

            // Depuración: Verificar el resultado de la consulta
            error_log('Consulta ejecutada: ' . print_r($query, true));
            error_log('Parámetros: ' . print_r(['id_tecnico' => $tecnicoId], true));
            error_log('Resultado de la consulta: ' . print_r($detalles, true));

            // Si no hay detalles de asignación de línea
            if (empty($detalles)) {
                $error = new Error('No se encontraron detalles de asignación de línea completados para este usuario');
                return $error->send();
            }

            // Enviar respuesta de éxito con los detalles
            $success = new Success(['tareas' => $detalles]);
            return $success->send();
        } catch (\Exception $e) {
            // Capturar cualquier excepción y devolver un error genérico
            $error = new Error('Error al obtener detalles de asignación de línea completados: ' . $e->getMessage());
            return $error->send();
        }
    }
}
