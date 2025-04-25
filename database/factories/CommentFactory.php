<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content' => 'ini adalah komentar',
            // 'post_id' => '9c9696f9-2c8e-4b5f-a9df-d0b085e335f3' ini yang asli
            'post_id'   => '9d0bbe52-302f-47b4-be3c-5636ceb971c6'
        ];
    }
}
