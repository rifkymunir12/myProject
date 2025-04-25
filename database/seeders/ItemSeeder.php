<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        Item::create([
            'name'                 => 'Air Palsu',
            'price'                => 5,
            'description'          => 'Ini adalah description air palsunya',
            'amount'               => 5000000,
            'stock_in'             => 5000000,
            'stock_out'            => 0,
            'item_image'           => 'ccccccccc.png',
            'item_unit'            => '9dbd0f2c-ba4a-4e91-83ef-9c78f47fdb96',
        ]);

        Item::create([
            'name'                 => 'Kursi palsu',
            'price'                => 10000,
            'description'          => 'Ini adalah description',
            'amount'               => 20000,
            'stock_in'             => 20000,
            'stock_out'            => 0,
            'item_image'           => 'ddddddd.png',
            'item_unit'            => '9dbd0f2c-aa32-49b1-8a9b-aa9349446c7c',
        ]);

        Item::create([
            'name'                 => 'Gula palsu',
            'price'                => 10,
            'description'          => 'Ini adalah description gula palsunya',
            'amount'               => 5000000,
            'stock_in'             => 5000000,
            'stock_out'            => 0,
            'item_image'           => 'eeeeeee.png',
            'item_unit'            => '9dbd0f2c-bd5d-4c48-a7a3-15951c9aeb2a',
        ]);

        Item::create([
            'name'                 => 'Buku palsu',
            'price'                => 50000,
            'description'          => 'Ini adalah description buku palsunya',
            'amount'               => 20000,
            'stock_in'             => 20000,
            'stock_out'            => 0,
            'item_image'           => 'ffffffffff.png',
            'item_unit'            => '9dbd0f2c-aa32-49b1-8a9b-aa9349446c7c',
        ]);
    }

}