<style>
    @page { size: 216mm 330mm; margin: 10mm 8mm; }
    * { box-sizing: border-box; }
    body { font-family: Arial, sans-serif; font-size: 8px; color: #111; }

    .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 4px; margin-bottom: 6px; }
    .header .form-a { font-size: 9px; font-weight: bold; text-align: left; }
    .header .titulo { font-size: 11px; font-weight: bold; text-transform: uppercase; }
    .header .subtitulo { font-size: 10px; font-weight: bold; }
    .header .gestion { font-size: 9px; text-align: right; }

    .datos { width: 100%; border-collapse: collapse; font-size: 8px; margin-bottom: 6px; }
    .datos td { padding: 2px 3px; vertical-align: top; border: 0.5px solid #999; }
    .datos .label { font-weight: bold; background: #f0f0f0; width: 18%; }

    table.inventario { width: 100%; border-collapse: collapse; font-size: 7px; margin-top: 4px; }
    table.inventario thead th {
        background: #e5e5e5; border: 0.7px solid #000; padding: 3px 2px;
        text-align: center; font-weight: bold; font-size: 7px; text-transform: uppercase;
    }
    table.inventario tbody td {
        border: 0.5px solid #555; padding: 2px 3px; vertical-align: top; font-size: 7px;
    }
    table.inventario tbody tr:nth-child(even) { background: #f9f9f9; }

    .col-num { width: 3%; text-align: center; }
    .col-cod { width: 12%; }
    .col-desc { width: 22%; }
    .col-carac { width: 25%; }
    .col-cant { width: 5%; text-align: center; }
    .col-estado { width: 6%; text-align: center; }
    .col-fuente { width: 6%; text-align: center; }
    .col-fecha { width: 7%; text-align: center; }
    .col-uso { width: 10%; }
    .col-obs { width: 4%; }

    .firmas { margin-top: 30px; width: 100%; border-collapse: collapse; }
    .firmas td { text-align: center; padding-top: 30px; font-size: 8px; vertical-align: bottom; width: 33%; }
    .firmas .linea { border-top: 1px solid #000; margin: 0 15px; padding-top: 3px; font-weight: bold; }

    .leyenda { margin-top: 10px; font-size: 7px; border-top: 1px solid #999; padding-top: 4px; }
    .leyenda table { width: 100%; border-collapse: collapse; }
    .leyenda td { padding: 1px 4px; font-size: 7px; }
</style>
