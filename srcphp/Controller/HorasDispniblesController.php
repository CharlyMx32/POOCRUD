<?php
// HorasDispniblesController.php
namespace proyecto\Controller;

use PDO;
use PDOException;
use proyecto\Models\Table;

class HorasDispniblesController 
{
    public function obtenerHorasOcupadas($fecha_cita) {
        try {
            $pdo = Table::getDataconexion(); // Obtén la conexión a la base de datos
            $stmt = $pdo->prepare("SELECT fecha_hora FROM orden_cita WHERE fecha_cita = ?");
            $stmt->execute([$fecha_cita]);
            $horas_ocupadas = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
            return $horas_ocupadas;
        } catch (PDOException $e) {
            error_log('Error al obtener horas ocupadas: ' . $e->getMessage());
            throw $e;
        }
    }
}

