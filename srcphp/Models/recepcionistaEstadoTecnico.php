<?php
namespace proyecto\Models;

use PDO;
use Exception;

class recepcionistaEstadoTecnico extends Models
{
    protected $fillable = [
        "id_tecnico", "estado"
    ];
    protected $table = "tecnico";
    protected $id = "id_tecnico";

    public function __construct()
    {
        parent::__construct();
    }

    // Método data que será llamado por el router
    public function data()
    {
        try {
            // Construir la consulta SQL
            $sql = "
SELECT
	t.id_tecnico,
    CONCAT(p.nombre, ' ', COALESCE(p.apellido_paterno, ''), ' ', COALESCE(p.apellido_materno, '')) AS nombre_tecnico,
    COUNT(al.id_asignacion_linea) AS cantidad_citas,
    CASE 
        WHEN COUNT(al.id_asignacion_linea) >= 3 THEN 'Ocupado'
        ELSE 'Disponible'
    END AS estado
FROM 
    tecnico t
INNER JOIN 
    empleado e ON t.id_empleado = e.id_empleado
INNER JOIN 
    persona p ON e.id_persona = p.id_persona
LEFT JOIN 
    asignacion_linea al ON t.id_tecnico = al.id_tecnico
GROUP BY 
    t.id_tecnico, nombre_tecnico;
            ";

            // Preparar y ejecutar la consulta
            $stmt = self::$pdo->prepare($sql);
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
