<?php

namespace proyecto\Models; // Cambia esto si el espacio de nombres es diferente
use Exception;
use PDO;
class ClienteAA extends models
{

    protected $filleable = [
        'id_cliente', 'id_persona'
    ];

    protected $table = "clientes";
    protected $id = "id_cliente";

    public function __construct()
    {
        parent::__construct();
    }

    public function data()
    {
        try {
            // Construir la consulta SQL
            $sql = "
               select * from clientes;
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