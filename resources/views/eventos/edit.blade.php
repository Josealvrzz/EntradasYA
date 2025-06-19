{{-- filepath: resources/views/eventos/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Editar Evento</h2>
    <form action="{{ route('eventos.update', $evento) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Nombre</label>
            <input type="text" name="nombre" class="form-control" value="{{ $evento->nombre }}" required>
        </div>
        <div class="mb-3">
            <label>Fecha y Hora</label>
            <input type="datetime-local" name="fecha" class="form-control" value="{{ $evento->fecha->format('Y-m-d\TH:i') }}" required>
        </div>
        <div class="mb-3">
            <label>Lugar</label>
            <select name="lugar_id" class="form-control" required>
                @foreach($lugares as $lugar)
                    <option value="{{ $lugar->id }}" @if($evento->lugar_id == $lugar->id) selected @endif>{{ $lugar->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Capacidad</label>
            <input type="number" name="capacidad" class="form-control" value="{{ $evento->capacidad }}" required>
        </div>
        <div class="mb-3">
            <label>Estado</label>
            <select name="estado" class="form-control" required>
                <option value="activo" @if($evento->estado == 'activo') selected @endif>Activo</option>
                <option value="finalizado" @if($evento->estado == 'finalizado') selected @endif>Finalizado</option>
                <option value="cancelado" @if($evento->estado == 'cancelado') selected @endif>Cancelado</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('eventos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection