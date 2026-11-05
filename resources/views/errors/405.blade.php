@extends('errors.layout')

@section('code', '405')
@section('tone', 'brand')
@section('title', 'Acción no válida')
@section('panel', 'La página se abrió de una forma que no corresponde.')
@section('icon') @include('errors._icons', ['name' => 'alert']) @endsection

@section('message')
    Intentaste abrir directamente una dirección que solo funciona al enviar un formulario o pulsar un botón.
@endsection

@section('hint', 'Pasa al recargar la página justo después de guardar, o al abrir un enlace copiado de una acción.')

@section('actions')
    @include('errors._actions', ['home' => true, 'back' => true])
@endsection
