<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'the item',
            'unit_id' => '9dbd0f2c-aa32-49b1-8a9b-aa9349446c7c',
            'price' => 6000,
            'description' => 'sebuah item ya',
            'amount' => 10000, 
            'stock_in'  => 10000,
            'stock_out' => 0,
        ];
    }
}