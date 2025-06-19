{{-- filepath: resources/views/eventos/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <h2>{{ $evento->nombre }}</h2>
    <ul class="list-group mb-3">
        <li class="list-group-item"><strong>Fecha y Hora:</strong> {{ $evento->fecha->format('d/m/Y H:i') }}</li>
        <li class="list-group-item"><strong>Lugar:</strong> {{ $evento->lugar->nombre }}</li>
        <li class="list-group-item"><strong>Capacidad:</strong> {{ $evento->capacidad }}</li>
        <li class="list-group-item"><strong>Entradas Vendidas:</strong> {{ $evento->entradas_vendidas }}</li>
        <li class="list-group-item"><strong>Estado:</strong> {{ ucfirst($evento->estado) }}</li>
    </ul>
    <a href="{{ route('eventos.index') }}" class="btn btn-secondary">Volver</a>
    <a href="{{ route('eventos.edit', $evento) }}" class="btn btn-warning">Editar</a>
</div>
@endsection