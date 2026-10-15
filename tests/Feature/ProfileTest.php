<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_pagina_de_perfil_se_muestra(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_los_datos_del_perfil_se_pueden_actualizar(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'nombre_completo' => 'Ana Lía Zapana Cortez',
                'email'           => 'ana.lia@ejemplo.bo',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('profile.edit'));

        $user->refresh();

        $this->assertSame('Ana Lía Zapana Cortez', $user->nombre_completo);
        $this->assertSame('ana.lia@ejemplo.bo', $user->email);
    }

    public function test_el_correo_no_se_puede_repetir(): void
    {
        $otro = User::factory()->create(['email' => 'ocupado@ejemplo.bo']);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from('/profile')
            ->patch('/profile', [
                'nombre_completo' => 'Otro Usuario',
                'email'           => $otro->email,
            ])
            ->assertSessionHasErrors(['email']);

        $this->assertNotSame('ocupado@ejemplo.bo', $user->fresh()->email);
    }

    public function test_la_contrasena_se_puede_cambiar(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->put('/password', [
                'current_password'      => 'password',
                'password'              => 'nueva-clave-123',
                'password_confirmation' => 'nueva-clave-123',
            ])
            ->assertSessionHasNoErrors();

        $this->assertTrue($user->fresh()->verifyPassword('nueva-clave-123'));
    }

    public function test_la_contrasena_actual_debe_ser_correcta(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password'      => 'equivocada',
                'password'              => 'nueva-clave-123',
                'password_confirmation' => 'nueva-clave-123',
            ])
            ->assertSessionHasErrorsIn('updatePassword', 'current_password');

        $this->assertTrue($user->fresh()->verifyPassword('password'));
    }
}