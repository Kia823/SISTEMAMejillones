<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * La raíz manda al login a quien no tiene sesión y al tablero a quien sí.
     */
    public function test_la_raiz_manda_al_login_sin_sesion(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_la_raiz_manda_al_tablero_con_sesion(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/')
            ->assertRedirect(route('dashboard'));
    }
}