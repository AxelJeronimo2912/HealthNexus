<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\MedicamentoController;
use App\Http\Controllers\CamaController;
use App\Http\Controllers\SignoVitalController;
use App\Http\Controllers\TurnoController;
use App\Http\Controllers\AsignacionTurnoController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\ConsultaController;
use App\Http\Controllers\ExpedienteController;
use App\Http\Controllers\ExistenciaController;
use App\Http\Controllers\DispensacionController;
use App\Http\Controllers\SeguimientoController;
 use App\Http\Controllers\MovimientoController;
    use App\Http\Controllers\ServicioController;
    use App\Http\Controllers\EspecialidadController;
    use App\Http\Controllers\NotaEnfermeriaController;
    use App\Http\Controllers\AdministracionMedicamentoController;
    use App\Http\Controllers\PrediccionController;
    use App\Http\Controllers\DispositivoController;

/*
|--------------------------------------------------------------------------
| Raíz
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Dashboard genérico: redirige según el rol
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->get('/dashboard', function () {
    $user = auth()->user();

    if ($user->hasRole('administrador')) {
        return redirect()->route('admin.dashboard');
    }

    return view('dashboard');
})->name('dashboard');

/*
|--------------------------------------------------------------------------
| Módulo de Pacientes (compartido por permisos)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permission:pacientes.ver'])
    ->group(function () {
        Route::get('pacientes/buscar', [PacienteController::class, 'buscar'])
            ->name('pacientes.buscar');
        Route::get('estados/{estado}/municipios', [PacienteController::class, 'municipiosPorEstado'])
            ->name('estados.municipios');

        Route::resource('pacientes', PacienteController::class);
    });

/*
|--------------------------------------------------------------------------
| Rutas del Administrador (solo rol administrador)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:administrador'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Dashboard del admin
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Usuarios
        Route::resource('users', UserController::class);
        Route::post('users/{user}/regenerar-pin', [UserController::class, 'regenerarPin'])
            ->name('users.regenerar-pin');

        // Roles y permisos
        Route::resource('roles', RoleController::class);

        Route::resource('turnos', TurnoController::class);

        // Asignaciones de turnos por usuario
        Route::get('users/{user}/turnos', [AsignacionTurnoController::class, 'index'])
            ->name('users.turnos.index');
        Route::get('users/{user}/turnos/crear', [AsignacionTurnoController::class, 'create'])
            ->name('users.turnos.create');
        Route::post('users/{user}/turnos', [AsignacionTurnoController::class, 'store'])
            ->name('users.turnos.store');
        Route::delete('users/{user}/turnos/{pivotId}', [AsignacionTurnoController::class, 'destroy'])
            ->name('users.turnos.destroy');
        Route::post('users/{user}/turnos/{pivotId}/toggle', [AsignacionTurnoController::class, 'toggle'])
            ->name('users.turnos.toggle');

            Route::get('users/{user}/especialidades', [UserController::class, 'especialidades'])
            ->name('users.especialidades');
            Route::post('users/{user}/especialidades', [UserController::class, 'asignarEspecialidad'])
            ->name('users.especialidades.asignar');
            Route::delete('users/{user}/especialidades/{pivotId}', [UserController::class, 'quitarEspecialidad'])
            ->name('users.especialidades.quitar');
            Route::post('users/{user}/especialidades/{pivotId}/principal', [UserController::class, 'marcarPrincipal'])
            ->name('users.especialidades.principal');
            });


Route::middleware(['auth', 'permission:medicamentos.ver'])
    ->group(function () {
        Route::resource('medicamentos', MedicamentoController::class);
        
    });


    Route::post('/medicamentos/{medicamento}/entrada', [MedicamentoController::class, 'entrada'])
    ->middleware(['auth', 'permission:medicamentos.ver'])
    ->name('medicamentos.entrada');




Route::middleware(['auth', 'permission:expediente.ver'])
    ->prefix('expedientes')
    ->name('expedientes.')
    ->group(function () {
        Route::get('/', [ExpedienteController::class, 'index'])->name('index');
        Route::get('/{paciente}', [ExpedienteController::class, 'show'])->name('show');
    });


/*
|--------------------------------------------------------------------------
| Módulo de Camas
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permission:camas.ver'])
    ->group(function () {
        Route::resource('camas', CamaController::class);
        Route::post('camas/{cama}/estado', [CamaController::class, 'cambiarEstado'])
            ->name('camas.cambiar-estado');
    });


/*
|--------------------------------------------------------------------------
| Módulo de Signos Vitales
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'permission:signos-vitales.ver'])
    ->group(function () {
        Route::resource('signos-vitales', SignoVitalController::class);
        Route::post('signos-vitales/{signoVital}/asignar-cama', [SignoVitalController::class, 'asignarCama'])
            ->name('signos-vitales.asignar-cama');
        Route::post('signos-vitales/{signoVital}/liberar-cama', [SignoVitalController::class, 'liberarCama'])
            ->name('signos-vitales.liberar-cama');
    });

/*
|--------------------------------------------------------------------------
| Módulo de agenda
|--------------------------------------------------------------------------
*/

    Route::middleware(['auth', 'permission:agenda.ver'])
    ->prefix('agenda')
    ->name('agenda.')
    ->group(function () {
        Route::get('/', [AgendaController::class, 'index'])->name('index');
        Route::get('/dia', [AgendaController::class, 'dia'])->name('dia');
        Route::get('/crear', [AgendaController::class, 'create'])->name('create');
        Route::post('/', [AgendaController::class, 'store'])->name('store');
        Route::get('/api/medicos-disponibles', [AgendaController::class, 'medicosDisponibles'])->name('medicos-disponibles');
        Route::get('/api/pacientes-disponibles', [AgendaController::class, 'pacientesDisponibles'])->name('pacientes-disponibles');
    });



