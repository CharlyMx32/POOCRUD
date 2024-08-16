<?php

namespace proyecto;

use DateTimeImmutable;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use proyecto\Response\Failure;

class Router
{
    public static function get($route, $callback)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            self::route($route, $callback);
        }
    }

    public static function post($route, $callback, $valid_token = false)
    {
        if ($valid_token) {
            self::is_token_valid();
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            self::route($route, $callback);
        }
    }

    public static function route($route, $callback)
    {
        self::headers();
        $request_url = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);
        $request_url = rtrim($request_url, '/');
        $request_url = strtok($request_url, '?');

        if ($route == "/404") {
            include_once __DIR__ . "/404.php"; // Asegúrate de tener un archivo 404.php
            exit();
        }

        $route_parts = explode('/', trim($route, '/'));
        $request_url_parts = explode('/', trim($request_url, '/'));

        if (count($route_parts) != count($request_url_parts)) {
            return;
        }

        $parameters = [];
        for ($i = 0; $i < count($route_parts); $i++) {
            $route_part = $route_parts[$i];
            if (preg_match("/^[$]/", $route_part)) {
                $route_part = ltrim($route_part, '$');
                $parameters[] = $request_url_parts[$i];
                $$route_part = $request_url_parts[$i];
            } elseif ($route_parts[$i] != $request_url_parts[$i]) {
                return;
            }
        }

        if (is_array($callback)) {
            try {
                $controller = new $callback[0]();
                call_user_func_array([$controller, $callback[1]], $parameters);
                exit();
            } catch (\Exception $e) {
                echo json_encode(["error" => $e->getMessage()]);
                exit();
            }
        } elseif (is_callable($callback)) {
            call_user_func_array($callback, $parameters);
            exit();
        } else {
            include_once __DIR__ . "/$callback";
            exit();
        }
    }

    public static function headers()
{
    $allowedOrigins = ['http://18.223.212.207/','*']; // Agrega aquí los orígenes permitidos
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

    if (in_array($origin, $allowedOrigins)) {
        header('Access-Control-Allow-Origin: ' . $origin);
    } else {
        header('Access-Control-Allow-Origin: *'); // Opción más permisiva, usa con precaución
    }

    header('Content-Type: application/json');
    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Credentials: true');
}


    public static function is_token_valid()
    {
        $secretKey = 'Carlos3223';
        $jwt = self::getBearerToken();
        try {
            $token = JWT::decode($jwt, new Key($secretKey, 'HS256'));
            $now = new DateTimeImmutable();
            if ($token->exp < $now->getTimestamp()) {
                header('HTTP/1.1 401 Unauthorized');
                exit();
            }
            return true;
        } catch (ExpiredException $e) {
            header('HTTP/1.1 401 Unauthorized');
            $r = new Failure(401, "Token expirado");
            $r->Send();
        } catch (\Exception $e) {
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(["error" => "Token inválido"]);
            exit();
        }
    }

    public static function getBearerToken()
    {
        $headers = self::getTokenRequest();
        if (!empty($headers) && preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
        return null;
    }

    public static function getTokenRequest()
    {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER['Authorization']);
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    public static function set_csrf()
    {
        session_start();
        if (!isset($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(50));
        }
        echo '<input type="hidden" name="csrf" value="' . $_SESSION['csrf'] . '">';
    }

    public static function is_csrf_valid()
    {
        session_start();
        return isset($_SESSION['csrf']) && isset($_POST['csrf']) && $_SESSION['csrf'] === $_POST['csrf'];
    }
}
