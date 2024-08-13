<?php

namespace proyecto\Models;

class RolUsuario extends Models {
    protected $table = 'rol_usuario';
    protected $filleable = ['id_rol', 'id_usuario'];
    protected $id = 'id_rol_usuario';

    public $id_rol;
    public $id_usuario;

    public function __construct() {
        parent::__construct();
    }

    public function assignRole($data) {
        $this->id_rol = $data['id_rol'];
        $this->id_usuario = $data['id_usuario'];
        $this->save();
    }
}
