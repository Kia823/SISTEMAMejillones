<?php

namespace App\Support;

use Barryvdh\DomPDF\Facade\Pdf;

class PdfHelper
{
    /**
     * PDF tamaño OFICIO (8.5" × 13" = 612pt × 936pt)
     */
    public static function oficio(string $view, array $data = [], string $orientation = 'portrait')
    {
        $pdf = Pdf::loadView($view, $data);

        // Tamaño OFICIO
        if ($orientation === 'landscape') {
            $pdf->setPaper([0, 0, 936, 612], 'landscape');
        } else {
            $pdf->setPaper([0, 0, 612, 936], 'portrait');
        }

        $pdf->setOption([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => true,
            'defaultFont'          => 'Arial',
            'dpi'                  => 150,
        ]);

        return $pdf;
    }
}
