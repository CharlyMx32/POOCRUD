<?php

namespace proyecto\Controller;

use proyecto\Response\Success;
use proyecto\Response\Error;
use proyecto\Models\Table;
use proyecto\Auth;

class DetalleTareaController
{
    public function guardarDetalles()
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
                    throw new \Exception('Token inválido o expirado');
                }
            } else {
                throw new \Exception('Encabezado de autorización no encontrado');
            }

            if (!$tecnicoId) {
                $error = new Error('Usuario no encontrado en sesión');
                return $error->send();
            }

            // Obtener los datos de la solicitud
            $data = json_decode(file_get_contents('php://input'), true);
            $idAsignacionLinea = isset($data['id_asignacion_linea']) ? $data['id_asignacion_linea'] : null;
            $diagnostico = isset($data['diagnostico']) ? $data['diagnostico'] : null;
            $cambios = isset($data['cambios']) ? $data['cambios'] : null;
            $costoChequeo = isset($data['costo_chequeo']) ? $data['costo_chequeo'] : null;
            $costoReparacion = isset($data['costo_reparacion']) ? $data['costo_reparacion'] : null;

            // Verificar que todos los campos necesarios estén presentes
            if (!$idAsignacionLinea || !$diagnostico || !$cambios || !$costoChequeo || !$costoReparacion) {
                throw new \Exception('Faltan datos necesarios');
            }

            // Consultar para verificar si la asignación de línea existe
            $query = "SELECT COUNT(*) AS count FROM asignacion_linea WHERE id_asignacion_linea = :id_asignacion_linea";
            $result = Table::query($query, ['id_asignacion_linea' => $idAsignacionLinea]);

            // Acceder al resultado
            $count = $result[0]->count;

            if ($count == 0) {
                throw new \Exception('Asignación de línea no encontrada');
            }

            // Insertar en la tabla detalle_asignacion_linea

            $insertQuery = "
                INSERT INTO detalle_asignacion_linea (
                    id_asignacion_linea,
                    diagnostico_linea,
                    cambios,
                    costo_chequeo,
                    costo_reparacion
                ) VALUES (
                    :id_asignacion_linea,
                    :diagnostico_linea,
                    :cambios,
                    :costo_chequeo,
                    :costo_reparacion
                )
                ";

            $params = [
                'id_asignacion_linea' => $idAsignacionLinea,
                'diagnostico_linea' => $diagnostico,
                'cambios' => $cambios,
                'costo_chequeo' => $costoChequeo,
                'costo_reparacion' => $costoReparacion
            ];

            Table::query($insertQuery, $params);

            // Eliminar la asignación de línea de la tabla asignacion_linea
            // ...

            // En lugar de eliminar la asignación de línea, actualiza el campo estado a "inactivo"
            $updateQuery = "UPDATE asignacion_linea SET estado = 'inactivo' WHERE id_asignacion_linea = :id_asignacion_linea";
            Table::query($updateQuery, ['id_asignacion_linea' => $idAsignacionLinea]);

            // ...
            // Enviar respuesta de éxito
            $success = new Success('Detalles guardados exitosamente');
            // Mover el return al final
            return $success->send();
        } catch (\Exception $e) {
            // Capturar cualquier excepción y devolver un error genérico
            $error = new Error('Error al guardar detalles: ' . $e->getMessage());
            return $error->send();
        }
    }
}
