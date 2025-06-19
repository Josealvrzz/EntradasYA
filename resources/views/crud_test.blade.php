<!DOCTYPE html>
<html>
<head>
    <title>CRUD Test</title>
</head>
<body>
    <h1>CRUD Básico</h1>

    <!-- Formulario para crear un nuevo ítem -->
    <form action="{{ url('/crud-test') }}" method="POST">
        @csrf
        <input type="text" name="nombre" placeholder="Nombre">
        <button type="submit">Agregar</button>
    </form>

    <!-- Lista de ítems -->
    <ul>
        @foreach($items as $item)
            <li>
                {{ $item->nombre }}
                <!-- Editar -->
                <a href="{{ url('/crud-test/'.$item->id.'/edit') }}">Editar</a>
                <!-- Eliminar -->
                <form action="{{ url('/crud-test/'.$item->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Eliminar</button>
                </form>
            </li>
        @endforeach