Route::middleware(['auth', 'permission:citas.ver'])
    ->prefix('citas')
    ->name('citas.')
    ->group(function () {
        Route::get('/', [CitaController::class, 'index'])->name('index');
        Route::get('/{cita}', [CitaController::class, 'show'])->name('show');
        Route::post('/{cita}/estado', [CitaController::class, 'cambiarEstado'])->name('cambiar-estado');
        Route::delete('/{cita}', [CitaController::class, 'destroy'])->name('destroy');
    });




Route::middleware(['auth', 'permission:consultas.ver'])
    ->prefix('consultas')
    ->name('consultas.')
    ->group(function () {
        Route::get('/cita/{cita}/iniciar', [ConsultaController::class, 'iniciar'])->name('iniciar');
        Route::post('/cita/{cita}', [ConsultaController::class, 'store'])->name('store');
        Route::get('/{consulta}', [ConsultaController::class, 'show'])->name('show');
        Route::get('/{consulta}/editar', [ConsultaController::class, 'edit'])->name('edit');
        Route::put('/{consulta}', [ConsultaController::class, 'update'])->name('update');
        Route::get('/{consulta}/pdf', [ConsultaController::class, 'pdf'])->name('pdf');
        Route::get('/{consulta}/receta/pdf', [ConsultaController::class, 'pdfReceta'])->name('receta.pdf');
    });



Route::middleware(['auth', 'permission:existencias.ver'])
    ->prefix('existencias')
    ->name('existencias.')
    ->group(function () {
        Route::get('/', [ExistenciaController::class, 'index'])->name('index');
        Route::get('/lotes', [ExistenciaController::class, 'lotes'])->name('lotes');
        Route::post('/movimientos', [ExistenciaController::class, 'storeMovimiento'])->name('movimientos.store'); 
        Route::get('/{medicamento}', [ExistenciaController::class, 'show'])->name('show');
    });



Route::middleware(['auth', 'permission:dispensaciones.ver'])
    ->prefix('dispensaciones')
    ->name('dispensaciones.')
    ->group(function () {
        Route::get('/', [DispensacionController::class, 'index'])->name('index');
        Route::get('/{consulta}', [DispensacionController::class, 'show'])->name('show');
        Route::post('/{consulta}/dispensar', [DispensacionController::class, 'dispensar'])->name('dispensar');
        Route::post('/{consulta}/revertir', [DispensacionController::class, 'revertir'])->name('revertir');
    });




Route::middleware(['auth', 'permission:seguimiento.ver'])
    ->prefix('seguimientos')
    ->name('seguimientos.')
    ->group(function () {
        Route::get('/', [SeguimientoController::class, 'index'])->name('index');
        Route::get('/{paciente}', [SeguimientoController::class, 'show'])->name('show');
        Route::get('/{paciente}/crear', [SeguimientoController::class, 'create'])->name('create');
        Route::post('/{paciente}', [SeguimientoController::class, 'store'])->name('store');
        Route::delete('/{seguimiento}', [SeguimientoController::class, 'destroy'])->name('destroy');
    });



Route::middleware(['auth', 'permission:movimientos.ver'])
    ->prefix('movimientos')
    ->name('movimientos.')
    ->group(function () {
        Route::get('/', [MovimientoController::class, 'index'])->name('index');
        Route::get('/{movimiento}', [MovimientoController::class, 'show'])->name('show');
    });



Route::middleware(['auth', 'permission:servicios.ver'])
    ->prefix('servicios')
    ->name('servicios.')
    ->group(function () {
        Route::get('/', [ServicioController::class, 'index'])->name('index');
        Route::get('/crear', [ServicioController::class, 'create'])->name('create');
        Route::post('/', [ServicioController::class, 'store'])->name('store');
        Route::get('/{servicio}', [ServicioController::class, 'show'])->name('show');
        Route::get('/{servicio}/editar', [ServicioController::class, 'edit'])->name('edit');
        Route::put('/{servicio}', [ServicioController::class, 'update'])->name('update');
        Route::delete('/{servicio}', [ServicioController::class, 'destroy'])->name('destroy');

        // Personal
        Route::get('/{servicio}/personal', [ServicioController::class, 'personal'])->name('personal');
        Route::post('/{servicio}/personal', [ServicioController::class, 'asignarPersonal'])->name('personal.asignar');
        Route::delete('/{servicio}/personal/{pivotId}', [ServicioController::class, 'quitarPersonal'])->name('personal.quitar');
    });




