<?php

namespace proyecto\Models;

class OrdenCita extends Models
{
    protected $fillable = [
        'fecha_cita', 'fecha_hora', 'id_cliente', 'producto', 
        'problema', 'fecha_registro', 'asistencia', 'cancelacion'
    ];

    protected $table = "orden_cita";
    protected $id = "id_orden_cita";

    public function __construct()
    {
        parent::__construct();
    }

    public function updateAsistencia($id, $asistencia)
    {
        $stmt = self::$pdo->prepare("UPDATE $this->table SET asistencia = :asistencia WHERE $this->id = :id");
        $stmt->bindParam(':asistencia', $asistencia);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function updateCancelacion($id, $cancelacion)
    {
        $stmt = self::$pdo->prepare("UPDATE $this->table SET cancelacion = :cancelacion WHERE $this->id = :id");
        $stmt->bindParam(':cancelacion', $cancelacion);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function mostrarOrden()
    {
        return "Mostrando orden";
    }
}
