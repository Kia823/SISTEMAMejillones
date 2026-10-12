<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|----------------------------------------------------------------------
| RUTAS DE INSTALACIÓN
|----------------------------------------------------------------------
| Local: el núcleo es la carpeta de arriba (public/ y el sistema juntos).
|
| InfinityFree: el servidor solo deja leer dentro de htdocs (open_basedir),
| así que el núcleo va en htdocs/core y la carpeta pública es htdocs.
| En el servidor hay que descomentar estas dos líneas:
|
|   define('RUTA_NUCLEO', __DIR__.'/core');
|   define('RUTA_PUBLICO', '/home/vol1000_3/infinityfree.com/if0_43088437/htdocs');
|
| RUTA_PUBLICO es la importante: hace que public_path() apunte a htdocs, que
| es donde el servidor publica los archivos (ahí van los QR y las fotos).
*/

$rutaNucleo  = defined('RUTA_NUCLEO') ? RUTA_NUCLEO : __DIR__.'/..';
$rutaPublico = defined('RUTA_PUBLICO') ? RUTA_PUBLICO : __DIR__;

// Modo mantenimiento
if (file_exists($maintenance = $rutaNucleo.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Autoload de Composer
require $rutaNucleo.'/vendor/autoload.php';

/** @var \Illuminate\Foundation\Application $app */
$app = require_once $rutaNucleo.'/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle($request = Request::capture())->send();

$kernel->terminate($request, $response);