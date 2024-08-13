<?php

namespace proyecto\Controller;

use proyecto\Response\Success;
use proyecto\Models\Table;

class RegistroAdminController
{
    public function registro()
    {
        // Obtener los datos del cuerpo de la solicitud
        $JSONData = file_get_contents("php://input");
        $dataObject = json_decode($JSONData);

        // Validar datos obligatorios
        if (!isset($dataObject->nombre) || !isset($dataObject->apellido_materno) || 
            !isset($dataObject->apellido_paterno) || !isset($dataObject->correo) || 
            !isset($dataObject->id_rol) || !isset($dataObject->contraseña)) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }

        $nombre = $dataObject->nombre;
        $apellido_materno = $dataObject->apellido_materno;
        $apellido_paterno = $dataObject->apellido_paterno;
        $correo = $dataObject->correo;
        $contraseña = $dataObject->contraseña;
        $id_rol = $dataObject->id_rol;

        $contraseñaHasheada = password_hash($contraseña, PASSWORD_BCRYPT);


        $validRoles = [1, 2, 3, 4]; // IDs válidos de roles
        if (!in_array($id_rol, $validRoles)) {
            echo json_encode(['success' => false, 'message' => 'Rol no válido']);
            return;
        }

        // Datos opcionales
        $calle = isset($dataObject->calle) ? $dataObject->calle : null;
        $colonia = isset($dataObject->colonia) ? $dataObject->colonia : null;
        $codigo_postal = isset($dataObject->codigo_postal) ? $dataObject->codigo_postal : null;
        $telefono = isset($dataObject->telefono) ? $dataObject->telefono : null;
        $rfc = isset($dataObject->rfc) ? $dataObject->rfc : null;
        $curp = isset($dataObject->curp) ? $dataObject->curp : null;
        $nss = isset($dataObject->nss) ? $dataObject->nss : null;

        // Preparar la llamada al procedimiento almacenado
        $query = "CALL AdminRegistrarUsuario(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        try {
            $pdo = Table::getDataconexion();
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                $correo, 
                $contraseñaHasheada,
                $nombre, 
                $apellido_paterno, 
                $apellido_materno, 
                $calle,
                $colonia,
                $codigo_postal,
                $telefono, 
                $rfc, 
                $curp, 
                $nss, 
                $id_rol
            ]);

            // Retornar la respuesta
            $r = new Success(['success' => true, 'message' => 'Usuario registrado con éxito']);
            return $r->send();
        } catch (\PDOException $e) {
            // Manejo más detallado del error
            echo json_encode([
                'success' => false, 
                'message' => 'Error al registrar usuario: ' . $e->getMessage() ]);
        }
    }
}
