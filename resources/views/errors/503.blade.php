@extends('errors.layout')

@section('code', '503')
@section('tone', 'sky')
@section('title', 'Estamos en mantenimiento')
@section('panel', 'Estamos aplicando una actualización para mejorar el sistema.')
@section('icon') @include('errors._icons', ['name' => 'wrench']) @endsection

@section('message')
    HealthNexus se está actualizando y volverá en unos minutos. Tus datos no se ven afectados.
@endsection

@section('hint', 'Esta página se recarga sola cada 30 segundos.')

@section('actions')
    @include('errors._actions', ['retry' => true, 'safe' => true])
    <script>setTimeout(function () { location.reload(); }, 30000);</script>
@endsection
