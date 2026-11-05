@extends('errors.layout')

@section('code', '404')
@section('tone', 'brand')
@section('title', 'No encontramos lo que buscas')
@section('panel', 'La página o el registro que buscas no existe o ya no está disponible.')
@section('icon') @include('errors._icons', ['name' => 'search']) @endsection

@section('message')
    La dirección no existe, o el registro (paciente, cita, consulta o expediente) fue eliminado o dado de baja.
@endsection

@section('hint')
    Revisa que la dirección esté bien escrita.@auth Dentro del sistema puedes usar el buscador con <kbd style="font:600 12px ui-monospace,monospace;padding:1px 6px;border:1px solid var(--line);border-radius:6px;background:#fff;color:var(--ink)">Ctrl K</kbd> para encontrar módulos y pacientes.@endauth
@endsection

@section('actions')
    @include('errors._actions', ['home' => true, 'back' => true])
@endsection
