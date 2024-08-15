<?php
namespace proyecto\Models;
use PDO;
use Exception;

class recepcionistaAsignacionFisico extends Models
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
                CONCAT(ofi.nombre, ' ', ofi.apellido_paterno, ' ', ofi.apellido_materno) AS Nombre_Cliente,
                ofi.producto AS Producto,
                daf.pago AS Pago,
                daf.uso_garantia AS Uso_Garantia
            FROM 
                detalle_asignacion_fisica daf
            INNER JOIN 
                asignacion_fisica af ON daf.id_asignacion_fisica = af.id_asignacion_fisica
            INNER JOIN 
                orden_fisica ofi ON af.id_orden_fisica = ofi.id_orden_fisica;
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