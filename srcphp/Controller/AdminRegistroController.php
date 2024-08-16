<?php

namespace proyecto\Controller;
use proyecto\Response\Success;
use proyecto\Models\Table;

class RegistroAdminController
{
    public function registro()
    {
        $JSONData = file_get_contents("php://input");
        $dataObject = json_decode($JSONData);
        
        if (!isset($dataObject->nombre) || !isset($dataObject->apellido_materno) || 
        !isset($dataObject->apellido_paterno) || !isset($dataObject->correo) || 
        !isset($dataObject->id_rol) || !isset($dataObject->contraseña)) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }

        // Obtener los datos del objeto JSON
        $nombre = $dataObject->nombre;
        $apellido_materno = $dataObject->apellido_materno;
        $apellido_paterno = $dataObject->apellido_paterno;
        $rol = $dataObject->id_rol;
        $correo = $dataObject->correo;
        $contraseña = $dataObject->contraseña;

        // Encriptar la contraseña en PHP
        $contraseñaHasheada = password_hash($contraseña, PASSWORD_BCRYPT);

        // Datos adicionales para roles que no sean "Cliente"
        $direccion = isset($dataObject->direccion) ? $dataObject->direccion : null;
        $telefono = isset($dataObject->telefono) ? $dataObject->telefono : null;
        $rfc = isset($dataObject->rfc) ? $dataObject->rfc : null;
        $curp = isset($dataObject->curp) ? $dataObject->curp : null;
        $nss = isset($dataObject->nss) ? $dataObject->nss : null;

        // Llamar al procedimiento almacenado para registrar al usuario
        $query = "CALL RegistrarUsuario(
            '$nombre', 
            '$apellido_materno', 
            '$apellido_paterno', 
            '$correo', 
            '$contraseñaHasheada', 
            $rol, 
            '$direccion',
            '$telefono',
            '$rfc',
            '$curp',
            '$nss'
        )";

        // Ejecutar la consulta
        $resultados = Table::query($query);

        // Retornar la respuesta
        $r = new Success($resultados);
        return $r->send();
    }
}
