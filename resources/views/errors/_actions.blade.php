{{--
    Botones comunes. Uso: @include('errors._actions', ['back' => true, 'home' => true, 'login' => false, 'retry' => false])
    No consulta la sesión si $safe es true (para 500 y 503).
--}}
@php
    $safe = $safe ?? false;
    $logged = ! $safe && auth()->check();
    $homeUrl = $logged && Route::has('dashboard') ? route('dashboard') : url('/');
    $prev = url()->previous();
    $hasPrev = $prev && $prev !== url()->current();
@endphp

@if (! empty($retry))
    <a class="btn btn-primary" href="{{ url()->current() }}">@include('errors._icons', ['name' => 'refresh']) Intentar de nuevo</a>
@endif

@if (! empty($login) && Route::has('login'))
    <a class="btn btn-primary" href="{{ route('login') }}">@include('errors._icons', ['name' => 'login']) Iniciar sesión</a>
@endif

@if (! empty($home))
    <a class="btn {{ empty($retry) && empty($login) ? 'btn-primary' : 'btn-secondary' }}" href="{{ $homeUrl }}">
        @include('errors._icons', ['name' => 'home']) {{ $logged ? 'Ir a mi panel' : 'Ir al inicio' }}
    </a>
@endif

@if (! empty($back) && $hasPrev)
    <a class="btn btn-secondary" href="{{ $prev }}">@include('errors._icons', ['name' => 'back']) Volver</a>
@endif
