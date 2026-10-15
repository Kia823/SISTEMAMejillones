<?php

namespace Tests\Feature;

use App\Models\ActaBaja;
use App\Models\ActaTransferencia;
use App\Models\ActivoFijo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * El inventario es de la administradora: nada se aplica sin su aprobación y
 * cada rol solo ve lo que le corresponde.
 */
class AprobacionesTest extends TestCase
{
    use RefreshDatabase;

    protected function activo(array $atributos = []): ActivoFijo
    {
        $categoria = \App\Models\Categoria::firstOrCreate(
            ['codigo' => 'EQP-TEST'],
            ['nombre' => 'Equipos', 'id_ambiente' => $this->ambiente->id_ambiente]
        );

        $fuente = \App\Models\FuenteFinanciamiento::firstOrCreate(
            ['nombre' => 'Tesorería']
        );

        return ActivoFijo::create(array_merge([
            'codigo_activo'     => 'TPM-'.strtoupper(Str::random(8)),
            'nombre'            => 'Laptop',
            'id_categoria'      => $categoria->id_categoria,
            'id_ambiente'       => $this->ambiente->id_ambiente,
            'id_fuente'         => $fuente->id_fuente,
            'id_custodio'       => $this->custodio->id_usuario,
            'tipo_bien'         => 'ACTIVO_FIJO',
            'marca'             => 'Dell',
            'modelo'            => 'Latitude',
            'numero_serie'      => (string) fake()->unique()->numerify('########'),
            'estado_registro'   => 'ASIGNADO',
        ], $atributos));
    }

    protected $carrera;
    protected $ambiente;
    protected $otroAmbiente;
    protected $admin;
    protected $inventariador;
    protected $custodio;
    protected $jefe;
    protected $rector;

    protected function setUp(): void
    {
        parent::setUp();

        $this->carrera = \App\Models\Carrera::create(['nombre' => 'Sistemas Informáticos', 'codigo' => 'SIF']);
        $otra = \App\Models\Carrera::create(['nombre' => 'Contabilidad', 'codigo' => 'CON']);

        $this->ambiente = \App\Models\Ambiente::create([
            'nombre' => 'Laboratorio 3', 'codigo' => 'LAB-3', 'id_carrera' => $this->carrera->id_carrera,
        ]);

        $this->otroAmbiente = \App\Models\Ambiente::create([
            'nombre' => 'Laboratorio 2', 'codigo' => 'LAB-2', 'id_carrera' => $otra->id_carrera,
        ]);

        $this->admin         = User::factory()->admin()->create();
        $this->inventariador = User::factory()->inventariador()->create();
        $this->custodio      = User::factory()->custodio()->create(['id_carrera' => $this->carrera->id_carrera]);
        $this->jefe          = User::factory()->jefeCarrera()->create(['id_carrera' => $this->carrera->id_carrera]);
        $this->rector        = User::factory()->rector()->create();
    }

    public function test_los_paneles_de_actas_se_abren_sin_errores(): void
    {
        $this->activo();

        foreach (['admin', 'inventariador', 'custodio'] as $rol) {
            $this->actingAs($this->{$rol})
                ->get(route('actas.transferencia.index'))
                ->assertOk();

            $this->actingAs($this->{$rol})
                ->get(route('actas.transferencia.create'))
                ->assertOk();
        }

        $this->actingAs($this->admin)
            ->get(route('actas.baja.index'))
            ->assertOk();
    }

    public function test_una_transferencia_queda_pendiente_y_el_bien_no_se_mueve_hasta_la_aprobacion(): void
    {
        $activo = $this->activo();

        $response = $this->actingAs($this->inventariador)->post(route('actas.transferencia.store'), [
            'id_activo'           => $activo->id_activo,
            'id_destino_ambiente' => $this->otroAmbiente->id_ambiente,
            'id_custodio_nuevo'   => $this->rector->id_usuario,
            'fecha_transferencia' => now()->toDateString(),
        ]);

        $acta = ActaTransferencia::firstOrFail();

        $response->assertRedirect(route('actas.transferencia.show', $acta->id_acta_transferencia));
        $this->assertSame(ActaTransferencia::ESTADO_PENDIENTE, $acta->estado);

        // El bien sigue en su ambiente y con su custodio: solo espera.
        $activo->refresh();
        $this->assertSame('EN_TRANSFERENCIA', $activo->estado_registro);
        $this->assertSame($this->ambiente->id_ambiente, $activo->id_ambiente);
        $this->assertSame($this->custodio->id_usuario, $activo->id_custodio);

        // La administración aprueba: recién ahí se mueve.
        $this->actingAs($this->admin)->post(route('actas.transferencia.aprobar', $acta->id_acta_transferencia))
            ->assertSessionHas('status');

        $activo->refresh();
        $this->assertSame($this->otroAmbiente->id_ambiente, $activo->id_ambiente);
        $this->assertSame('ASIGNADO', $activo->estado_registro);
    }

