<?php

namespace proyecto\Controller;
use proyecto\Response\Success;
use proyecto\Models\Table;

class AsignacionCitasLineaController
{
    public function asignacionl()
    {
        $JSONData = file_get_contents("php://input");
        $dataObject = json_decode($JSONData);
        
        if (!isset($dataObject->orderId) || !isset($dataObject->technicianId)) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }

        // Obtener los datos del objeto JSON
        $p_id_orden_cita = $dataObject->orderId;
        $p_id_tecnico = $dataObject->technicianId;

        // Llamar al procedimiento almacenado para registrar a la persona
        $query = "CALL AsignarCitaEnLinea(
            ' $p_id_orden_cita',
            '$p_id_tecnico'
        )";

        $resultados = Table::query($query);

        // Retornar la respuesta
        $r = new Success($resultados);
        return $r->send();
    }
}
