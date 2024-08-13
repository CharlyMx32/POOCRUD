<?php

namespace proyecto\Models;

use PDO;
use proyecto\Conexion;

class Table
{
    private static $pdo = null;

    public function __construct()
    {
        // Constructor vacío o puedes inicializar algo si es necesario
    }

    // Método para obtener la conexión PDO
    public static function getDataconexion()
    {
        if (self::$pdo === null) {
            $cc = new Conexion("hardwaresolutionss", "localhost", "root", "3223");
            self::$pdo = $cc->getPDO();
        }
        return self::$pdo;
    }

    // Método para ejecutar una consulta SQL con parámetros
    public static function query($query, $params = [])
    {
        $pdo = self::getDataconexion();

        $stmt = $pdo->prepare($query);

        // Ejecutar la consulta con los parámetros proporcionados
        $stmt->execute($params);

        // Recuperar los resultados
        $resultados = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $resultados;
    }
}
