<?php

namespace proyecto\Controller;
use proyecto\Response\Success;
use proyecto\Models\Table;

class AgendarFisicaController
{
    public function registro()
    {
        $JSONData = file_get_contents("php://input");
        $dataObject = json_decode($JSONData);
        
        if (!isset($dataObject->nombre) || !isset($dataObject->apellido_materno) || 
        !isset($dataObject->apellido_paterno) || !isset($dataObject->contacto) || 
        !isset($dataObject->producto) || !isset($dataObject->problema)) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }

        // Obtener los datos del objeto JSON
        $nombre = $dataObject->nombre;
        $apellido_materno = $dataObject->apellido_materno;
        $apellido_paterno = $dataObject->apellido_paterno;
        $contacto = $dataObject->contacto;
        $producto = $dataObject->producto ;
        $problema = $dataObject->problema;

        // Llamar al procedimiento almacenado para registrar a la persona
        $query = "CALL AgregarClienteOrdenFisica(
            '$nombre', 
            '$apellido_materno', 
            '$apellido_paterno', 
            '$contacto', 
            '$problema', 
            '$producto'
        )";

        $resultados = Table::query($query);

        // Retornar la respuesta
        $r = new Success($resultados);
        return $r->send();
    }
}