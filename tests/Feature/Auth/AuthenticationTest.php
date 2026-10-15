<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_un_usuario_entra_con_su_ci_y_su_contrasena(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'ci'       => $user->ci,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_no_se_entra_con_una_contrasena_incorrecta(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'ci'       => $user->ci,
            'password' => 'contrasena-equivocada',
        ]);

        $this->assertGuest();
    }

    public function test_un_usuario_desactivado_no_puede_entrar(): void
    {
        $user = User::factory()->inactivo()->create();

        $this->post('/login', [
            'ci'       => $user->ci,
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public function test_se_puede_cerrar_la_sesion(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('login', absolute: false));
    }
}