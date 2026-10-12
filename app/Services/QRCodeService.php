<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QRCodeService
{
    /**
     * Genera el contenido del QR (URL para escanear).
     */
    public function generarCodigoQR(string $codigoActivo): string
    {
        return route('activos.buscarPorCodigo', ['codigo' => $codigoActivo]);
    }

    /**
     * Genera la imagen SVG del QR y la guarda en storage/public/qrcodes.
     * Retorna la ruta relativa (ej: qrcodes/act-2026-0001.svg).
     */
    public function generarImagenQR(string $contenido, string $codigoActivo): string
    {
        $fileName = 'qrcodes/' . Str::slug($codigoActivo) . '-' . uniqid() . '.svg';

        $svg = QrCode::format('svg')
            ->size(300)
            ->margin(1)
            ->generate($contenido);

        Storage::disk('public')->put($fileName, $svg);

        return $fileName;
    }
}
