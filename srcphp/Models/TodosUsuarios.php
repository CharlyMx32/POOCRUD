<?php
namespace proyecto\Models;

use PDO;

class TodosUsuarios extends Models
{
    protected $table = "todos_los_usuarios";
    protected $id = "id_persona";

    public function __construct()
    {
        parent::__construct();
    }

    public function TodosLosUsuarios()
    {
        $stmt = self::$pdo->prepare("SELECT * FROM $this->table");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($results);
    }
}
