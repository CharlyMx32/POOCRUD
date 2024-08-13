<?php
// CitasController.php
namespace proyecto\Controller;

use proyecto\Models\AsignacionLinea;
use proyecto\Models\detalleServicioAdmin;
use proyecto\Models\TecnicoCitas;
use Exception;

class CitasController
{
    public function ATL() 
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $orderId = $data['orderId'] ?? null;
            $technicianId = $data['technicianId'] ?? null;
    
            if ($orderId === null || $technicianId === null) {
                echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
                return;
            }
    
            $ordenCita = detalleServicioAdmin::find($orderId);
            $tecnico = TecnicoCitas::find($technicianId);
    
            if (!$ordenCita || !$tecnico) {
                throw new Exception("Orden de cita o técnico no encontrados");
            }
    
            $asignacion = new AsignacionLinea();
            $asignacion->id_orden_cita = $orderId;
            $asignacion->id_tecnico = $technicianId;
            $asignacion->save();
    
            echo json_encode(['status' => 'success', 'message' => 'Técnico asignado correctamente']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }


    
    
}
