@extends('errors.layout')

@php
    // Los controladores de HealthNexus envían mensajes en español con abort(403, '...').
    // Los mensajes por defecto (Laravel y spatie/laravel-permission) vienen en inglés: se traducen aquí.
    $raw = trim($exception->getMessage() ?? '');
    $map = [
        '' => null,
        'Forbidden' => null,
        'This action is unauthorized.' => null,
        'User does not have the right permissions.' => 'Tu rol no tiene el permiso necesario para esta sección.',
        'User does not have the right roles.' => 'Esta sección está reservada para otro rol.',
        'User does not have the right roles or permissions.' => 'Tu rol no tiene acceso a esta sección.',
        'User is not logged in.' => 'Tu sesión no está activa.',
        'No autenticado.' => 'Tu sesión no está activa.',
    ];
    $motivo = array_key_exists($raw, $map) ? $map[$raw] : $raw;
    $rol = auth()->check() ? (auth()->user()->getRoleNames()->first() ?? null) : null;
@endphp

@section('code', '403')
@section('tone', 'amber')
@section('title', 'No tienes acceso a esta sección')
@section('panel', 'Cada rol ve solo la información que necesita para su trabajo.')
@section('icon') @include('errors._icons', ['name' => 'shield']) @endsection

@section('message')
    Tu cuenta está activa, pero tu rol no tiene permiso para ver o realizar esta acción.
@endsection

@if ($motivo || $rol)
    @section('detail')
        @if ($motivo)<strong>Motivo:</strong> {{ $motivo }}<br>@endif
        @if ($rol)<strong>Tu rol:</strong> {{ ucfirst($rol) }}@endif
    @endsection
@endif

@section('hint', 'Si necesitas este acceso para tu trabajo, pídele al administrador que revise los permisos de tu rol.')

@section('actions')
    @include('errors._actions', ['home' => true, 'back' => true])
@endsection
