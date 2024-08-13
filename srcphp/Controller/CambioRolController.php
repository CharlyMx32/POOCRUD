<?php

namespace proyecto\Controller;

use proyecto\Models\Models;
use proyecto\Models\TodosUsuarios;
use PDOException;
use proyecto\Response\Success;
use proyecto\Models\Table;
use PDO;


class CambioRolController extends Models
{

    public function updateUserRole()
    {
        // Obtener los datos enviados en la solicitud
        $data = json_decode(file_get_contents("php://input"), true);

        // Validar los datos recibidos
        if (isset($data['id']) && isset($data['rol'])) {
            $userId = $data['id'];
            $newRole = $data['rol'];

            try {
                // Iniciar una transacción
                self::$pdo->beginTransaction();

                // Obtener el id del rol según el nombre del rol
                $stmt = self::$pdo->prepare("SELECT id_rol FROM rol WHERE nombre_rol = :nombre_rol");
                $stmt->bindParam(':nombre_rol', $newRole);
                $stmt->execute();
                $rolId = $stmt->fetchColumn();

                // Si no se encuentra el rol, lanzar un error
                if (!$rolId) {
                    throw new PDOException("Rol no encontrado");
                }

                // Actualizar el rol del usuario en la tabla rol_usuario
                $stmt = self::$pdo->prepare("UPDATE rol_usuario SET id_rol = :id_rol WHERE id_usuario = :id_usuario");
                $stmt->bindParam(':id_rol', $rolId);
                $stmt->bindParam(':id_usuario', $userId);
                $stmt->execute();

                // Confirmar la transacción
                self::$pdo->commit();

                // Respuesta exitosa
                header('Content-Type: application/json');
                echo json_encode(["message" => "Rol actualizado exitosamente"]);
            } catch (PDOException $e) {
                // Revertir la transacción en caso de error
                self::$pdo->rollBack();

                // Responder con el error
                header('Content-Type: application/json', true, 500);
                echo json_encode(["error" => $e->getMessage()]);
            }
        } else {
            // Responder con un error si los datos son inválidos
            header('Content-Type: application/json', true, 400);
            echo json_encode(["error" => "Datos inválidos"]);
        }
    }
}
