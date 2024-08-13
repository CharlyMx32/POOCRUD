<?php

namespace proyecto\Models;

class Persona extends Models {
    protected $table = 'persona';
    protected $filleable = ['nombre', 'apellido_paterno', 'apellido_materno', 'telefono', 'id_usuario'];
    protected $id = 'id_persona';


    public function __construct() {
        parent::__construct();
    }

    public function createPersona($data) {
        $this->nombre = $data['nombre'];
        $this->apellido_paterno = $data['apellido_paterno'];
        $this->apellido_materno = $data['apellido_materno'];
        $this->telefono = $data['telefono'];
        $this->id_usuario = $data['id_usuario'];
        $this->save();
    }
}
?>
