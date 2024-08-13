<?php
namespace proyecto\Models;

use PDO;
use Exception;

class TecnicoCitas extends Models
{
    protected $fillable = [
        "id_tecnico", "estado", "id_empleado"
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
               
select t.id_tecnico,t.nombre_empleado as Tecnico, cct.citas_agendadas_fisica, citas_agendadas_linea, sum(cct.citas_agendadas_fisica + citas_agendadas_linea) as total_agendadas
	           from solo_tecnicos t 
               inner join conteo_citas_tecnico cct on cct.id_tecnico = t.id_tecnico
               group by id_tecnico ;
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
