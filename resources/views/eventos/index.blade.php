{{-- filepath: resources/views/eventos/index.blade.php --}}
@extends('layouts.app')

@section('content')
{{-- ...existing code... --}}
<div class="max-w-7xl mx-auto py-6 px-4">
    <h1 class="mb-8 text-2xl font-bold">Gestión de Eventos</h1>

    {{-- Resumen rápido --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded shadow p-6 text-center">
            <h5 class="text-gray-500">Total Eventos</h5>
            <p class="text-3xl font-bold">{{ $totalEventos }}</p>
        </div>
        <div class="bg-white rounded shadow p-6 text-center">
            <h5 class="text-gray-500">Entradas Vendidas</h5>
            <p class="text-3xl font-bold">{{ $entradasVendidas }}</p>
        </div>
        <div class="bg-white rounded shadow p-6 text-center">
            <h5 class="text-gray-500">Próximos Eventos</h5>
            <p class="text-3xl font-bold">{{ $proximosEventos }}</p>
        </div>
    </div>

    {{-- Buscador y botón nuevo evento --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-2">
        <form method="GET" action="{{ route('eventos.index') }}" class="flex flex-1 gap-2">
            <input type="text" name="buscar" class="flex-1 border rounded px-3 py-2" placeholder="Buscar evento, lugar, fecha..." value="{{ request('buscar') }}">
            <button class="px-4 py-2 bg-blue-500 text-white rounded" type="submit">Buscar</button>
        </form>
        <a href="{{ route('eventos.create') }}" class="px-4 py-2 bg-green-500 text-white rounded text-center">+ Nuevo Evento</a>
    </div>

    {{-- Tabla de eventos --}}
    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha y Hora</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Lugar</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Capacidad</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Entradas Vendidas</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($eventos as $evento)
                    <tr>
                        <td class="px-4 py-2">{{ $evento->nombre }}</td>
                        <td class="px-4 py-2">{{ $evento->fecha->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-2">{{ optional($evento->lugar)->nombre ?? 'Sin lugar' }}</td>
                        <td class="px-4 py-2">{{ $evento->capacidad }}</td>
                        <td class="px-4 py-2">{{ $evento->entradas_vendidas }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded text-xs font-semibold
                                @if($evento->estado == 'activo') bg-green-100 text-green-800
                                @elseif($evento->estado == 'finalizado') bg-gray-200 text-gray-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($evento->estado) }}
                            </span>
                        </td>
                        <td class="px-4 py-2 space-x-1">
                            <a href="{{ route('eventos.show', $evento) }}" class="text-blue-600 hover:underline">Ver</a>
                            <a href="{{ route('eventos.edit', $evento) }}" class="text-yellow-600 hover:underline">Editar</a>
                            <form action="{{ route('eventos.destroy', $evento) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline" onclick="return confirm('¿Eliminar este evento?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">No hay eventos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">
            {{ $eventos->links() }}
        </div>
    </div>
</div>
@endsection