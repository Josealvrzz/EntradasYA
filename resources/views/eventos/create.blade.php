{{-- filepath: resources/views/eventos/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Crear Evento</h2>
    <form action="{{ route('eventos.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Nombre</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Fecha y Hora</label>
            <input type="datetime-local" name="fecha" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Lugar</label>
            <select name="lugar_id" class="form-control" required>
                @foreach($lugares as $lugar)
                    <option value="{{ $lugar->id }}">{{ $lugar->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Capacidad</label>
            <input type="number" name="capacidad" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Estado</label>
            <select name="estado" class="form-control" required>
                <option value="activo">Activo</option>
                <option value="finalizado">Finalizado</option>
                <option value="cancelado">Cancelado</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Crear</button>
        <a href="{{ route('eventos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection