<?php
namespace proyecto\Models;

class AsignacionLinea extends Models
{
    protected $table = 'asignacion_linea';
    protected $filleable = ['id_orden_cita', 'id_tecnico'];
    public $id_orden_cita;
    public $id_tecnico;

    public function __construct()
    {
        parent::__construct();
    }

    public function save()
    {
        $ob = [];
        foreach ($this->filleable as $campo) {
            $ob[$campo] = $this->$campo;
        }
        $this->create($ob);
    }
}
