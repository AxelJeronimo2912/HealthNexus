@extends('errors.layout')

@php
    $headers = method_exists($exception, 'getHeaders') ? $exception->getHeaders() : [];
    $segundos = (int) ($headers['Retry-After'] ?? 0);
    $espera = $segundos > 0
        ? ($segundos >= 60 ? ceil($segundos / 60) . ' ' . (ceil($segundos / 60) == 1 ? 'minuto' : 'minutos') : $segundos . ' segundos')
        : 'unos minutos';
@endphp

@section('code', '429')
@section('tone', 'amber')
@section('title', 'Demasiados intentos')
@section('panel', 'Limitamos los intentos para proteger las cuentas del personal.')
@section('icon') @include('errors._icons', ['name' => 'gauge']) @endsection

@section('message')
    Recibimos muchas solicitudes seguidas desde tu equipo. Espera {{ $espera }} y vuelve a intentarlo.
@endsection

@section('hint', 'Si no recuerdas tu contraseña o tu PIN, usa “¿La olvidaste?” en la pantalla de inicio de sesión en lugar de seguir probando.')

@section('actions')
    @include('errors._actions', ['home' => true, 'back' => true])
@endsection
