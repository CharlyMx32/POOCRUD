<?php

namespace proyecto\Models;
use proyecto\Response\Success;
use proyecto\Models\Table;

class TecOrden extends Models {

    protected $filable = [
        "id_orden_cita", "fecha_cita", "fecha_hora", "id_cliente", "producto",
        "problema", "fecha_registro", "asistencia", "cancelacion"
    ];
    protected $table = "orden_cita";

    public function ver() {
        $TableOrden = new Table();
        $allOrden = $TableOrden->query("
            -- Reemplaza `:id_tecnico` por el identificador del técnico deseado en tu aplicación.
SELECT
    CONCAT(p.nombre, ' ', p.apellido_paterno) AS nombre_cliente,
    oc.producto,
    oc.problema,
    'Orden de Cita' AS tipo_orden,
    NULL AS estado
FROM
    orden_cita oc
    INNER JOIN clientes c ON oc.id_cliente = c.id_cliente
    INNER JOIN persona p ON c.id_persona = p.id_persona

UNION

SELECT
    CONCAT(o_f.nombre, ' ', o_f.apellido_paterno) AS nombre_cliente,
    o_f.producto,
    o_f.problema,
    'Orden Física' AS tipo_orden,
    daf.estado
FROM
    orden_fisica o_f
    INNER JOIN asignacion_fisica af ON o_f.id_orden_fisica = af.id_orden_fisica
    INNER JOIN detalle_asignacion_fisica daf ON af.id_asignacion_fisica = daf.id_asignacion_fisica
    INNER JOIN tecnico t ON af.id_tecnico = t.id_tecnico
    INNER JOIN empleado e ON t.id_empleado = e.id_empleado
    INNER JOIN persona p ON e.id_persona = p.id_persona
WHERE
    af.id_tecnico = :id_tecnico

        ");

        $success = new Success($allOrden);
        return $success->Send();
    }

    // Si necesitas un método `ver`, lo agregas aquí
}
