@extends('errors.layout')

@section('code', '419')
@section('tone', 'sky')
@section('title', 'La página expiró')
@section('panel', 'Protegemos cada formulario para que nadie pueda enviarlo en tu nombre.')
@section('icon') @include('errors._icons', ['name' => 'clock']) @endsection

@section('message')
    El formulario estuvo abierto demasiado tiempo y su token de seguridad venció. Por eso no se guardó nada.
@endsection

@section('detail')
    <strong>¿Qué hacer?</strong> Regresa, recarga la página y vuelve a llenar el formulario. Si estabas en el login o en la verificación de PIN, inicia sesión de nuevo.
@endsection

@section('hint', 'Pasa cuando dejas una pestaña abierta mucho tiempo o tienes la sesión abierta en otra ventana.')

@section('actions')
    @include('errors._actions', ['back' => true, 'home' => true])
@endsection
