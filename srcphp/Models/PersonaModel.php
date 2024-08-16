<?php

namespace proyecto\Models;

use PDO;

class PersonaModel extends Models
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Crea una nueva persona en la base de datos.
     * 
     * @param string $nombre El nombre de la persona.
     * @param string $apellidoPaterno El apellido paterno de la persona.
     * @param string $apellidoMaterno El apellido materno de la persona.
     * @param string $telefono El teléfono de la persona.
     * 
     * @return int El ID de la persona recién creada.
     * @throws \Exception Si ocurre un error al ejecutar la consulta.
     */
    public function createPersona($nombre, $apellido_paterno, $apellido_materno, $telefono) {
        try {
            $stmt = self::$pdo->prepare("INSERT INTO persona (nombre, apellido_paterno, apellido_materno, telefono) VALUES (:nombre, :apellido_paterno, :apellido_materno, :telefono)");
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindParam(':apellido_paterno', $apellido_paterno, PDO::PARAM_STR);
            $stmt->bindParam(':apellido_materno', $apellido_materno, PDO::PARAM_STR);
            $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
            $stmt->execute();
            return self::$pdo->lastInsertId();
        } catch (\PDOException $e) {
            // Manejo de errores: puedes registrar el error o lanzar una excepción
            throw new \Exception("Error al crear la persona: " . $e->getMessage());
        }
    }
}
