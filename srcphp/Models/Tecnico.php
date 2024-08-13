<?php

namespace proyecto\Models;
use PDO;
class Tecnico extends Models
{
    protected $filleable = [
        'tipo_tecnico', 'estado', 'id_empleado'
    ];

    protected $table = "tecnico";
    protected $id = "id_tecnico";

    public function __construct()
    {
        parent::__construct();
    }

    public function updateEstado($id, $estado)
    {
        $stmt = self::$pdo->prepare("UPDATE $this->table SET estado = :estado WHERE $this->id = :id");
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getTecnicosDisponibles()
    {
        $stmt = self::$pdo->prepare("SELECT * FROM $this->table WHERE estado = 'libre'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, get_called_class());
    }
    
}
