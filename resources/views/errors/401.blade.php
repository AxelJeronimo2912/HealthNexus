@extends('errors.layout')

@section('code', '401')
@section('tone', 'brand')
@section('title', 'Inicia sesión para continuar')
@section('panel', 'Esta sección es solo para personal autorizado del hospital.')
@section('icon') @include('errors._icons', ['name' => 'lock']) @endsection

@section('message')
    Tu sesión no está activa o expiró. Inicia sesión con tu cuenta institucional y tu PIN para volver a entrar.
@endsection

@section('hint', 'Por seguridad, la sesión se cierra tras un tiempo sin actividad.')

@section('actions')
    @include('errors._actions', ['login' => true, 'home' => false, 'back' => false])
@endsection
