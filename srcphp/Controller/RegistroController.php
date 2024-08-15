<?php

namespace proyecto\Controller;
use proyecto\Response\Success;
use proyecto\Models\Table;

class RegistroController
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

    // Verificar si el correo ya existe en la base de datos
    $query = "SELECT * FROM usuario WHERE correo = '$correo'";
    $resultado = Table::query($query);

    if (isset($resultado[0])) {
        echo json_encode(['success' => false, 'message' => 'El correo ya existe. ¿Desea iniciar sesión?']);
        return;
    }

    // Encriptar la contraseña en PHP
    $contraseñaHasheada = password_hash($contraseña, PASSWORD_BCRYPT);

    error_log($contraseñaHasheada);

    // Llamar al procedimiento almacenado para registrar a la persona
    $query = "CALL RegistrarUsuario(
        '$nombre', 
        '$apellido_materno', 
        '$apellido_paterno', 
        '$correo', 
        '$contraseñaHasheada', 
        $rol
    )";

    // Ejecutar la consulta
    $resultados = Table::query($query);

    // Retornar la respuesta
    $r = new Success($resultados);
    return $r->send();
}
}