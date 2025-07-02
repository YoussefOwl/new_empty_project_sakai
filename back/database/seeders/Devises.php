<?php

namespace Database\Seeders;

use App\Models\Devise\devises as DeviseDevises;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Devises extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $liste_currencies = array_map(fn($a) => [
            'label' => $a['label'],
            'abrv' => $a['abrv'],
            'created_at' => now()
        ] ,config("config_arrays.liste_currencies"));

        dd(DeviseDevises::insert($liste_currencies));
    }
}