    public function test_rechazar_una_transferencia_deja_el_bien_como_estaba(): void
    {
        $activo = $this->activo(['estado_registro' => 'ASIGNADO']);

        $this->actingAs($this->inventariador)->post(route('actas.transferencia.store'), [
            'id_activo'           => $activo->id_activo,
            'id_destino_ambiente' => $this->otroAmbiente->id_ambiente,
            'id_custodio_nuevo'   => $this->rector->id_usuario,
            'fecha_transferencia' => now()->toDateString(),
        ]);

        $acta = ActaTransferencia::firstOrFail();

        $this->actingAs($this->admin)->post(route('actas.transferencia.rechazar', $acta->id_acta_transferencia));

        $activo->refresh();
        $this->assertSame('ASIGNADO', $activo->estado_registro);
        $this->assertSame($this->ambiente->id_ambiente, $activo->id_ambiente);
    }

    public function test_solo_la_administracion_aprueba_transferencias(): void
    {
        $activo = $this->activo();

        $this->actingAs($this->inventariador)->post(route('actas.transferencia.store'), [
            'id_activo'           => $activo->id_activo,
            'id_destino_ambiente' => $this->otroAmbiente->id_ambiente,
            'id_custodio_nuevo'   => $this->rector->id_usuario,
            'fecha_transferencia' => now()->toDateString(),
        ]);

        $acta = ActaTransferencia::firstOrFail();

        $this->actingAs($this->inventariador)
            ->post(route('actas.transferencia.aprobar', $acta->id_acta_transferencia))
            ->assertForbidden();

        $this->actingAs($this->custodio)
            ->post(route('actas.transferencia.aprobar', $acta->id_acta_transferencia))
            ->assertForbidden();

        $this->assertSame(ActaTransferencia::ESTADO_PENDIENTE, $acta->fresh()->estado);
    }

    public function test_una_baja_no_se_aplica_hasta_que_la_administracion_la_aprueba(): void
    {
        $activo = $this->activo();

        $this->actingAs($this->inventariador)->post(route('actas.baja.store'), [
            'activo_ids'  => [$activo->id_activo],
            'motivo_baja' => 'Equipo averiado.',
        ])->assertSessionHasNoErrors();

        $acta = ActaBaja::firstOrFail();

        $this->assertSame(ActaBaja::ESTADO_PENDIENTE, $acta->estado);
        $this->assertNotSame('BAJA', $activo->fresh()->estado_registro);

        $this->actingAs($this->admin)->post(route('actas.baja.aprobar', $acta->id_acta_baja))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status');

        $this->assertSame('BAJA', $activo->fresh()->estado_registro);
        $this->assertSame(ActaBaja::ESTADO_APROBADA, $acta->fresh()->estado);
    }

    public function test_una_sustitucion_da_de_alta_el_equipo_nuevo_y_guarda_el_viejo_en_el_almacen(): void
    {
        $almacen = \App\Models\Ambiente::create([
            'nombre' => 'Almacén', 'codigo' => 'ALM-1', 'id_carrera' => $this->carrera->id_carrera,
        ]);

        $viejo = $this->activo(['codigo_activo' => 'TPM-VIEJO-1']);

        $this->actingAs($this->inventariador)->post(route('actas.baja.store'), [
            'activo_ids'         => [$viejo->id_activo],
            'motivo_baja'        => 'Se cambia por el repuesto del almacén.',
            'es_sustitucion'     => 1,
            'sustituto_de'       => $viejo->id_activo,
            'sustituto_codigo'   => 'TPM-NUEVO-1',
            'sustituto_nombre'   => 'Laptop de repuesto',
            'sustituto_marca'    => 'HP',
        ]);

        $acta = ActaBaja::firstOrFail();
        $this->assertTrue($acta->es_sustitucion);

        $this->actingAs($this->admin)->post(route('actas.baja.aprobar', $acta->id_acta_baja));

        // El viejo queda de baja, guardado en el almacén.
        $viejo->refresh();
        $this->assertSame('BAJA', $viejo->estado_registro);
        $this->assertSame($almacen->id_ambiente, $viejo->id_ambiente);

        // El nuevo entra a trabajar en el mismo ambiente y con el mismo custodio.
        $nuevo = ActivoFijo::where('codigo_activo', 'TPM-NUEVO-1')->first();

        $this->assertNotNull($nuevo);
        $this->assertSame($this->ambiente->id_ambiente, $nuevo->id_ambiente);
        $this->assertSame($this->custodio->id_usuario, $nuevo->id_custodio);
    }

