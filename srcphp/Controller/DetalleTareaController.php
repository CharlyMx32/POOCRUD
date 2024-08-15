<?php

namespace proyecto\Controller;

use proyecto\Response\Success;
use proyecto\Response\Error;
use proyecto\Models\Table;
use proyecto\Auth;

class DetalleTareaController
{
    // Función privada para obtener las tareas asignadas
    private function obtenerTareasAsignadas($tecnicoId) {
        $query = "SELECT * FROM asignacion_linea WHERE id_tecnico = :tecnicoId";
        $params = ['tecnicoId' => $tecnicoId];
        return Table::query($query, $params);
    }

    public function guardarDetalles()
    {
        try {
            // Obtener el encabezado de autorización
            $headers = getallheaders();
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

            // Obtener los datos de la solicitud
            $data = json_decode(file_get_contents('php://input'), true);
            $id_asignacion = $data['id_asignacion'] ?? null;
            $diagnostico = $data['diagnostico'] ?? null;
            $cambios = $data['cambios'] ?? null;
            $costo_chequeo = $data['costo_chequeo'] ?? null;
            $costo_reparacion = $data['costo_reparacion'] ?? null;

            // Verificar que todos los campos necesarios estén presentes
            if (!$diagnostico || !$cambios || !$costo_chequeo || !$costo_reparacion) {
                throw new \Exception('Faltan datos necesarios');
            }

            error_log("ID de asignación recibido: $id_asignacion");

            // Verificar si la asignación es de línea o física
            $asignacionLinea = $this->verificarAsignacion('asignacion_linea', $id_asignacion);
            $asignacionFisica = $this->verificarAsignacion('asignacion_fisica', $id_asignacion);
            
            if ($asignacionLinea) {
                // Insertar en la tabla detalle_asignacion_linea
                $insertQuery = "
                    INSERT INTO detalle_asignacion_linea (
                        id_asignacion_linea,    
                        diagnostico_linea,
                        cambios,
                        costo_chequeo,
                        costo_reparacion
                    ) VALUES (
                        :id_asignacion,
                        :diagnostico,
                        :cambios,
                        :costo_chequeo,
                        :costo_reparacion
                    )
                ";

                $params = [
                    'id_asignacion' => $id_asignacion,
                    'diagnostico' => $diagnostico,
                    'cambios' => $cambios,
                    'costo_chequeo' => $costo_chequeo,
                    'costo_reparacion' => $costo_reparacion
                ];

                Table::query($insertQuery, $params);

                // Actualizar el campo estado a "inactivo"
                $updateQuery = "UPDATE asignacion_linea SET estado = 'inactivo' WHERE id_asignacion_linea = :id_asignacion";
                $updateResult = Table::query($updateQuery, ['id_asignacion' => $id_asignacion]);

                // Log para verificar si la actualización fue exitosa
                error_log("Resultado de actualización estado en asignacion_linea: " . print_r($updateResult, true));
            } elseif ($asignacionFisica) {
                // Insertar en la tabla detalle_asignacion_fisica
                $insertQuery = "
                    INSERT INTO detalle_asignacion_fisica (
                        id_asignacion_fisica,
                        diagnostico_fisico,
                        cambios,
                        costo_chequeo,
                        costo_reparacion
                    ) VALUES (
                        :id_asignacion,
                        :diagnostico,
                        :cambios,
                        :costo_chequeo,
                        :costo_reparacion
                    )
                ";

                $params = [
                    'id_asignacion' => $id_asignacion,
                    'diagnostico' => $diagnostico,
                    'cambios' => $cambios,
                    'costo_chequeo' => $costo_chequeo,
                    'costo_reparacion' => $costo_reparacion
                ];

                Table::query($insertQuery, $params);
            } else {
                throw new \Exception('Asignación no encontrada');
            }

            // Obtener las tareas actualizadas
            $detalles = $this->obtenerTareasAsignadas($tecnicoId);

            // Preparar y devolver la respuesta de éxito
            $response = [
                'message' => 'Detalles guardados exitosamente',
                'detalles' => $detalles,
                'token' => $token
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
        } catch (\Exception $e) {
            // Capturar cualquier excepción y devolver un error genérico
            $response = [
                'message' => 'Error al guardar detalles: ' . $e->getMessage(),
                'error' => true,
                'detalles' => []
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
        }
    }

    private function verificarAsignacion($tabla, $id_asignacion) {
        // Definir correctamente el nombre de la columna basado en la tabla
        $columna_id = ($tabla === 'asignacion_linea') ? 'id_asignacion_linea' : 'id_asignacion_fisica';
        
        // Log para verificar los valores
        error_log("Verificando en tabla $tabla con columna $columna_id y id_asignacion $id_asignacion");
        
        // Consulta SQL para verificar la existencia de la asignación
        $query = "SELECT COUNT(*) AS count FROM $tabla WHERE $columna_id = :id_asignacion";
        $result = Table::query($query, ['id_asignacion' => $id_asignacion]);
        
        // Depuración para mostrar el resultado de la consulta
        if ($result) {
            error_log("Resultado de la consulta: " . print_r($result, true));
            if (isset($result[0]->count) && $result[0]->count > 0) {
                error_log("Asignación encontrada.");
                return true;
            } else {
                error_log("Asignación no encontrada.");
                return false;
            }
        } else {
            error_log("Error en la consulta o no se encontraron resultados.");
            return false;
        }
    }
}
