<?php
/**
 * Diagnóstico de despliegue. Súbelo a `htdocs/diagnostico.php`, entra a
 * https://tusitio.infinityfreeapp.com/diagnostico.php y después lo borras.
 */

header('Content-Type: text/plain; charset=utf-8');

$nucleo = __DIR__.'/core';

if (is_file(__DIR__.'/index.php')) {
    $codigo = file_get_contents(__DIR__.'/index.php');

    // RUTA_NUCLEO con ruta absoluta: define('RUTA_NUCLEO', '/home/.../sisitpm');
    if (preg_match("/define\(\s*'RUTA_NUCLEO'\s*,\s*'([^']+)'\s*\)/", $codigo, $m)) {
        $nucleo = $m[1];
    }
    // RUTA_NUCLEO con ruta relativa: define('RUTA_NUCLEO', __DIR__.'/core');
    elseif (preg_match("/define\(\s*'RUTA_NUCLEO'\s*,\s*__DIR__\s*\.\s*'\/?([^']*)'/", $codigo, $m)) {
        $nucleo = rtrim(__DIR__.'/'.$m[1], '/');
    }
}

echo "=== SERVIDOR ===\n";
echo 'PHP: '.PHP_VERSION."\n";
echo 'Limite de ejecucion: '.ini_get('max_execution_time')."s\n";
echo 'open_basedir: '.(ini_get('open_basedir') ?: '(sin restriccion)')."\n";
echo 'Raiz del nucleo: '.$nucleo."\n\n";

