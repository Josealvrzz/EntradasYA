<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\Lugar;
use App\Models\Evento;
use App\Models\Entrada;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        /*User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);*/

 $this->call([
        LugarSeeder::class,
        EventoSeeder::class,
        EntradaSeeder::class,
    ]);

    }
}

class LugarSeeder extends Seeder
{
    public function run()
    {
        Lugar::create([
            'nombre' => 'Teatro Principal',
            'direccion' => 'Calle 123, Ciudad',
            'capacidad' => 500,
        ]);
        Lugar::create([
            'nombre' => 'Estadio Central',
            'direccion' => 'Avenida 456, Ciudad',
            'capacidad' => 2000,
        ]);
        Lugar::create([
            'nombre' => 'Sala de Conciertos',
            'direccion' => 'Boulevard 789, Ciudad',
            'capacidad' => 800,
        ]);
    }
}

class EventoSeeder extends Seeder
{
    public function run()
    {
        $lugares = Lugar::all();

        Evento::create([
            'nombre' => 'Concierto de Rock',
            'fecha' => Carbon::now()->addDays(10),
            'lugar_id' => $lugares[0]->id ?? 1,
            'capacidad' => 500,
            'estado' => 'activo',
        ]);
        Evento::create([
            'nombre' => 'Obra de Teatro',
            'fecha' => Carbon::now()->addDays(20),
            'lugar_id' => $lugares[1]->id ?? 2,
            'capacidad' => 2000,
            'estado' => 'activo',
        ]);
        Evento::create([
            'nombre' => 'Festival de Jazz',
            'fecha' => Carbon::now()->addDays(30),
            'lugar_id' => $lugares[2]->id ?? 3,
            'capacidad' => 800,
            'estado' => 'activo',
        ]);
    }
}

class EntradaSeeder extends Seeder
{
    public function run()
    {
        $eventos = Evento::all();

        foreach ($eventos as $evento) {
            for ($i = 1; $i <= 10; $i++) {
                Entrada::create([
                    'evento_id' => $evento->id,
                    'usuario_id' => null,
                    'codigo' => strtoupper(uniqid('ENTRADA')),
                ]);
            }
        }
    }
}