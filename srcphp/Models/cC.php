<?php
namespace proyecto\Models;
use PDO;
use Exception;

class cC extends Models
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
    'Linea' AS Tipo_Asignacion,
    CONCAT(p.nombre, ' ', p.apellido_paterno, ' ', p.apellido_materno) AS NombreCliente,
    oc.producto AS Producto, 
    dal.uso_garantia AS Uso_Garantia
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

UNION 

SELECT 
    'Fisico' AS Tipo_Asignacion,
    CONCAT(ofi.nombre, ' ', ofi.apellido_paterno, ' ', ofi.apellido_materno) AS NombreCliente,
    ofi.producto AS Producto,
    daf.uso_garantia AS Uso_Garantia
FROM 
    detalle_asignacion_fisica daf
INNER JOIN 
    asignacion_fisica af ON daf.id_asignacion_fisica = af.id_asignacion_fisica
INNER JOIN 
    orden_fisica ofi ON af.id_orden_fisica = ofi.id_orden_fisica



ORDER BY 
    NombreCliente;
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