    public function test_un_custodio_solo_ve_los_activos_de_su_ambiente(): void
    {
        $suyo = $this->activo(['codigo_activo' => 'TPM-SUYO-1']);
        $ajeno = $this->activo([
            'codigo_activo' => 'TPM-AJENO-1',
            'id_ambiente'   => $this->otroAmbiente->id_ambiente,
            'id_custodio'   => $this->rector->id_usuario,
        ]);

        $vistos = ActivoFijo::paraUsuario($this->custodio)->pluck('codigo_activo');

        $this->assertTrue($vistos->contains('TPM-SUYO-1'));
        $this->assertFalse($vistos->contains('TPM-AJENO-1'));

        $this->actingAs($this->custodio)
            ->get(route('activos.tarjeta', $ajeno->id_activo))
            ->assertNotFound();
    }

    public function test_el_jefe_de_carrera_ve_todos_los_activos_de_su_carrera_pero_no_de_otras(): void
    {
        $deSuCarrera = $this->activo(['codigo_activo' => 'TPM-SIF-1']);
        $deOtra = $this->activo([
            'codigo_activo' => 'TPM-CON-1',
            'id_ambiente'   => $this->otroAmbiente->id_ambiente,
            'id_custodio'   => $this->rector->id_usuario,
        ]);

        $vistos = ActivoFijo::paraUsuario($this->jefe)->pluck('codigo_activo');

        $this->assertTrue($vistos->contains('TPM-SIF-1'));
        $this->assertFalse($vistos->contains('TPM-CON-1'));

        $this->actingAs($this->jefe)->get(route('ambientes.detalle', $this->otroAmbiente->id_ambiente))
            ->assertNotFound();
    }

    public function test_un_rol_de_solo_lectura_no_registra_verificaciones_ni_solicita_bajas(): void
    {
        $activo = $this->activo();

        $this->actingAs($this->custodio)
            ->post(route('verificaciones.guardarItem'), [
                'activo_id'           => $activo->id_activo,
                'ambiente_escaneo_id' => $this->ambiente->id_ambiente,
                'es_correspondencia'  => 1,
            ])
            ->assertForbidden();

        $this->actingAs($this->custodio)
            ->post(route('actas.baja.store'), [
                'activo_ids'  => [$activo->id_activo],
                'motivo_baja' => 'No me sirve.',
            ])
            ->assertForbidden();
    }

