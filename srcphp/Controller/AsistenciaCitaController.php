<?php

namespace proyecto\Controller;
use proyecto\Response\Success;
use proyecto\Models\Table;

class AsistenciaCitaController
{
    public function asistencia()
    {
        $JSONData = file_get_contents("php://input");
        $dataObject = json_decode($JSONData);
        
        if (!isset($dataObject->id_orden_cita)) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos o incorrectos']);
            return;
        }

        // Obtener los datos del objeto JSON
        $id_orden_cita = $dataObject->id_orden_cita;

        // Llamar al procedimiento almacenado para registrar la asistencia
        $query = "CALL RegistrarAsistencia('$id_orden_cita')";

        $resultados = Table::query($query);

        // Verificar si la actualización fue exitosa
        if ($resultados) {
            $r = new Success(['success' => true, 'message' => 'Asistencia registrada correctamente']);
        } else {
            $r = new Success(['success' => false, 'message' => 'No se pudo registrar la asistencia']);
        }
        return $r->send();
    }
}
