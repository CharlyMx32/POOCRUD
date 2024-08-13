<?php

namespace proyecto\Models;

class AsignacionFisica extends Models
{
    
    protected $table = 'asignacion_fisica';
    protected $filleable = ['id_orden_fisica', 'id_tecnico'];
    public $id_orden_fisica;
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
