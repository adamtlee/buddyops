<?php

namespace Database\Factories;

use App\Models\Dog;
use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition(): array
    {
        return [
            'dog_id' => Dog::factory(),
            'title' => fake()->randomElement(['Vaccination record', 'Food label', 'Training notes']),
            'type' => fake()->randomElement(['vet_record', 'food_label', 'training', 'other']),
            'file_path' => 'demo-placeholders/not-a-real-upload.pdf',
            'status' => 'uploaded',
        ];
    }
}