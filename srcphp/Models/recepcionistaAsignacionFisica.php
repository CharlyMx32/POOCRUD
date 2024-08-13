<?php

namespace proyecto\Models;
use PDO;
use Exception;

class recepcionistaAsignacionFisica extends Models
{
    protected $fillable = [
        'fecha', 'nombre', 'id_apellido_materno', 'apellido_paterno', 
        'contaacto', 'producto', 'problema', 'id_empleado'
    ];

    protected $table = "orden_fisica";
    protected $id = "id_orden_fisica";
    public function __construct()
    {
        parent::__construct();
    }

    public function data($filters)
    {
        try {
            $clientName = $filters['client_name'] ?? '';
            $technicianName = $filters['technician_name'] ?? '';

            $date = !empty($date) ? $date : '';

            $sql = "
    SELECT * FROM (
        SELECT 
            o_f.fecha, 
            o_f.producto, 
            o_f.problema,
            o_f.id_orden_fisica,
            CONCAT(o_f.nombre, ' ', COALESCE(o_f.apellido_paterno, ''), ' ', COALESCE(o_f.apellido_materno, '')) AS nombre_cliente,
            o_f.contacto AS contacto_cliente,
            CONCAT(pr.nombre, ' ', COALESCE(pr.apellido_paterno, ''), ' ', COALESCE(pr.apellido_materno, '')) AS nombre_recepcionista,
            COALESCE(CONCAT(pt.nombre, ' ', COALESCE(pt.apellido_paterno, ''), ' ', COALESCE(pt.apellido_materno, '')), 'Sin Asignar') AS nombre_tecnico,
            COALESCE(daf.estado, 'Sin Asignar') AS estatus_asignacion
        FROM orden_fisica o_f
        LEFT JOIN asignacion_fisica af ON o_f.id_orden_fisica = af.id_orden_fisica
        LEFT JOIN detalle_asignacion_fisica daf ON af.id_asignacion_fisica = daf.id_asignacion_fisica
        LEFT JOIN empleado e ON o_f.id_empleado = e.id_empleado
        LEFT JOIN persona pr ON e.id_persona = pr.id_persona
        LEFT JOIN tecnico t ON af.id_tecnico = t.id_tecnico
        LEFT JOIN empleado te ON t.id_empleado = te.id_empleado
        LEFT JOIN persona pt ON te.id_persona = pt.id_persona
    ) AS subquery
    WHERE nombre_tecnico = 'Sin Asignar'
            ";


            if (!empty($clientName)) {
                $sql .= " AND CONCAT(o_f.nombre, ' ', COALESCE(o_f.apellido_paterno, ''), ' ', COALESCE(o_f.apellido_materno, '')) LIKE :client_name";
            }
            if (!empty($technicianName)) {
                $sql .= " AND CONCAT(pt.nombre, ' ', COALESCE(pt.apellido_paterno, ''), ' ', COALESCE(pt.apellido_materno, '')) LIKE :technician_name";
            }

            $stmt = self::$pdo->prepare($sql);

            if (!empty($clientName)) {
                $stmt->bindValue(':client_name', "%$clientName%");
            }
            if (!empty($technicianName)) {
                $stmt->bindValue(':technician_name', "%$technicianName%");
            }

            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            header('Content-Type: application/json');
            echo json_encode($results);

        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}