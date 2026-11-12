<?php

namespace Tests\Feature\Reportes;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Los reportes PDF del asistente son privados: enlace firmado, con sesión y solo para quien lo generó.
 */
class ReporteDescargaTest extends TestCase
{
    use RefreshDatabase;

    private const ARCHIVO = 'citas-2026-11-11-abc123.pdf';

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Storage::disk('local')->put('reportes/'.self::ARCHIVO, '%PDF-1.4 prueba');
    }

    private function usuario(): User
    {
        return User::create([
            'name' => 'U '.Str::random(5), 'email' => Str::random(8).'@test.com', 'password' => bcrypt('password'),
            'nombre' => 'Ana', 'apellido_paterno' => 'Ruiz', 'activo' => true,
        ]);
    }

    private function enlace(User $para, int $minutos = 30): string
    {
        return URL::temporarySignedRoute('reportes.descargar', now()->addMinutes($minutos), ['archivo' => self::ARCHIVO, 'usuario' => $para->id]);
    }

    public function test_quien_genero_el_reporte_lo_descarga(): void
    {
        $user = $this->usuario();

        $respuesta = $this->actingAs($user)->get($this->enlace($user))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');

        $this->assertSame('%PDF-1.4 prueba', $respuesta->streamedContent());
    }

    public function test_sin_sesion_redirige_al_login(): void
    {
        $this->get($this->enlace($this->usuario()))->assertRedirect(route('login'));
    }

    public function test_otro_usuario_con_el_mismo_enlace_recibe_403_y_queda_en_la_bitacora(): void
    {
        $dueno = $this->usuario();
        $otro = $this->usuario();

        $this->actingAs($otro)->get($this->enlace($dueno))->assertForbidden();
        $this->assertDatabaseHas('audit_logs', ['user_id' => $otro->id, 'evento' => 'acceso_denegado', 'modulo' => 'reportes']);
    }

    public function test_un_enlace_sin_firma_o_vencido_no_sirve(): void
    {
        $user = $this->usuario();

        $this->actingAs($user)->get(route('reportes.descargar', ['archivo' => self::ARCHIVO, 'usuario' => $user->id]))->assertForbidden();

        $enlace = $this->enlace($user, 30);
        $this->travel(31)->minutes();
        $this->actingAs($user)->get($enlace)->assertForbidden();
    }

    public function test_el_pdf_ya_no_se_puede_abrir_por_storage(): void
    {
        // En producción /storage es el enlace al disco público (404: ahí ya no está); en pruebas Laravel
        // protege el disco privado con firma (403). En ningún caso se entrega el archivo.
        $estado = $this->get('/storage/reportes/'.self::ARCHIVO)->getStatusCode();
        $this->assertContains($estado, [403, 404]);
    }
}
