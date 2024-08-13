<?php

namespace proyecto\Controller;
use proyecto\Response\Success;
use proyecto\Models\Table;

class PagoFisicoController
{
    public function pagofisico()
    {
        $JSONData = file_get_contents("php://input");
        $dataObject = json_decode($JSONData);
        
        if (!isset($dataObject->id_detalle_fisico)) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }

        // Obtener los datos del objeto JSON
        $p_id_detalle_fisico = $dataObject->id_detalle_fisico;


        // Llamar al procedimiento almacenado para registrar a la persona
        $query = "CALL ModificarEstadoPagoFisico(
            ' $p_id_detalle_fisico'
        )";

        $resultados = Table::query($query);

        // Retornar la respuesta
        $r = new Success($resultados);
        return $r->send();
    }
}
