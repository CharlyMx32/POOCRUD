<?php
namespace proyecto\Models;

use PDO;
use proyecto\Response\Success;

class UserModel extends Models {

    public function __construct()
    {
        parent::__construct();
    }

    public function createUser($correo, $contraseña) {
        $stmt = $this->pdo->prepare("INSERT INTO usuario (correo, contraseña) VALUES (?, ?)");
        $stmt->execute([$correo, $contraseña]);
        return $this->pdo->lastInsertId();
    }

    public static function auth($user, $contraseña) {
        // Asumimos que 'correo' es el nombre de la columna para el campo 'user'
        $stmt = self::$pdo->prepare("SELECT * FROM usuario WHERE correo = :user");
        $stmt->bindParam(':user', $user);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && password_verify($contraseña, $result['contraseña'])) {
            return new Success($result);
        } else {
            throw new \Exception("Credenciales incorrectas");
        }
    }
}
