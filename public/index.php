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
use proyecto\Controller\AuthController;
use proyecto\Controller\CitasFisicasController;
use proyecto\Controller\CitasController; 
use proyecto\Controller\RegistroController;
use proyecto\Controller\LoginController;
use proyecto\Controller\RegistroAdminController;
use proyecto\Models\DetalleServicioAdmin;
use proyecto\Models\CC;
use proyecto\Models\AsignacionFisica;
use proyecto\Models\AsignacionLinea;
use proyecto\Models\orden_cita;
use proyecto\Models\TERorden;
use proyecto\Router;

// ochoa
use proyecto\Controller\PagoLineaAceptadoController;
use proyecto\Controller\PagoLineaRechazadoController;


// mar 
use proyecto\Models\recepcionistaCitasLinea;
use proyecto\Models\recepcionistaCitasFisico;
use proyecto\Models\recepcionistaAsistenciaCitasLineaP;
use proyecto\Models\TodosUsuarios;
use proyecto\Controller\AgendarController;
use proyecto\Models\ClienteAA;
use proyecto\Controller\CambioRolController;
use proyecto\Controller\DetalleAsignacionLineaController;
use proyecto\Controller\TareasController;
use proyecto\Controller\DetalleTareaController;
use proyecto\Controller\ClienteCitasController;

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
Router::get('/TU', [TodosUsuarios::class, 'TodosLosUsuarios']);
Router::post('/registro', [RegistroController::class, 'registro']);
Router::post('/AR', [RegistroAdminController::class, "registro"]);
Router::get('/auth', [AuthController::class, "checkAuth"]);
Router::post('/RolCambio', [CambioRolController::class, "updateUserRole"]);
Router::post('/tecOrden', [DetalleTareaController::class, 'guardarDetalles']);
Router::post('/agendar',[AgendarController::class,"agendar"]);
Router::post('/ClienteCitas',[ClienteCitasController::class,"obtenerCitasCliente"]);
Router::post('/ClienteCitasCompletadas',[ClienteCitasController::class,"obtenerCitasCompletadas"]);
Router::post('/ClienteCitasProceso',[ClienteCitasController::class,"obtenerCitasEnProceso"]);
Router::get('/cliente',[ClienteAA::class, "data"]);
Router::post('/actualizar_proceso', [DetalleAsignacionLineaController::class, "actualizarSeguimiento"]);
Router::post('/Completados',[DetalleAsignacionLineaController::class,"obtenerDetallesAsignacionLineaCompletados"]);
Router::get('/obtener_tareas_en_proceso',[DetalleAsignacionLineaController::class,"obtenerDetallesAsignacionLinea"]);

// cheche
Router::get('/TERorden',[TERorden::class,"mirar"]);
Router::get('/orden',[orden_cita::class,"mostrarOrden"]);
Router::get('/tareas_asignadas', [TareasController::class, 'obtenerTareasAsignadas']);

Router::post('/ActualizarPago', [ClienteCitasController::class, 'actualizarPago']);


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


// Ruta para el inicio de sesión
Router::post('/login', [LoginController::class, 'login']);

Router::post('/ATL', function() {
    $controller = new CitasController();
    $controller->ATL();
});

Router::post('/ATF', function() {
    $controller = new CitasFisicasController();
    $controller->ATF();
});

Router::get('/CC', function() {
    $cuentasClientes = new CC(); 

    $filters = [
        'client_name' => $_GET['client_name'] ?? '',
    ];

    $cuentasClientes->data($filters);
});

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

// ochoa
Router::post('/lineaaceptado', [PagoLineaAceptadoController::class, 'pagolineaaceptado']);
Router::post('/linearechazado', [PagoLineaRechazadoController::class, 'pagolinearechazado']);
