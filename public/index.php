<?php

namespace proyecto;

require("../vendor/autoload.php");

use proyecto\Models\OrdenCita;

use proyecto\Models\recepcionistaEstadoTecnico;
use proyecto\Models\recepcionistaAsignacionFisica;
use proyecto\Models\recepcionistaAsignacionLinea;
use proyecto\Controller\AsignacionCitaFisicaController;
use proyecto\Controller\AsignacionCitasLineaController;
use proyecto\Controller\PagoLineaController;
use proyecto\Controller\PagoFisicoController;
use proyecto\Controller\GarantiaFisicoController;
use proyecto\Controller\GarantiaLineaController;
use proyecto\Controller\EntregaLineaController;
use proyecto\Controller\EntregaFisicoController;
use proyecto\Controller\AsistenciaCitaController;
use proyecto\Models\TecnicoCitas;
use proyecto\Models\DetalleServicioFisicoAdmin;
use proyecto\Controller\AgendarFisicaController;
use proyecto\Models\DetalleServicioAdmin;
use proyecto\Models\cC;
use proyecto\Router;

// mar 
use proyecto\Models\recepcionistaCitasLinea;
use proyecto\Models\recepcionistaCitasFisico;
use proyecto\Models\recepcionistaAsistenciaCitasLineaP;

use proyecto\Models\AsignacionFisica;
use proyecto\Models\AsignacionLinea;

// Configurar encabezados
Router::headers();
require_once __DIR__ . '/../vendor/autoload.php'; // Asegúrate de incluir el autoload si usas Composer
require_once __DIR__ . '/../srcphp/Router.php'; // Incluye el archivo del enrutador

// Definir rutas
Router::post('/AF', [AsignacionFisica::class, "save"]);
Router::post('/AL', [AsignacionLinea::class, "save"]);
Router::post('/CitasFisicas',[AgendarFisicaController::class, "registro"]);
Router::get('/ordenCita', [OrdenCita::class, "mostrarOrden"]);
Router::get('/citasTecnico', [TecnicoCitas::class, 'data']);

Router::get('/DSA', function() {
    $detalleServicioAdmin = new DetalleServicioAdmin();

    $filters = [
        'fecha' => $_GET['fecha'] ?? '',
        'client_name' => $_GET['client_name'] ?? '',
        'technician_name' => $_GET['technician_name'] ?? ''
    ];

    $detalleServicioAdmin->data($filters);
});

Router::get('/DSFA', function() {
    $detalleServicioFisicoAdmin = new DetalleServicioFisicoAdmin();

    $filters = [
        'fecha' => $_GET['fecha'] ?? '',
        'client_name' => $_GET['client_name'] ?? '',
        'technician_name' => $_GET['technician_name'] ?? ''
    ];

    $detalleServicioFisicoAdmin->data($filters);
});


// mar
Router::get('/AsistenciaCitas', function() {
    $cuentasClientes = new recepcionistaAsistenciaCitasLineaP(); 

    $filterss = [   
        'client_name' => $_GET['client_name'] ?? '',
    ];

    $cuentasClientes->data($filterss);
});

Router::get('/CitasFisico', function() {
    $cuentasClientes = new recepcionistaCitasFisico();

    $filterss = [
        'client_name' => $_GET['client_name'] ?? '',
    ];

    $cuentasClientes->data($filterss);
});

Router::get('/CitasLinea', function() {
    $cuentasClientes = new recepcionistaCitasLinea();

    $filterss = [
        'client_name' => $_GET['client_name'] ?? '',
    ];

    $cuentasClientes->data($filterss);
});

Router::get('/estadotec', function() {
    $cuentasClientes = new recepcionistaEstadoTecnico();

    $filterss = [
        'client_name' => $_GET['client_name'] ?? '',
    ];

    $cuentasClientes->data($filterss);
});
    
Router::get('/RAsignacionLinea', function() {
    $cuentasClientes = new recepcionistaAsignacionLinea(); 
    $filterss = [   
        'client_name' => $_GET['client_name'] ?? '',
    ];
    $cuentasClientes->data($filterss); // Añadido
});

Router::get('/RAsignacionFisica', function() {
    $cuentasClientes = new recepcionistaAsignacionFisica(); 
    $filterss = [   
        'client_name' => $_GET['client_name'] ?? '',
    ]; 
    $cuentasClientes->data($filterss); // Añadido
});

Router::post('/citasfisicas',[AgendarFisicaController::class, "registro"]);
Router::post('/asistencia', [AsistenciaCitaController::class, "asistencia"]);
Router::post('/asignacionl', [AsignacionCitasLineaController::class, 'asignacionl']);
Router::post('/asignacionf', [AsignacionCitaFisicaController::class, 'asignacionf']);
Router::post('/pagolinea', [PagoLineaController::class, 'pagolinea']);
Router::post('/garantialinea', [GarantiaLineaController::class, 'garantialinea']);
Router::post('/pagofisico', [PagoFisicoController::class, 'pagofisico']);
Router::post('/garantiafisico', [GarantiaFisicoController::class, 'garantiafisico']);
Router::post('/entregalinea', [EntregaLineaController::class, 'entregal']);
Router::post('/entregafisico', [EntregaFisicoController::class, 'entregaf']);