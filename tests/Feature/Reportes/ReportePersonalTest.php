<?php

namespace Tests\Feature\Reportes;

use App\Models\Especialidad;
use App\Models\User;
use App\Services\ReporteService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\Concerns\CreaDatosClinicos;
use Tests\TestCase;

/**
 * Pruebas automatizadas de generación de reportes de personal:
 *  - Reporte de colaboradores   (admin.users.reporte-pdf)
 *  - Reporte de personal activo (ReporteService::personalActivo)
 */
class ReportePersonalTest extends TestCase
{
    use RefreshDatabase, CreaDatosClinicos;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->prepararRolesYPermisos();
    }

    /* ============================================================
     |  Helpers
     ============================================================ */

    private function crearEspecialidad(string $codigo, string $nombre): Especialidad
    {
        return Especialidad::create([
            'codigo'                    => $codigo,
            'nombre'                    => $nombre,
            'grupo'                     => 'clinica',
            'duracion_consulta_default' => 30,
            'activo'                    => true,
        ]);
    }

    private function crearEnfermero(array $atributos = []): User
    {
        return $this->crearUsuarioConRol('enfermeria', array_merge([
            'nombre'           => 'Mario',
            'apellido_paterno' => 'Gómez',
            'apellido_materno' => 'Ruiz',
        ], $atributos));
    }

    /* ============================================================
     |  REPORTE DE COLABORADORES — endpoint
     ============================================================ */

    public function test_administrador_descarga_el_reporte_de_colaboradores_en_pdf(): void
    {
        $admin = $this->crearUsuarioConRol('administrador');
        $this->crearMedico();

        $response = $this->actingAs($admin)->get(route('admin.users.reporte-pdf'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');

        $disposicion = $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('attachment', $disposicion);
        $this->assertStringContainsString('reporte-colaboradores-' . now()->format('Y-m-d') . '.pdf', $disposicion);

        $contenido = $response->getContent();
        $this->assertStringStartsWith('%PDF-', $contenido, 'El contenido no inicia con la firma %PDF-.');
        $this->assertStringContainsString('%%EOF', $contenido, 'El PDF está truncado.');
    }

    public function test_el_reporte_envia_los_totales_correctos_a_la_plantilla(): void
    {
        $admin = $this->crearUsuarioConRol('administrador');   // activo
        $this->crearMedico();                                  // activo
        $this->crearMedico(['activo' => false]);               // inactivo

        Pdf::shouldReceive('loadView')
            ->once()
            ->with('admin.users.pdf.reporte', Mockery::on(
                fn (array $datos) => $datos['stats'] === ['total' => 3, 'activos' => 2, 'inactivos' => 1]
                    && $datos['users']->count() === 3
            ))
            ->andReturnSelf();

        Pdf::shouldReceive('download')
            ->once()
            ->with('reporte-colaboradores-' . now()->format('Y-m-d') . '.pdf')
            ->andReturn(response('PDF-SIMULADO', 200, ['Content-Type' => 'application/pdf']));

        $this->actingAs($admin)
            ->get(route('admin.users.reporte-pdf'))
            ->assertOk()
            ->assertSee('PDF-SIMULADO');
    }

    public function test_medico_no_puede_descargar_el_reporte_de_colaboradores(): void
    {
        $this->actingAs($this->crearMedico())
            ->get(route('admin.users.reporte-pdf'))
            ->assertForbidden();
    }

    public function test_invitado_es_redirigido_al_login_al_pedir_el_reporte_de_colaboradores(): void
    {
        $this->get(route('admin.users.reporte-pdf'))
            ->assertRedirect(route('login'));
    }

    /* ============================================================
     |  REPORTE DE COLABORADORES — contenido de la plantilla
     ============================================================ */

    public function test_plantilla_de_colaboradores_muestra_datos_roles_y_estado(): void
    {
        $this->crearMedico(['email' => 'laura@healthnexus.test']);
        $this->crearEnfermero(['activo' => false]);

        $users = User::with('roles')->get();

        $this->view('admin.users.pdf.reporte', [
            'users'        => $users,
            'stats'        => ['total' => 2, 'activos' => 1, 'inactivos' => 1],
            'fechaEmision' => '03/11/2026 21:30',
        ])
            ->assertSee('Reporte Operativo de Colaboradores y Personal')
            ->assertSee('03/11/2026 21:30')
            ->assertSee('Total Colaboradores:')
            ->assertSee('Laura Ramírez Soto')
            ->assertSee('laura@healthnexus.test')
            ->assertSee('medico')
            ->assertSee('Mario Gómez Ruiz')
            ->assertSee('enfermeria')
            ->assertSee('badge activo', false)
            ->assertSee('badge inactivo', false);
    }

    public function test_plantilla_de_colaboradores_avisa_cuando_no_hay_registros(): void
    {
        $this->view('admin.users.pdf.reporte', [
            'users'        => collect(),
            'stats'        => ['total' => 0, 'activos' => 0, 'inactivos' => 0],
            'fechaEmision' => '03/11/2026 21:30',
        ])->assertSee('No hay colaboradores registrados.');
    }

    /* ============================================================
     |  REPORTE DE PERSONAL ACTIVO (con especialidades)
     ============================================================ */

    public function test_reporte_de_personal_activo_genera_y_guarda_el_pdf(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $medico = $this->crearMedico();
        $medico->especialidades()->attach($this->crearEspecialidad('CARD', 'Cardiología')->id, [
            'es_principal' => true,
            'activo'       => true,
        ]);
        $this->crearMedico(['activo' => false]);

        $resultado = ReporteService::personalActivo();

        $this->assertSame('Reporte de personal activo', $resultado['nombre']);
        $this->assertSame(1, $resultado['total_personal']);
        $this->assertStringContainsString('/reportes/personal-activo-', $resultado['url']);
        $this->assertStringContainsString('signature=', $resultado['url']);

        $this->assertSame([], Storage::disk('public')->allFiles());
        $archivos = Storage::disk('local')->allFiles('reportes');
        $this->assertCount(1, $archivos);
        $this->assertStringStartsWith('%PDF-', Storage::disk('local')->get($archivos[0]));
    }

    public function test_plantilla_de_personal_activo_lista_las_especialidades_de_cada_persona(): void
    {
        $medico = $this->crearMedico();
        foreach ([['CARD', 'Cardiología'], ['PED', 'Pediatría']] as [$codigo, $nombre]) {
            $medico->especialidades()->attach($this->crearEspecialidad($codigo, $nombre)->id, [
                'es_principal' => false,
                'activo'       => true,
            ]);
        }
        $this->crearEnfermero();

        $personal = User::where('activo', true)->with(['roles', 'especialidades'])->get();

        $this->view('reportes.personal-activo', ['personal' => $personal])
            ->assertSee('Reporte de personal activo')
            ->assertSee('Total de personal activo:')
            ->assertSee('Laura Ramírez Soto')
            ->assertSee('Cardiología')
            ->assertSee('Pediatría')
            ->assertSee('Mario Gómez Ruiz');
    }

    public function test_plantilla_de_personal_activo_usa_guion_si_no_tiene_especialidades(): void
    {
        $enfermero = $this->crearEnfermero();

        $personal = User::with(['roles', 'especialidades'])->whereKey($enfermero->id)->get();

        $this->view('reportes.personal-activo', ['personal' => $personal])
            ->assertSee('Mario Gómez Ruiz')
            ->assertSee('<td>—</td>', false);
    }

    public function test_plantilla_de_personal_activo_avisa_cuando_no_hay_personal(): void
    {
        $this->view('reportes.personal-activo', ['personal' => collect()])
            ->assertSee('No hay personal activo.');
    }
}