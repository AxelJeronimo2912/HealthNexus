@extends('errors.layout')

@php
    // Referencia corta para que soporte encuentre el error en storage/logs/laravel.log por fecha y hora.
    $ref = strtoupper(substr(md5(request()->fullUrl() . microtime()), 0, 8));
@endphp

@section('code', '500')
@section('tone', 'red')
@section('title', 'Algo salió mal de nuestro lado')
@section('panel', 'El equipo técnico ya puede revisar este error en el registro del sistema.')
@section('icon') @include('errors._icons', ['name' => 'alert']) @endsection

@section('message')
    Ocurrió un error inesperado al procesar tu solicitud. No es un problema de tu equipo.
@endsection

@section('detail')
    <strong>Tu información está a salvo.</strong> Si estabas guardando algo, revisa si se registró antes de volver a capturarlo, para no duplicarlo.
@endsection

@section('hint', 'Si el error se repite, avisa al administrador con la hora y la referencia de abajo.')

@section('reference', 'Ref: ' . $ref)

@section('actions')
    @include('errors._actions', ['retry' => true, 'home' => true, 'safe' => true])
@endsection
