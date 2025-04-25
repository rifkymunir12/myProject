<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ItemUnit;

class ItemUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        ItemUnit::create([
            'name'          => 'unit',
            'multiple'      => 1,
            'note'          => 'Ini adalah tipe unit',
        ]);

        ItemUnit::create([
            'name'          => 'mililiter',
            'multiple'      =>  100,
            'note'          => 'Ini adalah tipe mililiter',
        ]);

        ItemUnit::create([
            'name'          => 'gram',
            'multiple'      =>  100,
            'note'          => 'Ini adalah tipe gram',
        ]);
    }

}