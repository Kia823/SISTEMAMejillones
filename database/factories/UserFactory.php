<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * La tabla `usuarios` no tiene `name` ni `email_verified_at`: el nombre
 * completo vive en `nombre_completo` y la verificación de correo no se
 * guarda en este sistema.
 *
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ci'              => (string) fake()->unique()->numerify('########'),
            'nombre_completo' => fake()->name(),
            'email'           => fake()->unique()->safeEmail(),
            'cargo'           => 'Docente',
            'unidad'          => 'Sistemas Informáticos',
            'password'        => Hash::make('password'),
            'rol'             => User::ROL_CUSTODIO,
            'estado'          => 'ACTIVO',
            'id_carrera'      => null,
            'puede_verificar' => false,
            'remember_token'  => \Illuminate\Support\Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => ['rol' => User::ROL_ADMINISTRADOR]);
    }

    public function inventariador(): static
    {
        return $this->state(fn () => ['rol' => User::ROL_INVENTARIADOR]);
    }

    public function ayudante(): static
    {
        return $this->state(fn () => ['rol' => User::ROL_AYUDANTE]);
    }

    public function jefeCarrera(): static
    {
        return $this->state(fn () => ['rol' => User::ROL_JEFE_CARRERA]);
    }

    public function custodio(): static
    {
        return $this->state(fn () => ['rol' => User::ROL_CUSTODIO]);
    }

    public function rector(): static
    {
        return $this->state(fn () => ['rol' => User::ROL_RECTOR]);
    }

    public function inactivo(): static
    {
        return $this->state(fn () => ['estado' => 'INACTIVO']);
    }
}