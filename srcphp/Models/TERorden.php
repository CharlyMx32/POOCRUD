<?php

namespace proyecto\Models;

use proyecto\Response\Success;
use proyecto\Models\Table;

class TERorden extends Models
{
    protected $fillable = [
        "id_orden_cita",
        "fecha_cita",
        "fecha_hora",
        "id_cliente",
        "producto",
        "problema",
        "seguimiento",
    ];

    protected $table = "orden_cita";

    // Método para obtener y mostrar órdenes completadas
    public function mirar($id_tecnico)
    {
        // Crear una instancia de la clase Table
        $TableOrden = new Table();

        // Consulta SQL para obtener órdenes completadas, incluyendo el tipo de orden
        $query = "
            SELECT
                CONCAT(p.nombre, ' ', p.apellido_paterno) AS nombre_cliente,
                oc.producto,
                oc.problema,
                'Orden de Cita' AS tipo_orden
            FROM
                orden_cita oc
                INNER JOIN clientes c ON oc.id_cliente = c.id_cliente
                INNER JOIN persona p ON c.id_persona = p.id_persona
                INNER JOIN asignacion_linea al ON oc.id_orden_cita = al.id_orden_cita
                INNER JOIN detalle_asignacion_linea dal ON al.id_asignacion_linea = dal.id_asignacion_linea
            WHERE
                dal.seguimiento = 'Completado'
                AND al.id_tecnico = :id_tecnico

            UNION

            SELECT
                CONCAT(p.nombre, ' ', p.apellido_paterno) AS nombre_cliente,
                o_f.producto,
                o_f.problema,
                'Orden Física' AS tipo_orden
            FROM
                orden_fisica o_f
                INNER JOIN asignacion_fisica af ON o_f.id_orden_fisica = af.id_orden_fisica
                INNER JOIN detalle_asignacion_fisica daf ON af.id_asignacion_fisica = daf.id_asignacion_fisica
                INNER JOIN tecnico t ON af.id_tecnico = t.id_tecnico
                INNER JOIN empleado e ON t.id_empleado = e.id_empleado
                INNER JOIN persona p ON e.id_persona = p.id_persona
            WHERE
                daf.seguimiento = 'Completado'
                AND af.id_tecnico = :id_tecnico
        ";

        // Ejecutar la consulta y obtener los resultados
        $allOrden = $TableOrden->query($query, ['id_tecnico' => $id_tecnico]);

        if ($allOrden === false) {
            $success = new Success([]);
            return $success->Send();
        }

        $success = new Success($allOrden);
        return $success->Send();
    }
}
