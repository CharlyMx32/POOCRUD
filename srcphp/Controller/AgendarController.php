<?php
// AgendarController.php
namespace proyecto\Controller;

use proyecto\Response\Success;
use proyecto\Response\Error;
use proyecto\Models\Table;
use proyecto\Auth;
use PDOException;
use PDO;

class AgendarController
{
    public function agendar()
    {
        $headers = getallheaders();
        $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : null;

        if (!$authHeader) {
            $error = new Error('Token de autenticación faltante');
            return $error->send();
        }

        $token = str_replace('Bearer ', '', $authHeader);
        $tokenData = Auth::verifyToken($token);
        if (!$tokenData || !isset($tokenData->data->clienteId)) {
            $error = new Error('Token de autenticación inválido o clienteId faltante');
            return $error->send();
        }

        $id_cliente = $tokenData->data->clienteId;
        $JSONData = file_get_contents("php://input");
        $dataObject = json_decode($JSONData);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $error = new Error('Error en el formato JSON');
            return $error->send();
        }

        if (
            !isset($dataObject->producto) || !isset($dataObject->problema) ||
            !isset($dataObject->fecha_cita) || !isset($dataObject->fecha_hora)
        ) {
            $error = new Error('Datos incompletos');
            return $error->send();
        }

        $producto = $dataObject->producto;
        $problema = $dataObject->problema;
        $fecha_cita = $dataObject->fecha_cita;
        $fecha_hora = $dataObject->fecha_hora;

        // Verifica horas ocupadas
        $horasController = new HorasDispniblesController();
        $horas_ocupadas = $horasController->obtenerHorasOcupadas($fecha_cita);

        if (in_array($fecha_hora, $horas_ocupadas)) {
            $error = new Error('La hora seleccionada ya está ocupada en la fecha indicada.');
            return $error->send();
        }

        $query = "CALL AgendarCitaEnLinea(?, ?, ?, ?, ?)";

        try {
            $pdo = Table::getDataconexion();
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                $id_cliente,
                $producto,
                $problema,
                $fecha_cita,
                $fecha_hora
            ]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $id_orden_cita = $result['id_orden_cita'];

            $r = new Success([
                'success' => true,
                'message' => 'Cita agendada con éxito',
                'id_orden_cita' => $id_orden_cita
            ]);
            return $r->send();
        } catch (PDOException $e) {
            error_log('Error al agendar la cita: ' . $e->getMessage());
            $error = new Error('Error al agendar la cita: ' . $e->getMessage());
            return $error->send();
        }
    }
}
