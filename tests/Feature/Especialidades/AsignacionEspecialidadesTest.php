<?php

namespace Tests\Feature\Especialidades;

use App\Models\Especialidad;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\CreaDatosClinicos;
use Tests\TestCase;

/**
 * Pruebas automatizadas de asignación de especialidades:
 *  1. Desde Usuarios       (admin.users.especialidades.*)      especialidad -> colaborador
 *  2. Desde Especialidades (especialidades.medicos.*)          médico -> especialidad
 */
class AsignacionEspecialidadesTest extends TestCase
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

    private function crearEspecialidad(array $atributos = []): Especialidad
    {
        return Especialidad::create(array_merge([
            'codigo'                    => 'CARD',
            'nombre'                    => 'Cardiología',
            'grupo'                     => 'clinica',
            'duracion_consulta_default' => 30,
            'activo'                    => true,
        ], $atributos));
    }

    private function crearPediatria(): Especialidad
    {
        return $this->crearEspecialidad(['codigo' => 'PED', 'nombre' => 'Pediatría']);
    }

    private function asignar(User $medico, Especialidad $especialidad, bool $principal = false): void
    {
        $medico->especialidades()->attach($especialidad->id, [
            'es_principal' => $principal,
            'activo'       => true,
        ]);
    }

    private function pivotId(User $medico, Especialidad $especialidad): int
    {
        return (int) DB::table('especialidad_user')
            ->where('user_id', $medico->id)
            ->where('especialidad_id', $especialidad->id)
            ->value('id');
    }

    private function administrador(): User
    {
        return $this->crearUsuarioConRol('administrador');
    }

    /* ============================================================
     |  1. DESDE USUARIOS: asignar especialidad a un colaborador
     ============================================================ */

    public function test_administrador_asigna_una_especialidad_a_un_medico(): void
    {
        $medico = $this->crearMedico();
        $especialidad = $this->crearEspecialidad();

        $this->actingAs($this->administrador())
            ->post(route('admin.users.especialidades.asignar', $medico), [
                'especialidad_id'            => $especialidad->id,
                'numero_cedula_especialidad' => '98765432',
                'fecha_certificacion'        => '2020-01-15',
            ])
            ->assertSessionHas('success', 'Especialidad asignada correctamente.');

        $this->assertDatabaseHas('especialidad_user', [
            'user_id'                    => $medico->id,
            'especialidad_id'            => $especialidad->id,
            'numero_cedula_especialidad' => '98765432',
            'fecha_certificacion'        => '2020-01-15',
            'es_principal'               => false,
            'activo'                     => true,
        ]);
    }

    public function test_no_permite_asignar_dos_veces_la_misma_especialidad(): void
    {
        $medico = $this->crearMedico();
        $especialidad = $this->crearEspecialidad();
        $admin = $this->administrador();
        $url = route('admin.users.especialidades.asignar', $medico);

        $this->actingAs($admin)->post($url, ['especialidad_id' => $especialidad->id]);

        $this->actingAs($admin)
            ->post($url, ['especialidad_id' => $especialidad->id])
            ->assertSessionHas('error', 'Esa especialidad ya está asignada al usuario.');

        $this->assertSame(1, $medico->especialidades()->count());
    }

    public function test_al_asignar_una_principal_nueva_se_desmarca_la_anterior(): void
    {
        $medico = $this->crearMedico();
        $cardiologia = $this->crearEspecialidad();
        $pediatria = $this->crearPediatria();
        $admin = $this->administrador();
        $url = route('admin.users.especialidades.asignar', $medico);

        $this->actingAs($admin)->post($url, ['especialidad_id' => $cardiologia->id, 'es_principal' => 1]);
        $this->actingAs($admin)->post($url, ['especialidad_id' => $pediatria->id, 'es_principal' => 1]);

        $this->assertDatabaseHas('especialidad_user', [
            'user_id'         => $medico->id,
            'especialidad_id' => $cardiologia->id,
            'es_principal'    => false,
        ]);
        $this->assertDatabaseHas('especialidad_user', [
            'user_id'         => $medico->id,
            'especialidad_id' => $pediatria->id,
            'es_principal'    => true,
        ]);
    }

    public static function datosInvalidosDeAsignacion(): array
    {
        return [
            'sin especialidad'         => [['especialidad_id' => null], 'especialidad_id'],
            'especialidad inexistente' => [['especialidad_id' => 999999], 'especialidad_id'],
            'fecha de certificación inválida' => [['fecha_certificacion' => 'no-es-fecha'], 'fecha_certificacion'],
            'cédula demasiado larga'   => [['numero_cedula_especialidad' => str_repeat('9', 51)], 'numero_cedula_especialidad'],
        ];
    }

    #[DataProvider('datosInvalidosDeAsignacion')]
    public function test_rechaza_datos_invalidos_al_asignar_especialidad(array $datosInvalidos, string $campoConError): void
    {
        $medico = $this->crearMedico();
        $especialidad = $this->crearEspecialidad();

        $this->actingAs($this->administrador())
            ->post(
                route('admin.users.especialidades.asignar', $medico),
                array_merge(['especialidad_id' => $especialidad->id], $datosInvalidos)
            )
            ->assertSessionHasErrors($campoConError);

        $this->assertDatabaseCount('especialidad_user', 0);
    }

    public function test_administrador_quita_una_especialidad_del_medico(): void
    {
        $medico = $this->crearMedico();
        $especialidad = $this->crearEspecialidad();
        $this->asignar($medico, $especialidad);
        $pivotId = $this->pivotId($medico, $especialidad);

        $this->actingAs($this->administrador())
            ->delete(route('admin.users.especialidades.quitar', [$medico, $pivotId]))
            ->assertSessionHas('success', 'Especialidad removida.');

        $this->assertDatabaseMissing('especialidad_user', ['id' => $pivotId]);
    }

    public function test_no_puede_quitar_la_especialidad_de_otro_colaborador(): void
    {
        $medicoA = $this->crearMedico();
        $medicoB = $this->crearMedico();
        $especialidad = $this->crearEspecialidad();
        $this->asignar($medicoB, $especialidad);
        $pivotDeB = $this->pivotId($medicoB, $especialidad);

        // Se intenta borrar el registro de B usando la URL del médico A.
        $this->actingAs($this->administrador())
            ->delete(route('admin.users.especialidades.quitar', [$medicoA, $pivotDeB]));

        $this->assertDatabaseHas('especialidad_user', ['id' => $pivotDeB]);
    }

    public function test_administrador_marca_una_especialidad_como_principal(): void
    {
        $medico = $this->crearMedico();
        $cardiologia = $this->crearEspecialidad();
        $pediatria = $this->crearPediatria();
        $this->asignar($medico, $cardiologia, true);
        $this->asignar($medico, $pediatria);

        $this->actingAs($this->administrador())
            ->post(route('admin.users.especialidades.principal', [$medico, $this->pivotId($medico, $pediatria)]))
            ->assertSessionHas('success', 'Especialidad marcada como principal.');

        $this->assertDatabaseHas('especialidad_user', [
            'user_id' => $medico->id, 'especialidad_id' => $cardiologia->id, 'es_principal' => false,
        ]);
        $this->assertDatabaseHas('especialidad_user', [
            'user_id' => $medico->id, 'especialidad_id' => $pediatria->id, 'es_principal' => true,
        ]);
    }

    public function test_la_pantalla_solo_ofrece_especialidades_activas_y_no_asignadas(): void
    {
        $medico = $this->crearMedico();
        $asignada = $this->crearEspecialidad();
        $libre = $this->crearPediatria();
        $this->crearEspecialidad(['codigo' => 'GIN', 'nombre' => 'Ginecología', 'activo' => false]);
        $this->asignar($medico, $asignada);

        $this->actingAs($this->administrador())
            ->get(route('admin.users.especialidades', $medico))
            ->assertOk()
            ->assertViewHas('disponibles', fn ($disponibles) => $disponibles->pluck('id')->all() === [$libre->id]);
    }

    /* ============================================================
     |  Control de acceso (pantalla de Usuarios)
     ============================================================ */

    public static function rolesNoAdministrativos(): array
    {
        return [
            'médico'     => ['medico'],
            'enfermería' => ['enfermeria'],
            'farmacia'   => ['farmacia'],
        ];
    }

    #[DataProvider('rolesNoAdministrativos')]
    public function test_roles_no_administrativos_no_pueden_asignar_especialidades_a_usuarios(string $rol): void
    {
        $medico = $this->crearMedico();
        $especialidad = $this->crearEspecialidad();

        $this->actingAs($this->crearUsuarioConRol($rol))
            ->post(route('admin.users.especialidades.asignar', $medico), ['especialidad_id' => $especialidad->id])
            ->assertForbidden();

        $this->assertDatabaseCount('especialidad_user', 0);
    }

    public function test_invitado_es_redirigido_al_login_al_asignar_especialidades(): void
    {
        $medico = $this->crearMedico();
        $especialidad = $this->crearEspecialidad();

        $this->post(route('admin.users.especialidades.asignar', $medico), ['especialidad_id' => $especialidad->id])
            ->assertRedirect(route('login'));
    }

    /* ============================================================
     |  2. DESDE ESPECIALIDADES: asignar médico a una especialidad
     ============================================================ */

    public function test_administrador_asigna_un_medico_desde_el_catalogo_de_especialidades(): void
    {
        $medico = $this->crearMedico();
        $especialidad = $this->crearEspecialidad();

        $this->actingAs($this->administrador())
            ->post(route('especialidades.medicos.asignar', $especialidad), [
                'user_id'                    => $medico->id,
                'numero_cedula_especialidad' => '11223344',
                'es_principal'               => 1,
            ])
            ->assertSessionHas('success', 'Médico asignado correctamente.');

        $this->assertDatabaseHas('especialidad_user', [
            'user_id'                    => $medico->id,
            'especialidad_id'            => $especialidad->id,
            'numero_cedula_especialidad' => '11223344',
            'es_principal'               => true,
            'activo'                     => true,
        ]);
    }

    public function test_no_permite_asignar_dos_veces_al_mismo_medico_a_una_especialidad(): void
    {
        $medico = $this->crearMedico();
        $especialidad = $this->crearEspecialidad();
        $admin = $this->administrador();
        $url = route('especialidades.medicos.asignar', $especialidad);

        $this->actingAs($admin)->post($url, ['user_id' => $medico->id]);

        $this->actingAs($admin)
            ->post($url, ['user_id' => $medico->id])
            ->assertSessionHas('error', 'Ese médico ya está asignado a esta especialidad.');

        $this->assertSame(1, $especialidad->medicos()->count());
    }

    public function test_rechaza_asignar_un_medico_inexistente(): void
    {
        $especialidad = $this->crearEspecialidad();

        $this->actingAs($this->administrador())
            ->post(route('especialidades.medicos.asignar', $especialidad), ['user_id' => 999999])
            ->assertSessionHasErrors('user_id');

        $this->assertDatabaseCount('especialidad_user', 0);
    }

    public function test_administrador_quita_un_medico_de_la_especialidad(): void
    {
        $medico = $this->crearMedico();
        $especialidad = $this->crearEspecialidad();
        $this->asignar($medico, $especialidad);
        $pivotId = $this->pivotId($medico, $especialidad);

        $this->actingAs($this->administrador())
            ->delete(route('especialidades.medicos.quitar', [$especialidad, $pivotId]))
            ->assertSessionHas('success', 'Médico removido de la especialidad.');

        $this->assertDatabaseMissing('especialidad_user', ['id' => $pivotId]);
    }

    public function test_rol_sin_permiso_de_especialidades_no_puede_asignar_medicos(): void
    {
        $medico = $this->crearMedico();
        $especialidad = $this->crearEspecialidad();

        // Farmacia no tiene el permiso especialidades.ver
        $this->actingAs($this->crearUsuarioConRol('farmacia'))
            ->post(route('especialidades.medicos.asignar', $especialidad), ['user_id' => $medico->id])
            ->assertForbidden();

        $this->assertDatabaseCount('especialidad_user', 0);
    }
}