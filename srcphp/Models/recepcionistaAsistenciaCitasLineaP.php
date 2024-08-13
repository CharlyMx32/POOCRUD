<?php
namespace proyecto\Models;
use PDO;
use Exception;

class recepcionistaAsistenciaCitasLineaP extends Models
{
    public function __construct()
    {
        parent::__construct();
    }

    public function data($filterss)
    {
        try {
            $clientName = $filterss['client_name'] ?? '';

            $sql = "
      SELECT 
    oc.id_orden_cita,
    CONCAT(p.nombre, ' ', p.apellido_paterno, ' ', p.apellido_materno) AS Nombre_Cliente,
    oc.fecha_cita AS Fecha,
    oc.fecha_hora AS Hora,
    oc.producto AS Producto
FROM 
    orden_cita oc
INNER JOIN 
    clientes c ON oc.id_cliente = c.id_cliente
INNER JOIN 
    persona p ON c.id_persona = p.id_persona
WHERE 
    oc.asistencia = 'Pendiente';

            ";

            if (!empty($clientName)) {
                $sql .= " AND CONCAT(p.nombre, ' ', COALESCE(p.apellido_paterno, ''), ' ', COALESCE(p.apellido_materno, '')) LIKE :client_name";
            }

            $stmt = self::$pdo->prepare($sql);

            if (!empty($clientName)) {
                $stmt->bindValue(':client_name', "%$clientName%");
            }

            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            header('Content-Type: application/json');
            echo json_encode($results);

        } catch (Exception $e) {
            // Manejo de errores
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
