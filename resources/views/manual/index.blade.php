@extends('layouts.app')
@section('title','Manual del Sistema')
@section('content')
<div class="container py-4">
<h2>Manual de Usuario — SISActivos</h2>
<p class="text-muted">Guía rápida del sistema de verificación física de activos.</p>

<h4>Rol: Administrador</h4>
<ul>
  <li>Gestiona usuarios, carreras, ambientes, categorías y activos.</li>
  <li>Aprueba/rechaza verificaciones y actas de baja/transferencia.</li>
  <li>Configuración y copias de seguridad.</li>
</ul>

<h4>Rol: Inventariador</h4>
<ul>
  <li>Selecciona carrera y ambiente, inicia cronómetro.</li>
  <li>Escanea QR de activos o marca manualmente como verificado/observado.</li>
  <li>Finaliza verificación para enviar a aprobación.</li>
  <li>Genera reportes PDF/Excel.</li>
</ul>

<h4>Rol: Ayudante (temporal)</h4>
<ul>
  <li>Apoya la verificación física en campo.</li>
  <li>Puede escanear y marcar activos.</li>
</ul>

<h4>Rol: Jefe de Carrera / Docente Custodio / Rector</h4>
<ul>
  <li>Solo lectura: revisa activos y verificaciones.</li>
  <li>No registra ni modifica nada.</li>
</ul>

<h4>Flujo de verificación</h4>
<ol>
  <li>Elegir carrera y ambiente.</li>
  <li>Iniciar cronómetro.</li>
  <li>Escanear QR: el activo verificado sube al inicio de la lista.</li>
  <li>Marcar observaciones si corresponde.</li>
  <li>Finalizar: si faltan activos, el sistema avisa.</li>
  <li>El administrador aprueba las verificaciones.</li>
</ol>
</div>
@endsection