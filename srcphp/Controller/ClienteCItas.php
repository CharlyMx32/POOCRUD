<?php
namespace proyecto\Models;

use PDO;
use Exception;

class ClienteCitas extends Models
{
    protected $table = "orden_cita";
    protected $id = "id_orden_cita";

    public function __construct()
    {
        parent::__construct();
    }

    // Método para obtener los detalles de la cita de un cliente específico
    public function getCitasCliente($clienteId)
    {
        try {
            // Construir la consulta SQL
            $sql = "
                SELECT 
                    oc.producto,
                    oc.problema,
                    CONCAT(p.nombre, ' ', p.apellido_paterno, ' ', p.apellido_materno) AS nombre_tecnico,
                    dal.costo_chequeo,
                    dal.costo_reparacion,
                    (dal.costo_chequeo + dal.costo_reparacion) AS total,
                    dal.seguimiento,
                    dal.entregado,
                    dal.tiempo_garantia,
                    dal.uso_garantia
                FROM 
                    orden_cita oc
                JOIN 
                    asignacion_linea al ON oc.id_orden_cita = al.id_orden_cita
                JOIN 
                    detalle_asignacion_linea dal ON al.id_asignacion_linea = dal.id_asignacion_linea
                JOIN 
                    tecnico t ON al.id_tecnico = t.id_tecnico
                JOIN 
                    empleado e ON t.id_empleado = e.id_empleado
                JOIN 
                    persona p ON e.id_persona = p.id_persona
                WHERE 
                    oc.id_cliente = :id_cliente;
            ";

            // Preparar y ejecutar la consulta
            $stmt = self::$pdo->prepare($sql);
            $stmt->bindParam(':id_cliente', $clienteId, PDO::PARAM_INT);
            $stmt->execute();

            // Obtener los resultados
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Establecer el encabezado de tipo de contenido
            header('Content-Type: application/json');

            // Devolver los resultados como JSON
            echo json_encode($data);

        } catch (Exception $e) {
            // Manejo de errores
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
