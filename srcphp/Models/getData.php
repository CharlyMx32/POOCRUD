<?php
namespace proyecto\Models;

use PDO;

class getData extends Models
{
    protected $fillable = ["nombre", "telefono"];
    protected $table = "persona";

    public function __construct()
    {
        parent::__construct();
    }

    // Método data que será llamado por el router
    public function data()
    {
        // Implementa la lógica para recuperar datos de la base de datos
        $stmt = self::$pdo->query("SELECT * FROM " . $this->table);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Devuelve los resultados como JSON
        header('Content-Type: application/json');
        echo json_encode($results);
    }
}



