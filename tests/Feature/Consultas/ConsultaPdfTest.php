<?php

namespace Tests\Feature\Consultas;

use App\Models\Consulta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\CreaDatosClinicos;
use Tests\TestCase;

/**
 * Pruebas automatizadas de la generación de PDF:
 *  - PDF de la consulta  (consultas.pdf)
 *  - PDF de la receta    (consultas.receta.pdf)
 *
 * Se validan tres capas:
 *  1. El endpoint devuelve un PDF real (cabeceras + estructura del archivo).
 *  2. Las plantillas Blade muestran los datos correctos del paciente,
 *     médico, SOAP, diagnósticos y receta.
 *  3. El control de acceso (roles, propietario, invitados).
 */
class ConsultaPdfTest extends TestCase
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

    /**
     * Consulta completa: diagnósticos, receta con medicamento y notas.
     */
    private function consultaCompleta(?\App\Models\User $medico = null): Consulta
    {
        $medico ??= $this->crearMedico();
        $paciente = $this->crearPaciente();

        $consulta = $this->crearConsulta($medico, [
            'diagnostico_principal_id'  => $this->crearDiagnostico('J00', 'Rinofaringitis aguda')->id,
            'diagnostico_secundario_id' => $this->crearDiagnostico('R51', 'Cefalea')->id,
            'frecuencia_respiratoria'   => 18,
            'glucosa'                   => 95,
            'perimetro_abdominal'       => 88.5,
            'receta_libre'              => 'Reposo relativo e hidratación abundante.',
            'notas'                     => 'Regresar a control en 7 días.',
        ], $paciente);

        $consulta->medicamentos()->attach($this->crearMedicamento(10)->id, [
            'dosis'        => '1 tableta',
            'via'          => 'Oral',
            'frecuencia'   => 'Cada 8 horas',
            'duracion'     => '5 días',
            'indicaciones' => 'Después de los alimentos',
        ]);

        return $consulta;
    }

    private function cargarRelaciones(Consulta $consulta): Consulta
    {
        return $consulta->fresh([
            'paciente', 'medico', 'cita', 'medicamentos',
            'diagnosticoPrincipal', 'diagnosticoSecundario',
        ]);
    }

    /**
     * Comprueba que la respuesta es un PDF real y no una página de error.
     */
    private function assertEsPdfValido(TestResponse $response, string $nombreArchivo): void
    {
        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');

        $this->assertStringContainsString(
            $nombreArchivo,
            $response->headers->get('Content-Disposition'),
            'El nombre del archivo PDF no es el esperado.'
        );

        $contenido = $response->getContent();

        $this->assertStringStartsWith('%PDF-', $contenido, 'El contenido no inicia con la firma %PDF-.');
        $this->assertStringContainsString('%%EOF', $contenido, 'El PDF está truncado: no tiene marca de fin de archivo.');
        $this->assertGreaterThan(1000, strlen($contenido), 'El PDF generado es sospechosamente pequeño.');
    }

    /* ============================================================
     |  PDF DE LA CONSULTA — endpoint
     ============================================================ */

    public function test_medico_descarga_el_pdf_de_su_consulta(): void
    {
        $medico = $this->crearMedico();
        $consulta = $this->consultaCompleta($medico);

        $response = $this->actingAs($medico)->get(route('consultas.pdf', $consulta));

        $this->assertEsPdfValido($response, "consulta-{$consulta->id}.pdf");
    }

    public function test_el_pdf_se_muestra_en_linea_y_no_como_descarga_forzada(): void
    {
        $medico = $this->crearMedico();
        $consulta = $this->consultaCompleta($medico);

        $response = $this->actingAs($medico)->get(route('consultas.pdf', $consulta));

        $this->assertStringStartsWith('inline', $response->headers->get('Content-Disposition'));
    }

    public function test_administrador_puede_generar_el_pdf_de_cualquier_consulta(): void
    {
        $consulta = $this->consultaCompleta();

        $response = $this->actingAs($this->crearUsuarioConRol('administrador'))
            ->get(route('consultas.pdf', $consulta));

        $this->assertEsPdfValido($response, "consulta-{$consulta->id}.pdf");
    }

    public function test_pdf_de_consulta_minima_sin_datos_opcionales_se_genera_sin_error(): void
    {
        $medico = $this->crearMedico();

        // Solo lo obligatorio: sin diagnósticos, receta, notas ni signos.
        $consulta = $this->crearConsulta($medico, [
            'subjetivo'           => null,
            'objetivo'            => null,
            'analisis'            => null,
            'plan'                => null,
            'temperatura'         => null,
            'frecuencia_cardiaca' => null,
            'presion_arterial'    => null,
            'saturacion_oxigeno'  => null,
            'peso'                => null,
            'talla'               => null,
        ]);

        $response = $this->actingAs($medico)->get(route('consultas.pdf', $consulta));

        $this->assertEsPdfValido($response, "consulta-{$consulta->id}.pdf");
    }

    public function test_pdf_soporta_acentos_enie_y_simbolos(): void
    {
        $medico = $this->crearMedico(['nombre' => 'Ángeles', 'apellido_paterno' => 'Peña']);
        $paciente = $this->crearPaciente([
            'nombre'           => 'José Ñandú',
            'apellido_paterno' => 'Muñoz',
            'apellido_materno' => 'Núñez',
        ]);
        $consulta = $this->crearConsulta($medico, [
            'subjetivo' => '¿Dolor? Sí: fiebre de 38.5 °C, tos y cefalea — evolución de 3 días.',
        ], $paciente);

        $response = $this->actingAs($medico)->get(route('consultas.pdf', $consulta));

        $this->assertEsPdfValido($response, "consulta-{$consulta->id}.pdf");
    }

    public function test_usa_la_plantilla_y_el_nombre_de_archivo_correctos(): void
    {
        $medico = $this->crearMedico();
        $consulta = $this->consultaCompleta($medico);

        // Se aísla DomPDF para verificar únicamente el contrato del controlador.
        Pdf::shouldReceive('loadView')
            ->once()
            ->with('consultas.pdf.consulta', Mockery::on(
                fn (array $datos) => isset($datos['consulta']) && $datos['consulta']->is($consulta)
            ))
            ->andReturnSelf();

        Pdf::shouldReceive('stream')
            ->once()
            ->with("consulta-{$consulta->id}.pdf")
            ->andReturn(response('PDF-SIMULADO', 200, ['Content-Type' => 'application/pdf']));

        $this->actingAs($medico)
            ->get(route('consultas.pdf', $consulta))
            ->assertOk()
            ->assertSee('PDF-SIMULADO');
    }

    /* ============================================================
     |  PDF DE LA CONSULTA — contenido de la plantilla
     ============================================================ */

    public function test_plantilla_de_consulta_muestra_paciente_medico_y_signos_vitales(): void
    {
        $consulta = $this->cargarRelaciones($this->consultaCompleta());

        $this->view('consultas.pdf.consulta', ['consulta' => $consulta])
            // Encabezado
            ->assertSee('HealthNexus')
            ->assertSee('Consulta médica')
            // Médico y firma
            ->assertSee('Laura Ramírez Soto')
            ->assertSee('Cédula: 12345678')
            // Paciente
            ->assertSee('Juan Pérez López')
            ->assertSee('Penicilina')
            ->assertSee('O+')
            ->assertSee($consulta->paciente->edad . ' años')
            // Signos vitales y somatometría
            ->assertSee('36.8 °C')
            ->assertSee('120/80')
            ->assertSee('98%')
            ->assertSee('70.00 kg')
            ->assertSee('1.75 m')
            ->assertSee('22.9')
            ->assertSee('88.50 cm');
    }

    public function test_plantilla_de_consulta_muestra_el_soap_descifrado(): void
    {
        $consulta = $this->cargarRelaciones($this->consultaCompleta());

        $this->view('consultas.pdf.consulta', ['consulta' => $consulta])
            ->assertSee('Paciente refiere cefalea de 3 días de evolución.')
            ->assertSee('Consciente, orientado, sin datos de focalización.')
            ->assertSee('Cefalea tensional.')
            ->assertSee('Analgésico y control en 7 días.');
    }

    public function test_plantilla_de_consulta_muestra_diagnosticos_receta_y_notas(): void
    {
        $consulta = $this->cargarRelaciones($this->consultaCompleta());

        $this->view('consultas.pdf.consulta', ['consulta' => $consulta])
            ->assertSee('[J00]')
            ->assertSee('Rinofaringitis aguda')
            ->assertSee('[R51]')
            ->assertSee('Cefalea')
            // Receta
            ->assertSee('Paracetamol 500 mg')
            ->assertSee('1 tableta')
            ->assertSee('Cada 8 horas')
            ->assertSee('5 días')
            ->assertSee('Después de los alimentos')
            ->assertSee('Reposo relativo e hidratación abundante.')
            // Notas
            ->assertSee('Regresar a control en 7 días.');
    }

    public function test_plantilla_de_consulta_omite_receta_y_notas_si_no_existen(): void
    {
        $consulta = $this->cargarRelaciones($this->crearConsulta($this->crearMedico(), [
            'receta_libre' => null,
            'notas'        => null,
        ]));

        $this->view('consultas.pdf.consulta', ['consulta' => $consulta])
            ->assertDontSee('Receta')
            ->assertDontSee('Notas');
    }

    public function test_plantilla_de_consulta_usa_guion_cuando_faltan_diagnosticos_y_datos(): void
    {
        $consulta = $this->cargarRelaciones($this->crearConsulta($this->crearMedico(), [
            'temperatura'  => null,
            'presion_arterial' => null,
        ]));

        $html = $this->view('consultas.pdf.consulta', ['consulta' => $consulta])->__toString();

        $this->assertStringContainsString('—', $html);
        $this->assertStringNotContainsString('[]', $html);
    }

    public function test_plantilla_de_consulta_escapa_html_para_evitar_inyeccion(): void
    {
        $consulta = $this->cargarRelaciones($this->crearConsulta($this->crearMedico(), [
            'subjetivo' => '<script>alert("xss")</script>',
        ]));

        $html = $this->view('consultas.pdf.consulta', ['consulta' => $consulta])->__toString();

        $this->assertStringNotContainsString('<script>alert("xss")</script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }

    /* ============================================================
     |  PDF DE LA RECETA — endpoint
     ============================================================ */

    public function test_medico_descarga_el_pdf_de_la_receta(): void
    {
        $medico = $this->crearMedico();
        $consulta = $this->consultaCompleta($medico);

        $response = $this->actingAs($medico)->get(route('consultas.receta.pdf', $consulta));

        $this->assertEsPdfValido($response, "receta-{$consulta->id}.pdf");
    }

    public function test_pdf_de_receta_sin_medicamentos_se_genera_sin_error(): void
    {
        $medico = $this->crearMedico();
        $consulta = $this->crearConsulta($medico, ['receta_libre' => 'Solo reposo.']);

        $response = $this->actingAs($medico)->get(route('consultas.receta.pdf', $consulta));

        $this->assertEsPdfValido($response, "receta-{$consulta->id}.pdf");
    }

    public function test_receta_usa_la_plantilla_y_el_nombre_de_archivo_correctos(): void
    {
        $medico = $this->crearMedico();
        $consulta = $this->consultaCompleta($medico);

        Pdf::shouldReceive('loadView')
            ->once()
            ->with('consultas.pdf.receta', Mockery::on(
                fn (array $datos) => isset($datos['consulta']) && $datos['consulta']->is($consulta)
            ))
            ->andReturnSelf();

        Pdf::shouldReceive('stream')
            ->once()
            ->with("receta-{$consulta->id}.pdf")
            ->andReturn(response('RECETA-SIMULADA', 200, ['Content-Type' => 'application/pdf']));

        $this->actingAs($medico)
            ->get(route('consultas.receta.pdf', $consulta))
            ->assertOk()
            ->assertSee('RECETA-SIMULADA');
    }

    /* ============================================================
     |  PDF DE LA RECETA — contenido de la plantilla
     ============================================================ */

    public function test_plantilla_de_receta_muestra_paciente_medicamentos_y_firma(): void
    {
        $consulta = $this->cargarRelaciones($this->consultaCompleta());

        $this->view('consultas.pdf.receta', ['consulta' => $consulta])
            ->assertSee('Receta médica')
            ->assertSee('Juan Pérez López')
            ->assertSee($consulta->paciente->edad . ' años')
            ->assertSee($consulta->created_at->format('d/m/Y'))
            // Medicamento con su posología
            ->assertSee('Paracetamol 500 mg')
            ->assertSee('1 tableta')
            ->assertSee('Oral')
            ->assertSee('Cada 8 horas')
            ->assertSee('5 días')
            ->assertSee('Después de los alimentos')
            // Indicaciones adicionales
            ->assertSee('Reposo relativo e hidratación abundante.')
            // Firma
            ->assertSee('Firma del médico')
            ->assertSee('Laura Ramírez Soto')
            ->assertSee('Cédula: 12345678');
    }

    public function test_plantilla_de_receta_omite_la_tabla_si_no_hay_medicamentos(): void
    {
        $consulta = $this->cargarRelaciones($this->crearConsulta($this->crearMedico(), [
            'receta_libre' => 'Solo reposo.',
        ]));

        $this->view('consultas.pdf.receta', ['consulta' => $consulta])
            ->assertDontSee('Medicamento')
            ->assertSee('Solo reposo.');
    }

    /* ============================================================
     |  CONTROL DE ACCESO (ambos PDF)
     ============================================================ */

    public static function rutasPdf(): array
    {
        return [
            'PDF de consulta' => ['consultas.pdf'],
            'PDF de receta'   => ['consultas.receta.pdf'],
        ];
    }

    #[DataProvider('rutasPdf')]
    public function test_medico_no_puede_generar_el_pdf_de_la_consulta_de_otro_medico(string $ruta): void
    {
        $consulta = $this->consultaCompleta($this->crearMedico());

        $this->actingAs($this->crearMedico())
            ->get(route($ruta, $consulta))
            ->assertForbidden();
    }

    #[DataProvider('rutasPdf')]
    public function test_roles_sin_permiso_de_consultas_no_pueden_generar_pdf(string $ruta): void
    {
        $consulta = $this->consultaCompleta();

        foreach (['enfermeria', 'farmacia'] as $rol) {
            $this->actingAs($this->crearUsuarioConRol($rol))
                ->get(route($ruta, $consulta))
                ->assertForbidden();
        }
    }

    #[DataProvider('rutasPdf')]
    public function test_invitado_es_redirigido_al_login_al_pedir_un_pdf(string $ruta): void
    {
        $consulta = $this->consultaCompleta();

        $this->get(route($ruta, $consulta))
            ->assertRedirect(route('login'));
    }

    #[DataProvider('rutasPdf')]
    public function test_pdf_de_consulta_inexistente_devuelve_404(string $ruta): void
    {
        $this->actingAs($this->crearMedico())
            ->get(route($ruta, 999999))
            ->assertNotFound();
    }
}