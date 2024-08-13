<?php
namespace proyecto\Controller;

use proyecto\Models\detalleServicioFisicoAdmin;
use proyecto\Models\AsignacionFisica; // Asegúrate de que este sea el nombre correcto del modelo
use proyecto\Models\TecnicoCitas;
use Exception;

class CitasFisicasController
{
    public function ATF() 
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $orderId = $data['orderId'] ?? null;
            $technicianId = $data['technicianId'] ?? null;
    
            if ($orderId === null || $technicianId === null) {
                echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
                return;
            }
    
            $ordenCita = detalleServicioFisicoAdmin::find($orderId);
            $tecnico = TecnicoCitas::find($technicianId);
    
            if (!$ordenCita || !$tecnico) {
                throw new Exception("Orden de cita o técnico no encontrados");
            }
    
            $asignacion = new AsignacionFisica(); // Asegúrate de que este sea el nombre correcto del modelo
            $asignacion->id_orden_fisica = $orderId; // Asegúrate de que este campo esté en la tabla
            $asignacion->id_tecnico = $technicianId;
            $asignacion->save();
    
            echo json_encode(['status' => 'success', 'message' => 'Técnico asignado correctamente']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
