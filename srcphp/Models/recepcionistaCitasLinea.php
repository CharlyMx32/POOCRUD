<?php
namespace proyecto\Models;
use PDO;
use Exception;

class recepcionistaCitasLinea extends Models
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
    dal.id_detalle_linea,
    CONCAT(p.nombre, ' ', p.apellido_paterno, ' ', p.apellido_materno) AS Nombre_Cliente,
    oc.producto AS Producto, 
    dal.entregado AS Entregado,
    dal.costo_chequeo AS CostoChequeo,
    dal.costo_reparacion AS CostoReparacion,
    SUM(dal.costo_chequeo + dal.costo_reparacion) AS CostoTotal,
    dal.pago AS Pago,
    dal.uso_garantia AS Garantia
FROM 
    detalle_asignacion_linea dal
INNER JOIN 
    asignacion_linea al ON dal.id_asignacion_linea = al.id_asignacion_linea
INNER JOIN 
    orden_cita oc ON al.id_orden_cita = oc.id_orden_cita
INNER JOIN 
    clientes c ON oc.id_cliente = c.id_cliente
INNER JOIN 
    persona p ON c.id_persona = p.id_persona
GROUP BY 
    p.id_persona, oc.id_orden_cita, dal.id_asignacion_linea;

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
