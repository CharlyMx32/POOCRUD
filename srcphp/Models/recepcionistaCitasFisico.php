<?php
namespace proyecto\Models;
use PDO;
use Exception;

class recepcionistaCitasFisico extends Models
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
            daf.id_detalle_fisico,
         CONCAT(ofi.nombre, ' ', ofi.apellido_paterno, ' ', ofi.apellido_materno) AS Nombre_Cliente,
                ofi.contacto AS Contacto,
                ofi.producto AS Producto,
                daf.costo_chequeo AS CostoChequeo,
                daf.costo_reparacion AS CostoReparacion,
            SUM(daf.costo_chequeo + daf.costo_reparacion) AS CostoTotal,
                daf.pago AS Pago,
                daf.uso_garantia AS Uso_Garantia
            FROM 
                detalle_asignacion_fisica daf
            INNER JOIN 
                asignacion_fisica af ON daf.id_asignacion_fisica = af.id_asignacion_fisica
            INNER JOIN 
                orden_fisica ofi ON af.id_orden_fisica = ofi.id_orden_fisica
            GROUP BY 
                ofi.nombre, ofi.apellido_paterno, ofi.apellido_materno, ofi.contacto, ofi.producto, daf.pago, daf.costo_chequeo, daf.costo_reparacion, daf.uso_garantia;

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