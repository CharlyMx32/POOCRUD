<?php
namespace proyecto\Models;

use PDO;
use Exception;
use PDOException;

class DetalleServicioAdmin extends Models
{
    
    protected $fillable = [
        'fecha_cita', 'fecha_hora', 'id_cliente', 'producto', 
        'problema', 'fecha_registro_cita', 'asistencia', 'cancelacion'
    ];

    protected $table = "orden_cita";
    protected $id = "id_orden_cita";

    public function __construct()
    {
        parent::__construct();
    }

    // Método data que será llamado por el router
    public function data($filters)
    {
        try {
            $clientName = $filters['client_name'] ?? '';
            $technicianName = $filters['technician_name'] ?? '';


            // Construir la consulta SQL
            $sql = "
                SELECT concat(oc.fecha_cita,' a las ', oc.fecha_hora) as diaHora, oc.producto, oc.problema, oc.fecha_registro_cita,oc.id_orden_cita,
                    CONCAT(p.nombre, ' ', COALESCE(p.apellido_paterno, ''), ' ', COALESCE(p.apellido_materno, '')) AS nombre_cliente,
                    COALESCE(CONCAT(pt.nombre, ' ', COALESCE(pt.apellido_paterno, ''), ' ', COALESCE(pt.apellido_materno, '')), 'Sin Asignar') AS nombre_tecnico,
                    COALESCE(dal.estado_del_pago, 'Sin Asignar') AS estatus_cita
                FROM orden_cita oc
                LEFT JOIN clientes c ON oc.id_cliente = c.id_cliente
                LEFT JOIN persona p ON c.id_persona = p.id_persona
                LEFT JOIN asignacion_linea al ON oc.id_orden_cita = al.id_orden_cita
                LEFT JOIN tecnico t ON al.id_tecnico = t.id_tecnico
                LEFT JOIN empleado e ON t.id_empleado = e.id_empleado
                LEFT JOIN persona pt ON e.id_persona = pt.id_persona
                LEFT JOIN detalle_asignacion_linea dal ON al.id_asignacion_linea = dal.id_asignacion_linea
            ";

            if (!empty($clientName)) {
                $sql .= " AND CONCAT(p.nombre, ' ', COALESCE(p.apellido_paterno, ''), ' ', COALESCE(p.apellido_materno, '')) LIKE :client_name";
            }
            if (!empty($technicianName)) {
                $sql .= " AND COALESCE(CONCAT(pt.nombre, ' ', COALESCE(pt.apellido_paterno, ''), ' ', COALESCE(pt.apellido_materno, '')), 'Sin Asignar') LIKE :technician_name";
            }

            // Preparar y ejecutar la consulta
            $stmt = self::$pdo->prepare($sql);

            if (!empty($clientName)) {
                $stmt->bindValue(':client_name', "%$clientName%");
            }
            if (!empty($technicianName)) {
                $stmt->bindValue(':technician_name', "%$technicianName%");
            }

            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Devuelve los resultados como JSON
            header('Content-Type: application/json');
            echo json_encode($results);

        } catch (Exception $e) {
            // Manejo de errores
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        }catch (PDOException $e) {
            // Manejo de errores de PDO
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        } catch (Exception $e) {
            // Manejo de errores generales
            header('Content-Type: application/json');
            echo json_encode(['error' => 'General error: ' . $e->getMessage()]);
        }
        
    }
}