Route::middleware(['auth', 'permission:especialidades.ver'])
    ->prefix('especialidades')
    ->name('especialidades.')
    ->group(function () {
        Route::get('/', [EspecialidadController::class, 'index'])->name('index');
        Route::get('/crear', [EspecialidadController::class, 'create'])->name('create');
        Route::post('/', [EspecialidadController::class, 'store'])->name('store');
        Route::get('/{especialidad}', [EspecialidadController::class, 'show'])->name('show');
        Route::get('/{especialidad}/editar', [EspecialidadController::class, 'edit'])->name('edit');
        Route::put('/{especialidad}', [EspecialidadController::class, 'update'])->name('update');
        Route::delete('/{especialidad}', [EspecialidadController::class, 'destroy'])->name('destroy');

        // Médicos
        Route::get('/{especialidad}/medicos', [EspecialidadController::class, 'medicos'])->name('medicos');
        Route::post('/{especialidad}/medicos', [EspecialidadController::class, 'asignarMedico'])->name('medicos.asignar');
        Route::delete('/{especialidad}/medicos/{pivotId}', [EspecialidadController::class, 'quitarMedico'])->name('medicos.quitar');

        // Servicios
        Route::get('/{especialidad}/servicios', [EspecialidadController::class, 'servicios'])->name('servicios');
        Route::post('/{especialidad}/servicios', [EspecialidadController::class, 'asignarServicio'])->name('servicios.asignar');
        Route::delete('/{especialidad}/servicios/{pivotId}', [EspecialidadController::class, 'quitarServicio'])->name('servicios.quitar');
    });



Route::middleware(['auth', 'permission:enfermeria.ver'])
    ->prefix('enfermeria/notas')
    ->name('enfermeria.notas.')
    ->group(function () {
        Route::get('/', [NotaEnfermeriaController::class, 'index'])->name('index');
        Route::get('/crear', [NotaEnfermeriaController::class, 'create'])->name('create');
        Route::post('/', [NotaEnfermeriaController::class, 'store'])->name('store');
        Route::get('/{nota}', [NotaEnfermeriaController::class, 'show'])->name('show');
        Route::delete('/{nota}', [NotaEnfermeriaController::class, 'destroy'])->name('destroy');
    });




Route::middleware(['auth', 'permission:enfermeria.ver'])
    ->prefix('enfermeria/administraciones')
    ->name('enfermeria.administraciones.')
    ->group(function () {
        Route::get('/', [AdministracionMedicamentoController::class, 'index'])->name('index');
        Route::get('/crear', [AdministracionMedicamentoController::class, 'create'])->name('create');
        Route::post('/', [AdministracionMedicamentoController::class, 'store'])->name('store');
        Route::get('/{administracion}', [AdministracionMedicamentoController::class, 'show'])->name('show');
        Route::delete('/{administracion}', [AdministracionMedicamentoController::class, 'destroy'])->name('destroy');
    });



Route::middleware(['auth', 'permission:prediccion.ver'])
    ->prefix('prediccion')
    ->name('prediccion.')
    ->group(function () {
        Route::get('/', [PrediccionController::class, 'index'])->name('index');
        Route::get('/{medicamento}', [PrediccionController::class, 'show'])->name('show');
    });




Route::middleware(['auth', 'permission:dispositivos.ver'])
    ->prefix('dispositivos')
    ->name('dispositivos.')
    ->group(function () {
        Route::get('/', [DispositivoController::class, 'index'])->name('index');
        Route::get('/{dispositivo}', [DispositivoController::class, 'show'])->name('show');

        // Solo admin
        Route::middleware('role:administrador')->group(function () {
            Route::post('/{dispositivo}/confiar', [DispositivoController::class, 'confiar'])->name('confiar');
            Route::post('/{dispositivo}/reactivar', [DispositivoController::class, 'reactivar'])->name('reactivar');
            Route::delete('/{dispositivo}', [DispositivoController::class, 'destroy'])->name('destroy');
        });

        // Cualquier usuario puede bloquear sus propios dispositivos
        Route::post('/{dispositivo}/bloquear', [DispositivoController::class, 'bloquear'])->name('bloquear');
    });


    use App\Http\Controllers\AdmisionController;

Route::middleware(['auth', 'permission:admision.ver'])
    ->prefix('admisiones')
    ->name('admisiones.')
    ->group(function () {
        Route::get('/', [AdmisionController::class, 'index'])->name('index');
        Route::get('/crear', [AdmisionController::class, 'create'])->name('create');
        Route::post('/', [AdmisionController::class, 'store'])->name('store');
        Route::get('/{admision}', [AdmisionController::class, 'show'])->name('show');
        Route::post('/{admision}/asignar', [AdmisionController::class, 'asignar'])->name('asignar');
        Route::post('/{admision}/derivar', [AdmisionController::class, 'derivar'])->name('derivar');
        Route::get('/{admision}/pase-salida', [AdmisionController::class, 'paseSalida'])->name('pase-salida');
        Route::delete('/{admision}', [AdmisionController::class, 'destroy'])->name('destroy');
    });
require __DIR__.'/auth.php';