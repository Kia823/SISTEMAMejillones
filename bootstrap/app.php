<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'rol'             => \App\Http\Middleware\RolMiddleware::class,
            'puede.verificar' => \App\Http\Middleware\PuedeVerificarMiddleware::class,
            'usuario.activo'  => \App\Http\Middleware\UsuarioActivoMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();

// En hosting compartido la carpeta pública (`htdocs`) vive fuera del núcleo.
// `index.php` define RUTA_PUBLICO y aquí la aplicamos, para que
// `public_path()` siga apuntando a donde el servidor publica los archivos.
if (defined('RUTA_PUBLICO')) {
    $app->usePublicPath(RUTA_PUBLICO);
}

return $app;
