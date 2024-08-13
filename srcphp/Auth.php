<?php

namespace proyecto;

use Carbon\Carbon;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use proyecto\Models\User;

class Auth
{
    private $user;
    private static $secretKey = 'Carlos3223'; // Usa una clave secreta más segura

    public static function generateToken($data, $time = 3600): string
    {
        $t = Carbon::now()->timestamp + $time;
        $key = 'Carlos3223';
        $payload = ['exp' => $t, 'data' => $data];
        return JWT::encode($payload, $key, 'HS256');
    }

    /**
     * @return mixed
     */
    public static function getUser()
    {
        $secretKey = 'Carlos3223';
        $jwt = Router::getBearerToken();
        $token = JWT::decode($jwt, new key($secretKey, 'HS256'));
        return User::find($token->data[0]);
    }

    /**
     * @param mixed $user
     */
    public static function setUser($user): void
    {
        $session = new Session();
        $session->set('user', $user);

    }

    public function clearUser($user): void
    {
        $se = new Session();
        $se->remove("user");
    }

    public static function verifyToken($token): ?object
    {
        try {
            return JWT::decode($token, new Key(self::$secretKey, 'HS256'));
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function logout()
{
    // Destruir la sesión
    session_start();
    session_unset();
    session_destroy();

    // Opcional: invalidar el token JWT si es necesario
    // Esto se puede hacer guardando los tokens inválidos en una lista de "blacklist"
    // y verificando los tokens en cada solicitud.
}


}
