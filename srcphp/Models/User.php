<?php

namespace proyecto\Models;

use PDO;

class User extends Models
{
    protected $filleable = ['nombre', 'apellido_materno', 'apellido_paterno', 'correo', 'contraseña', 'id_rol'];
    protected $table = 'usuario'; // Tabla específica para User
    protected $id = 'id_usuario'; // Clave primaria para User

    public function __construct()
    {
        parent::__construct();
        $this->table = 'usuario'; // Asigna la tabla específica
        $this->id = 'id_usuario'; // Asigna el campo de ID específico
    }

    public function registerUser($nombre, $apellido_materno, $apellido_paterno, $correo, $contraseña, $id_rol)
    {
        try {
            // Iniciar una transacción
            $this->pdo->beginTransaction();

            // Insertar en tabla persona
            $stmt = $this->pdo->prepare("INSERT INTO persona (nombre, apellido_materno, apellido_paterno) VALUES (:nombre, :apellido_materno, :apellido_paterno)");
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':apellido_materno', $apellido_materno);
            $stmt->bindParam(':apellido_paterno', $apellido_paterno);
            $stmt->execute();
            $last_id_persona = $this->pdo->lastInsertId();

            // Insertar en tabla usuario
            $stmt = $this->pdo->prepare("INSERT INTO usuario (correo, contraseña, id_persona) VALUES (:correo, :contraseña, :id_persona)");
            $stmt->bindParam(':correo', $correo);
            $stmt->bindParam(':contraseña', $contraseña); // Asegúrate de encriptar la contraseña antes de almacenarla
            $stmt->bindParam(':id_persona', $last_id_persona, PDO::PARAM_INT);
            $stmt->execute();
            $last_id_usuario = $this->pdo->lastInsertId();

            // Insertar en tabla rol_usuario
            $stmt = $this->pdo->prepare("INSERT INTO rol_usuario (id_usuario, id_rol) VALUES (:id_usuario, :id_rol)");
            $stmt->bindParam(':id_usuario', $last_id_usuario, PDO::PARAM_INT);
            $stmt->bindParam(':id_rol', $id_rol, PDO::PARAM_INT);
            $stmt->execute();

            // Confirmar la transacción
            $this->pdo->commit();

            return $last_id_usuario;
        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            $this->pdo->rollBack();
            echo json_encode(['error' => $e->getMessage()]);
            return false;
        }
    }
}