    public function test_la_administradora_desactiva_al_ayudante_y_este_no_puede_entrar(): void
    {
        $ayudante = User::factory()->ayudante()->create();

        $this->actingAs($ayudante)->get(route('dashboard'))->assertOk();

        $this->actingAs($this->admin)->put(route('usuarios.update', $ayudante->id_usuario), [
            'nombre_completo' => $ayudante->nombre_completo,
            'ci'              => $ayudante->ci,
            'email'           => $ayudante->email,
            'cargo'           => $ayudante->cargo,
            'unidad'          => $ayudante->unidad,
            'rol'             => User::ROL_AYUDANTE,
            'estado'          => 'INACTIVO',
        ])->assertSessionHasNoErrors();

        $this->assertSame('INACTIVO', $ayudante->fresh()->estado);

        // La sesión viva se corta: vuelve al login.
        // Se relee el usuario de la base, como lo haría una petición real.
        $this->actingAs(User::findOrFail($ayudante->id_usuario))
            ->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_la_administradora_no_puede_quedarse_sin_una_gestion_activa(): void
    {
        $this->actingAs($this->admin)->put(route('usuarios.update', $this->admin->id_usuario), [
            'nombre_completo' => $this->admin->nombre_completo,
            'ci'              => $this->admin->ci,
            'email'           => $this->admin->email,
            'cargo'           => $this->admin->cargo,
            'unidad'          => $this->admin->unidad,
            'rol'             => User::ROL_ADMINISTRADOR,
            'estado'          => 'INACTIVO',
        ])->assertSessionHas('error');

        $this->assertSame('ACTIVO', $this->admin->fresh()->estado);
    }

    public function test_reescanear_un_activo_no_pisa_la_verificacion_ya_aprobada(): void
    {
        $activo = $this->activo();

        $this->actingAs($this->inventariador)->post(route('verificaciones.guardarItem'), [
            'activo_id'           => $activo->id_activo,
            'ambiente_escaneo_id' => $this->ambiente->id_ambiente,
            'es_correspondencia'  => 1,
            'estado_fisico'       => 'B',
        ])->assertOk();

        $verificacion = \App\Models\VerificacionFisica::firstOrFail();

        $this->actingAs($this->admin)->post(route('verificaciones-aprobacion.aprobar', $verificacion->id_verificacion));

        $this->assertSame(\App\Models\VerificacionFisica::ESTADO_APROBADA, $verificacion->fresh()->estado_aprobacion);

        // Segundo escaneo: la aprobación anterior se conserva.
        $this->actingAs($this->inventariador)->post(route('verificaciones.guardarItem'), [
            'activo_id'           => $activo->id_activo,
            'ambiente_escaneo_id' => $this->ambiente->id_ambiente,
            'es_correspondencia'  => 1,
            'estado_fisico'       => 'R',
        ])->assertOk();

        $this->assertSame(2, \App\Models\VerificacionFisica::count());
        $this->assertSame(\App\Models\VerificacionFisica::ESTADO_APROBADA, $verificacion->fresh()->estado_aprobacion);
        $this->assertSame(\App\Models\VerificacionFisica::ESTADO_PENDIENTE, \App\Models\VerificacionFisica::latest('id_verificacion')->first()->estado_aprobacion);
    }

    public function test_el_qr_devuelve_custodio_ambiente_y_si_es_mio(): void
    {
        $activo = $this->activo(['codigo_activo' => 'TPM-QR-1']);

        $this->actingAs($this->custodio)
            ->getJson(route('activos.buscarPorCodigo', ['codigo' => 'TPM-QR-1']))
            ->assertOk()
            ->assertJsonPath('activo.custodio', $this->custodio->nombre_completo)
            ->assertJsonPath('activo.ambiente', 'Laboratorio 3')
            ->assertJsonPath('es_mi_activo', true);
    }

    public function test_la_sincronizacion_offline_registra_y_respeta_el_ambito(): void
    {
        // Un docente al que la administradora habilitó la verificación:
        // su alcance son los activos de su ambiente, no todo el inventario.
        $verificador = User::findOrFail($this->custodio->id_usuario);
        $verificador->update(['puede_verificar' => true]);

        $suyo = $this->activo(['codigo_activo' => 'TPM-OFF-1']);
        $ajeno = $this->activo([
            'codigo_activo' => 'TPM-OFF-2',
            'id_ambiente'   => $this->otroAmbiente->id_ambiente,
            'id_custodio'   => $this->rector->id_usuario,
        ]);

        $response = $this->actingAs($verificador)
            ->postJson(route('sincronizacion.verificaciones'), [
                'verificaciones' => [
                    [
                        'id_local'            => 'v-1',
                        'codigo_activo'       => $suyo->codigo_activo,
                        'ambiente_escaneo_id' => $this->ambiente->id_ambiente,
                        'fecha_verificacion'  => now()->toIso8601String(),
                        'estado_fisico'       => 'B',
                    ],
                    [
                        'id_local'            => 'v-2',
                        'codigo_activo'       => $ajeno->codigo_activo,
                        'ambiente_escaneo_id' => $this->otroAmbiente->id_ambiente,
                        'fecha_verificacion'  => now()->toIso8601String(),
                    ],
                ],
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        // El ajeno no entra: el dispositivo solo debe borrar lo ya guardado.
        $this->assertSame(['v-1'], array_column($response->json('registradas'), 'id_local'));
        $this->assertSame(['v-2'], array_column($response->json('rechazadas'), 'id_local'));

        $this->assertSame(1, \App\Models\VerificacionFisica::count());

        $creada = \App\Models\VerificacionFisica::first();
        $this->assertTrue($creada->sincronizado_offline);
        $this->assertSame($verificador->id_usuario, $creada->id_verificador);
        $this->assertSame(\App\Models\VerificacionFisica::ESTADO_PENDIENTE, $creada->estado_aprobacion);
    }
}