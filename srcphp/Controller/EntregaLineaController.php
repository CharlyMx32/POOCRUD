<?php

namespace proyecto\Controller;
use proyecto\Response\Success;
use proyecto\Models\Table;

class EntregaLineaController
{
    public function entregal()
    {
        $JSONData = file_get_contents("php://input");
        $dataObject = json_decode($JSONData);
        
        if (!isset($dataObject->id_detalle_linea)) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }

        // Obtener los datos del objeto JSON
        $p_id_detalle_linea = $dataObject->id_detalle_linea;


        // Llamar al procedimiento almacenado para registrar a la persona
        $query = "CALL ActualizarEntregaLinea(
            ' $p_id_detalle_linea'
        )";

        $resultados = Table::query($query);

        // Retornar la respuesta
        $r = new Success($resultados);
        return $r->send();
    }
}