echo "=== EXTENSIONES ===\n";
foreach (['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'fileinfo', 'gd', 'zip', 'dom', 'curl', 'bcmath'] as $ext) {
    echo str_pad($ext, 14).(extension_loaded($ext) ? 'si' : 'FALTA')."\n";
}

echo "\n=== ARCHIVOS ===\n";
$ruta = __DIR__.'/index.php';
foreach ([
    'indice en htdocs'      => is_file($ruta),
    'core'                  => is_dir($nucleo),
    'vendor/autoload.php'   => is_file($nucleo.'/vendor/autoload.php'),
    'vendor/platform_check' => is_file($nucleo.'/vendor/composer/platform_check.php'),
    '.env'                  => is_file($nucleo.'/.env'),
    'app/Http/Kernel.php'   => is_file($nucleo.'/app/Http/Kernel.php'),
    'public/build manifest' => is_file(__DIR__.'/build/manifest.json'),
    '.htaccess'             => is_file(__DIR__.'/.htaccess'),
] as $etiqueta => $ok) {
    echo str_pad($etiqueta, 26).($ok ? 'ok' : 'FALTA')."\n";
}

echo "\n=== CARPETAS CON PERMISO DE ESCRITURA ===\n";
foreach (['storage', 'storage/app', 'storage/framework', 'storage/framework/cache', 'storage/framework/views', 'storage/framework/sessions', 'storage/logs', 'bootstrap/cache'] as $carpeta) {
    $existe = is_dir($nucleo.'/'.$carpeta);
    echo str_pad($carpeta, 30)
        .'existe:'.($existe ? 'si' : 'NO')
        .'  escribible:'.($existe && is_writable($nucleo.'/'.$carpeta) ? 'si' : 'NO')."\n";
}

echo "\n=== .env ===\n";
foreach (glob($nucleo.'/.env*') as $archivo) {
    echo 'archivo encontrado: '.basename($archivo).' ('.filesize($archivo)." bytes)\n";
}
$env = is_file($nucleo.'/.env') ? parse_ini_file($nucleo.'/.env') : [];
if (! is_array($env)) {
    $env = [];
    echo "No se pudo leer el .env\n";
}
foreach (['APP_ENV', 'APP_DEBUG', 'APP_URL', 'DB_CONNECTION', 'DB_HOST', 'DB_DATABASE', 'DB_USERNAME', 'SESSION_DRIVER', 'CACHE_STORE', 'QUEUE_CONNECTION'] as $clave) {
    echo str_pad($clave, 20).($env[$clave] ?? '(no esta)')."\n";
}
echo str_pad('APP_KEY', 20).(empty($env['APP_KEY']) ? 'VACIA' : 'definida')."\n";

echo "\n=== VENDOR (autoload y clases) ===\n";
foreach ([
    'vendor/autoload.php',
    'vendor/composer/autoload_real.php',
    'vendor/composer/autoload_static.php',
    'vendor/composer/ClassLoader.php',
    'vendor/composer/platform_check.php',
    'vendor/composer/InstalledVersions.php',
    'vendor/composer/installed.json',
    'vendor/laravel/framework/src/Illuminate/Foundation/Application.php',
] as $relativo) {
    echo str_pad($relativo, 58).(is_file($nucleo.'/'.$relativo) ? 'ok' : 'FALTA')."\n";
}

$cuenta = 0;
if (is_dir($nucleo.'/vendor')) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($nucleo.'/vendor', FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($iterator as $archivo) {
        if ($archivo->isFile()) {
            $cuenta++;
        }
    }
}
echo 'archivos dentro de vendor: '.$cuenta." (deben ser unos 8.200)\n";

if (! is_file($nucleo.'/vendor/autoload.php')) {
    echo "No existe vendor/autoload.php: la carpeta vendor no subio completa\n";
} else {
    try {
        require_once $nucleo.'/vendor/autoload.php';
        echo "autoload.php se cargo bien\n";
    } catch (Throwable $e) {
        echo 'ERROR al cargar el autoload: '.$e->getMessage()."\n";
    }

    foreach ([
        'Illuminate\Foundation\Application',
        'Illuminate\Http\Request',
        'Barryvdh\DomPDF\Facade\Pdf',
        'SimpleSoftwareIO\QrCode\Facades\QrCode',
        'PhpOffice\PhpSpreadsheet\Spreadsheet',
        'setasign\Fpdi\Fpdi',
    ] as $clase) {
        echo str_pad($clase, 46).(class_exists($clase) ? 'ok' : 'FALTA (vendor incompleto)')."\n";
    }

    // El vendor exige una version minima de PHP: si el hosting tiene menos, esto corta.
    $check = $nucleo.'/vendor/composer/platform_check.php';
    if (is_file($check)) {
        $codigo = file_get_contents($check);
        if (preg_match('/PHP_VERSION_ID\s*<\s*(\d+)/', $codigo, $m)) {
            $minima = (int) $m[1];
            $actual = PHP_VERSION_ID;
            echo 'PHP minimo que exige el vendor: '.number_format($minima / 10000, 2)."\n";
            echo 'PHP de este hosting: '.PHP_VERSION."\n";
            echo $actual < $minima
                ? "ATENCION: el hosting esta por debajo del minimo, Laravel no arranca\n"
                : "Version de PHP suficiente\n";
        }
    }
}

echo "\n=== BASE DE DATOS ===\n";
try {
    $pdo = new PDO(
        sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $env['DB_HOST'] ?? '', $env['DB_PORT'] ?? '3306', $env['DB_DATABASE'] ?? ''),
        $env['DB_USERNAME'] ?? '',
        $env['DB_PASSWORD'] ?? '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo 'Conexion: correcta'."\n";

    foreach (['usuarios', 'carreras', 'ambientes', 'items', 'activos_fijos', 'sessions', 'cache', 'cache_locks', 'jobs', 'migrations'] as $tabla) {
        try {
            echo str_pad($tabla, 18).$pdo->query("SELECT COUNT(*) FROM `$tabla`")->fetchColumn()." filas\n";
        } catch (Throwable $e) {
            echo str_pad($tabla, 18).'NO EXISTE'."\n";
        }
    }
} catch (Throwable $e) {
    echo 'ERROR DE CONEXION: '.$e->getMessage()."\n";
}

echo "\n=== ULTIMOS ERRORES DE laravel.log ===\n";
$log = $nucleo.'/storage/logs/laravel.log';
if (is_file($log) && filesize($log) > 0) {
    echo implode('', array_slice(file($log, FILE_IGNORE_NEW_LINES), -30))."\n";
} else {
    echo "Sin registros (puede que no se pueda escribir en storage/logs)\n";
}